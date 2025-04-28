<?php get_header(); ?>
<?php get_template_part('template-parts/search-form', 'search-form'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="error-page-wrapper">
                <div class="error-page-header">
                    <h1>404</h1>
                    <h2>Página no encontrada</h2>
                    <p>Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
                </div>
                
                <div class="error-page-content">
                    <div class="error-suggestions">
                        <h3>¿Qué puedes hacer ahora?</h3>
                        
                        <div class="suggestion-item">
                            <span class="suggestion-icon">🏠</span>
                            <a href="<?php echo esc_url(home_url('/')); ?>">Volver a la página de inicio</a>
                        </div>
                        
                        <div class="suggestion-item">
                            <span class="suggestion-icon">🔍</span>
                            <span>Utilizar el buscador en la parte superior</span>
                        </div>
                        
                        <div class="suggestion-item">
                            <span class="suggestion-icon">⚠️</span>
                            <span>Verificar que la URL esté escrita correctamente</span>
                        </div>
                    </div>
                    
                    <div class="error-resources">
                        <div class="resource-section">
                            <h3>Categorías populares</h3>
                            <div class="category-buttons">
                                <?php
                                $categories = get_categories(array(
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 5
                                ));
                                
                                foreach ($categories as $category) {
                                    printf(
                                        '<a href="%s" class="category-button %s">%s</a>',
                                        esc_url(get_category_link($category->term_id)),
                                        esc_attr($category->slug),
                                        esc_html($category->name)
                                    );
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="resource-section">
                            <h3>Artículos populares</h3>
                            <div class="popular-articles">
                                <?php
                                $popular_posts = get_posts(array(
                                    'posts_per_page' => 5,
                                    'orderby' => 'comment_count',
                                    'order' => 'DESC'
                                ));
                                
                                foreach ($popular_posts as $post) {
                                    setup_postdata($post);
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="article-link">
                                        <span class="article-title"><?php the_title(); ?></span>
                                        <span class="article-arrow">→</span>
                                    </a>
                                    <?php
                                }
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para la página 404 */
.error-page-wrapper {
    max-width: 900px;
    margin-block: 40px 80px;
    margin-inline: auto;
    font-family: var(--font-family);
}

.error-page-header {
    text-align: center;
    margin-block-end: 40px;
    padding-block-end: 30px;
    border-block-end: 1px solid #eaeaea;
}

.error-page-header h1 {
    font-size: 72px;
    font-weight: 700;
    color: #008ec2;
    margin: 0 0 10px;
    letter-spacing: -0.02em;
}

.error-page-header h2 {
    font-size: 28px;
    font-weight: 600;
    color: #222;
    margin-block: 0 20px;
    border: none;
}

.error-page-header p {
    font-size: 18px;
    color: #555;
    max-width: 600px;
    margin-inline: auto;
    line-height: 1.6;
}

.error-suggestions {
    background-color: #f9f9f9;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
}

.error-suggestions h3 {
    font-size: 22px;
    font-weight: 600;
    color: #333;
    margin-block: 0 25px;
    padding-block-end: 15px;
    border-block-end: 2px solid #008ec2;
}

.suggestion-item {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 15px;
    align-items: center;
    margin-block-end: 20px;
}

.suggestion-icon {
    font-size: 20px;
    min-width: 24px;
    text-align: center;
}

.suggestion-item a, 
.suggestion-item span:not(.suggestion-icon) {
    font-size: 16px;
    color: #444;
    line-height: 1.5;
    text-decoration: none;
}

.suggestion-item a {
    color: #0077a3;
    font-weight: 500;
    transition: color 0.2s ease;
}

.suggestion-item a:hover {
    color: #008ec2;
    text-decoration: underline;
}

.resource-section {
    margin-block-end: 40px;
}

.resource-section h3 {
    font-size: 22px;
    font-weight: 600;
    color: #333;
    margin-block: 0 20px;
    padding-block-end: 10px;
    border-block-end: 2px solid #008ec2;
}

.category-button {
    display: inline-block;
    padding-block: 8px;
    padding-inline: 16px;
    background-color: #008ec2;
    color: white;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.category-button:hover {
    background-color: #0077a3;
    transform: translateY(-2px);
    color: white;
}

.category-button.linux { background-color: #7c30d9; }
.category-button.programacion { background-color: #008080; }
.category-button.gameplays { background-color: #0077a3; }
.category-button.videojuegos { background-color: #ff7f00; }
.category-button.uncategorized { background-color: #151319; }

.popular-articles {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.article-link {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    padding: 12px 18px;
    background-color: #f9f9f9;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.article-link:hover {
    background-color: #f0f0f0;
    transform: translateX(5px);
}

.article-title {
    font-weight: 500;
    font-size: 15px;
    line-height: 1.4;
}

.article-arrow {
    font-size: 18px;
    color: #008ec2;
    opacity: 0.7;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.article-link:hover .article-arrow {
    opacity: 1;
    transform: translateX(2px);
}

/* Responsive ajustments */
@media (max-width: 768px) {
    .error-page-header h1 {
        font-size: 60px;
    }
    
    .error-page-header h2 {
        font-size: 24px;
    }
    
    .error-page-header p {
        font-size: 16px;
    }
    
    .error-page-content {
        grid-template-columns: 1fr;
    }
    
    .category-buttons {
        justify-content: center;
    }
}
</style>

<?php get_footer(); ?>