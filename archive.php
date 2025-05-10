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
        $tank_count = get_posts(array(
            'category_name' => 'tank',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<div class="role-count">' . count($tank_count) . ' builds</div>';
        echo '</div>';
        
        // Imagen 2 - Healer
        echo '<div class="subcategory-item">';
        echo '<a href="https://pc-solucion.es/rol/builds/healer/" title="Healer">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788528/healer5_ues0su.png" alt="Healer" class="subcategory-image">';
        echo '</a>';
        $healer_count = get_posts(array(
            'category_name' => 'healer',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<div class="role-count">' . count($healer_count) . ' builds</div>';
        echo '</div>';
        
        // Imagen 3 - DPS
        echo '<div class="subcategory-item">';
        echo '<a href="https://pc-solucion.es/rol/builds/dps/" title="DPS">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788472/dps5_ewferb.png" alt="DPS" class="subcategory-image">';
        echo '</a>';
        $dps_count = get_posts(array(
            'category_name' => 'dps',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<div class="role-count">' . count($dps_count) . ' builds</div>';
        echo '</div>';
        
        echo '</div>';
        
        // Añadir sección de armas
        echo '<div class="weapons-section">';
        echo '<div class="weapons-grid">';
        
        // Mangual
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Mangual">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789072/latigo_wzv45l.png" alt="Mangual" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Espadón
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Espadón">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746879077/serenidad_qoxzez.png" alt="Espadón" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Espada
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Espada">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789067/espadaescudo_ntg78c.png" alt="Espada" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Estoque
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Estoque">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/rapier_i1icgl.webp" alt="Estoque" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Báculo de fuego
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Báculo de fuego">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789068/firestaff_mjvj7w.png" alt="Báculo de fuego" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Báculo de vida
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Báculo de vida">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746879096/baculo_de_vida_progenitor_pvfzzj.webp" alt="Báculo de vida" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Arco
        echo '<div class="weapon-item">';
        echo '<a href="https://pc-solucion.es/t/arco/" title="Arco">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789066/arco2_gfza10.png" alt="Arco" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Martillo
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Martillo">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/martillodeguerra_katfte.png" alt="Martillo" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Mosquete
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Mosquete">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/mosquete2_uuvqiy.png" alt="Mosquete" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Hachuela
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Hachuela">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789070/hatchet_dlslsr.webp" alt="Hachuela" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Trabuco
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Trabuco">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746879119/bluder_tgrmqt.webp" alt="Trabuco" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Gran Hacha
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Gran Hacha">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789070/gran_hacha_fodiyg.webp" alt="Gran Hacha" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Manopla de hielo
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Manopla de hielo">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746879151/guantedehielo_u43hdy.png" alt="Manopla de hielo" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Manopla de vacío
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Manopla de vacío">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746879170/void_uzngfx.webp" alt="Manopla de vacío" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        // Lanza
        echo '<div class="weapon-item">';
        echo '<a href="#" title="Lanza">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746789071/lanza2_ojc6vy.png" alt="Lanza" class="weapon-icon">';
        echo '</a>';
        echo '</div>';
        
        echo '</div>'; // Cierre de weapons-grid
        echo '</div>'; // Cierre de weapons-section
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