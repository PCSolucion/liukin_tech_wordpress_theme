<?php get_header();?>
<?php get_template_part('template-parts/search-form', 'search-form');?>
    <div class="container">
    <br/>
    <br/>
    <h1 class="titlecat"><?php echo single_term_title(); ?></h1>
    <p class="descat"><?php echo term_description(); ?></p>
    <?php 
    // Comprobar si estamos en la categoría con slug "builds"
    if (is_category('builds')) {
        echo '<div class="alert alert-info" role="alert">Selecciona un filtro para encontrar la build que necesitas</div>';
        
        // Añadir imágenes con enlaces a subcategorías
        echo '<div class="subcategory-images">';
        
        // Imagen 1 - Tank
        echo '<div class="subcategory-item">';
        echo '<a href="https://pc-solucion.es/rol/builds/tank/" title="Tank">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788379/tank5_we9zzm.png" alt="Tank" class="subcategory-image">';
        echo '</a>';
        echo '</div>';
        
        // Imagen 2 - Healer (actualizada)
        echo '<div class="subcategory-item">';
        echo '<a href="https://pc-solucion.es/rol/builds/healer/" title="Healer">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788528/healer5_ues0su.png" alt="Healer" class="subcategory-image">';
        echo '</a>';
        echo '</div>';
        
        // Imagen 3 - DPS
        echo '<div class="subcategory-item">';
        echo '<a href="https://pc-solucion.es/rol/builds/dps/" title="DPS">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788472/dps5_ewferb.png" alt="DPS" class="subcategory-image">';
        echo '</a>';
        echo '</div>';
        
        echo '</div>';
    }
    ?>
        <div class="row">
    <?php $i = 1; while(have_posts()) : the_post();?>
        <div class="col-lg-4">
            <div class="col-lg-12 featured-archive card-body phome">
                <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <?php the_post_thumbnail(); ?>
                    </a>
                    <?php endif; ?>
                <h2 class="name-archive">
                    <a class="name-archive text-center" href="<?php the_permalink();?>"><?php the_title();?></a>
                </h2>      
            </div>
        </div>
        <?php if ( $i % 3 === 0 ) { echo '</div><div class="row">'; } ?>
        <?php $i++; endwhile; wp_reset_query(); ?>
        </div>
        <div class="card-body">
            <?php get_template_part('template-parts/content', 'paginacion');?>
        </div>
    </div>
<?php get_footer();?>