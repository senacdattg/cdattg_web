<?php

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
    'logo_img' => 'vendor/adminlte/dist/img/LogoSena.png',
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
            'path' => 'vendor/adminlte/dist/img/LogoSena.png',
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
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/LogoSena.png',
            'alt' => 'AdminLTE Preloader Image',
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

    'usermenu_enabled' => true,
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
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
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
    'sidebar_collapse_remember' => false,
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
            'id' => 'my-notification',
            'icon' => 'fas fa-user-circle',
            'text' => 'Usuario',
            'topnav_right' => true,
            'dropdown_mode' => true,
            'dropdown_flabel' => 'Mi Cuenta',
            'submenu' => [
                [
                    'text' => 'Mi Perfil',
                    'url' => '/mi-perfil',
                    'icon' => 'fas fa-user-circle text-info',
                    'can' => 'VER MI PERFIL',
                ],
                [
                    'text' => 'Cambiar Contraseña',
                    'url' => 'cambiar-password',
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

        // ========================================
        // MÓDULO: CONFIGURACIÓN DEL SISTEMA
        // ========================================
        [
            
        ],
        [
            'text' => 'Sistema',
            'icon' => 'fas fa-fw fa-cogs',
            'can' => ['VER PARAMETRO', 'VER TEMA', 'ASIGNAR PERMISOS'],
            'submenu' => [
                [
                    'text' => 'Parámetros',
                    'url' => 'parametro',
                    'icon' => 'fas fa-fw fa-sliders-h',
                    'can' => 'VER PARAMETRO',
                ],
                [
                    'text' => 'Temas',
                    'url' => 'tema',
                    'icon' => 'fas fa-fw fa-paint-brush',
                    'can' => 'VER TEMA',
                ],
                [
                    'text' => 'Permisos',
                    'url' => 'permiso',
                    'icon' => 'fas fa-fw fa-lock',
                    'can' => 'ASIGNAR PERMISOS',
                ],
            ],
        ],
        [
            'text' => 'Personas',
            'url' => 'personas',
            'icon' => 'fas fa-fw fa-users',
            'can' => 'VER PERSONA',
        ],

        // ========================================
        // MÓDULO: INFRAESTRUCTURA
        // ========================================
        [
            
        ],
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
        [
            'text' => 'Sedes',
            'icon' => 'fas fa-fw fa-building',
            'can' => 'VER SEDE',
            'submenu' => [
                [
                    'text' => 'Ver Sedes',
                    'url' => 'sede',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER SEDE',
                ],
                [
                    'text' => 'Crear Sede',
                    'url' => 'sede/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR SEDE',
                ],
            ],
        ],
        [
            'text' => 'Bloques',
            'icon' => 'fas fa-fw fa-th-large',
            'can' => 'VER BLOQUE',
            'submenu' => [
                [
                    'text' => 'Ver Bloques',
                    'url' => 'bloque',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER BLOQUE',
                ],
                [
                    'text' => 'Crear Bloque',
                    'url' => 'bloque/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR BLOQUE',
                ],
            ],
        ],
        [
            'text' => 'Pisos',
            'icon' => 'fas fa-fw fa-layer-group',
            'can' => 'VER PISO',
            'submenu' => [
                [
                    'text' => 'Ver Pisos',
                    'url' => 'piso',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER PISO',
                ],
                [
                    'text' => 'Crear Piso',
                    'url' => 'piso/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR PISO',
                ],
            ],
        ],
        [
            'text' => 'Ambientes',
            'icon' => 'fas fa-fw fa-door-open',
            'can' => 'VER AMBIENTE',
            'submenu' => [
                [
                    'text' => 'Ver Ambientes',
                    'url' => 'ambiente',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER AMBIENTE',
                ],
                [
                    'text' => 'Crear Ambiente',
                    'url' => 'ambiente/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR AMBIENTE',
                ],
            ],
        ],

        // ========================================
        // MÓDULO: GESTIÓN ACADÉMICA
        // ========================================
        [
            
        ],
        [
            'text' => 'Programas de Formación',
            'icon' => 'fas fa-fw fa-book',
            'can' => ['VER PROGRAMA DE CARACTERIZACION', 'programa.index'],
            'submenu' => [
                [
                    'text' => 'Ver Programas',
                    'url' => 'programa',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => ['VER PROGRAMA DE CARACTERIZACION', 'programa.index'],
                ],
                [
                    'text' => 'Crear Programa',
                    'url' => 'programa/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => ['VER PROGRAMA DE CARACTERIZACION', 'programa.create'],
                ],
            ],
        ],
        [
            'text' => 'Redes de Conocimiento',
            'url' => 'red-conocimiento',
            'icon' => 'fas fa-fw fa-network-wired',
            'can' => ['VER RED CONOCIMIENTO', 'CREAR RED CONOCIMIENTO'],
        ],
        [
            'text' => 'Competencias',
            'icon' => 'fas fa-fw fa-clipboard-list',
            'can' => ['VER COMPETENCIA', 'CREAR COMPETENCIA'],
            'submenu' => [
                [
                    'text' => 'Ver Competencias',
                    'url' => 'competencias',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER COMPETENCIA',
                ],
                [
                    'text' => 'Crear Competencia',
                    'url' => 'competencias/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR COMPETENCIA',
                ],
            ],
        ],
        [
            'text' => 'Resultados de Aprendizaje',
            'icon' => 'fas fa-fw fa-trophy',
            'can' => ['VER RESULTADO APRENDIZAJE', 'CREAR RESULTADO APRENDIZAJE'],
            'submenu' => [
                [
                    'text' => 'Ver Resultados',
                    'url' => 'resultados-aprendizaje',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER RESULTADO APRENDIZAJE',
                ],
                [
                    'text' => 'Crear Resultado',
                    'url' => 'resultados-aprendizaje/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR RESULTADO APRENDIZAJE',
                ],
            ],
        ],
        [
            'text' => 'Guías de Aprendizaje',
            'icon' => 'fas fa-fw fa-book-open',
            'can' => ['VER GUIA APRENDIZAJE', 'CREAR GUIA APRENDIZAJE'],
            'submenu' => [
                [
                    'text' => 'Ver Guías',
                    'url' => 'guias-aprendizaje',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER GUIA APRENDIZAJE',
                ],
                [
                    'text' => 'Crear Guía',
                    'url' => 'guias-aprendizaje/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR GUIA APRENDIZAJE',
                ],
            ],
        ],

        // ========================================
        // MÓDULO: FICHAS Y CARACTERIZACIÓN
        // ========================================
        [
            
        ],
        [
            'text' => 'Fichas de Caracterización',
            'icon' => 'fas fa-fw fa-file-alt',
            'can' => ['VER FICHA CARACTERIZACION', 'VER PROGRAMA DE CARACTERIZACION'],
            'submenu' => [
                [
                    'text' => 'Ver Fichas',
                    'url' => 'fichaCaracterizacion',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER FICHA CARACTERIZACION',
                ],
                [
                    'text' => 'Crear Ficha',
                    'url' => 'fichaCaracterizacion/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR FICHA CARACTERIZACION',
                ],
            ],
        ],
        [
            'text' => 'Jornadas de Formación',
            'icon' => 'fas fa-fw fa-calendar-alt',
            'can' => ['VER JORNADA', 'VER PROGRAMA DE CARACTERIZACION'],
            'submenu' => [
                [
                    'text' => 'Ver Jornadas',
                    'url' => 'jornada',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER JORNADA',
                ],
                [
                    'text' => 'Crear Jornada',
                    'url' => 'jornada/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR JORNADA',
                ],
            ],
        ],

        // ========================================
        // MÓDULO: PERSONAL
        // ========================================
        [
            
        ],
        [
            'text' => 'Instructores',
            'icon' => 'fas fa-fw fa-chalkboard-teacher',
            'can' => 'VER PROGRAMA DE CARACTERIZACION',
            'submenu' => [
                [
                    'text' => 'Ver Instructores',
                    'url' => 'instructor',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER PROGRAMA DE CARACTERIZACION',
                ],
                [
                    'text' => 'Crear Instructor',
                    'url' => 'instructor/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'VER PROGRAMA DE CARACTERIZACION',
                ],
                [
                    'text' => 'Importar CSV',
                    'url' => 'createImportarCSV',
                    'icon' => 'fas fa-fw fa-file-csv',
                    'can' => 'VER PROGRAMA DE CARACTERIZACION',
                ],
            ],
        ],
        [
            'text' => 'Aprendices',
            'icon' => 'fas fa-fw fa-user-graduate',
            'can' => 'VER APRENDIZ',
            'submenu' => [
                [
                    'text' => 'Ver Aprendices',
                    'url' => 'aprendices',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER APRENDIZ',
                ],
                [
                    'text' => 'Crear Aprendiz',
                    'url' => 'aprendices/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR APRENDIZ',
                ],
            ],
        ],

        // ========================================
        // MÓDULO: ASISTENCIA
        // ========================================
        [
            
        ],
        [
            'text' => 'Tomar Asistencia',
            'url' => 'asistencia',
            'icon' => 'fas fa-fw fa-check-square',
            'can' => 'TOMAR ASISTENCIA',
        ],
        [
            'text' => 'Consultar Asistencias',
            'icon' => 'fas fa-fw fa-clipboard-check',
            'can' => ['VER ASISTENCIA', 'VER PROGRAMA DE CARACTERIZACION'],
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
                    'can' => 'VER PROGRAMA DE CARACTERIZACION',
                ],
            ],
        ],

        // ========================================
        // MÓDULO: CARNETS
        // ========================================
        [
            
        ],
        [
            'text' => 'Gestión de Carnets',
            'icon' => 'fas fa-fw fa-id-card',
            'can' => ['VER PROGRAMA DE CARACTERIZACION', 'VER CREAR CARNET'],
            'submenu' => [
                [
                    'text' => 'Administrar Carnets',
                    'url' => 'administrar-carnet',
                    'icon' => 'fas fa-fw fa-tasks',
                    'can' => 'VER ADMINISTRAR CARNET',
                ],
                [
                    'text' => 'Crear Carnet',
                    'url' => 'carnet',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => ['VER PROGRAMA DE CARACTERIZACION', 'VER CREAR CARNET'],
                ],
            ],
        ],
        // Gestion de complementarios
        [
            'text' => 'Complementarios',
            'icon' => 'fa-solid fa-folder-open',
            'can' => 'VER PROGRAMA DE CARACTERIZACION',
            'submenu' => [
                [
                    'text' => 'Gestión complementarios',
                    'icon' => 'fas fa-graduation-cap',
                    'classes' => 'text-sm',
                    'submenu' => [
                        [
                            'text' => 'Ver complementarios',
                            'url' => 'gestion-programas-complementarios',
                            'icon' => 'fas fa-list',
                            'can' => 'VER PROGRAMA COMPLEMENTARIO',
                            'classes' => 'text-sm',
                        ],
                        [
                            'text' => 'Crear complementario',
                            'url' => 'complementarios-ofertados/create',
                            'icon' => 'fas fa-plus',
                            'can' => 'CREAR PROGRAMA COMPLEMENTARIO',
                            'classes' => 'text-sm',
                        ],
                    ],
                ],
                [
                    'text' => 'Gestion de aspirantes',
                    'url' => 'gestion-aspirantes',
                    'icon' => 'fas fa-users',
                    'can' => [
                        'VER PROGRAMA DE CARACTERIZACION',
                        'CREAR PROGRAMA DE CARACTERIZACION',
                        'EDITAR PROGRAMA DE CARACTERIZACION',
                        'ELIMINAR PROGRAMA DE CARACTERIZACION',
                    ],
                ],
                [
                    'text' => 'Estadisticas',
                    'url' => 'estadisticas',
                    'icon' => 'fas fa-chart-line',
                    'can' => 'VER ESTADISTICAS',
                ],
                [
                    'text' => 'Procesar documentos',
                    'url' => 'procesar-documentos',
                    'icon' => 'fas fa-file-signature',
                    'can' => [
                        'VER PROGRAMA DE CARACTERIZACION',
                        'CREAR PROGRAMA DE CARACTERIZACION',
                        'EDITAR PROGRAMA DE CARACTERIZACION',
                        'ELIMINAR PROGRAMA DE CARACTERIZACION',
                    ]
                ],
            ],
        ],

        // ========================================
        // MÓDULO: TALENTO HUMANO
        // ========================================
        [
            'header' => 'TALENTO HUMANO',
        ],
        [
            'text' => 'Talento Humano',
            'icon' => 'fas fa-fw fa-users',
            'can' => 'VER TALENTO HUMANO',
            'submenu' => [
                [
                    'text' => 'Ver Talento Humano',
                    'url' => 'talento-humano',
                    'icon' => 'fas fa-fw fa-list',
                    'can' => 'VER TALENTO HUMANO',
                ],
                [
                    'text' => 'Crear Talento Humano',
                    'url' => 'talento-humano/create',
                    'icon' => 'fas fa-fw fa-plus',
                    'can' => 'CREAR TALENTO HUMANO',
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
            'active' => false,
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
            'active' => false,
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
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
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

    'livewire' => false,

    'custom_css' => [
        'css/custom.css',
        'public/css/app.css',
    ],
];
