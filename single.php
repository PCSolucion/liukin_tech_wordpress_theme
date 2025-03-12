<?php get_header(); ?>

<main class="container">
    <div class="row">
        <div class="col-lg-8 posts-single">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'single'); ?>
            <?php endwhile; endif; ?>
        </div>
        
        <div class="col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_template_part('template-parts/search-form', 'search-form'); ?>

<?php get_footer(); ?>