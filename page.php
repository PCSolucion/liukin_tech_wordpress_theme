<?php get_header();?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <div class="card-body">
                        <h1><?php the_title();?></h1>
                        <a class="small"><?php the_category('');?></a>
                        <?php the_content();?>
                    </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
<?php get_footer();?>