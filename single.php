<?php get_header();?>
    <?php get_template_part('template-parts/search-form', 'search-form');?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 posts-single">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <div class="card-body entry-content">
                        <h1><?php the_title();?></h1>
						<div class="minibox">
                        <?php 
                        $sep = '';
                        foreach ((get_the_category()) as $cat) {
                            echo $sep . '<a href="' . get_category_link($cat->term_id) . '"  class="cathome ' . $cat->slug . '" title="Ver todos los post de '. esc_attr($cat->name) . '">' . $cat->cat_name . '</a>';
                        $sep = ' ';
                        }
                    ?>
					</div>
					<div class="lcontent">
                    <?php
                    echo the_content();
                    ?>
					</div>
                </div>
            </div>
                <?php endwhile; endif; ?>
        </div>
        </div>
<?php get_footer();?>