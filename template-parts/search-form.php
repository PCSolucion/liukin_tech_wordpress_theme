<?php
/**
 * Template part for displaying the search form
 *
 * @package Liukin_Tech
 */
?>

<div class="search-web" role="search" aria-label="Buscador principal">
    <div class="container">
        <div class="search-wrapper">
            <div class="logo-container">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logow" aria-label="<?php echo get_bloginfo('name'); ?> - Ir a página de inicio">
                    <img src="https://pc-solucion.es/wp-content/uploads/2025/02/cropped-cropped-cropped-favicon.png" width="55" height="122" alt="<?php echo get_bloginfo('name'); ?>">
                </a>
            </div>
            <div class="search-container-wrapper">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>" aria-label="Formulario de búsqueda">
                    <div class="search-container">
                        <label for="search-input" class="screen-reader-text"><?php echo esc_attr_x('Buscar:', 'label', 'liukin_tech'); ?></label>
                        <input type="search" 
                               id="search-input"
                               class="search-input" 
                               placeholder="<?php echo esc_attr_x('Buscar artículos, tutoriales y más...', 'placeholder', 'liukin_tech'); ?>"
                               value="<?php echo get_search_query(); ?>" 
                               name="s"
                               aria-label="<?php echo esc_attr_x('Buscar artículos, tutoriales y más...', 'aria label', 'liukin_tech'); ?>"
                               title="<?php echo esc_attr_x('Buscar:', 'label', 'liukin_tech'); ?>"
                               required>
                        <button type="submit" class="search-submit" aria-label="Buscar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>