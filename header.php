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
