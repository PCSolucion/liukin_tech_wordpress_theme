<?php 
// Cargar header y buscador
get_header();
get_template_part('template-parts/search-form', 'search-form');

// Preparar la consulta principal
$args = array(
    'post_type' => 'post',
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => 1
);
$query = new WP_Query($args);
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 posts-home" id="infinite-posts">
            <?php if ($query->have_posts()) : 
                while ($query->have_posts()) : $query->the_post(); 
                    // Obtener categorías una sola vez
                    $categories = get_the_category();
                    ?>
                    <div class="card-body phome">
                        <?php 
                        if (!empty($categories)) :
                            foreach ($categories as $cat) :
                                printf(
                                    '<a href="%s" class="cathome %s" title="Ver todos los post de %s">%s</a> ',
                                    esc_url(get_category_link($cat->term_id)),
                                    esc_attr($cat->slug),
                                    esc_attr($cat->name),
                                    esc_html($cat->name)
                                );
                            endforeach;
                        endif;
                        ?>
                        <a href="<?php the_permalink(); ?>">
                            <h2 class="entry-title"><?php the_title(); ?></h2>
                        </a>
                    </div>
                <?php 
                endwhile;
                wp_reset_postdata();
            else : ?>
                <div class="no-posts">
                    <p><?php esc_html_e('No hay entradas disponibles.', 'liukin'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="infinite-loader" style="display: none; text-align: center; width: 100%; padding: 20px;">
            <p><?php _e('Cargando...', 'liukin'); ?></p>
        </div>
        
        <?php if ($query->max_num_pages > 1) : ?>
            <script type="text/javascript">
                var liukinMaxPages = <?php echo $query->max_num_pages; ?>;
                var liukinCurrentPage = 1;
            </script>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>