<?php
//Custom Logo Support
add_theme_support( 'custom-logo' );
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
//Menu Requires
require_once get_template_directory() . '/template-parts/class-wp-bootstrap-navwalker.php';
    function liukin_agregar_css_js(){
        wp_enqueue_style( 'style', get_stylesheet_uri());
        wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
        wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array ( 'jquery'), '1.14', true);
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array ( 'popper'), '4.3', true);
    }
function register_navwalker(){
	require_once get_template_directory() . '/template-parts/class-wp-bootstrap-navwalker.php';
}
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'THEMENAME' ),
) );
add_action( 'after_setup_theme', 'register_navwalker' );
add_action('wp_enqueue_scripts','liukin_agregar_css_js');
//Post Thumbnails
function liukin_setup(){
if ( function_exists('add_theme_support')){
    add_theme_support('post-thumbnails');
}
add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'liukin_setup' );
//Add Sidebar
function liukin_widgets(){
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
//Words in Excerpt
function custom_excerpt_length( $length ) {
	return 60;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
//Delete[...]from Excerpt
function new_excerpt_more( $more ) {
    return '';
}
add_filter('excerpt_more', 'new_excerpt_more');
//Force alt atribute on image
function add_alt_tags($content)
{
    global $post;
    preg_match_all('/<img (.*?)\/>/', $content, $images);
    if(!is_null($images))
    {
            foreach($images[1] as $index => $value)
            {
                    if(!preg_match('/alt=/', $value))
                    {
                            $new_img = str_replace('<img', '<img alt="'.$post->post_title.'"', $images[0][$index]);
                            $content = str_replace($images[0][$index], $new_img, $content);
                    }
            }
    }
    return $content;
}
add_filter('the_content', 'add_alt_tags', 99999);
//Delete type atribute in script tag
add_action('wp_loaded', 'output_buffer_start');
function output_buffer_start() { 
    ob_start("output_callback"); 
}
add_action('shutdown', 'output_buffer_end');
function output_buffer_end() { 
 if (ob_get_length() > 0) { ob_end_clean();}
 }
function output_callback($buffer) {
    return preg_replace( "%[ ]type=[\'\"]text\/(javascript|css)[\'\"]%", '', $buffer );
}
?>