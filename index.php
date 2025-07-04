<?php 
// Cargar header
get_header();

// Mostrar la cabecera especial solo en la categoría 'builds', nunca en la home
if (is_category('builds')) {
    get_template_part('template-parts/search-form-builds', 'search-form-builds');
} else {
    get_template_part('template-parts/search-form', 'search-form');
}
?>

<main id="main-content" role="main">
    <div class="container">
        <section class="posts-home" id="infinite-posts">
            <?php
            // Preparar la consulta principal
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => get_option('posts_per_page'),
                'paged' => 1
            );
            $query = new WP_Query($args);
            
            if ($query->have_posts()) : 
                // Título oculto para lectores de pantalla que identifica la sección
                echo '<h1 class="screen-reader-text">Entradas recientes</h1>';
                
                while ($query->have_posts()) : $query->the_post(); 
                    // Obtener categorías una sola vez
                    $categories = get_the_category();
                    ?>
                    <article class="card-body phome" aria-labelledby="post-<?php the_ID(); ?>">
                        <?php 
                        if (!empty($categories)) :
                            echo '<nav class="post-categories" aria-label="Categorías del artículo">';
                            foreach ($categories as $cat) :
                                printf(
                                    '<a href="%s" class="cathome %s" title="Ver todos los post de %s">%s</a> ',
                                    esc_url(get_category_link($cat->term_id)),
                                    esc_attr($cat->slug),
                                    esc_attr($cat->name),
                                    esc_html($cat->name)
                                );
                            endforeach;
                            echo '</nav>';
                        endif;
                        ?>
                        <header>
                            <a href="<?php the_permalink(); ?>" aria-labelledby="post-<?php the_ID(); ?>">
                                <h2 id="post-<?php the_ID(); ?>" class="entry-title"><?php the_title(); ?></h2>
                            </a>
                            <?php liukin_display_weapon_icons(); ?>
                            
                            <div class="post-meta">
                                <time class="sr-date screen-reader-text" datetime="<?php echo get_the_date('c'); ?>">Publicado el: <?php echo get_the_date(); ?></time>
                            </div>
                        </header>
                    </article>
                <?php 
                endwhile;
                wp_reset_postdata();
            else : ?>
                <div class="no-posts" role="alert" aria-live="polite">
                    <p><?php esc_html_e('No hay entradas disponibles.', 'liukin'); ?></p>
                </div>
            <?php endif; ?>
        </section>
        
        <div id="infinite-loader" style="display: none;" aria-live="polite" role="status">
            <p class="loader-message"><?php _e('Cargando...', 'liukin'); ?></p>
        </div>
        
        <?php if (isset($query) && $query->max_num_pages > 1) : ?>
            <nav class="pagination-nav screen-reader-text" aria-label="Paginación">
                <p>Página 1 de <?php echo $query->max_num_pages; ?></p>
            </nav>
            <script type="text/javascript">
                var liukinMaxPages = <?php echo $query->max_num_pages; ?>;
                var liukinCurrentPage = 1;
            </script>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>