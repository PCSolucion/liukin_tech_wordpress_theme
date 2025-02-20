<?php get_header();?>
<?php get_template_part('template-parts/search-form', 'search-form');?>
    <div class="container">
    <br/>
    <br/>
    <h1 class="titlecat"><?php echo single_term_title(); ?></h1>
    <p class="descat"><?php echo term_description(); ?></p>
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