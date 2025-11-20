<?php

$rutaLogoAdmin = 'vendor/adminlte/dist/img/LogoSena.png';
$permisoVerProducto = 'VER PRODUCTO';
$permisoVerCatalogoProducto = 'VER CATALOGO PRODUCTO';
$permisoVerCategoria = 'VER CATEGORIA';
$permisoVerMarca = 'VER MARCA';
$permisoVerProveedor = 'VER PROVEEDOR';
$permisoVerContrato = 'VER CONTRATO';
$permisoVerOrden = 'VER ORDEN';

// Constantes para permisos duplicados
$permisoVerProgramaCaracterizacion = 'VER PROGRAMA DE CARACTERIZACION';
$permisoVerCompetencia = 'VER COMPETENCIA';
$permisoVerResultadoAprendizaje = 'VER RESULTADO APRENDIZAJE';
$permisoVerSede = 'VER SEDE';
$permisoVerBloque = 'VER BLOQUE';
$permisoVerPiso = 'VER PISO';
$permisoVerAmbiente = 'VER AMBIENTE';
$permisoVerParametro = 'VER PARAMETRO';
$permisoVerTema = 'VER TEMA';

// Constantes para íconos duplicados
$iconoLista = 'fas fa-fw fa-list';
$iconoAgregar = 'fas fa-fw fa-plus';

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Administración de SENA',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Admin</b> SENA',
    'logo_img' => $rutaLogoAdmin,
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => $rutaLogoAdmin,
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => $rutaLogoAdmin,
            'alt' => 'Servicio Nacional de Aprendizaje - SENA',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => false,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4 text-sm',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],
        [
            'type' => 'navbar-notification',
            'id' => 'notificaciones-dropdown',
            'icon' => 'fas fa-bell',
            'icon_color' => 'warning',
            'topnav_right' => true,
            'dropdown_mode' => 'sliding',
            'dropdown_flabel' => 'Notificaciones',
            'update_cfg' => [
                'url' => 'inventario/notificaciones/unread',
                'period' => 30,
                'method' => 'GET',
            ],
            'submenu' => [
                [
                    'text' => 'Ver todas las notificaciones',
                    'url' => 'inventario/notificaciones',
                    'icon' => 'fas fa-bell text-primary',
                ],
            ],
        ],
        [
            'type' => 'navbar-notification',
            'id' => 'my-notification',
            'icon' => 'fas fa-user-circle',
            'text' => 'Usuario',
            'topnav_right' => true,
            'dropdown_mode' => true,
            'dropdown_flabel' => 'Mi Cuenta',
            'submenu' => [
                [
                    'text' => 'Mi Perfil',
                    'route' => 'profile.index',
                    'icon' => 'fas fa-user-circle text-info',
                ],
                [
                    'text' => 'Cambiar Contraseña',
                    'route' => 'password.change',
                    'icon' => 'fas fa-key text-warning',
                ],
                [
                    'type' => 'divider'
                ],
                [
                    'text' => 'Cerrar Sesión',
                    'url' => 'logout',
                    'icon' => 'fas fa-sign-out-alt text-danger',
                    'classes' => 'text-danger',
                    'route' => 'logout',
                    'method' => 'post',
                ]
            ],
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],

        // ========================================
        // MÓDULO: INICIO
        // ========================================
        [
            'header' => 'INICIO',
        ],
        [
            'text' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        [
            'text' => 'Notificaciones',
            'url'  => 'inventario/notificaciones',
            'icon' => 'fas fa-fw fa-bell',
            'can'  => 'VER NOTIFICACION',
        ],
        // ========================================
        // GESTIÓN ACADÉMICA
        // ========================================
        [
            'header' => 'GESTIÓN ACADÉMICA',
        ],
        /*
        [
            'text' => 'Programas de Formación',
            'icon' => 'fas fa-fw fa-graduation-cap',
            'can' => [$permisoVerProgramaCaracterizacion],
            'submenu' => [
                [
                    'text' => 'Programas Técnicos',
                    'icon' => 'fas fa-fw fa-book',
                    'submenu' => [
                        [
                            'text' => 'Ver Programas',
                            'url' => 'programa',
                            'icon' => $iconoLista,
                            'can' => [$permisoVerProgramaCaracterizacion],
                        ],
                        [
                            'text' => 'Crear Programa',
                            'url' => 'programa/create',
                            'icon' => $iconoAgregar,
                            'can' => [$permisoVerProgramaCaracterizacion],
                        ],
                        [
                            'text' => 'Redes de Conocimiento',
                            'url' => 'red-conocimiento',
                            'icon' => 'fas fa-fw fa-network-wired',
                            'can' => ['VER RED CONOCIMIENTO', 'CREAR RED CONOCIMIENTO'],
                        ],
                    ],
                ],
                [
                    'text' => 'Programas Complementarios',
                    'icon' => 'fas fa-fw fa-certificate',
                    'can' => $permisoVerProgramaCaracterizacion,
                    'submenu' => [
                        [
                            'text' => 'Ver Complementarios',
                            'url' => 'complementarios-ofertados',
                            'icon' => $iconoLista,
                            'can' => 'VER PROGRAMA COMPLEMENTARIO',
                        ],
                        [
                            'text' => 'Crear Complementario',
                            'url' => 'complementarios-ofertados/create',
                            'icon' => $iconoAgregar,
                            'can' => 'CREAR PROGRAMA COMPLEMENTARIO',
                        ],
                        [
                            'text' => 'Gestión de Aspirantes',
                            'url' => 'gestion-aspirantes',
                            'icon' => 'fas fa-fw fa-users',
                            'can' => [$permisoVerProgramaCaracterizacion],
                        ],
                        [
                            'text' => 'Estadísticas',
                            'url' => 'estadisticas',
                            'icon' => 'fas fa-fw fa-chart-line',
                            'can' => 'VER ESTADISTICAS',
                        ],
                        [
                            'text' => 'Procesar Documentos',
                            'url' => 'procesar-documentos',
                            'icon' => 'fas fa-fw fa-file-signature',
                            'can' => [$permisoVerProgramaCaracterizacion],
                        ],
                    ],
                ],
            ],
        ],
        */

        [
            'text' => 'Redes de conocimiento',
            'icon' => 'fas fa-fw fa-network-wired',
            'can' => ['VER RED CONOCIMIENTO'],
            'route' => 'red-conocimiento.index',
        ],
        [
            'text' => 'Programas de formación',
            'icon' => 'fas fa-fw fa-graduation-cap',
            'can' => ['VER PROGRAMA DE FORMACION'],
            'route' => 'programa.index',
        ],
        [
            'text' => 'Competencias y Resultados',
            'icon' => 'fas fa-fw fa-clipboard-check',
            'can' => [$permisoVerCompetencia, $permisoVerResultadoAprendizaje],
            'submenu' => [
                [
                    'text' => 'Competencias',
                    'route' => 'competencias.index',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                    'can' => [$permisoVerCompetencia]
                ],
                [
                    'text' => 'Resultados de Aprendizaje',
                    'icon' => 'fas fa-fw fa-trophy',
                    'can' => [$permisoVerResultadoAprendizaje],
                    'route' => 'resultados-aprendizaje.index',
                ],
                [
                    'text' => 'Guías de Aprendizaje',
                    'icon' => 'fas fa-fw fa-book-open',
                    'can' => ['VER GUIA APRENDIZAJE'],
                    'route' => 'guias-aprendizaje.index',
                ],
            ],
        ],
        [
            'text' => 'Fichas y Jornadas',
            'icon' => 'fas fa-fw fa-file-alt',
            'can' => ['VER FICHA CARACTERIZACION', 'VER JORNADA'],
            'submenu' => [
                [
                    'text' => 'Fichas de Caracterización',
                    'icon' => 'fas fa-fw fa-file-alt',
                    'route' => 'fichaCaracterizacion.index',
                    'can' => ['VER FICHA CARACTERIZACION'],
                ],
                [
                    'text' => 'Jornadas de Formación',
                    'icon' => 'fas fa-fw fa-calendar-alt',
                    'route' => 'jornada.index',
                    'can' => ['VER JORNADA'],
                ],
            ],
        ],

        // ========================================
        // GESTIÓN DE PERSONAL
        // ========================================
        [
            'header' => 'GESTIÓN DE PERSONAL',
        ],
        [
            'text' => 'Instructores',
            'icon' => 'fas fa-fw fa-chalkboard-teacher',
            'can' => ['VER INSTRUCTOR'],
            'route' => 'instructor.index',
        ],
        [
            'text' => 'Aprendices',
            'icon' => 'fas fa-fw fa-user-graduate',
            'can' => 'VER APRENDIZ',
            'route' => 'aprendices.index',
        ],

        // [
        //     'text' => 'Personas',
        //     'url' => 'personas',
        //     'icon' => 'fas fa-fw fa-users',
        //     'can' => 'VER PERSONA',
        // ],

        // ========================================
        // CONTROL Y SEGUIMIENTO
        // ========================================
        [
            'header' => 'CONTROL Y SEGUIMIENTO',
        ],
        // [
        //     'text' => 'Ingreso y Salida',
        //     'url' => 'control-seguimiento/ingreso-salida',
        //     'icon' => 'fas fa-fw fa-sign-in-alt',
        //     'can' => 'VER INGRESO SALIDA',
        // ],
        [
            'text' => 'Asistencia',
            'icon' => 'fas fa-fw fa-user-check',
            'can' => ['TOMAR ASISTENCIA', 'VER ASISTENCIA'],
            'submenu' => [
                [
                    'text' => 'Tomar Asistencia',
                    'url' => 'asistencia',
                    'icon' => 'fas fa-fw fa-check-square',
                    'can' => 'TOMAR ASISTENCIA',
                ],
                [
                    'text' => 'Consultar Asistencias',
                    'icon' => 'fas fa-fw fa-clipboard-check',
                    'can' => ['VER ASISTENCIA', $permisoVerProgramaCaracterizacion],
                    'submenu' => [
                        [
                            'text' => 'Consultas',
                            'url' => 'consulta',
                            'icon' => 'fas fa-fw fa-search',
                            'can' => 'VER CONSULTA',
                        ],
                        [
                            'text' => 'Consulta Personalizada',
                            'url' => '#',
                            'icon' => 'fas fa-fw fa-filter',
                            'can' => $permisoVerProgramaCaracterizacion,
                        ],
                    ],
                ],
            ],
        ],

        // ========================================
        // INFRAESTRUCTURA FÍSICA
        // ========================================
        [
            'header' => 'INFRAESTRUCTURA',
        ],
        [
            'text' => 'Organización Territorial',
            'icon' => 'fas fa-fw fa-map-marked-alt',
            'can' => ['VER REGIONAL', 'VER CENTROS DE FORMACION'],
            'submenu' => [
                [
                    'text' => 'Regionales',
                    'url' => 'regional',
                    'icon' => 'fas fa-fw fa-map-marker-alt',
                    'can' => 'VER REGIONAL',
                ],
                [
                    'text' => 'Centros de Formación',
                    'url' => 'centros',
                    'icon' => 'fas fa-fw fa-school',
                    'can' => 'VER CENTROS DE FORMACION',
                ],
            ],
        ],
        [
            'text' => 'Instalaciones',
            'icon' => 'fas fa-fw fa-building',
            'can' => [$permisoVerSede, $permisoVerBloque, $permisoVerPiso, $permisoVerAmbiente],
            'submenu' => [
                [
                    'text' => 'Sedes',
                    'icon' => 'fas fa-fw fa-building',
                    'can' => $permisoVerSede,
                    'submenu' => [
                        [
                            'text' => 'Ver Sedes',
                            'url' => 'sede',
                            'icon' => $iconoLista,
                            'can' => $permisoVerSede,
                        ],
                        [
                            'text' => 'Crear Sede',
                            'url' => 'sede/create',
                            'icon' => $iconoAgregar,
                            'can' => 'CREAR SEDE',
                        ],
                    ],
                ],
                [
                    'text' => 'Bloques',
                    'icon' => 'fas fa-fw fa-th-large',
                    'can' => $permisoVerBloque,
                    'submenu' => [
                        [
                            'text' => 'Ver Bloques',
                            'url' => 'bloque',
                            'icon' => $iconoLista,
                            'can' => $permisoVerBloque,
                        ],
                        [
                            'text' => 'Crear Bloque',
                            'url' => 'bloque/create',
                            'icon' => $iconoAgregar,
                            'can' => 'CREAR BLOQUE',
                        ],
                    ],
                ],
                [
                    'text' => 'Pisos',
                    'icon' => 'fas fa-fw fa-layer-group',
                    'can' => $permisoVerPiso,
                    'submenu' => [
                        [
                            'text' => 'Ver Pisos',
                            'url' => 'piso',
                            'icon' => $iconoLista,
                            'can' => $permisoVerPiso,
                        ],
                        [
                            'text' => 'Crear Piso',
                            'url' => 'piso/create',
                            'icon' => $iconoAgregar,
                            'can' => 'CREAR PISO',
                        ],
                    ],
                ],
                [
                    'text' => 'Ambientes',
                    'icon' => 'fas fa-fw fa-door-open',
                    'can' => $permisoVerAmbiente,
                    'submenu' => [
                        [
                            'text' => 'Ver Ambientes',
                            'url' => 'ambiente',
                            'icon' => $iconoLista,
                            'can' => $permisoVerAmbiente,
                        ],
                        [
                            'text' => 'Crear Ambiente',
                            'url' => 'ambiente/create',
                            'icon' => $iconoAgregar,
                            'can' => 'CREAR AMBIENTE',
                        ],
                    ],
                ],
            ],
        ],

        // ========================================
        // INVENTARIO
        // ========================================
        // [
        //     'header' => 'INVENTARIO',
        // ],
        // [
        //     'text' => 'Inventario',
        //     'icon' => 'fas fa-fw fa-boxes',
        //     'can'  => [
        //         $permisoVerProducto,
        //         $permisoVerCatalogoProducto,
        //         $permisoVerCategoria,
        //         $permisoVerMarca,
        //         $permisoVerProveedor,
        //         $permisoVerContrato,
        //         $permisoVerOrden,
        //     ],
        //     'submenu' => [
        //         [
        //             'text' => 'Dashboard',
        //             'url'  => 'inventario/dashboard',
        //             'icon' => 'fas fa-fw fa-chart-bar',
        //             'can'  => 'VER DASHBOARD INVENTARIO',
        //         ],
        //         [
        //             'text' => 'Productos',
        //             'icon' => 'fas fa-fw fa-box',
        //             'can'  => [
        //                 $permisoVerProducto,
        //                 $permisoVerCatalogoProducto,
        //             ],
        //             'submenu' => [
        //                 [
        //                     'text' => 'Lista de Productos',
        //                     'url'  => 'inventario/productos',
        //                     'icon' => $iconoLista,
        //                     'can'  => $permisoVerProducto,
        //                 ],
        //                 [
        //                     'text' => 'Catálogo de Productos',
        //                     'url'  => 'inventario/productos/catalogo',
        //                     'icon' => 'fas fa-fw fa-th',
        //                     'can'  => $permisoVerCatalogoProducto,
        //                 ],
        //                 [
        //                     'text' => 'Crear Producto',
        //                     'url'  => 'inventario/productos/create',
        //                     'icon' => $iconoAgregar,
        //                     'can'  => 'CREAR PRODUCTO',
        //                 ],
        //             ],
        //         ],
        //         [
        //             'text' => 'Carrito Sena',
        //             'url'  => 'inventario/carrito-sena',
        //             'icon' => 'fas fa-fw fa-shopping-cart',
        //             'can'  => 'VER CARRITO',
        //         ],
        //         [
        //             'text' => 'Órdenes',
        //             'icon' => 'fas fa-fw fa-file-invoice',
        //             'can'  => $permisoVerOrden,
        //             'submenu' => [
        //                 [
        //                     'text' => 'Todas las Órdenes',
        //                     'url'  => 'inventario/ordenes',
        //                     'icon' => $iconoLista,
        //                     'can'  => $permisoVerOrden,
        //                 ],
        //                 [
        //                     'text' => 'Aprobaciones Pendientes',
        //                     'url'  => 'inventario/aprobaciones/pendientes',
        //                     'icon' => 'fas fa-fw fa-hourglass-half',
        //                     'can'  => 'APROBAR ORDEN',
        //                 ],
        //                 [
        //                     'text' => 'Órdenes Aprobadas',
        //                     'url'  => 'inventario/ordenes/completadas',
        //                     'icon' => 'fas fa-fw fa-check-circle',
        //                     'can'  => $permisoVerOrden,
        //                 ],
        //                 [
        //                     'text' => 'Órdenes Rechazadas',
        //                     'url'  => 'inventario/ordenes/rechazadas',
        //                     'icon' => 'fas fa-fw fa-times-circle',
        //                     'can'  => $permisoVerOrden,
        //                 ],
        //             ],
        //         ],
        //         [
        //             'text' => 'Devoluciones',
        //             'icon' => 'fas fa-fw fa-undo',
        //             'can'  => 'DEVOLVER PRESTAMO',
        //             'submenu' => [
        //                 [
        //                     'text' => 'Préstamos Pendientes',
        //                     'url'  => 'inventario/devoluciones',
        //                     'icon' => 'fas fa-fw fa-clock',
        //                     'can'  => 'DEVOLVER PRESTAMO',
        //                 ],
        //                 [
        //                     'text' => 'Historial Devoluciones',
        //                     'url'  => 'inventario/devoluciones-historial',
        //                     'icon' => 'fas fa-fw fa-history',
        //                     'can'  => 'VER DEVOLUCION',
        //                 ],
        //             ],
        //         ],
        //         [
        //             'text' => 'Configuración',
        //             'icon' => 'fas fa-fw fa-cog',
        //             'can'  => [
        //                 $permisoVerCategoria,
        //                 $permisoVerMarca,
        //                 $permisoVerProveedor,
        //                 $permisoVerContrato,
        //             ],
        //             'submenu' => [
        //                 [
        //                     'text' => 'Categorías',
        //                     'url'  => 'inventario/categorias',
        //                     'icon' => 'fas fa-fw fa-tags',
        //                     'can'  => $permisoVerCategoria,
        //                 ],
        //                 [
        //                     'text' => 'Marcas',
        //                     'url'  => 'inventario/marcas',
        //                     'icon' => 'fas fa-fw fa-trademark',
        //                     'can'  => $permisoVerMarca,
        //                 ],
        //                 [
        //                     'text' => 'Proveedores',
        //                     'url'  => 'inventario/proveedores',
        //                     'icon' => 'fas fa-fw fa-truck',
        //                     'can'  => $permisoVerProveedor,
        //                 ],
        //                 [
        //                     'text' => 'Contratos/Convenios',
        //                     'url'  => 'inventario/contratos-convenios',
        //                     'icon' => 'fas fa-fw fa-file-contract',
        //                     'can'  => $permisoVerContrato,
        //                 ],
        //             ],
        //         ],
        //     ],
        // ],

        // ========================================
        // CONFIGURACIÓN DEL SISTEMA
        // ========================================
        [
            'header' => 'CONFIGURACIÓN',
        ],
        [
            'text' => 'Sistema',
            'icon' => 'fas fa-fw fa-cogs',
            'can' => [$permisoVerParametro, $permisoVerTema, 'ASIGNAR PERMISOS'],
            'submenu' => [
                [
                    'text' => 'Configuración General',
                    'icon' => 'fas fa-fw fa-wrench',
                    'can' => [$permisoVerParametro, $permisoVerTema],
                    'submenu' => [
                        [
                            'text' => 'Parámetros',
                            'url' => 'parametro',
                            'icon' => 'fas fa-fw fa-sliders-h',
                            'can' => $permisoVerParametro,
                        ],
                        [
                            'text' => 'Temas',
                            'url' => 'tema',
                            'icon' => 'fas fa-fw fa-paint-brush',
                            'can' => $permisoVerTema,
                        ],
                    ],
                ],
                [
                    'text' => 'Permisos',
                    'url' => 'permiso',
                    'icon' => 'fas fa-fw fa-lock',
                    'can' => 'ASIGNAR PERMISOS',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/sweetalert2/sweetalert2.all.min.js',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => implode('', [
                        '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue',
                        '/pace-theme-center-radar.min.css',
                    ]),
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => true,

    'custom_css' => [
        'css/custom.css',
        'public/css/app.css',
    ],
];
