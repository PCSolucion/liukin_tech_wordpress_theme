<?php get_header();?>
<?php get_template_part('template-parts/search-form', 'search-form');?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="card-body phome">
                    <a href="<?php the_permalink();?>">
                        <h2 class="entry-title"><?php the_title();?></h2>
                    </a>
                    <?php the_excerpt();?>
                </div>
                <?php endwhile;?>
                <?php else: ?>
                <div class="no-results-container">
                    <div class="no-results-message">
                        <span class="no-results-icon">🔍</span>
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
    
<style>
/* Estilos para mensaje de no resultados con mejor contraste */
.no-results-container {
    padding: 30px;
    margin: 40px 0;
    background-color: #f9f9f9;
    border-radius: 10px;
    border-left: 5px solid #0070aa;
}

.no-results-message {
    text-align: center;
}

.no-results-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 20px;
}

.no-results-message h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 15px;
    font-weight: 600;
}

.no-results-message p {
    color: #444;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.no-results-suggestions {
    text-align: left;
    max-width: 500px;
    margin: 0 auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
}

.no-results-suggestions h3 {
    color: #333;
    font-size: 18px;
    margin-bottom: 10px;
    font-weight: 600;
}

.no-results-suggestions ul {
    padding-left: 20px;
}

.no-results-suggestions li {
    color: #444;
    margin-bottom: 8px;
    line-height: 1.4;
}
</style>
    
<?php get_footer();?>
