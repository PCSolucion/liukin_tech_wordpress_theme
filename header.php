<!doctype html>
<html <?php language_attributes();?>>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php wp_head();?>
  </head>
  <body <?php body_class();?>>
    <?php if ( is_home() ) { ?>
    <h1 class="nope"><?php bloginfo("name"); ?></h1>
    <?php }  else { ?>
    <?php
    }
    ?>
	  <nav class="navbar navbar-expand-md navbar-light bg-white">
      <div class="container">
        <div class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'your-theme-slug' ); ?>">
          <span class="navbar-toggler-icon"></span>
        </div>
        <div class="navbar-brand">
			    <?php 
            the_custom_logo();
            echo '</div>';
              wp_nav_menu( array(
                'theme_location'    => 'primary',
                'depth'             => 2,
                'container'         => 'div',
                'container_class'   => 'collapse navbar-collapse',
                'container_id'      => 'bs-example-navbar-collapse-1',
                'menu_class'        => 'nav navbar-nav',
                'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                'walker'            => new WP_Bootstrap_Navwalker(),
            ) );
          ?>
        </div>
    </nav>    
