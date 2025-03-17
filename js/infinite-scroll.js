// Función IIFE para evitar conflictos y reducir el scope global
(function() {
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        var loading = false;
        var container = document.getElementById('infinite-posts');
        var loader = document.getElementById('infinite-loader');
        var requestQueue = [];
        var isProcessingQueue = false;
        
        // Detectar el tipo de página
        var isArchive = document.body.classList.contains('archive');
        var isSearch = document.body.classList.contains('search');
        var isHome = document.body.classList.contains('home');
        
        // Función debounce para limitar la frecuencia de las llamadas
        function debounce(func, wait) {
            var timeout;
            return function() {
                var context = this;
                var args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Función throttle mejorada para optimizar el evento scroll
        function throttle(func, limit) {
            var inThrottle;
            var lastResult;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                        if (lastResult) {
                            func.apply(context, lastResult);
                            lastResult = null;
                        }
                    }, limit);
                } else {
                    lastResult = args;
                }
            };
        }

        // Función para procesar la cola de solicitudes
        function processQueue() {
            if (isProcessingQueue || requestQueue.length === 0) {
                return;
            }

            isProcessingQueue = true;
            var request = requestQueue.shift();
            sendRequest(request);
        }

        // Función para enviar la solicitud AJAX
        function sendRequest(request) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.liukinInfinite.ajaxurl, true);
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText) {
                        container.insertAdjacentHTML('beforeend', xhr.responseText);
                    } else {
                        loader.innerHTML = '<p>' + window.liukinInfinite.no_more + '</p>';
                    }
                }
                loading = false;
                isProcessingQueue = false;
                loader.style.display = 'none';
                processQueue();
            };

            xhr.onerror = function() {
                loading = false;
                isProcessingQueue = false;
                loader.style.display = 'none';
                processQueue();
            };

            xhr.send(request);
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
                var formData = new FormData();
                formData.append('action', 'liukin_load_more_posts');
                formData.append('nonce', window.liukinInfinite.nonce);
                formData.append('page', window.liukinCurrentPage);
                
                // Agregar parámetros específicos según el tipo de página
                if (isArchive) {
                    formData.append('is_archive', 'true');
                    formData.append('term_id', window.liukinInfinite.termId);
                    formData.append('taxonomy', window.liukinInfinite.taxonomy);
                } else if (isSearch) {
                    formData.append('is_search', 'true');
                    formData.append('search_query', window.liukinInfinite.searchQuery);
                }

                // Agregar la solicitud a la cola
                requestQueue.push(formData);
                processQueue();
            }
        }

        // Agregar evento scroll con throttle y debounce
        var throttledLoadMore = throttle(loadMorePosts, 250);
        var debouncedLoadMore = debounce(throttledLoadMore, 100);
        window.addEventListener('scroll', debouncedLoadMore);
    });
})(); 