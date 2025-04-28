<?php get_header();?>
<?php get_template_part('template-parts/search-form', 'search-form');?>

<main id="main-content" role="main">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <header class="search-header">
                    <h1 class="search-title screen-reader-text">
                        <?php 
                        printf(
                            esc_html__('Resultados de búsqueda para: %s', 'liukin'), 
                            '<span class="search-term">' . get_search_query() . '</span>'
                        ); 
                        ?>
                    </h1>
                </header>
                
                <?php if ( have_posts() ) : ?>
                    <div class="search-results" role="region" aria-label="Resultados de búsqueda">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <article class="card-body phome" aria-labelledby="search-result-<?php the_ID(); ?>">
                                <a href="<?php the_permalink();?>" aria-labelledby="search-result-<?php the_ID(); ?>">
                                    <h2 id="search-result-<?php the_ID(); ?>" class="entry-title"><?php the_title();?></h2>
                                </a>
                                <div class="entry-summary">
                                    <?php the_excerpt();?>
                                </div>
                                <div class="post-meta">
                                    <span class="sr-date screen-reader-text">Publicado el: <?php echo get_the_date(); ?></span>
                                </div>
                            </article>
                        <?php endwhile;?>
                    </div>
                    
                    <?php
                    // Añadir paginación accesible si es necesario
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<span aria-hidden="true">&laquo;</span><span class="screen-reader-text">Anterior</span>',
                        'next_text' => '<span class="screen-reader-text">Siguiente</span><span aria-hidden="true">&raquo;</span>',
                        'aria-label' => 'Paginación de resultados de búsqueda'
                    ));
                    ?>
                    
                <?php else: ?>
                    <div class="no-results-container" role="alert" aria-live="polite">
                        <div class="no-results-message">
                            <span class="no-results-icon" aria-hidden="true">🔍</span>
                            <h2>No se encontraron resultados</h2>
                            <p>No se ha encontrado nada relacionado con tu búsqueda "<strong><?php echo get_search_query(); ?></strong>". Prueba con diferentes palabras clave o explora nuestras categorías.</p>
                            
                            <div class="no-results-suggestions">
                                <h3>Sugerencias:</h3>
                                <ul>
                                    <li>Revisa que las palabras estén escritas correctamente</li>
                                    <li>Prueba con términos más generales</li>
                                    <li>Utiliza palabras clave diferentes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</main>
    
<?php get_footer();?>
