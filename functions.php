<?php
/**
 * Funciones principales del tema Liukin Tech
 * 
 * @package Liukin
 * @version 1.1.0
 * @since 2025-04-12
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuración inicial del tema
 */
function liukin_setup() {
    $features = [
        'post-thumbnails',
        'title-tag',
        'html5' => [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script'
        ],
        'custom-logo' => [
            'height' => 55,
            'width' => 55,
            'flex-height' => true,
            'flex-width' => true,
            'header-text' => ['site-title', 'site-description'],
            'unlink-homepage-logo' => true
        ]
    ];

    foreach ($features as $feature => $args) {
        if (is_array($args)) {
            add_theme_support($feature, $args);
        } else {
            add_theme_support($args);
        }
    }
}
add_action('after_setup_theme', 'liukin_setup');

/**
 * Gestión de assets (CSS y JavaScript)
 */
function liukin_agregar_css_js() {
    $cache_buster = WP_DEBUG ? time() : wp_get_theme()->get('Version');
    
    // Desregistrar el estilo actual
    wp_deregister_style('style');
    
    // CSS principal con versión basada en última modificación
    $css_path = get_stylesheet_uri();
    $css_version = filemtime(get_stylesheet_directory() . '/style.css');
    wp_enqueue_style('liukin-style', $css_path, [], $css_version);

    // Configuración de scripts
    $scripts = [
        'infinite-scroll' => [
            'path' => '/js/infinite-scroll.js',
            'deps' => ['jquery'],
            'condition' => true
        ],
        'lightbox' => [
            'path' => '/js/lightbox.js',
            'deps' => [],
            'condition' => is_single()
        ]
    ];

    // Cargar scripts según condiciones
    foreach ($scripts as $handle => $script) {
        if ($script['condition']) {
            wp_enqueue_script(
                "liukin-{$handle}",
                get_template_directory_uri() . $script['path'],
                $script['deps'],
                $cache_buster,
                true
            );
        }
    }

    // Agregar atributo defer a los scripts
    add_filter('script_loader_tag', function($tag, $handle) {
        if (strpos($handle, 'liukin-') !== false) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);

    // Configuración JS centralizada
    $js_config = [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('liukin_infinite_scroll'),
        'i18n' => [
            'loading' => __('Cargando...', 'liukin'),
            'no_more' => __('No hay más entradas', 'liukin')
        ]
    ];

    // Agregar datos específicos según el contexto
    if (is_archive()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $js_config['term'] = [
                'id' => $term->term_id,
                'taxonomy' => $term->taxonomy
            ];
        }
    } elseif (is_search()) {
        $js_config['search'] = get_search_query();
    }

    wp_localize_script('liukin-infinite-scroll', 'liukinConfig', $js_config);
}
add_action('wp_enqueue_scripts', 'liukin_agregar_css_js');

/**
 * Optimización de imágenes y accesibilidad
 */
function add_alt_tags($content) {
    if (empty($content)) {
        return $content;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $images = $dom->getElementsByTagName('img');
    foreach ($images as $img) {
        if (!$img->hasAttribute('alt')) {
            $img->setAttribute('alt', get_the_title());
        }
    }

    return $dom->saveHTML();
}
add_filter('the_content', 'add_alt_tags', 99999);

/**
 * Optimización del buffer de salida
 */
function output_callback($buffer) {
    return preg_replace('/\s+type=[\'"]text\/(javascript|css)[\'"]/', '', $buffer);
}

function manage_output_buffer() {
    ob_start('output_callback');
}
add_action('wp_loaded', 'manage_output_buffer');

/**
 * Desactivar funcionalidades no necesarias
 */
function disable_unused_features() {
    // Desactivar emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Desactivar el editor de bloques
    add_filter('use_block_editor_for_post', '__return_false', 10);
    
    // Desactivar CSS de Gutenberg
    wp_dequeue_style('wp-block-library');
}
add_action('init', 'disable_unused_features');

/**
 * Gestión de comentarios
 */
function disable_comments_completely() {
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
    add_filter('comments_array', '__return_array', 10, 2);
}
add_action('init', 'disable_comments_completely');

/**
 * Optimización de incrustaciones de YouTube
 */
function optimize_youtube_embed($html, $url, $attr, $post_id) {
    if (strpos($url, 'youtube.com') !== false) {
        $privacy_args = [
            'rel' => 0,
            'modestbranding' => 1,
            'iv_load_policy' => 3
        ];
        
        $nocookie_url = str_replace('youtube.com', 'youtube-nocookie.com', $url);
        $nocookie_url = add_query_arg($privacy_args, $nocookie_url);
        
        return str_replace($url, $nocookie_url, $html);
    }
    return $html;
}
add_filter('embed_oembed_html', 'optimize_youtube_embed', 10, 4);
add_filter('embed_handler_html', 'optimize_youtube_embed', 10, 4);

/**
 * Gestión de cookies
 */
function disable_cookies_for_guests() {
    if (is_user_logged_in() || is_admin()) {
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    if (!empty($_COOKIE)) {
        $past = time() - 3600;
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, '', $past, '/', '', true, true);
        }
    }
    
    $_COOKIE = [];
    if (isset($_SESSION)) {
        $_SESSION = [];
    }
}
add_action('init', 'disable_cookies_for_guests', 1);
add_filter('comment_cookie_lifetime', '__return_zero');

/**
 * Carga infinita de posts vía AJAX
 */
function liukin_load_more_posts() {
    if (!check_ajax_referer('liukin_infinite_scroll', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }
    
    $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT) ?: 1;
    
    $query = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => get_option('posts_per_page'),
        'paged' => $page,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => true
    ]);
    
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/content', 'card');
        }
    }
    $html = ob_get_clean();
    wp_reset_postdata();
    
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_liukin_load_more_posts', 'liukin_load_more_posts');
add_action('wp_ajax_nopriv_liukin_load_more_posts', 'liukin_load_more_posts');

/**
 * Modificar el orden de posts en las páginas de etiquetas
 */
function modify_tag_query($query) {
    if ($query->is_tag() && $query->is_main_query()) {
        $query->set('orderby', 'date');
        $query->set('order', 'ASC');
    }
}
add_action('pre_get_posts', 'modify_tag_query');