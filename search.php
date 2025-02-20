<?php get_header();?>
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
		            <p class="noresults">No se ha encontrado nada relacionado con tu búsqueda, vuelve a probar con otras palabras</p>
		            <br>
                    </p>
			    <?php endif;?>
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
    <?php get_template_part('template-parts/search-form', 'search-form');?>
<?php get_footer();?>
