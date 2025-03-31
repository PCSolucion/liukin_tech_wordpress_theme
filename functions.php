<?php
function themename_custom_logo_setup() {
    $defaults = array(
        'height'      => 55,
        'width'       => 55,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
        'unlink-homepage-logo' => true, 
    );
    add_theme_support( 'custom-logo', $defaults );
}
add_action( 'after_setup_theme', 'themename_custom_logo_setup' );

/**
 * Encola hojas de estilo y scripts.
 */
function liukin_agregar_css_js() {
    // Desregistrar el estilo actual
    wp_deregister_style('style');
    
    // Obtener la ruta del CSS
    $css_path = get_stylesheet_uri();
    $css_version = filemtime(get_stylesheet_directory() . '/style.css');
    wp_enqueue_style('liukin-style', $css_path, array(), $css_version);

    // Agregar JavaScript para scroll infinito
    wp_enqueue_script('liukin-infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.js', array(), '1.0', true);
    
    // Agregar JavaScript para lightbox solo en single.php
    if (is_single()) {
        wp_enqueue_script('liukin-lightbox', get_template_directory_uri() . '/js/lightbox.js', array(), '1.0', true);
    }
    
    // Agregar atributo defer al script
    add_filter('script_loader_tag', function($tag, $handle) {
        if ('liukin-infinite-scroll' === $handle || 'liukin-lightbox' === $handle) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);
    
    // Preparar variables para JavaScript
    $js_vars = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('liukin_infinite_scroll'),
        'loading' => __('Cargando...', 'liukin'),
        'no_more' => __('No hay más entradas', 'liukin')
    );

    // Agregar variables específicas según el tipo de página
    if (is_archive()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $js_vars['termId'] = $term->term_id;
            $js_vars['taxonomy'] = $term->taxonomy;
        }
    } elseif (is_search()) {
        $js_vars['searchQuery'] = get_search_query();
    }
    
    // Pasar variables a JavaScript
    wp_add_inline_script('liukin-infinite-scroll', 'window.liukinInfinite = ' . json_encode($js_vars) . ';', 'before');
}
add_action('wp_enqueue_scripts', 'liukin_agregar_css_js');


// Soporte para miniaturas de publicaciones
/**
 * Configura el soporte del tema para miniaturas de publicaciones y la etiqueta del título.
 */
function liukin_setup() {
    if ( function_exists('add_theme_support') ) {
        add_theme_support('post-thumbnails');
    }
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'liukin_setup' );
// Forzar atributo alt en imágenes
/**
 * Asegura que todas las imágenes tengan atributos alt.
 *
 * @param string $content El contenido de la publicación.
 * @return string Contenido de la publicación modificado.
 */
function add_alt_tags($content) {
    global $post;
    preg_match_all('/<img (.*?)\/>/', $content, $images);
    if (!is_null($images)) {
        foreach ($images[1] as $index => $value) {
            if (!preg_match('/alt=/', $value)) {
                $new_img = str_replace('<img', '<img alt="'.$post->post_title.'"', $images[0][$index]);
                $content = str_replace($images[0][$index], $new_img, $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'add_alt_tags', 99999);

// Eliminar el atributo type de las etiquetas de script y estilo
/**
 * Inicia el almacenamiento en búfer de salida.
 */
add_action('wp_loaded', 'output_buffer_start');
function output_buffer_start() { 
    ob_start("output_callback"); 
}

/**
 * Finaliza el almacenamiento en búfer de salida.
 */
add_action('shutdown', 'output_buffer_end');
function output_buffer_end() { 
    if (ob_get_length() > 0) { ob_end_clean(); }
}

/**
 * Función de devolución de llamada para modificar la salida almacenada en búfer.
 *
 * @param string $buffer La salida almacenada en búfer.
 * @return string Salida modificada.
 */
function output_callback($buffer) {
    return preg_replace( "%[ ]type=[\'\"]text\/(javascript|css)[\'\"]%", '', $buffer );
}

/**
 * Desactivar la carga del JS de los emojis de Wordpress
 */
add_action('init', 'disable_emojis');
function disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}

// Forzar el uso del editor clásico
add_filter('use_block_editor_for_post', '__return_false', 10);

//Desactivar la carga del css que necesita Guttenberg, el editor de bloques
function remove_block_library_css() {
    wp_dequeue_style('wp-block-library');
}
add_action('wp_enqueue_scripts', 'remove_block_library_css', 100);

// Cerrar comentarios en el frontend
function disable_comments_status() {
    return false;
}
add_filter('comments_open', 'disable_comments_status', 20, 2);
add_filter('pings_open', 'disable_comments_status', 20, 2);

// Ocultar la sección de comentarios en el frontend
function disable_comments_hide_existing_comments($comments) {
    return array();
}
add_filter('comments_array', 'disable_comments_hide_existing_comments', 10, 2);

// Función para reemplazar el dominio de YouTube por youtube-nocookie.com
function replace_youtube_embed_url($html, $url, $attr, $post_id) {
    if (strpos($url, 'youtube.com') !== false) {
        // Reemplazar el dominio de YouTube por youtube-nocookie.com
        $nocookie_url = str_replace('youtube.com', 'youtube-nocookie.com', $url);
        // Agregar parámetros adicionales para mejorar la privacidad
        $nocookie_url = add_query_arg(array(
            'rel' => 0,   // No mostrar videos relacionados al final
            'modestbranding' => 1,  // Minimizar el branding de YouTube
            'iv_load_policy' => 3   // Deshabilitar las anotaciones
        ), $nocookie_url);
        // Reemplazar la URL en el HTML incrustado
        $html = str_replace($url, $nocookie_url, $html);
    }
    return $html;
}
add_filter('embed_oembed_html', 'replace_youtube_embed_url', 10, 4);
add_filter('embed_handler_html', 'replace_youtube_embed_url', 10, 4);

// Deshabilitar el uso de cookies en WordPress para usuarios no autenticados
add_action('init', 'disable_cookies_for_guests', 1);
function disable_cookies_for_guests() {
    if (!is_user_logged_in()) {
        // Deshabilitar sesiones de PHP
        if (session_id()) {
            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // Eliminar cualquier cookie que ya se haya establecido
        foreach ($_COOKIE as $cookie_name => $cookie_value) {
            setcookie($cookie_name, '', time() - 3600, '/');
        }
        
        // Deshabilitar la configuración de cookies
        $_COOKIE = array();
        $_SESSION = array();
    }
}

// Deshabilitar el uso de cookies para comentarios
add_filter('comment_cookie_lifetime', '__return_zero');

// Función para manejar la carga de posts por AJAX
function liukin_load_more_posts() {
    check_ajax_referer('liukin_infinite_scroll', 'nonce');
    
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => get_option('posts_per_page'),
        'paged' => $page,
        'post_status' => 'publish'
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $categories = get_the_category();
            ?>
            <div class="card-body phome">
                <?php 
                if (!empty($categories)) {
                    foreach ($categories as $cat) {
                        printf(
                            '<a href="%s" class="cathome %s" title="Ver todos los post de %s">%s</a> ',
                            esc_url(get_category_link($cat->term_id)),
                            esc_attr($cat->slug),
                            esc_attr($cat->name),
                            esc_html($cat->name)
                        );
                    }
                }
                ?>
                <a href="<?php the_permalink(); ?>">
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                </a>
            </div>
            <?php
        }
    }
    wp_reset_postdata();
    die();
}
add_action('wp_ajax_liukin_load_more_posts', 'liukin_load_more_posts');
add_action('wp_ajax_nopriv_liukin_load_more_posts', 'liukin_load_more_posts');
function theme_enqueue_scripts() {
    wp_enqueue_script('theme-toggle', get_template_directory_uri() . '/js/theme-toggle.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
?>