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
</body>
</html>
