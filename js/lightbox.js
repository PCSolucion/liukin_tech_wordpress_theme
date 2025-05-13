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
                <div class="lightbox-image-container">
                    <img src="" alt="Imagen ampliada" role="img" tabindex="0">
                </div>
                <div class="lightbox-caption" role="status" aria-live="polite"></div>
                <div class="zoom-indicator">
                    <div class="zoom-level"></div>
                </div>
            </div>
        `;
        
        // Añadir estilos específicos para mejorar la experiencia
        var styleElement = document.createElement('style');
        styleElement.textContent = `
            .lightbox {
                display: none;
                position: fixed;
                z-index: 999;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.95);
                cursor: default;
            }
            
            .lightbox-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                max-width: 98%;
                width: 98vw;
                max-height: 90vh;
                text-align: center;
            }
            
            .lightbox-image-container {
                position: relative;
                overflow: hidden;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: zoom-in;
                margin: 0 auto;
            }
            
            .lightbox-image-container.zoomed {
                cursor: move;
            }
            
            .lightbox-image-container img {
                max-width: 100%;
                max-height: 85vh;
                width: auto;
                height: auto;
                object-fit: contain;
                transition: transform 0.3s ease;
                transform-origin: 0 0;
            }
            
            .lightbox-image-container img.wide-image {
                width: 100%;
                height: auto;
                max-height: 85vh;
            }
            
            .lightbox-image-container img.panoramic {
                width: 100%;
                max-height: 80vh;
            }
            
            .lightbox-image-container img.ultra-wide {
                width: 100%;
                max-height: 75vh;
            }
            
            .lightbox-close {
                position: absolute;
                top: -50px;
                right: 0;
                background-color: rgba(0, 0, 0, 0.6);
                color: white;
                font-size: 40px;
                font-weight: bold;
                cursor: pointer;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.2s;
                z-index: 1001;
            }
            
            .lightbox-close:hover, .lightbox-close:focus {
                background-color: rgba(0, 0, 0, 0.8);
                box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5);
            }
            
            .lightbox-caption {
                color: white;
                background-color: rgba(0, 0, 0, 0.7);
                padding: 10px;
                margin-top: 10px;
                border-radius: 4px;
                font-weight: 500;
                max-width: 100%;
            }
            
            .zoom-indicator {
                position: absolute;
                bottom: -40px;
                left: 50%;
                transform: translateX(-50%);
                width: 200px;
                height: 6px;
                background-color: rgba(255, 255, 255, 0.3);
                border-radius: 3px;
                overflow: hidden;
            }
            
            .zoom-level {
                height: 100%;
                width: 0%;
                background-color: rgba(255, 255, 255, 0.8);
                border-radius: 3px;
                transition: width 0.3s ease;
            }
            
            @media (max-width: 768px) {
                .lightbox-content {
                    width: 100vw;
                    max-width: 100%;
                }
                
                .lightbox-image-container img.wide-image,
                .lightbox-image-container img.panoramic,
                .lightbox-image-container img.ultra-wide {
                    width: 100%;
                    max-height: 80vh;
                }
            }
        `;
        document.head.appendChild(styleElement);
        document.body.appendChild(lightbox);

        // Elementos del lightbox
        const imageContainer = lightbox.querySelector('.lightbox-image-container');
        const lightboxImg = lightbox.querySelector('img');
        const closeBtn = lightbox.querySelector('.lightbox-close');
        const lightboxContent = lightbox.querySelector('.lightbox-content');
        const lightboxCaption = lightbox.querySelector('.lightbox-caption');
        const zoomLevel = lightbox.querySelector('.zoom-level');
        
        // Variable para almacenar el elemento que tenía el foco antes de abrir el lightbox
        let focusedElementBeforeModal;
        
        // Variables para el control de foco
        let focusableElements;
        let firstFocusableElement;
        let lastFocusableElement;

        // Variables para el zoom y movimiento
        let currentZoom = 1;
        const minZoom = 1;
        const maxZoom = 3;
        const panoramicMaxZoom = 5;
        const ultraWideMaxZoom = 7;
        let isDragging = false;
        let startX, startY, startOffsetX = 0, startOffsetY = 0;
        let lastTouchDistance = 0;
        
        // Función para obtener elementos focusables dentro del lightbox
        function setFocusableElements() {
            focusableElements = lightbox.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            firstFocusableElement = focusableElements[0];
            lastFocusableElement = focusableElements[focusableElements.length - 1];
        }

        // Actualizar el indicador de zoom
        function updateZoomIndicator() {
            // Calcular porcentaje de zoom (1 = 0%, maxZoom = 100%)
            const zoomPercentage = ((currentZoom - minZoom) / (maxZoom - minZoom)) * 100;
            zoomLevel.style.width = `${zoomPercentage}%`;
        }

        // Aplicar transformación a la imagen
        function applyTransform(scale, translateX = 0, translateY = 0) {
            lightboxImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
            
            // Actualizar clase según zoom
            if (scale > 1) {
                imageContainer.classList.add('zoomed');
            } else {
                imageContainer.classList.remove('zoomed');
                // Reiniciar posición si no hay zoom
                startOffsetX = 0;
                startOffsetY = 0;
            }
            
            updateZoomIndicator();
        }

        // Función para abrir el lightbox
        function openLightbox(img) {
            // Guardar el elemento que tiene el foco antes de abrir el lightbox
            focusedElementBeforeModal = document.activeElement;
            
            // Establecer la imagen
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
            
            // Mostrar el lightbox
            lightbox.style.display = 'block';
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            // Asegurar que la imagen ocupe el ancho máximo posible
            setTimeout(function() {
                const imgRatio = lightboxImg.naturalWidth / lightboxImg.naturalHeight;
                
                // Eliminar clases anteriores
                lightboxImg.classList.remove('wide-image', 'panoramic', 'ultra-wide');
                
                // Si la imagen es más ancha que alta, darle todo el ancho
                if (imgRatio > 1) {
                    lightboxImg.classList.add('wide-image');
                }
                
                // Si es realmente panorámica (más del doble de ancha que alta)
                if (imgRatio > 2) {
                    lightboxImg.classList.add('panoramic');
                }
                
                // Si es ultra-panorámica
                if (imgRatio > 3.5) {
                    lightboxImg.classList.add('ultra-wide');
                }
            }, 50);
            
            // Reiniciar zoom
            currentZoom = 1;
            startOffsetX = 0;
            startOffsetY = 0;
            applyTransform(currentZoom);
            
            // Actualizar los elementos focusables
            setFocusableElements();
            
            // Capturar el foco dentro del lightbox
            closeBtn.focus();
            
            // Añadir evento de keydown para el trap focus
            document.addEventListener('keydown', trapFocus);
        }

        // Función para cerrar el lightbox
        function closeLightbox() {
            lightbox.style.display = 'none';
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            
            // Reiniciar zoom y posición
            currentZoom = 1;
            startOffsetX = 0;
            startOffsetY = 0;
            applyTransform(currentZoom);
            
            // Eliminar evento de keydown
            document.removeEventListener('keydown', trapFocus);
            
            // Devolver el foco al elemento que lo tenía antes de abrir el lightbox
            if (focusedElementBeforeModal) {
                focusedElementBeforeModal.focus();
            }
        }

        // Función para mantener el foco dentro del lightbox (trap focus)
        function trapFocus(e) {
            // Si la tecla presionada es Escape, cerrar el lightbox
            if (e.key === 'Escape') {
                closeLightbox();
                return;
            }
            
            // Si la tecla presionada es Tab
            if (e.key === 'Tab') {
                // Si se presiona Shift + Tab y el foco está en el primer elemento
                if (e.shiftKey && document.activeElement === firstFocusableElement) {
                    e.preventDefault();
                    lastFocusableElement.focus();
                } 
                // Si se presiona Tab y el foco está en el último elemento
                else if (!e.shiftKey && document.activeElement === lastFocusableElement) {
                    e.preventDefault();
                    firstFocusableElement.focus();
                }
            }
            
            // Controles de zoom con teclado
            if (e.key === '+' || (e.key === '=' && e.shiftKey)) {
                e.preventDefault();
                zoomIn();
            } else if (e.key === '-') {
                e.preventDefault();
                zoomOut();
            } else if (e.key === '0') {
                e.preventDefault();
                resetZoom();
            }
        }

        // Función para hacer zoom en un punto específico
        function zoomAtPoint(targetZoom, clientX, clientY) {
            // Limitar el zoom según tipo de imagen
            let actualMaxZoom = maxZoom;
            
            // Ajustar el zoom máximo según el tipo de imagen
            if (lightboxImg.classList.contains('ultra-wide')) {
                actualMaxZoom = ultraWideMaxZoom; // Mayor zoom para imágenes ultra anchas
            } else if (lightboxImg.classList.contains('panoramic')) {
                actualMaxZoom = panoramicMaxZoom; // Mayor zoom para panorámicas
            }
            
            // Limitar el zoom
            const clampedZoom = Math.max(minZoom, Math.min(actualMaxZoom, targetZoom));
            
            if (clampedZoom === currentZoom) return;
            
            // Calcular el punto donde se realiza el zoom
            const rect = lightboxImg.getBoundingClientRect();
            const offsetX = clientX - rect.left;
            const offsetY = clientY - rect.top;
            
            // Calcular el punto en coordenadas de la imagen (0-1)
            const pointX = offsetX / rect.width;
            const pointY = offsetY / rect.height;
            
            // Calcular nueva posición manteniendo el punto bajo el cursor
            const scaleFactor = clampedZoom / currentZoom;
            const newOffsetX = startOffsetX - (offsetX * (scaleFactor - 1));
            const newOffsetY = startOffsetY - (offsetY * (scaleFactor - 1));
            
            // Actualizar zoom y posición
            currentZoom = clampedZoom;
            startOffsetX = newOffsetX;
            startOffsetY = newOffsetY;
            
            // Aplicar transformación
            applyTransform(currentZoom, startOffsetX, startOffsetY);
        }

        // Función simple de zoom in/out (sin punto específico)
        function zoomIn() {
            if (currentZoom < maxZoom) {
                const center = lightboxImg.getBoundingClientRect();
                const centerX = center.left + center.width / 2;
                const centerY = center.top + center.height / 2;
                zoomAtPoint(currentZoom + 0.5, centerX, centerY);
            }
        }

        function zoomOut() {
            if (currentZoom > minZoom) {
                const center = lightboxImg.getBoundingClientRect();
                const centerX = center.left + center.width / 2;
                const centerY = center.top + center.height / 2;
                zoomAtPoint(currentZoom - 0.5, centerX, centerY);
            }
        }

        function resetZoom() {
            currentZoom = 1;
            startOffsetX = 0;
            startOffsetY = 0;
            applyTransform(currentZoom);
        }

        // Iniciar arrastre
        function startDrag(e) {
            e.preventDefault();
            
            // Solo permitir arrastre si hay zoom
            if (currentZoom <= 1) return;
            
            isDragging = true;
            
            if (e.type === 'mousedown') {
                startX = e.clientX;
                startY = e.clientY;
            } else if (e.type === 'touchstart') {
                const touch = e.touches[0];
                startX = touch.clientX;
                startY = touch.clientY;
                
                // Si hay dos toques, registrar distancia inicial para pinch zoom
                if (e.touches.length === 2) {
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    lastTouchDistance = Math.hypot(
                        touch2.clientX - touch1.clientX,
                        touch2.clientY - touch1.clientY
                    );
                }
            }
        }

        // Mover la imagen cuando se arrastra
        function drag(e) {
            if (!isDragging) return;
            e.preventDefault();
            
            let moveX, moveY;
            
            if (e.type === 'mousemove') {
                moveX = e.clientX;
                moveY = e.clientY;
                
                // Calcular la diferencia de movimiento
                const deltaX = moveX - startX;
                const deltaY = moveY - startY;
                
                // Actualizar posición
                const newOffsetX = startOffsetX + deltaX;
                const newOffsetY = startOffsetY + deltaY;
                
                // Aplicar transformación
                applyTransform(currentZoom, newOffsetX, newOffsetY);
            } else if (e.type === 'touchmove') {
                // Pinch para zoom
                if (e.touches.length === 2) {
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    
                    // Calcular nueva distancia entre dedos
                    const currentTouchDistance = Math.hypot(
                        touch2.clientX - touch1.clientX,
                        touch2.clientY - touch1.clientY
                    );
                    
                    // Calcular factor de zoom basado en la diferencia de distancia
                    const touchDelta = currentTouchDistance - lastTouchDistance;
                    const zoomFactor = 0.01; // Sensibilidad de pinch zoom
                    
                    // Punto central entre los dos dedos
                    const centerX = (touch1.clientX + touch2.clientX) / 2;
                    const centerY = (touch1.clientY + touch2.clientY) / 2;
                    
                    // Aplicar nuevo zoom
                    zoomAtPoint(currentZoom + (touchDelta * zoomFactor), centerX, centerY);
                    
                    // Actualizar distancia para próximo evento
                    lastTouchDistance = currentTouchDistance;
                    return;
                }
                
                // Arrastre con un solo dedo
                const touch = e.touches[0];
                moveX = touch.clientX;
                moveY = touch.clientY;
                
                // Calcular la diferencia de movimiento
                const deltaX = moveX - startX;
                const deltaY = moveY - startY;
                
                // Actualizar posición
                const newOffsetX = startOffsetX + deltaX;
                const newOffsetY = startOffsetY + deltaY;
                
                // Aplicar transformación
                applyTransform(currentZoom, newOffsetX, newOffsetY);
            }
        }

        // Finalizar arrastre
        function stopDrag(e) {
            if (!isDragging) return;
            
            if (e.type === 'mouseup' || (e.type === 'touchend' && e.touches.length === 0)) {
                isDragging = false;
                
                // Guardar la posición final
                const computedStyle = window.getComputedStyle(lightboxImg);
                const transform = new DOMMatrix(computedStyle.transform);
                startOffsetX = transform.m41;
                startOffsetY = transform.m42;
            }
        }

        // Click en la imagen para alternar zoom
        function toggleZoom(e) {
            e.preventDefault();
            
            // Si ya tiene zoom, volver a tamaño original
            if (currentZoom > 1) {
                resetZoom();
            } else {
                // Determinar nivel de zoom según tipo de imagen
                let zoomLevel = 2;
                
                if (lightboxImg.classList.contains('ultra-wide')) {
                    zoomLevel = 3;
                } else if (lightboxImg.classList.contains('panoramic')) {
                    zoomLevel = 2.5;
                }
                
                // Aumentar zoom en el punto donde se hizo clic
                zoomAtPoint(zoomLevel, e.clientX, e.clientY);
            }
        }

        // Manejar zoom con rueda del mouse
        function handleWheel(e) {
            e.preventDefault();
            
            // Factor de zoom
            const zoomFactor = 0.1 * (e.deltaY > 0 ? -1 : 1);
            const newZoom = currentZoom + zoomFactor;
            
            // Aplicar zoom en la posición del cursor
            zoomAtPoint(newZoom, e.clientX, e.clientY);
        }

        // Registrar eventos
        // Zoom y arrastre
        imageContainer.addEventListener('mousedown', startDrag);
        imageContainer.addEventListener('touchstart', startDrag, { passive: false });
        document.addEventListener('mousemove', drag);
        document.addEventListener('touchmove', drag, { passive: false });
        document.addEventListener('mouseup', stopDrag);
        document.addEventListener('touchend', stopDrag);
        imageContainer.addEventListener('click', toggleZoom);
        imageContainer.addEventListener('wheel', handleWheel, { passive: false });
        
        // Eventos de cierre
        closeBtn.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
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