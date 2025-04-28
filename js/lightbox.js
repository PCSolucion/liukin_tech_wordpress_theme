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
            </div>
        `;
        document.body.appendChild(lightbox);

        // Obtener referencias a los elementos
        var lightboxImg = lightbox.querySelector('img');
        var closeBtn = lightbox.querySelector('.lightbox-close');
        var lightboxContent = lightbox.querySelector('.lightbox-content');

        // Función para abrir el lightbox
        function openLightbox(img) {
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt || 'Imagen ampliada';
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