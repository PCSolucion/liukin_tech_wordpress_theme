<?php
// Soporte de logotipo personalizado
add_theme_support( 'custom-logo' );

/**
 * Configura el logotipo personalizado con parámetros predeterminados.
 */
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

// Requiere los archivos necesarios para el menú
require_once get_template_directory() . '/template-parts/class-wp-bootstrap-navwalker.php';

/**
 * Encola hojas de estilo y scripts.
 */
function liukin_agregar_css_js() {
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
}
add_action( 'wp_enqueue_scripts', 'liukin_agregar_css_js' );

/**
 * Registra el "navwalker" personalizado para el menú.
 */
function register_navwalker() {
    require_once get_template_directory() . '/template-parts/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );

/**
 * Registra los menús de navegación.
 */
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'THEMENAME' ),
) );

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

// Añadir barra lateral
/**
 * Registra el área de widgets en la barra lateral derecha.
 */
function liukin_widgets() {
    register_sidebar( array(
        'id'            => 'widgets-derecha',
        'name'          => __( 'Right Sidebar' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ) );
}
add_action('widgets_init', 'liukin_widgets');

// Longitud personalizada del extracto
/**
 * Establece la longitud personalizada del extracto.
 *
 * @param int $length El número de palabras en el extracto.
 * @return int Longitud modificada del extracto.
 */
function custom_excerpt_length( $length ) {
    return 37;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

// Eliminar [...] del extracto
/**
 * Elimina el texto [...] del extracto.
 *
 * @param string $more El texto mostrado dentro del enlace "more".
 * @return string Cadena vacía para reemplazar el [...].
 */
function new_excerpt_more( $more ) {
    return '';
}
add_filter('excerpt_more', 'new_excerpt_more');

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

?>