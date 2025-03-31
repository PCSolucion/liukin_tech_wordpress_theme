<?php
/**
 * Template part for displaying the search form
 *
 * @package Liukin_Tech
 */
?>

<div class="search-web">
    <div class="container">
        <div class="search-wrapper">
            <div class="logo-container">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logow">
                    <img src="https://pc-solucion.es/wp-content/uploads/2025/02/favicon.png" width="55" height="122" alt="<?php echo get_bloginfo('name'); ?>">
                </a>
            </div>
            <div class="search-container-wrapper">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-container">
                        <input type="search" 
                               class="search-input" 
                               placeholder="<?php echo esc_attr_x('Buscar artículos, tutoriales y más...', 'placeholder', 'liukin_tech'); ?>"
                               value="<?php echo get_search_query(); ?>" 
                               name="s"
                               title="<?php echo esc_attr_x('Buscar:', 'label', 'liukin_tech'); ?>"
                               required>
                        <button type="submit" class="search-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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