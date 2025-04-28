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
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logow" aria-label="<?php echo get_bloginfo('name'); ?> - Ir a página de inicio" tabindex="0">
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
                               autocomplete="off"
                               tabindex="0"
                               required>
                        <button type="submit" class="search-submit" aria-label="Buscar" tabindex="0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" role="img">
                                <title>Icono de búsqueda</title>
                                <desc>Una lupa que representa la acción de búsqueda</desc>
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span class="screen-reader-text">Buscar</span>
                        </button>
                    </div>
                    
                    <div class="search-instructions screen-reader-text" aria-live="polite">
                        Ingresa términos de búsqueda y presiona Enter o el botón Buscar
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script para mejorar la accesibilidad con atajos de teclado
document.addEventListener('DOMContentLoaded', function() {
    // Atajo de teclado para enfocar el buscador (Alt+S o Ctrl+/)
    document.addEventListener('keydown', function(e) {
        // Alt+S para enfocar el buscador
        if ((e.altKey && e.key === 's') || (e.ctrlKey && e.key === '/')) {
            e.preventDefault();
            document.getElementById('search-input').focus();
            
            // Anunciar para lectores de pantalla
            var announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'assertive');
            announcement.className = 'screen-reader-text';
            announcement.textContent = 'Campo de búsqueda activado';
            document.body.appendChild(announcement);
            
            // Eliminar anuncio después de que se haya leído
            setTimeout(function() {
                document.body.removeChild(announcement);
            }, 1000);
        }
    });
    
    // Añadir información sobre atajos de teclado para lectores de pantalla
    var srInfo = document.createElement('div');
    srInfo.className = 'screen-reader-text';
    srInfo.setAttribute('aria-live', 'polite');
    srInfo.textContent = 'Puedes usar Alt+S o Control+Barra para acceder rápidamente al buscador.';
    document.body.appendChild(srInfo);
    
    // Manejar envío del formulario con indicación para lectores de pantalla
    var searchForm = document.querySelector('.search-form');
    var searchInput = document.getElementById('search-input');
    
    searchForm.addEventListener('submit', function() {
        // Anunciar búsqueda para lectores de pantalla
        var announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'assertive');
        announcement.className = 'screen-reader-text';
        announcement.textContent = 'Buscando: ' + searchInput.value;
        document.body.appendChild(announcement);
    });
});
</script>