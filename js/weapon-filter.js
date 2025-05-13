/**
 * Filtrado dinámico de posts por armas y roles
 * 
 * Este script permite filtrar posts sin cambiar de página
 * al hacer clic en los iconos de armas o roles.
 */
(function($) {
    'use strict';
    
    // Variables globales
    let originalContent = null;
    let isFilterActive = false;
    let currentFilters = {
        weapon: '',
        role: ''
    };
    let $contentContainer = null;
    let $rowContainer = null;
    
    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        // Verificar que tenemos los datos necesarios
        if (typeof liukinWeaponFilter === 'undefined') {
            console.error('Error: liukinWeaponFilter no está definido');
            return;
        }
        
        console.log('Inicializando filtro de armas y roles...');
        
        // Inicializar con un pequeño retraso para asegurar que todo el DOM está disponible
        setTimeout(function() {
            try {
                initFilters();
                console.log('Filtros inicializados correctamente');
            } catch (e) {
                console.error('Error al inicializar filtros:', e);
            }
        }, 500); // 500ms de espera para asegurar que el DOM está completamente cargado
    });
    
    /**
     * Inicializa el filtrado de armas y roles
     */
    function initFilters() {
        console.log('Inicializando sistema de filtrado...');
        
        // Identificar todos los contenedores relevantes
        $contentContainer = $('.container').first();
        $rowContainer = $('.container > .row').first().parent();
        
        if (!$contentContainer || $contentContainer.length === 0) {
            console.error('Error: No se pudo identificar el contenedor principal');
            return;
        }
        
        console.log('Contenedor principal identificado');
        
        // Verificar si existen las secciones de filtro
        const hasWeaponsSection = $('.weapons-section').length > 0;
        const hasRolesSection = $('.roles-section').length > 0;
        
        if (hasWeaponsSection) {
            console.log('Sección de armas encontrada - Inicializando filtros de armas');
        }
        
        if (hasRolesSection) {
            console.log('Sección de roles encontrada - Inicializando filtros de roles');
        }
        
        // Capturar el contenido original para restaurarlo después
        saveOriginalContent();
        
        // Convertir los iconos de armas en elementos clicables para filtrado
        convertIconsToFilters();
        
        // Añadir tooltips para mostrar el nombre del arma al hacer hover
        initWeaponTooltips();
        
        // Añadir clases especiales a los elementos filtrables para mejor UX
        $('.weapon-card, .role-card').addClass('filterable-element');
        
        console.log('Filtros inicializados correctamente');
        
        // No mostrar mensaje informativo inicial
        /*
        setTimeout(function() {
            showFeedbackMessage('Haz clic en un arma o rol para filtrar las builds');
        }, 2000);
        */
        
        // Manejar eventos de clic en iconos de armas
        $(document).on('click', '.weapon-icon-tag', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Clic en icono de arma');
            
            // Obtener el título del arma o el atributo alt como alternativa
            let weaponTag = $(this).attr('title');
            if (!weaponTag) {
                weaponTag = $(this).attr('alt');
            }
            
            if (weaponTag) {
                // Extraer el nombre completo del arma sin modificarlo demasiado
                weaponTag = weaponTag.toLowerCase().trim();
                console.log('Nombre completo del arma: ' + weaponTag);
                
                // Si es la misma arma, deseleccionarla y eliminar clase de filtro activo
                if (currentFilters.weapon === weaponTag) {
                    currentFilters.weapon = '';
                    console.log('Deseleccionando arma actual');
                    
                    // Eliminar clase de filtro activo de armas
                    $(this).removeClass('filter-active');
                    $('.weapon-card').removeClass('filter-active');
                } else {
                    currentFilters.weapon = weaponTag;
                    console.log('Seleccionando nueva arma: ' + weaponTag);
                }
                
                // Mostrar un mensaje de feedback al usuario
                showFeedbackMessage('Aplicando filtro para ' + weaponTag + '...');
                
                applyFilters();
            } else {
                console.error('Error: No se pudo determinar el arma');
                showFeedbackMessage('No se pudo determinar el arma para filtrar', 'error');
            }
        });
        
        // Manejar eventos de clic en elementos de filtro de la sección de armas
        $(document).on('click', '.weapon-filter-action', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Clic en filtro de arma');
            
            // Obtener el arma desde el atributo data
            let weaponTag = $(this).data('weapon');
            
            if (weaponTag) {
                weaponTag = weaponTag.trim().toLowerCase(); // Normalizar pero conservar el nombre completo
                console.log('Nombre completo del arma desde tarjeta: ' + weaponTag);
                
                // Si es la misma arma, deseleccionarla y eliminar clase de filtro activo
                if (currentFilters.weapon === weaponTag) {
                    currentFilters.weapon = '';
                    console.log('Deseleccionando arma actual');
                    
                    // Eliminar clase de filtro activo de armas
                    $('.weapon-card').removeClass('filter-active');
                    $('.weapon-item-static').removeClass('filter-active');
                    $('.weapon-icon-tag').removeClass('filter-active');
                    
                    showFeedbackMessage('Eliminando filtro de arma');
                } else {
                    currentFilters.weapon = weaponTag;
                    console.log('Seleccionando nueva arma: ' + weaponTag);
                    showFeedbackMessage('Aplicando filtro para ' + weaponTag + '...');
                }
                
                applyFilters();
            } else {
                console.error('Error: No se pudo determinar el arma del filtro');
                showFeedbackMessage('No se pudo determinar el arma para filtrar', 'error');
            }
        });
        
        // Manejar eventos de clic en tarjetas de roles
        $(document).on('click', '.role-card, .role-link', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Clic en rol');
            
            // Determinar el rol de forma más directa
            let role = '';
            
            // Verificar si el elemento clicado o su padre tiene alguna de las clases de rol
            const $element = $(this).hasClass('role-card') ? $(this) : $(this).closest('.role-card');
            
            if ($element.hasClass('tank')) {
                role = 'tank';
            } else if ($element.hasClass('healer')) {
                role = 'healer';
            } else if ($element.hasClass('dps')) {
                role = 'dps';
            }
            
            if (role) {
                console.log('Filtrando por rol: ' + role);
                
                // Si es el mismo rol, deseleccionarlo y eliminar clase de filtro activo
                if (currentFilters.role === role) {
                    currentFilters.role = '';
                    console.log('Deseleccionando rol actual');
                    
                    // Eliminar clase de filtro activo del rol
                    $element.removeClass('filter-active');
                    
                    showFeedbackMessage('Eliminando filtro de rol');
                } else {
                    currentFilters.role = role;
                    console.log('Seleccionando nuevo rol: ' + role);
                    showFeedbackMessage('Aplicando filtro para rol ' + role);
                }
                
                applyFilters();
            } else {
                console.error('Error: No se pudo determinar el rol');
                showFeedbackMessage('No se pudo determinar el rol para filtrar', 'error');
            }
        });
        
        // Manejar eventos de clic en el botón de restablecer filtro
        $(document).on('click', '.reset-filter', function(e) {
            e.preventDefault();
            console.log('Restableciendo filtros');
            resetFilters();
        });
    }
    
    /**
     * Inicializa los tooltips para los iconos de armas
     */
    function initWeaponTooltips() {
        // Agregar tooltip al hacer hover sobre las tarjetas de armas
        $('.weapon-card').each(function() {
            const $this = $(this);
            const $img = $this.find('img');
            
            if ($img.length) {
                const weaponName = $img.attr('title') || $img.attr('alt');
                
                if (weaponName) {
                    // Crear un tooltip
                    const $tooltip = $('<div class="weapon-tooltip">' + weaponName + '</div>');
                    $this.append($tooltip);
                    
                    // Mostrar/ocultar tooltip al hacer hover
                    $this.hover(
                        function() {
                            $tooltip.addClass('visible');
                        },
                        function() {
                            $tooltip.removeClass('visible');
                        }
                    );
                }
            }
        });
    }
    
    /**
     * Guarda el contenido original de la página
     */
    function saveOriginalContent() {
        console.log('Guardando contenido original de la página...');
        
        // Guardar el contenido original solo una vez
        if (originalContent === null) {
            // Detectar el contenedor principal y el contenedor de builds
            const $container = $('.container').first();
            const $buildsCategoryWrapper = $('.builds-category-wrapper');
            
            // Asegurarse de que el contenedor principal existe
            if (!$container.length) {
                console.error('Error: No se pudo encontrar el contenedor principal');
                return false;
            }
            
            console.log('Contenedor principal encontrado, guardando contenido...');
            
            // Registrar todos los elementos principales
            const originalStructure = {
                // Guardar el estado de secciones relevantes
                buildsCategoryWrapper: $buildsCategoryWrapper.length ? $buildsCategoryWrapper.clone() : null,
                // Capturar todas las filas de posts
                rows: []
            };
            
            // Capturar todas las filas que no sean parte del sistema de navegación o filtrado
            const $filas = $container.find('.row').not('.navbar-row, .weapons-row, .roles-grid, .weapons-single-row');
            console.log('Encontradas ' + $filas.length + ' filas de contenido para guardar');
            
            if ($filas.length > 0) {
                $filas.each(function() {
                    originalStructure.rows.push($(this).clone());
                });
                console.log('Filas de contenido clonadas con éxito: ' + originalStructure.rows.length);
            } else {
                console.warn('No se encontraron filas de contenido para guardar');
            }
            
            // Verificar si tenemos el wrapper de builds
            if (originalStructure.buildsCategoryWrapper) {
                console.log('Wrapper de builds guardado con éxito');
            } else {
                console.warn('No se encontró wrapper de builds para guardar');
            }
            
            // Guardar la estructura original
            originalContent = originalStructure;
            
            // Verificar que se haya guardado correctamente
            if (originalContent.rows && originalContent.rows.length > 0) {
                console.log('Contenido original guardado correctamente con ' + originalContent.rows.length + ' filas');
                return true;
            } else {
                console.warn('El contenido original se guardó, pero no contiene filas de posts');
                return false;
            }
        } else {
            console.log('El contenido original ya estaba guardado previamente');
            return true;
        }
    }
    
    /**
     * Convierte los iconos de armas en elementos clicables para filtrado
     */
    function convertIconsToFilters() {
        // Buscar todos los iconos de armas en la página
        $('.weapon-icon-tag').each(function() {
            // Eliminar cualquier enlace envolvente
            if ($(this).parent().is('a')) {
                $(this).unwrap();
            }
            
            // Agregar clase y cursor para indicar que es clicable
            $(this).addClass('weapon-filter-trigger');
            $(this).css('cursor', 'pointer');
            
            // Agregar atributo de título si no tiene
            if (!$(this).attr('title') && $(this).attr('alt')) {
                $(this).attr('title', $(this).attr('alt'));
            }
        });
        
        // También asegurar que las tarjetas de armas tengan nombres correctos
        $('.weapon-filter-action').each(function() {
            // Si no tiene data-weapon, intentar obtenerlo del título o alt de la imagen
            if (!$(this).data('weapon')) {
                const $img = $(this).find('img');
                if ($img.length) {
                    const weaponName = $img.attr('title') || $img.attr('alt');
                    if (weaponName) {
                        $(this).attr('data-weapon', weaponName);
                    }
                }
            }
        });
        
        console.log('Iconos convertidos a filtros: ' + $('.weapon-filter-trigger').length);
        console.log('Tarjetas de armas disponibles: ' + $('.weapon-filter-action').length);
    }
    
    /**
     * Aplica los filtros actuales (arma y/o rol)
     */
    function applyFilters() {
        // Guardar los filtros actuales para comparar después
        let lastWeapon = currentFilters.weapon;
        let lastRole = currentFilters.role;
        
        // Si no hay filtros activos, restaurar el contenido original o cargar todos los posts
        if (!currentFilters.weapon && !currentFilters.role) {
            console.log('No hay filtros activos, restableciendo a contenido original');
            
            // Primero, eliminar todas las clases de filtro activo
            $('.weapon-card').removeClass('filter-active');
            $('.role-card').removeClass('filter-active');
            $('.weapon-icon-tag').removeClass('filter-active');
            
            // Si no hay contenido original guardado o no podemos restaurarlo,
            // cargar todos los posts directamente con AJAX en lugar de llamar a resetFilters()
            if (!originalContent || !originalContent.rows || originalContent.rows.length === 0) {
                console.log('No hay contenido original disponible, cargando todos los posts vía AJAX');
                
                // Hacer la solicitud AJAX para obtener todos los posts
                $.ajax({
                    url: liukinWeaponFilter.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'liukin_filter_posts_by_criteria',
                        nonce: liukinWeaponFilter.nonce,
                        weapon: '',
                        role: '',
                        category: liukinWeaponFilter.current_category
                    },
                    success: function(response) {
                        console.log('Respuesta de carga completa recibida:', response);
                        
                        if (response.success) {
                            // Eliminar todos los resultados filtrados actuales
                            $('.filter-results-container').remove();
                            
                            // Eliminar las filas de posts existentes para evitar duplicados
                            $('.row:not(.navbar-row)').not('.weapons-row, .roles-grid, .weapons-single-row').remove();
                            
                            // Reemplazar el contenido con todos los posts
                            replaceContent(response.html);
                            
                            // Actualizar estado del filtro
                            isFilterActive = false;
                            
                            // Asegurarnos nuevamente de que no queden filtros activos visualmente
                            $('.weapon-card').removeClass('filter-active');
                            $('.role-card').removeClass('filter-active');
                            $('.weapon-icon-tag').removeClass('filter-active');
                            
                            console.log('Todos los posts cargados exitosamente al desmarcar filtros');
                        } else {
                            console.error('Error en la respuesta AJAX al restablecer');
                            showFeedbackMessage('Error al cargar todos los posts', 'error');
                        }
                        
                        hideLoadingIndicator();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', error);
                        // Mostrar mensaje de error
                        showFeedbackMessage('Hubo un error al cargar todos los posts. Por favor, recarga la página.', 'error');
                        hideLoadingIndicator();
                    }
                });
            } else {
                // Si tenemos contenido original, intentar restaurarlo
                resetFilters();
            }
            return;
        }
        
        // Guardar los filtros como datos claros y normalizados
        let weaponFilter = currentFilters.weapon ? currentFilters.weapon.trim().toLowerCase() : '';
        let roleFilter = currentFilters.role ? currentFilters.role.trim().toLowerCase() : '';
        
        // Normalizar el nombre del arma para asegurar consistencia
        if (weaponFilter) {
            // Eliminar espacios y normalizar nombres compuestos
            weaponFilter = weaponFilter.replace(/\s+de\s+/gi, 'de');
            weaponFilter = weaponFilter.replace(/\s+/g, '');
            
            // Normalizar nombres específicos con mapeo exacto por tipo de arma
            const weaponMapping = {
                // Báculos - asignar identificadores únicos para cada tipo
                'báculodefuego': 'baculo_fuego',
                'báculofuego': 'baculo_fuego',
                'baculofuego': 'baculo_fuego',
                'baculodefuego': 'baculo_fuego',
                'baculo_de_fuego': 'baculo_fuego',
                'baculo_fuego': 'baculo_fuego',
                'fuego': 'baculo_fuego', // Para casos donde solo se menciona "fuego"
                
                'báculodevida': 'baculo_vida',
                'báculovida': 'baculo_vida',
                'baculovida': 'baculo_vida',
                'baculodevida': 'baculo_vida',
                'baculo_de_vida': 'baculo_vida',
                'baculo_vida': 'baculo_vida',
                'vida': 'baculo_vida', // Para casos donde solo se menciona "vida"
                
                // Manoplas - asignar identificadores únicos para cada tipo
                'manopladehielo': 'manopla_hielo',
                'manoplahielo': 'manopla_hielo',
                'manopla_de_hielo': 'manopla_hielo',
                'manopla_hielo': 'manopla_hielo',
                'hielo': 'manopla_hielo', // Para casos donde solo se menciona "hielo"
                
                'manopladevacio': 'manopla_vacio',
                'manoplavacio': 'manopla_vacio',
                'manopladevacío': 'manopla_vacio',
                'manoplavacío': 'manopla_vacio',
                'manopla_de_vacio': 'manopla_vacio',
                'manopla_vacio': 'manopla_vacio',
                'manopla_de_vacío': 'manopla_vacio',
                'manopla_vacío': 'manopla_vacio',
                'vacio': 'manopla_vacio', // Para casos donde solo se menciona "vacio"
                'vacío': 'manopla_vacio', // Para casos donde solo se menciona "vacío"
                
                // Otros tipos de armas
                'granhacha': 'gran_hacha',
                'hachagrande': 'gran_hacha',
                'gran_hacha': 'gran_hacha',
                
                'espadón': 'espadon',
                'espadon': 'espadon',
                'espada_grande': 'espadon',
                
                'hachadoble': 'hacha_doble',
                'hacha-doble': 'hacha_doble',
                'hacha_doble': 'hacha_doble',
                
                'arcológico': 'arco_logico',
                'arco_lógico': 'arco_logico',
                'arcologico': 'arco_logico',
                'arco_logico': 'arco_logico'
            };
            
            // Aplicar mapeo si existe
            if (weaponMapping[weaponFilter]) {
                console.log('Normalizando arma: ' + weaponFilter + ' -> ' + weaponMapping[weaponFilter]);
                weaponFilter = weaponMapping[weaponFilter];
            }
        }
        
        // No mostrar indicador de carga
        // showLoadingIndicator();
        
        console.log('Aplicando filtros - Arma: ' + (weaponFilter || 'ninguna') + ', Rol: ' + (roleFilter || 'ninguno'));
        
        // Variable para prevenir parpadeos
        let isProcessing = true;
        
        // Resaltar visualmente los filtros activos inmediatamente
        highlightActiveFilters();
        
        // Hacer la solicitud AJAX para obtener posts filtrados
        $.ajax({
            url: liukinWeaponFilter.ajaxurl,
            type: 'POST',
            data: {
                action: 'liukin_filter_posts_by_criteria',
                nonce: liukinWeaponFilter.nonce,
                weapon: weaponFilter,
                role: roleFilter,
                category: liukinWeaponFilter.current_category
            },
            success: function(response) {
                console.log('Respuesta recibida:', response);
                
                // Ocultar indicador de carga
                hideLoadingIndicator();
                
                if (response.success) {
                    // Guardar los filtros actuales antes de reemplazar el contenido
                    let savedWeapon = currentFilters.weapon;
                    let savedRole = currentFilters.role;
                    
                    console.log('Filtros guardados antes de reemplazar contenido - Arma: ' + 
                        (savedWeapon || 'ninguna') + ', Rol: ' + (savedRole || 'ninguno'));
                    
                    // No mostrar mensajes de feedback
                    // let resultCount = response.count || 0;
                    // showFeedbackMessage('Se encontraron ' + resultCount + ' builds que coinciden con los filtros aplicados');
                    
                    // Verificar que la respuesta contiene HTML válido
                    if (!response.html) {
                        console.error('Error: La respuesta no contiene HTML');
                        return;
                    }
                    
                    // Reemplazar el contenido con los resultados filtrados
                    replaceContent(response.html);
                    
                    // Restaurar los filtros que teníamos activos
                    currentFilters.weapon = savedWeapon;
                    currentFilters.role = savedRole;
                    
                    console.log('Filtros restaurados después de reemplazar contenido - Arma: ' + 
                        (currentFilters.weapon || 'ninguna') + ', Rol: ' + (currentFilters.role || 'ninguno'));
                    
                    // Actualizar estado del filtro
                    isFilterActive = true;
                    
                    // Resaltar visualmente los filtros activos
                    highlightActiveFilters();
                    
                    // Asegurarnos de que los filtros siguen siendo interactivos
                    $('.role-card').removeClass('disabled');
                    $('.weapon-card').removeClass('disabled');
                    
                    // Re-inicializar eventos de clic en la nueva versión del contenido
                    convertIconsToFilters();
                    
                    // Verificar si hay resultados dentro del contenedor
                    const $results = $('.filter-results-container');
                    if ($results.length > 0) {
                        const $postCards = $results.find('.post-card, article, .phome');
                        
                        if ($postCards.length === 0) {
                            console.warn('Advertencia: El contenedor de resultados está vacío o no contiene tarjetas de post');
                            // Agregar un mensaje amigable si no hay resultados
                            $results.html('<div class="no-results-message">No hay builds que coincidan con los filtros seleccionados. Prueba con otros filtros.</div>');
                        } else {
                            console.log('Contenedor de resultados contiene: ' + $postCards.length + ' elementos de post');
                            // Asegurarnos de que no haya mensaje de "no resultados" si hay tarjetas
                            $results.find('.no-results-message').remove();
                        }
                        
                        convertIconsToFilters();
                        // scrollToResults();
                    } else {
                        showFeedbackMessage('Error al mostrar los resultados. Por favor, intenta de nuevo.', 'error');
                    }
                } else {
                    console.error('Error en la respuesta AJAX');
                    showFeedbackMessage('Error al cargar los resultados', 'error');
                    
                    // Si hay un error en la respuesta, intentar restablecer los filtros
                    resetFilters();
                }
                
                // Marcar como terminado el procesamiento
                isProcessing = false;
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                // Mostrar mensaje de error
                showFeedbackMessage('Hubo un error al filtrar los resultados. Por favor, inténtalo de nuevo.', 'error');
                hideLoadingIndicator();
                
                // En caso de error AJAX, intentar restablecer los filtros
                resetFilters();
                
                // Marcar como terminado el procesamiento
                isProcessing = false;
            }
        });
    }
    
    /**
     * Restablece todos los filtros y muestra todos los posts
     */
    function resetFilters() {
        // No hacer nada si no hay filtro activo
        if (!isFilterActive) {
            console.log('No hay filtros activos que restablecer');
            return;
        }
        
        console.log('Iniciando restablecimiento de filtros...');
        
        // No mostrar indicador de carga
        // showLoadingIndicator();
        
        // Guardar filtros antes de resetear para depuración
        let oldWeapon = currentFilters.weapon;
        let oldRole = currentFilters.role;
        
        // Reiniciar filtros
        currentFilters = {
            weapon: '',
            role: ''
        };
        
        console.log('Filtros anteriores - Arma: ' + (oldWeapon || 'ninguna') + ', Rol: ' + (oldRole || 'ninguno'));
        console.log('Restableciendo todos los filtros...');
        
        // No mostrar mensaje de feedback
        // showFeedbackMessage('Mostrando todas las builds');
        
        // Eliminar clases de filtro activo inmediatamente
        $('.filter-active').removeClass('filter-active');
        
        // No manipular el botón flotante (deshabilitado)
        // $('.reset-filter-float').removeClass('visible');
        // setTimeout(function() {
        //    $('.reset-filter-float').remove();
        // }, 300);
        
        // Verificar si el contenido original es válido y utilizable
        const contenidoOriginalValido = 
            originalContent && 
            originalContent.rows && 
            originalContent.rows.length > 0;
            
        // Intento 1: Si tenemos el contenido original y es válido, restaurarlo
        if (contenidoOriginalValido) {
            try {
                console.log('Intentando restaurar contenido original guardado...');
                
                // Eliminar resultados de filtrado existentes
                $('.filter-results-container').remove();
                
                const $container = $('.container').first();
                
                // Restaurar sección de builds si existe
                if (originalContent.buildsCategoryWrapper) {
                    // Asegurarnos que no exista duplicado
                    $('.builds-category-wrapper').remove();
                    // Comprobar si ya tenemos el wrapper de builds 
                    if ($container.find('.builds-category-wrapper').length === 0) {
                        $container.prepend(originalContent.buildsCategoryWrapper);
                        console.log('Sección de builds restaurada');
                    }
                }
                
                // Eliminar filas de posts existentes para evitar duplicados
                $('.row:not(.navbar-row)').not('.weapons-row, .roles-grid, .weapons-single-row').remove();
                
                // Reemplazar el contenido con todos los posts
                if (originalContent.rows && originalContent.rows.length > 0) {
                    console.log('Restaurando ' + originalContent.rows.length + ' filas de contenido original');
                    
                    // Añadir filas originales después del wrapper de builds
                    const $buildsCategoryWrapper = $('.builds-category-wrapper');
                    if ($buildsCategoryWrapper.length > 0) {
                        // Añadir cada fila individualmente
                        $.each(originalContent.rows, function(index, row) {
                            $buildsCategoryWrapper.after(row);
                        });
                    } else {
                        // Añadir al final del contenedor si no hay wrapper
                        $.each(originalContent.rows, function(index, row) {
                            $container.append(row);
                        });
                    }
                    
                    console.log('Contenido original restaurado con éxito');
                } else {
                    console.error('Error: No se encontraron las filas de posts originales');
                    throw new Error('No se encontraron filas de posts en el contenido original');
                }
                
                // Volver a convertir los iconos en clicables
                convertIconsToFilters();
                
                // Reiniciar estado del filtro
                isFilterActive = false;
                
                console.log('Filtros restablecidos y contenido original restaurado');
                
                hideLoadingIndicator();
                return;
            } catch (e) {
                console.error('Error al restaurar contenido original:', e);
                // Continuar con el siguiente método si hay un error
            }
        } else {
            console.warn('No se encontró contenido original guardado o no es válido');
        }
        
        // Intento 2: Hacer una solicitud AJAX para obtener todos los posts
        console.log('Obteniendo todos los posts via AJAX...');
        
        // Hacer la solicitud AJAX para obtener todos los posts
        $.ajax({
            url: liukinWeaponFilter.ajaxurl,
            type: 'POST',
            data: {
                action: 'liukin_filter_posts_by_criteria',
                nonce: liukinWeaponFilter.nonce,
                weapon: '',
                role: '',
                category: liukinWeaponFilter.current_category
            },
            success: function(response) {
                console.log('Respuesta de reset recibida:', response);
                
                if (response.success) {
                    // Eliminar todos los resultados filtrados actuales
                    $('.filter-results-container').remove();
                    
                    // Eliminar las filas de posts existentes para evitar duplicados
                    $('.row:not(.navbar-row)').not('.weapons-row, .roles-grid, .weapons-single-row').remove();
                    
                    // Reemplazar el contenido con todos los posts
                    replaceContent(response.html);
                    
                    // Actualizar estado del filtro
                    isFilterActive = false;
                    
                    console.log('Todos los posts cargados y contenido limpiado');
                } else {
                    console.error('Error en la respuesta AJAX al restablecer');
                    showFeedbackMessage('Error al cargar todos los posts', 'error');
                }
                
                hideLoadingIndicator();
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                // Mostrar mensaje de error
                showFeedbackMessage('Hubo un error al cargar todos los posts. Por favor, recarga la página.', 'error');
                hideLoadingIndicator();
            }
        });
    }
    
    /**
     * Reemplaza el contenido con los resultados filtrados
     * 
     * @param {string} html - El HTML con los resultados filtrados
     */
    function replaceContent(html) {
        console.log('Iniciando reemplazo completo del contenido...');
        
        // Verificar que tenemos HTML válido para reemplazar
        if (!html || html.trim() === '') {
            console.error('Error: HTML vacío o no válido proporcionado');
            showFeedbackMessage('No se encontraron resultados con los filtros seleccionados', 'error');
            return;
        }
        
        try {
            // 1. Crear un div temporal para trabajar con el HTML recibido
            const $tempDiv = $('<div>').html(html);
            const $newResults = $tempDiv.find('.filter-results-container');
            
            // 2. Verificar si hay contenido real dentro de los resultados
            const hasContent = $newResults.find('.post-card, article, .phome').length > 0;
            console.log('Resultados recibidos contienen ' + 
                $newResults.find('.post-card, article, .phome').length + ' elementos de post');
            
            // 3. Si ya tenemos resultados filtrados, eliminarlos
            const $existingResults = $('.filter-results-container');
            if ($existingResults.length > 0) {
                $existingResults.fadeOut(200, function() {
                    $existingResults.remove();
                    insertNewResults();
                });
            } else {
                // Eliminar cualquier resultado de filtro anterior y filas de posts originales
                $('.row:not(.navbar-row)').not('.weapons-row, .roles-grid, .weapons-single-row').remove();
                insertNewResults();
            }
            
            // Función para insertar los resultados nuevos
            function insertNewResults() {
                // 4. Buscar la sección de builds (que contiene las armas y roles)
                const $buildsWrapper = $('.builds-category-wrapper');
                
                // 5. Determinar dónde insertar los resultados
                if ($buildsWrapper.length > 0) {
                    // Conservar solo la sección de armas y roles, eliminar el resto
                    console.log('Conservando sección de builds y reemplazando contenido...');
                    
                    // Eliminar cualquier contenido después del wrapper de builds excepto el footer
                    $buildsWrapper.nextAll().not('.site-footer, .copyright').remove();
                    
                    // Insertar los resultados filtrados después del wrapper de builds
                    if (hasContent) {
                        $buildsWrapper.after($newResults);
                        $newResults.hide().fadeIn(300);
                        console.log('Resultados insertados después de la sección de builds');
                    } else {
                        // Si no hay contenido real, mostrar mensaje de no resultados
                        const $noResults = $('<div class="filter-results-container"><div class="no-results-message">No hay builds que coincidan con los filtros seleccionados. Prueba con otros filtros.</div></div>');
                        $buildsWrapper.after($noResults);
                        $noResults.hide().fadeIn(300);
                    }
                } else {
                    // Si no existe el wrapper, trabajar con el contenedor principal
                    const $mainContainer = $('.container').first();
                    if ($mainContainer.length === 0) {
                        throw new Error('No se encontró ningún contenedor para insertar los resultados');
                    }
                    
                    // Limpiar el contenedor pero preservar los elementos importantes
                    const $importantElements = $mainContainer.find('.builds-header, .weapons-section, .roles-section').clone();
                    $mainContainer.empty();
                    
                    // Reinsertar los elementos importantes y luego los resultados
                    $mainContainer.append($importantElements);
                    
                    if (hasContent) {
                        $mainContainer.append($newResults);
                        $newResults.hide().fadeIn(300);
                    } else {
                        // Si no hay contenido real, mostrar mensaje de no resultados
                        const $noResults = $('<div class="filter-results-container"><div class="no-results-message">No hay builds que coincidan con los filtros seleccionados. Prueba con otros filtros.</div></div>');
                        $mainContainer.append($noResults);
                        $noResults.hide().fadeIn(300);
                    }
                    
                    console.log('Contenedor principal limpiado y resultados añadidos');
                }
                
                // 6. Volver a convertir los iconos en clicables
                convertIconsToFilters();
                
                // 7. Aplicar efectos visuales para destacar los resultados (más sutil)
                $('.filter-results-container').addClass('highlight-results');
                setTimeout(function() {
                    $('.filter-results-container').removeClass('highlight-results');
                }, 1000);
            }
        } catch (error) {
            console.error('Error al reemplazar el contenido:', error);
            
            // Método de emergencia: reemplazar todo el contenido
            const $container = $('.container').first();
            if ($container.length > 0) {
                console.log('Aplicando método de emergencia por error: ' + error.message);
                
                // Preservar las secciones importantes
                const $buildsSection = $('.builds-category-wrapper, .builds-header, .weapons-section, .roles-section').clone();
                
                // Reiniciar el contenedor
                $container.empty();
                
                // Agregar las secciones importantes primero
                if ($buildsSection.length > 0) {
                    $container.append($buildsSection);
                }
                
                // Agregar los resultados
                $container.append(html);
                
                // Verificar que se agregaron los resultados
                if ($('.filter-results-container').length > 0) {
                    console.log('Resultados agregados con método de emergencia');
                    
                    // Verificar si hay resultados dentro del contenedor
                    const $results = $('.filter-results-container');
                    if ($results.length > 0) {
                        const $postCards = $results.find('.post-card, article, .phome');
                        
                        if ($postCards.length === 0) {
                            console.warn('Advertencia: El contenedor de resultados está vacío o no contiene tarjetas de post');
                            // Agregar un mensaje amigable si no hay resultados
                            $results.html('<div class="no-results-message">No hay builds que coincidan con los filtros seleccionados. Prueba con otros filtros.</div>');
                        } else {
                            console.log('Contenedor de resultados contiene: ' + $postCards.length + ' elementos de post');
                            // Asegurarnos de que no haya mensaje de "no resultados" si hay tarjetas
                            $results.find('.no-results-message').remove();
                        }
                        
                        convertIconsToFilters();
                    } else {
                        showFeedbackMessage('Error al mostrar los resultados. Por favor, intenta de nuevo.', 'error');
                    }
                } else {
                    showFeedbackMessage('Error crítico al mostrar los resultados. Recarga la página.', 'error');
                }
            }
        }
        
        // Asegurarse de ocultar el indicador de carga
        hideLoadingIndicator();
    }
    
    /**
     * Resalta visualmente los filtros que están activos
     */
    function highlightActiveFilters() {
        console.log('Actualizando indicadores visuales de filtros activos');
        
        // Limpiar clases de filtro activo
        $('.role-card').removeClass('filter-active');
        $('.weapon-card').removeClass('filter-active');
        $('.weapon-icon-tag').removeClass('filter-active');
        $('.weapon-item-static').removeClass('filter-active');
        
        // Resaltar filtro de rol activo si existe
        if (currentFilters.role) {
            $('.role-card.' + currentFilters.role).addClass('filter-active');
        }
        
        // Resaltar filtro de arma activa si existe
        if (currentFilters.weapon) {
            // Procesar nombre del arma para la búsqueda (normalizar)
            const weaponName = currentFilters.weapon.trim().toLowerCase();
            
            // Aplicar clase activa a todas las representaciones del arma seleccionada
            $('.weapon-filter-action').each(function() {
                const thisWeapon = $(this).data('weapon');
                if (thisWeapon && thisWeapon.trim().toLowerCase() === weaponName) {
                    $(this).addClass('filter-active');
                    
                    // Si es un elemento dentro de una tarjeta, añadir la clase a la tarjeta también
                    if ($(this).closest('.weapon-card').length) {
                        $(this).closest('.weapon-card').addClass('filter-active');
                    }
                    
                    // Si es un elemento weapon-item-static, aplicar clase directamente
                    if ($(this).hasClass('weapon-item-static')) {
                        $(this).addClass('filter-active');
                    }
                }
            });
            
            // También aplicar a las etiquetas de armas en los posts
            $('.weapon-icon-tag').each(function() {
                const thisWeapon = $(this).data('weapon') || $(this).attr('title') || $(this).attr('alt');
                if (thisWeapon && thisWeapon.trim().toLowerCase() === weaponName) {
                    $(this).addClass('filter-active');
                }
            });
        }
        
        console.log('Indicadores visuales actualizados');
    }
    
    /**
     * Muestra un indicador de carga mientras se filtran los posts
     * (Deshabilitado para evitar mensajes)
     */
    function showLoadingIndicator() {
        // Función deshabilitada para evitar mostrar indicador de carga
        console.log('Indicador de carga deshabilitado');
        return;
        
        /*
        // Crear indicador de carga si no existe
        if ($('#weapon-filter-loading').length === 0) {
            $('body').append('<div id="weapon-filter-loading" class="weapon-filter-loading">Cargando resultados...</div>');
        }
        
        // Mostrar indicador con una animación más suave
        $('#weapon-filter-loading').addClass('visible').fadeIn(200);
        console.log('Indicador de carga mostrado');
        */
    }
    
    /**
     * Oculta el indicador de carga
     */
    function hideLoadingIndicator() {
        $('#weapon-filter-loading').removeClass('visible').fadeOut(200);
        console.log('Indicador de carga ocultado');
    }
    
    /**
     * Desplaza la ventana al inicio de los resultados
     * (Función deshabilitada para dar control manual al usuario)
     */
    function scrollToResults() {
        // Función deshabilitada para que el usuario controle el scroll manualmente
        console.log('Desplazamiento automático deshabilitado');
        return;
        
        /*
        // Buscar primero la cabecera de resultados filtrados
        const $target = $('.filter-results-container').first();
        
        if ($target.length) {
            console.log('Desplazando a los resultados filtrados');
            
            // Animar el desplazamiento para mejor experiencia de usuario
            $('html, body').animate({
                scrollTop: $target.offset().top - 50 // 50px de margen para mejor visualización
            }, 500, function() {
                // Destacar visualmente el contenedor de resultados para captar la atención
                $('.filter-results-container').addClass('highlight-results');
                
                // Eliminar la clase después de un tiempo
                setTimeout(function() {
                    $('.filter-results-container').removeClass('highlight-results');
                }, 1500);
            });
        } else {
            console.warn('No se encontró el contenedor de resultados para desplazarse');
        }
        */
    }
    
    /**
     * Muestra un mensaje de feedback temporal al usuario
     * (Deshabilitado para evitar mensajes)
     * 
     * @param {string} message - El mensaje a mostrar
     * @param {string} type - El tipo de mensaje ('info', 'error', etc)
     */
    function showFeedbackMessage(message, type = 'info') {
        // Función deshabilitada para evitar mostrar mensajes
        console.log('Mensaje de feedback (deshabilitado): ' + message + ' (tipo: ' + type + ')');
        return;
        
        /*
        // Eliminar cualquier mensaje anterior
        $('.weapon-filter-feedback').remove();
        
        // Crear el elemento de feedback
        const $feedback = $('<div class="weapon-filter-feedback weapon-filter-feedback-' + type + '">' + message + '</div>');
        $('body').append($feedback);
        
        // Mostrar con animación
        $feedback.fadeIn(200);
        
        // Ocultar después de 3 segundos
        setTimeout(function() {
            $feedback.fadeOut(200, function() {
                $(this).remove();
            });
        }, 3000);
        
        console.log('Mensaje de feedback: ' + message + ' (tipo: ' + type + ')');
        */
    }
    
})(jQuery); 