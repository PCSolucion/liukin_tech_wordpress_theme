<?php
 /**
  * Configura el logo personalizado del tema.
  * 
  * Establece las dimensiones predeterminadas y opciones para el logo del sitio.
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
 
 /**
  * Encola hojas de estilo y scripts.
  * 
  * Registra y carga los estilos CSS y archivos JavaScript necesarios para el tema,
  * y configura los parámetros para el scroll infinito y lightbox.
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
  * 
  * Comienza a capturar la salida HTML para poder modificarla antes de enviarla al navegador.
  */
 add_action('wp_loaded', 'output_buffer_start');
 function output_buffer_start() { 
     ob_start("output_callback"); 
 }
 
 /**
  * Finaliza el almacenamiento en búfer de salida.
  * 
  * Asegura que el búfer se limpie correctamente al finalizar la ejecución.
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
  * 
  * Mejora el rendimiento eliminando scripts y estilos relacionados con emojis.
  */
 add_action('init', 'disable_emojis');
 function disable_emojis() {
     remove_action('wp_head', 'print_emoji_detection_script', 7);
     remove_action('wp_print_styles', 'print_emoji_styles');
 }
 
 // Forzar el uso del editor clásico
 add_filter('use_block_editor_for_post', '__return_false', 10);
 
 /**
  * Desactiva la carga del CSS que necesita Gutenberg
  * 
  * Mejora el rendimiento eliminando hojas de estilo innecesarias del editor de bloques.
  */
 function remove_block_library_css() {
     wp_dequeue_style('wp-block-library');
 }
 add_action('wp_enqueue_scripts', 'remove_block_library_css', 100);
 
 /**
  * Deshabilita los comentarios en las publicaciones y páginas
  * 
  * @return bool Siempre devuelve false para cerrar los comentarios
  */
 function disable_comments_status() {
     return false;
 }
 add_filter('comments_open', 'disable_comments_status', 20, 2);
 add_filter('pings_open', 'disable_comments_status', 20, 2);
 
 /**
  * Oculta los comentarios existentes en el frontend
  * 
  * @param array $comments Lista de comentarios a mostrar
  * @return array Array vacío para no mostrar comentarios
  */
 function disable_comments_hide_existing_comments($comments) {
     return array();
 }
 add_filter('comments_array', 'disable_comments_hide_existing_comments', 10, 2);
 
 /**
  * Reemplaza el dominio de YouTube por youtube-nocookie.com
  * 
  * Mejora la privacidad del usuario al utilizar la versión sin cookies de YouTube
  * y añade parámetros para reducir el rastreo.
  * 
  * @param string $html El HTML del embed
  * @param string $url La URL original
  * @param array $attr Atributos del embed
  * @param int $post_id ID de la publicación
  * @return string HTML modificado con URL mejorada para privacidad
  */
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
 
 /**
  * Deshabilita el uso de cookies en WordPress para usuarios no autenticados
  * 
  * Mejora la privacidad al eliminar todas las cookies para visitantes no registrados,
  * eliminando el seguimiento innecesario.
  */
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
 
 /**
  * Maneja la carga de posts adicionales mediante AJAX
  * 
  * Implementa la funcionalidad de scroll infinito, cargando más publicaciones
  * cuando el usuario llega al final de la página.
  */
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
 
 /**
  * Modifica el orden de posts en las páginas de etiquetas
  * 
  * Establece el criterio de ordenación para las páginas de etiquetas,
  * mostrando las publicaciones más recientes primero.
  * 
  * @param WP_Query $query Objeto de consulta de WordPress
  */
 function modify_tag_query($query) {
     if ($query->is_tag() && $query->is_main_query()) {
         $query->set('orderby', 'date');
         $query->set('order', 'ASC'); // ASC para orden ascendente (más antiguos primero)
     }
 }
 add_action('pre_get_posts', 'modify_tag_query');
 
 /**
  * Registra scripts adicionales para el tema
  * 
  * Añade scripts personalizados utilizados por el tema.
  */
 function theme_enqueue_scripts() {
     wp_enqueue_script('theme-toggle', get_template_directory_uri() . '/js/theme-toggle.js', array(), '1.0.0', true);
 }
 add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
 
 /**
  * Muestra los iconos de armas para un post
  * Versión optimizada con coincidencia más precisa
  */
 function liukin_display_weapon_icons($post_id = null) {
     // Si no se proporciona ID, usar el post actual
     if (!$post_id) {
         $post_id = get_the_ID();
     }
     
     // Verificar si el post tiene etiquetas
     $post_tags = get_the_tags($post_id);
     if (!$post_tags) {
         return; // No mostrar nada si no hay etiquetas
     }
     
     // Registro de armas mostradas para debugging
     $debug_log = '';
     
     // Array de armas disponibles con sus iconos
     $weapons = array(
         'arco' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789066/arco2_gfza10.png',
         'estoque' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/rapier_i1icgl.webp',
         'mangual' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789072/latigo_wzv45l.png',
         'espadon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879077/serenidad_qoxzez.png',
         'espada' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789067/espadaescudo_ntg78c.png',
         'baculodefuego' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789068/firestaff_mjvj7w.png',
         'baculodevida' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879096/baculo_de_vida_progenitor_pvfzzj.webp',
         'martillo' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/martillodeguerra_katfte.png',
         'mosquete' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/mosquete2_uuvqiy.png',
         'hachuela' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789070/hatchet_dlslsr.webp',
         'trabuco' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879119/bluder_tgrmqt.webp',
         'granhacha' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789070/gran_hacha_fodiyg.webp',
         'manopladehielo' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879151/guantedehielo_u43hdy.png',
         'manopladevacio' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879170/void_uzngfx.webp',
         'lanza' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789071/lanza2_ojc6vy.png',
         'hachadoble' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879119/hachadoble_vqxtea.webp'
     );
     
     // Mapeo de variantes de nombres de armas para coincidencia más flexible
     $weapons_variants = array(
         'arco' => array('arco'),
         'estoque' => array('estoque', 'rapier'),
         'mangual' => array('mangual', 'latigo', 'látigo'),
         'espadon' => array('espadon', 'espadón', 'espada grande', 'espadagrande'),
         'espada' => array('espada', 'espada y escudo', 'espadaescudo', 'sword'),
         'baculodefuego' => array('baculodefuego', 'baculo de fuego', 'báculo de fuego', 'báculodefuego', 'baculo fuego', 'baculofuego', 'firestaff'),
         'baculodevida' => array('baculodevida', 'baculo de vida', 'báculo de vida', 'báculodevida', 'baculo vida', 'baculovida', 'lifestaff'),
         'martillo' => array('martillo', 'martillo de guerra', 'martillodeguerra', 'hammer'),
         'mosquete' => array('mosquete', 'musket'),
         'hachuela' => array('hachuela', 'hacha pequeña', 'hatchet'),
         'trabuco' => array('trabuco', 'blunderbuss', 'bluder'),
         'granhacha' => array('granhacha', 'gran hacha', 'hacha grande', 'hachagrande', 'greataxe'),
         'manopladehielo' => array('manopladehielo', 'manopla de hielo', 'manoplahielo', 'guante de hielo', 'ice gauntlet'),
         'manopladevacio' => array('manopladevacio', 'manopla de vacio', 'manoplavacio', 'guante de vacio', 'void gauntlet'),
         'lanza' => array('lanza', 'spear'),
         'hachadoble' => array('hacha-doble', 'hacha doble', 'hachadoble')
     );
     
     // Array para rastrear las armas que ya se han mostrado
     $shown_weapons = array();
     
     // Obtener nombres de etiquetas para registro
     $tag_names = array();
     foreach ($post_tags as $tag) {
         $tag_names[] = $tag->name;
     }
     $debug_log .= "Post ID: {$post_id}, Título: " . get_the_title($post_id) . "\n";
     $debug_log .= "Etiquetas: " . implode(', ', $tag_names) . "\n";
     
     // Primero normalizar todas las etiquetas para comparaciones precisas
     $normalized_tags = array();
     foreach ($post_tags as $tag) {
         $tag_name = strtolower(trim($tag->name));
         $tag_name_clean = str_replace(array('-', ' ', '_'), '', $tag_name);
         $normalized_tags[] = array(
             'original' => $tag_name,
             'clean' => $tag_name_clean,
             'tag' => $tag
         );
     }
     
     // Array para almacenar las coincidencias exactas encontradas
     $found_weapons = array();
     
     // Primera pasada: buscar coincidencias exactas
     foreach ($weapons_variants as $weapon_key => $variants) {
         foreach ($variants as $variant) {
             $variant_clean = strtolower(str_replace(array(' ', '-', '_'), '', $variant));
             
             foreach ($normalized_tags as $tag_data) {
                 // Coincidencia exacta
                 if ($tag_data['clean'] === $variant_clean || 
                     $tag_data['original'] === $variant) {
                     $found_weapons[$weapon_key] = array(
                         'confidence' => 1.0,  // Confianza máxima para coincidencia exacta
                         'matched_tag' => $tag_data['original']
                     );
                     $debug_log .= "✓ Coincidencia EXACTA: {$weapon_key} para etiqueta {$tag_data['original']}\n";
                     break 2; // Salir de los dos bucles internos
                 }
             }
         }
     }
     
     // Segunda pasada: buscar coincidencias parciales, solo si no hay coincidencia exacta para esa arma
     foreach ($weapons_variants as $weapon_key => $variants) {
         // Saltar si ya tenemos una coincidencia exacta para esta arma
         if (isset($found_weapons[$weapon_key])) {
             continue;
         }
         
         foreach ($variants as $variant) {
             $variant_clean = strtolower(str_replace(array(' ', '-', '_'), '', $variant));
             
             foreach ($normalized_tags as $tag_data) {
                 // La etiqueta contiene la variante completa o viceversa (más preciso)
                 if (strpos($tag_data['clean'], $variant_clean) === 0 || 
                     strpos($variant_clean, $tag_data['clean']) === 0) {
                     
                     // Calcular un factor de confianza basado en la longitud de las cadenas
                     $confidence = min(strlen($tag_data['clean']), strlen($variant_clean)) / 
                                   max(strlen($tag_data['clean']), strlen($variant_clean));
                     
                     // Solo considerar coincidencias con confianza superior a 0.5
                     if ($confidence > 0.5) {
                         // Guardar o actualizar solo si es mejor que una coincidencia anterior
                         if (!isset($found_weapons[$weapon_key]) || 
                             $found_weapons[$weapon_key]['confidence'] < $confidence) {
                             
                             $found_weapons[$weapon_key] = array(
                                 'confidence' => $confidence,
                                 'matched_tag' => $tag_data['original']
                             );
                             $debug_log .= "✓ Coincidencia PARCIAL: {$weapon_key} para etiqueta {$tag_data['original']} (confianza: {$confidence})\n";
                         }
                     }
                 }
             }
         }
     }
     
     // Si no encontramos ninguna coincidencia, terminar
     if (empty($found_weapons)) {
         if (defined('WP_DEBUG') && WP_DEBUG) {
             $log_file = dirname(__FILE__) . '/weapon-icons-debug.log';
             $debug_log .= "No se encontraron coincidencias de armas para este post\n";
             file_put_contents($log_file, $debug_log . "\n---\n", FILE_APPEND);
         }
         return;
     }
     
     // Crear contenedor para los iconos
     $icons_html = '<span class="weapon-icons">';
     
     // Ordenar las armas encontradas por nivel de confianza (descendente)
     uasort($found_weapons, function($a, $b) {
         return $b['confidence'] <=> $a['confidence'];
     });
     
     // Mostrar solo las armas con alta confianza (>0.7) o las 2 mejores si todas son de baja confianza
     $count = 0;
     $max_icons = 2; // Máximo 2 iconos por post
     
     foreach ($found_weapons as $weapon_key => $match_data) {
         // Si tenemos iconos de alta confianza, mostrar solo esos
         if ($count >= $max_icons) {
             break;
         }
         
         if (isset($weapons[$weapon_key])) {
             $icons_html .= '<img class="weapon-icon-tag" src="' . esc_url($weapons[$weapon_key]) . 
                            '" alt="' . esc_attr($weapon_key) . '" title="' . esc_attr($weapon_key) . 
                            '" data-match="' . esc_attr($match_data['matched_tag']) . 
                            '" width="24" height="24">';
             $shown_weapons[] = $weapon_key;
             $count++;
             $debug_log .= "➤ Mostrando icono: {$weapon_key} (confianza: {$match_data['confidence']})\n";
         }
     }
     
     // Cerrar el contenedor de iconos
     $icons_html .= '</span>';
     
     // Solo mostrar los iconos si realmente hay alguno
     if (count($shown_weapons) > 0) {
         echo $icons_html;
         $debug_log .= "Mostrando iconos: " . implode(', ', $shown_weapons) . "\n";
     } else {
         $debug_log .= "No se mostraron iconos para este post\n";
     }
     
     // Guardar log de depuración si está activado
     if (defined('WP_DEBUG') && WP_DEBUG) {
         $log_file = dirname(__FILE__) . '/weapon-icons-debug.log';
         file_put_contents($log_file, $debug_log . "\n---\n", FILE_APPEND);
     }
 }

/**
 * Registra y carga el script para filtrado de armas
 */
function liukin_weapon_filter_scripts() {
    // Solo cargar en páginas relevantes
    if (is_archive() || is_home() || is_category()) {
        // Asegurarse de que jQuery esté cargado
        wp_enqueue_script('jquery');
        
        // Registrar y cargar el script de filtrado con jQuery como dependencia
        wp_register_script('liukin-weapon-filter', get_template_directory_uri() . '/js/weapon-filter.js', array('jquery'), '1.0.1', true);
        wp_enqueue_script('liukin-weapon-filter');
        
        // Pasar datos del sitio al script
        wp_localize_script('liukin-weapon-filter', 'liukinWeaponFilter', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('liukin_weapon_filter_nonce'),
            'current_category' => get_queried_object_id(),
        ));
    }
}
add_action('wp_enqueue_scripts', 'liukin_weapon_filter_scripts', 20);

/**
 * Maneja la solicitud AJAX para filtrar posts por arma y/o rol
 */
function liukin_filter_posts_by_criteria() {
    // Verificar nonce
    check_ajax_referer('liukin_weapon_filter_nonce', 'nonce');
    
    // Obtener parámetros
    $weapon = isset($_POST['weapon']) ? sanitize_text_field($_POST['weapon']) : '';
    $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';
    $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
    
    // Crear archivo de log para depuración
    $log_file = dirname(__FILE__) . '/filter-debug.log';
    $log = "=== FILTRADO " . date('Y-m-d H:i:s') . " ===\n";
    $log .= "Parámetros recibidos: weapon={$weapon}, role={$role}, category={$category}\n\n";
    
    // Normalizar el nombre del arma (eliminar acentos, convertir a minúsculas, etc.)
    if (!empty($weapon)) {
        // Normalizar nombre del arma
        $weapon = strtolower(trim($weapon));
        $weapon = str_replace(' de ', 'de', $weapon);
        $weapon = str_replace(' ', '', $weapon);
        
        $log .= "Nombre de arma normalizado: {$weapon}\n";
        
        // Mapeo de variantes comunes 
        $weapon_mapping = array(
            'espadón' => 'espadon',
            'espadon' => 'espadon',
            'espada' => 'espada',
            'arco' => 'arco',
            'estoque' => 'estoque',
            'mangual' => 'mangual',
            'baculodefuego' => 'baculodefuego',
            'báculodefuego' => 'baculodefuego',
            'baculofuego' => 'baculodefuego',
            'baculodevida' => 'baculodevida',
            'báculodevida' => 'baculodevida',
            'baculovida' => 'baculodevida',
            'martillo' => 'martillo',
            'mosquete' => 'mosquete',
            'hachuela' => 'hachuela',
            'trabuco' => 'trabuco',
            'granhacha' => 'granhacha',
            'manopladehielo' => 'manopladehielo',
            'manoplahielo' => 'manopladehielo',
            'manopladevacio' => 'manopladevacio',
            'manoplavacio' => 'manopladevacio',
            'lanza' => 'lanza',
            'hachadoble' => 'hachadoble',
            'hacha-doble' => 'hachadoble'
        );
        
        // Verificar si tenemos una variante del arma en nuestro mapeo
        if (isset($weapon_mapping[$weapon])) {
            $original_weapon = $weapon;
            $weapon = $weapon_mapping[$weapon];
            $log .= "Arma mapeada: {$original_weapon} -> {$weapon}\n";
        }
    }
    
    // Argumentos básicos para la consulta
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    
    // Obtener IDs de armas (tags) y roles (categorías)
    if (!empty($weapon)) {
        // Buscar etiqueta por nombre o slug que coincida con el arma
        $weapon_tag = get_term_by('name', $weapon, 'post_tag');
        
        if (!$weapon_tag) {
            // Intentar con slug si no se encuentra por nombre
            $weapon_tag = get_term_by('slug', $weapon, 'post_tag');
        }
        
        if (!$weapon_tag) {
            // Búsqueda más flexible: etiquetas que contengan el nombre del arma
            $weapon_tags = get_terms(array(
                'taxonomy' => 'post_tag',
                'hide_empty' => false,
                'search' => $weapon
            ));
            
            if (!empty($weapon_tags) && !is_wp_error($weapon_tags)) {
                $weapon_tag = $weapon_tags[0]; // Tomar la primera coincidencia
                $log .= "Etiqueta encontrada por búsqueda parcial: {$weapon_tag->name} (ID: {$weapon_tag->term_id})\n";
            }
        }
        
        if ($weapon_tag && !is_wp_error($weapon_tag)) {
            $log .= "Etiqueta de arma encontrada: {$weapon_tag->name} (ID: {$weapon_tag->term_id})\n";
            // Usar el nombre exacto de la etiqueta encontrada
            $args['tag'] = $weapon_tag->slug;
        } else {
            // Si no encontramos una etiqueta exacta, usar el nombre original como fallback
            $args['tag'] = $weapon;
            $log .= "No se encontró etiqueta exacta, usando el nombre original: {$weapon}\n";
        }
    }
    
    // CASO 1: Filtrar solo por rol (categoría)
    if (!empty($role) && empty($weapon)) {
        $args['category_name'] = $role;
        $log .= "Filtrado SOLO POR ROL: {$role}\n";
    }
    // CASO 2: Filtrar solo por arma (etiqueta)
    else if (empty($role) && !empty($weapon)) {
        // Ya configurado anteriormente
        $log .= "Filtrado SOLO POR ARMA\n";
    }
    // CASO 3: Filtrar por ambos criterios
    else if (!empty($role) && !empty($weapon)) {
        $args['category_name'] = $role;
        // args['tag'] ya está configurado
        $log .= "Filtrado POR AMBOS: arma y rol={$role}\n";
    }
    
    // Si estamos en una página de categoría específica y no hay filtro de rol
    if ($category > 0 && empty($role)) {
        $args['cat'] = $category;
        $log .= "Añadido filtro de categoría específica: {$category}\n";
    }
    
    $log .= "Argumentos finales de consulta: " . print_r($args, true) . "\n\n";
    
    // Ejecutar la consulta
    $query = new WP_Query($args);
    $log .= "Posts encontrados: " . $query->post_count . "\n";
    
    // Si hay resultados, registrar los títulos para depuración
    if ($query->have_posts()) {
        $log .= "Títulos de posts encontrados:\n";
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $log .= "- " . get_the_title() . " (ID: {$post_id})\n";
            
            // Registrar tags del post
            $post_tags = get_the_tags($post_id);
            if ($post_tags) {
                $tag_names = array();
                foreach ($post_tags as $tag) {
                    $tag_names[] = $tag->name;
                }
                $log .= "  Tags: " . implode(', ', $tag_names) . "\n";
            } else {
                $log .= "  Sin tags\n";
            }
            
            // Registrar categorías del post
            $categories = get_the_category($post_id);
            if ($categories) {
                $cat_names = array();
                foreach ($categories as $cat) {
                    $cat_names[] = $cat->name;
                }
                $log .= "  Categorías: " . implode(', ', $cat_names) . "\n";
            } else {
                $log .= "  Sin categorías\n";
            }
        }
        // Reiniciar el loop para usarlo después
        $query->rewind_posts();
    }
    
    // Guardar el log
    file_put_contents($log_file, $log, FILE_APPEND);
    
    // Preparar respuesta
    $response = array();
    
    if ($query->have_posts()) {
        ob_start();
        ?>
        <div class="filter-results-container">
            <div class="filtered-results-row">
            <?php
            // Inicializar contador
            $count = 0;
            
            // Mostrar los posts
            while ($query->have_posts()) {
                $query->the_post();
                
                // Determinar el rol (categoría) del post
                $role_class = '';
                if (has_category('tank')) {
                    $role_class = 'role-tank';
                } elseif (has_category('healer')) {
                    $role_class = 'role-healer';
                } elseif (has_category('dps')) {
                    $role_class = 'role-dps';
                }
                ?>
                <div class="filtered-item">
                    <div class="featured-archive card-body phome <?php echo $role_class; ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                <?php the_post_thumbnail(); ?>
                            </a>
                        <?php endif; ?>
                        <h2 class="name-archive">
                            <a class="name-archive text-center" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            <?php liukin_display_weapon_icons(get_the_ID()); ?>
                        </h2>
                    </div>
                </div>
                <?php
                $count++;
            }
            
            // Mostrar mensaje si no hay resultados (aunque esto no debería ocurrir aquí)
            if ($count === 0) {
                ?>
                <div class="filtered-item empty-results">
                    <div class="card-body">
                        <p class="text-center">No se encontraron builds con los criterios seleccionados.</p>
                    </div>
                </div>
                <?php
            }
            
            // Cerrar el contenedor grid y el contenedor principal
            ?>
            </div><!-- Cierre de la fila grid -->
        </div><!-- Cierre del contenedor de resultados -->
        <?php
        
        wp_reset_postdata();
        
        $html = ob_get_clean();
        $response['html'] = $html;
        $response['count'] = $count;
        $response['success'] = true;
    } else {
        ob_start();
        ?>
        <div class="filter-results-container">
            <div class="filtered-results-row">
                <div class="filtered-item empty-results">
                    <div class="card-body">
                        <p class="text-center">No hay builds disponibles con estos criterios. Intenta con otra combinación o muestra todas las builds.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        $response['html'] = $html;
        $response['count'] = 0;
        $response['success'] = true;
    }
    
    wp_send_json($response);
}
add_action('wp_ajax_liukin_filter_posts_by_criteria', 'liukin_filter_posts_by_criteria');
add_action('wp_ajax_nopriv_liukin_filter_posts_by_criteria', 'liukin_filter_posts_by_criteria');

// Mantener la función original para compatibilidad
add_action('wp_ajax_liukin_filter_posts_by_weapon', 'liukin_filter_posts_by_criteria');
add_action('wp_ajax_nopriv_liukin_filter_posts_by_weapon', 'liukin_filter_posts_by_criteria');

/**
 * Función de depuración para registrar información sobre etiquetas y filtrados
 */
function liukin_debug_tags() {
    $log_file = dirname(__FILE__) . '/filter-debug.log';
    $message = "=== DEPURACIÓN DE FILTROS ===\n";
    $message .= "Fecha: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Obtener todas las etiquetas
    $tags = get_tags(array('hide_empty' => false));
    
    $message .= "ETIQUETAS DISPONIBLES (" . count($tags) . " total):\n";
    foreach ($tags as $tag) {
        $message .= "- {$tag->name} (ID: {$tag->term_id}, Slug: {$tag->slug})\n";
    }
    
    $message .= "\nDEPURANDO COINCIDENCIAS DE ARMAS:\n";
    
    // Lista de armas
    $weapons = array(
        'arco', 'estoque', 'mangual', 'espadon', 'espada', 
        'baculodefuego', 'baculodevida', 'martillo', 'mosquete', 
        'hachuela', 'trabuco', 'granhacha', 'manopladehielo', 
        'manopladevacio', 'lanza'
    );
    
    foreach ($weapons as $weapon) {
        $message .= "\nArma: {$weapon}\n";
        $message .= "  - Etiquetas coincidentes:\n";
        
        $found = false;
        foreach ($tags as $tag) {
            $tag_name = strtolower($tag->name);
            $tag_slug = strtolower($tag->slug);
            
            if (strpos($tag_name, $weapon) !== false || 
                strpos($weapon, $tag_name) !== false ||
                strpos($tag_slug, $weapon) !== false ||
                strpos($weapon, str_replace('-', '', $tag_slug)) !== false) {
                
                $message .= "    * {$tag->name} (ID: {$tag->term_id}, Slug: {$tag->slug})\n";
                $found = true;
                
                // Ver qué posts tienen esta etiqueta
                $posts = get_posts(array(
                    'tag_id' => $tag->term_id,
                    'posts_per_page' => -1
                ));
                
                if (!empty($posts)) {
                    $message .= "      - Posts con esta etiqueta (" . count($posts) . "):\n";
                    foreach ($posts as $post) {
                        $message .= "        * {$post->post_title} (ID: {$post->ID})\n";
                    }
                } else {
                    $message .= "      - No hay posts con esta etiqueta\n";
                }
            }
        }
        
        if (!$found) {
            $message .= "    * Ninguna etiqueta coincide con '{$weapon}'\n";
        }
    }
    
    // Guardar en el archivo
    file_put_contents($log_file, $message);
    
    return "Información de depuración guardada en: {$log_file}";
}

// Añadir hooky para ejecutar la depuración al cargar la página
add_action('wp_footer', function() {
    if (current_user_can('manage_options') && isset($_GET['debug_tags'])) {
        echo '<div style="position:fixed;bottom:0;left:0;right:0;background:#333;color:#fff;padding:10px;z-index:9999;">'.
             liukin_debug_tags() .
             '</div>';
    }
});
?>