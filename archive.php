<?php 
// Añadir clase específica al body para la categoría builds
if (is_category('builds')) {
    add_filter('body_class', function($classes) {
        $classes[] = 'category-builds';
        return $classes;
    });
}

get_header();?>
<?php get_template_part('template-parts/search-form', 'search-form');?>
    <div class="container">
    <br/>
    <?php 
    // Comprobar si estamos en la categoría con slug "builds"
    if (is_category('builds')) {
        // No mostrar el título ni la descripción de la categoría
    } else {
        echo '<h1 class="titlecat">' . single_term_title() . '</h1>';
        echo '<p class="descat">' . term_description() . '</p>';
    }
    ?>
    <?php 
    // Comprobar si estamos en la categoría con slug "builds"
    if (is_category('builds')) {
        echo '<div class="builds-category-wrapper">';
        // Sección de roles
        echo '<div class="roles-section">';
        echo '<div class="roles-grid">';
        
        // Tank
        echo '<div class="role-card tank">';
        echo '<div class="role-icon">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788379/tank5_we9zzm.png" alt="Tank">';
        echo '</div>';
        echo '<div class="role-info">';
        echo '<h3>Tank</h3>';
        $tank_count = get_posts(array(
            'category_name' => 'tank',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<span class="build-count">' . count($tank_count) . ' builds</span>';
        echo '</div>';
        echo '<a href="https://pc-solucion.es/rol/builds/tank/" class="role-link" aria-label="Ver builds de Tank"></a>';
        echo '</div>';
        
        // Healer
        echo '<div class="role-card healer">';
        echo '<div class="role-icon">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788528/healer5_ues0su.png" alt="Healer">';
        echo '</div>';
        echo '<div class="role-info">';
        echo '<h3>Healer</h3>';
        $healer_count = get_posts(array(
            'category_name' => 'healer',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<span class="build-count">' . count($healer_count) . ' builds</span>';
        echo '</div>';
        echo '<a href="https://pc-solucion.es/rol/builds/healer/" class="role-link" aria-label="Ver builds de Healer"></a>';
        echo '</div>';
        
        // DPS
        echo '<div class="role-card dps">';
        echo '<div class="role-icon">';
        echo '<img src="https://res.cloudinary.com/pcsolucion/image/upload/v1746788472/dps5_ewferb.png" alt="DPS">';
        echo '</div>';
        echo '<div class="role-info">';
        echo '<h3>DPS</h3>';
        $dps_count = get_posts(array(
            'category_name' => 'dps',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<span class="build-count">' . count($dps_count) . ' builds</span>';
        echo '</div>';
        echo '<a href="https://pc-solucion.es/rol/builds/dps/" class="role-link" aria-label="Ver builds de DPS"></a>';
        echo '</div>';
        
        echo '</div>'; // Cierre de roles-grid
        echo '</div>'; // Cierre de roles-section
        
        // Sección de armas
        echo '<div class="weapons-section">';
        echo '<h2 class="section-title">Armas</h2>';
        echo '<div class="weapons-container">';
        
        // Array de armas con sus datos
        $weapons = array(
            array('name' => 'Mangual', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789072/latigo_wzv45l.png', 'link' => '#'),
            array('name' => 'Espadón', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879077/serenidad_qoxzez.png', 'link' => '#'),
            array('name' => 'Espada', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789067/espadaescudo_ntg78c.png', 'link' => '#'),
            array('name' => 'Estoque', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/rapier_i1icgl.webp', 'link' => '#'),
            array('name' => 'Báculo de fuego', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789068/firestaff_mjvj7w.png', 'link' => '#'),
            array('name' => 'Báculo de vida', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879096/baculo_de_vida_progenitor_pvfzzj.webp', 'link' => '#'),
            array('name' => 'Arco', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789066/arco2_gfza10.png', 'link' => 'https://pc-solucion.es/t/arco/'),
            array('name' => 'Martillo', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/martillodeguerra_katfte.png', 'link' => '#'),
            array('name' => 'Mosquete', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789149/mosquete2_uuvqiy.png', 'link' => '#'),
            array('name' => 'Hachuela', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789070/hatchet_dlslsr.webp', 'link' => '#'),
            array('name' => 'Trabuco', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879119/bluder_tgrmqt.webp', 'link' => '#'),
            array('name' => 'Gran Hacha', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789070/gran_hacha_fodiyg.webp', 'link' => '#'),
            array('name' => 'Manopla de hielo', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879151/guantedehielo_u43hdy.png', 'link' => '#'),
            array('name' => 'Manopla de vacío', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746879170/void_uzngfx.webp', 'link' => '#'),
            array('name' => 'Lanza', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789071/lanza2_ojc6vy.png', 'link' => '#'),
            array('name' => 'Hacha doble', 'icon' => 'https://res.cloudinary.com/pcsolucion/image/upload/v1746789068/firestaff_mjvj7w.png', 'link' => '#')
        );
        
        // Dividir en dos filas exactamente
        $rows = array_chunk($weapons, 8);
        
        // Primera fila
        echo '<div class="weapons-row">';
        foreach ($rows[0] as $weapon) {
            echo '<div class="weapon-card">';
            echo '<a href="' . esc_url($weapon['link']) . '" class="weapon-link">';
            echo '<div class="weapon-icon">';
            echo '<img src="' . esc_url($weapon['icon']) . '" alt="' . esc_attr($weapon['name']) . '">';
            echo '</div>';
            echo '<span class="weapon-name">' . esc_html($weapon['name']) . '</span>';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
        
        // Segunda fila
        echo '<div class="weapons-row">';
        foreach ($rows[1] as $weapon) {
            echo '<div class="weapon-card">';
            echo '<a href="' . esc_url($weapon['link']) . '" class="weapon-link">';
            echo '<div class="weapon-icon">';
            echo '<img src="' . esc_url($weapon['icon']) . '" alt="' . esc_attr($weapon['name']) . '">';
            echo '</div>';
            echo '<span class="weapon-name">' . esc_html($weapon['name']) . '</span>';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
        
        echo '</div>'; // Cierre de weapons-container
        echo '</div>'; // Cierre de weapons-section
        echo '</div>'; // Cierre de builds-category-wrapper
    }
    ?>
        <div class="row">
    <?php $i = 1; while(have_posts()) : the_post();?>
        <?php 
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
        <div class="col-lg-4">
            <div class="col-lg-12 featured-archive card-body phome <?php echo $role_class; ?>">
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