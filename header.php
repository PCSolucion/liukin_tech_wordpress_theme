<!doctype html>
<html <?php language_attributes();?>>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Soluciones, guías y trucos en español para todo lo relacionado en tecnología, informática y videojuegos.">
    <link rel="preload" as="style" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" fetchpriority="high">
    <?php wp_head();?>
  </head>
  <body <?php body_class();?>>
    <!-- Enlace de salto para mejorar accesibilidad del teclado -->
    <a href="#main-content" class="skip-link screen-reader-text">Saltar al contenido principal</a>
    
    <?php if ( is_home() ) : ?>
      <h1 class="nope"><?php bloginfo("name"); ?></h1>
    <?php endif; ?>
