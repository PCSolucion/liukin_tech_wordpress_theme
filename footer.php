<!--Footer-->
<footer class="site-footer">
    <div class="container-fluid py-4">
        <div class="container">
            <div class="footer-grid">
                <!-- Columna de información del sitio -->
                <div class="footer-info">
                    <h4><?php echo get_bloginfo('name'); ?></h4>
                    <p><?php echo get_bloginfo('description'); ?></p>
                </div>
                
                <!-- Columna de enlaces útiles -->
                <div class="footer-links">
                    <h4>Enlaces útiles</h4>
                    <nav aria-label="Enlaces útiles">
                        <ul>
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" aria-label="Ir a la página de inicio">Inicio</a></li>
                            <li><a href="<?php echo esc_url(home_url('/contacto')); ?>" aria-label="Ir a la página de contacto">Contacto</a></li>
                            <li><a href="<?php echo esc_url(home_url('/politica-de-privacidad')); ?>" aria-label="Ver política de privacidad">Política de privacidad</a></li>
                            <li><a href="<?php echo esc_url(home_url('/aviso-legal')); ?>" aria-label="Ver aviso legal">Aviso legal</a></li>
                        </ul>
                    </nav>
                </div>
                
                <!-- Columna de categorías -->
                <div class="footer-categories">
                    <h4>Categorías</h4>
                    <nav aria-label="Categorías principales">
                        <ul>
                            <?php
                            $categories = get_categories(array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 5
                            ));
                            
                            foreach ($categories as $category) {
                                printf(
                                    '<li><a href="%s" aria-label="Ver categoría %s">%s</a></li>',
                                    esc_url(get_category_link($category->term_id)),
                                    esc_html($category->name),
                                    esc_html($category->name)
                                );
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
    <!--Footer-->
    <!-- Optional JavaScript -->
    <?php wp_footer();?>
    
    <!-- Polyfill para focus-visible en navegadores antiguos -->
    <script>
    (function() {
        // Solo aplicar el polyfill si el navegador no soporta :focus-visible
        if (!CSS.supports('selector(:focus-visible)')) {
            document.documentElement.classList.add('js-focus-visible');
            
            // Detectar cuando se usa el ratón vs. teclado
            var usingMouse = false;
            
            // Función para marcar un elemento como focus-visible
            function applyFocusVisibleClass(e) {
                if (e.target) {
                    if (!usingMouse) {
                        e.target.classList.add('focus-visible');
                    }
                }
            }
            
            // Función para eliminar la clase focus-visible
            function removeFocusVisibleClass(e) {
                if (e.target) {
                    e.target.classList.remove('focus-visible');
                }
            }
            
            // Detectar uso de ratón
            document.addEventListener('mousedown', function() {
                usingMouse = true;
                setTimeout(function() {
                    usingMouse = false;
                }, 100);
            }, true);
            
            // Detectar uso de teclado
            document.addEventListener('keydown', function() {
                usingMouse = false;
            }, true);
            
            // Aplicar clase a elementos con foco
            document.addEventListener('focus', applyFocusVisibleClass, true);
            document.addEventListener('blur', removeFocusVisibleClass, true);
        }
    })();
    </script>
</body>
</html>
