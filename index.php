<?php get_header();?>
<?php get_template_part('template-parts/search-form', 'search-form');?>
<div class="container">
    <div class="row">
        <div class="col-lg-12 posts-home">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <div class="card-body phome">
                <?php 
                    $sep = '';
                    foreach ((get_the_category()) as $cat) {
                        echo $sep . '<a href="' . get_category_link($cat->term_id) . '"  class="cathome ' . $cat->slug . '" title="Ver todos los post de '. esc_attr($cat->name) . '">' . $cat->cat_name . '</a>';
                    $sep = ' ';
                    }
                ?>
                <a href="<?php the_permalink($post);?>">
                <h2 class="entry-title <?php echo esc_attr($cat->name);?>" ><?php the_title();?></h2>
                </a>
            </div>
            <?php endwhile; endif; ?> 
        </div>
            
        <div class="card-body">
            <?php get_template_part('template-parts/content', 'paginacion');?>
        </div>
    </div>
</div>
<?php get_footer();?>