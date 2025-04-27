<!--Footer-->
<footer class="site-footer">
    <div class="container-fluid py-4">
        <div class="container">
            <div class="row">
                <!-- Columna de información del sitio -->
                <div class="col-lg-4">
                    <div class="footer-info">
                        <h4><?php echo get_bloginfo('name'); ?></h4>
                        <p><?php echo get_bloginfo('description'); ?></p>
                    </div>
                </div>
                
                <!-- Columna de enlaces útiles -->
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h4>Enlaces útiles</h4>
                        <ul>
                            <li><a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a></li>
                            <li><a href="<?php echo esc_url(home_url('/contacto')); ?>">Contacto</a></li>
                            <li><a href="<?php echo esc_url(home_url('/politica-de-privacidad')); ?>">Política de privacidad</a></li>
                            <li><a href="<?php echo esc_url(home_url('/aviso-legal')); ?>">Aviso legal</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Columna de categorías -->
                <div class="col-lg-4">
                    <div class="footer-categories">
                        <h4>Categorías</h4>
                        <ul>
                            <?php
                            $categories = get_categories(array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 5
                            ));
                            
                            foreach ($categories as $category) {
                                printf(
                                    '<li><a href="%s">%s</a></li>',
                                    esc_url(get_category_link($category->term_id)),
                                    esc_html($category->name)
                                );
                            }
                            ?>
                        </ul>
                    </div>
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
