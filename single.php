<?php get_header();?>
    <?php get_template_part('template-parts/search-form', 'search-form');?>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
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
                    <span class="ldate">
                    <?php the_date( get_option( 'date_format' ) ); ?>
                    </span>
					</div>
					<div class="lcontent">
                    <?php
                    echo the_content();
                    ?>
					</div>
                </div>
            </div>
                <?php endwhile; endif; ?>
                <?php get_sidebar();?>
        </div>
        </div>
<?php get_footer();?>