// Función IIFE para evitar conflictos y reducir el scope global
(function() {
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        // Crear el contenedor del lightbox
        var lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.setAttribute('role', 'dialog');
        lightbox.setAttribute('aria-modal', 'true');
        lightbox.setAttribute('aria-hidden', 'true');
        lightbox.setAttribute('aria-label', 'Visualizador de imágenes');
        lightbox.innerHTML = `
            <div class="lightbox-content" role="document">
                <button class="lightbox-close" aria-label="Cerrar imagen" title="Cerrar">&times;</button>
                <img src="" alt="Imagen ampliada" role="img">
                <div class="lightbox-caption" role="status" aria-live="polite"></div>
            </div>
        `;
        
        // Añadir estilos específicos para mejorar contraste
        var styleElement = document.createElement('style');
        styleElement.textContent = `
            .lightbox {
                background-color: rgba(0, 0, 0, 0.92) !important; /* Fondo más oscuro para mejor contraste */
            }
            .lightbox-close {
                background-color: rgba(0, 0, 0, 0.6) !important;
                color: white !important;
                font-size: 40px !important;
                border-radius: 50% !important;
                width: 50px !important;
                height: 50px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .lightbox-close:hover, .lightbox-close:focus {
                background-color: rgba(0, 0, 0, 0.8) !important;
                color: white !important;
                box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5) !important;
            }
            .lightbox-caption {
                color: white !important;
                background-color: rgba(0, 0, 0, 0.7) !important;
                padding: 10px !important;
                margin-top: 10px !important;
                border-radius: 4px !important;
                font-weight: 500 !important;
            }
        `;
        document.head.appendChild(styleElement);
        
        document.body.appendChild(lightbox);

        // Obtener referencias a los elementos
        var lightboxImg = lightbox.querySelector('img');
        var closeBtn = lightbox.querySelector('.lightbox-close');
        var lightboxContent = lightbox.querySelector('.lightbox-content');
        var lightboxCaption = lightbox.querySelector('.lightbox-caption');

        // Función para abrir el lightbox
        function openLightbox(img) {
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt || 'Imagen ampliada';
            
            // Mostrar título/leyenda de la imagen si existe
            if (img.alt && img.alt !== 'Imagen ampliada') {
                lightboxCaption.textContent = img.alt;
                lightboxCaption.style.display = 'block';
            } else if (img.getAttribute('data-caption')) {
                lightboxCaption.textContent = img.getAttribute('data-caption');
                lightboxCaption.style.display = 'block';
            } else {
                lightboxCaption.style.display = 'none';
            }
            
            lightbox.style.display = 'block';
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            lightboxImg.classList.remove('zoomed');
            // Capturar el foco dentro del lightbox
            closeBtn.focus();
        }

        // Función para cerrar el lightbox
        function closeLightbox() {
            lightbox.style.display = 'none';
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            lightboxImg.classList.remove('zoomed');
        }

        // Función para alternar el zoom
        function toggleZoom(e) {
            if (e.target === lightboxImg) {
                e.stopPropagation();
                lightboxImg.classList.toggle('zoomed');
                if (lightboxImg.classList.contains('zoomed')) {
                    lightboxImg.setAttribute('aria-expanded', 'true');
                } else {
                    lightboxImg.setAttribute('aria-expanded', 'false');
                }
            }
        }

        // Agregar eventos de cierre
        closeBtn.addEventListener('click', closeLightbox);
        
        // Cerrar al hacer clic en el fondo oscuro
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Cerrar al hacer clic en el contenedor de la imagen (fuera de la imagen)
        lightboxContent.addEventListener('click', function(e) {
            if (e.target === lightboxContent) {
                closeLightbox();
            }
        });

        // Agregar evento de zoom
        lightboxImg.addEventListener('click', toggleZoom);

        // Agregar evento de tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && lightbox.style.display === 'block') {
                closeLightbox();
            }
        });

        // Agregar eventos a todas las imágenes del contenido
        var contentImages = document.querySelectorAll('.entry-content img');
        contentImages.forEach(function(img) {
            // Remover el enlace si existe
            if (img.parentNode.tagName === 'A') {
                var parent = img.parentNode;
                parent.parentNode.replaceChild(img, parent);
            }

            // Agregar cursor pointer y atributos ARIA
            img.style.cursor = 'pointer';
            img.setAttribute('role', 'button');
            img.setAttribute('tabindex', '0');
            img.setAttribute('aria-label', img.alt || 'Ver imagen ampliada');

            // Agregar evento de clic
            img.addEventListener('click', function(e) {
                e.preventDefault();
                openLightbox(this);
            });
            
            // Agregar evento de teclado para accesibilidad
            img.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openLightbox(this);
                }
            });
        });
    });
})(); 