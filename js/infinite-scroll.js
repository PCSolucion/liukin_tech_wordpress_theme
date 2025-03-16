// Función IIFE para evitar conflictos y reducir el scope global
(function() {
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        var loading = false;
        var container = document.getElementById('infinite-posts');
        var loader = document.getElementById('infinite-loader');
        
        // Función throttle para optimizar el evento scroll
        function throttle(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                    }, limit);
                }
            };
        }

        // Función para cargar más posts
        function loadMorePosts() {
            if (loading || window.liukinCurrentPage >= window.liukinMaxPages) {
                return;
            }

            var scrollPosition = window.scrollY || window.pageYOffset;
            var windowHeight = window.innerHeight;
            var documentHeight = document.documentElement.scrollHeight;

            if (scrollPosition + windowHeight > documentHeight - 100) {
                loading = true;
                window.liukinCurrentPage++;
                
                loader.style.display = 'block';

                // Crear y configurar la petición AJAX
                var xhr = new XMLHttpRequest();
                var formData = new FormData();
                formData.append('action', 'liukin_load_more_posts');
                formData.append('nonce', window.liukinInfinite.nonce);
                formData.append('page', window.liukinCurrentPage);

                xhr.open('POST', window.liukinInfinite.ajaxurl, true);
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        if (xhr.responseText) {
                            container.insertAdjacentHTML('beforeend', xhr.responseText);
                            loading = false;
                        } else {
                            loader.innerHTML = '<p>' + window.liukinInfinite.no_more + '</p>';
                        }
                    } else {
                        loading = false;
                        loader.style.display = 'none';
                    }

                    if (window.liukinCurrentPage >= window.liukinMaxPages) {
                        loader.innerHTML = '<p>' + window.liukinInfinite.no_more + '</p>';
                    } else {
                        loader.style.display = 'none';
                    }
                };

                xhr.onerror = function() {
                    loading = false;
                    loader.style.display = 'none';
                };

                xhr.send(formData);
            }
        }

        // Agregar evento scroll con throttle
        window.addEventListener('scroll', throttle(loadMorePosts, 250));
    });
})(); 