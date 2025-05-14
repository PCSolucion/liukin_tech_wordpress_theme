/**
 * Abre imágenes en nuevas pestañas
 * Reemplaza la funcionalidad de lightbox
 */
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todas las imágenes dentro del contenido
    const imagesToOpen = document.querySelectorAll('.entry-content img');
    
    imagesToOpen.forEach(img => {
        if (img.closest('a')) { 
            // Si la imagen ya es un enlace, asegurarse de que abra en nueva pestaña
            const parentLink = img.closest('a');
            parentLink.setAttribute('target', '_blank');
            parentLink.setAttribute('rel', 'noopener noreferrer');
        } else {
            // Si la imagen no es un enlace, convertirla en uno
            img.style.cursor = 'pointer';
            img.setAttribute('tabindex', '0'); // Para accesibilidad
            
            // Crear un contenedor para no afectar el flujo del documento
            const container = document.createElement('span');
            container.style.display = 'inline-block';
            container.style.position = 'relative';
            
            // Insertar el contenedor antes de la imagen y mover la imagen dentro
            img.parentNode.insertBefore(container, img);
            container.appendChild(img);
            
            // Crear un enlace alrededor de la imagen
            const link = document.createElement('a');
            link.href = img.src;
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
            
            // Envolver la imagen con el enlace (dentro del contenedor)
            img.parentNode.insertBefore(link, img);
            link.appendChild(img);
            
            // Manejar navegación por teclado
            img.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.open(img.src, '_blank', 'noopener,noreferrer');
                }
            });
        }
    });
}); 