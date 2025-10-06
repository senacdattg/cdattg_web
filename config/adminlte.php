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
                    'url' => 'perfil',
                    'icon' => 'fas fa-user-circle text-info',
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
        // Home
        [
            'header' => 'Home',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        [
            'text' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],

        // Panel de control
        [
            'header' => 'Panel de control',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        // Configuración y Seguridad
        [
            'text' => 'Configuración y Seguridad',
            'icon' => 'fas fa-fw fa-cogs',
            'submenu' => [
                [
                    'text' => 'Parámetros',
                    'url' => 'parametro',
                    'icon' => 'fas fa-fw fa-sliders-h',
                    'can' => [
                        'CREAR PARAMETRO',
                        'EDITAR PARAMETRO',
                        'VER PARAMETRO',
                        'ELIMINAR PARAMETRO',
                    ]
                ],
                [
                    'text' => 'Temas',
                    'url' => 'tema',
                    'icon' => 'fas fa-fw fa-paint-brush',
                    'can' => [
                        'CREAR TEMA',
                        'EDITAR TEMA',
                        'VER TEMA',
                        'ELIMINAR TEMA',
                        'ELIMINAR PARAMETRO DE TEMA',
                    ]
                ],
                [
                    'text' => 'Asignar Permisos',
                    'url' => 'permiso',
                    'icon' => 'fas fa-fw fa-lock',
                    'can' => [
                        'ASIGNAR PERMISOS',
                    ]
                ],
            ],
        ],
        // Ubicación e Infraestructura
        [
            'text' => 'Ubicación e Infraestructura',
            'icon' => 'fas fa-fw fa-building',
            'submenu' => [
                [
                    'text' => 'Regionales',
                    'url' => 'regional',
                    'icon' => 'fas fa-fw fa-map-marker-alt',
                    'can' => [
                        'CREAR REGIONAL',
                        'EDITAR REGIONAL',
                        'VER REGIONAL',
                        'ELIMINAR REGIONAL',
                    ]
                ],
                [
                    'text' => 'Redes de Conocimiento',
                    'url' => 'red-conocimiento',
                    'icon' => 'fas fa-fw fa-network-wired',
                    'can' => [
                        'CREAR RED CONOCIMIENTO',
                        'EDITAR RED CONOCIMIENTO',
                        'VER RED CONOCIMIENTO',
                        'ELIMINAR RED CONOCIMIENTO',
                    ]
                ],
                [
                    'text' => 'Municipios',
                    'url' => 'municipio',
                    'icon' => 'fas fa-fw fa-map-marker-alt',
                    'can' => [
                        'CREAR MUNICIPIO',
                        'EDITAR MUNICIPIO',
                        'VER MUNICIPIO',
                        'ELIMINAR MUNICIPIO',
                    ]
                ],
                [
                    'text' => 'Centros de Formación',
                    'url' => 'centros',
                    'icon' => 'fas fa-fw fa-school',
                    'can' => [
                        'CREAR CENTRO DE FORMACION',
                        'EDITAR CENTRO DE FORMACION',
                        'VER CENTRO DE FORMACION',
                        'ELIMINAR CENTRO DE FORMACION',
                    ]
                ],
                [
                    'text' => 'Sedes',
                    'url' => 'sede',
                    'icon' => 'fas fa-fw fa-hospital',
                    'can' => [
                        'CREAR SEDE',
                        'EDITAR SEDE',
                        'VER SEDE',
                        'ELIMINAR SEDE',
                    ]
                ],
                [
                    'text' => 'Bloques',
                    'url' => 'bloque',
                    'icon' => 'fas fa-fw fa-th-large',
                    'can' => [
                        'CREAR BLOQUE',
                        'EDITAR BLOQUE',
                        'VER BLOQUE',
                        'ELIMINAR BLOQUE',
                    ],
                ],
                [
                    'text' => 'Pisos',
                    'url' => 'piso',
                    'icon' => 'fas fa-fw fa-layer-group',
                    'can' => [
                        'CREAR PISO',
                        'EDITAR PISO',
                        'VER PISO',
                        'ELIMINAR PISO',
                    ],
                ],
                [
                    'text' => 'Ambientes',
                    'url' => 'ambiente',
                    'icon' => 'fas fa-fw fa-door-open',
                    'can' => [
                        'CREAR AMBIENTE',
                        'EDITAR AMBIENTE',
                        'VER AMBIENTE',
                        'ELIMINAR AMBIENTE',
                    ],
                ],
            ],
        ],
        // Gestión de Personal y Roles
        [
            'text' => 'Gestión de Personal y Roles',
            'icon' => 'fas fa-fw fa-users',
            'submenu' => [
                [
                    'text' => 'Instructores',
                    'icon' => 'fas fa-fw fa-chalkboard-teacher',
                    'can' => [
                        'VER INSTRUCTOR',
                        'CREAR INSTRUCTOR',
                        'EDITAR INSTRUCTOR',
                        'ELIMINAR INSTRUCTOR',
                        'GESTIONAR ESPECIALIDADES INSTRUCTOR',
                        'VER FICHAS ASIGNADAS',
                        'CAMBIAR ESTADO INSTRUCTOR',
                    ],
                    'submenu' => [
                        [
                            'text' => 'Dashboard',
                            'route' => 'instructor.dashboard',
                            'icon' => 'fas fa-fw fa-tachometer-alt',
                            'can' => 'VER INSTRUCTOR',
                        ],
                        [
                            'text' => 'Lista de Instructores',
                            'url' => 'instructor',
                            'icon' => 'fas fa-fw fa-list',
                            'can' => 'VER INSTRUCTOR',
                        ],
                        [
                            'text' => 'Crear Instructor',
                            'url' => 'instructor/create',
                            'icon' => 'fas fa-fw fa-plus',
                            'can' => 'CREAR INSTRUCTOR',
                        ],
                    ],
                ],
                [
                    'text' => 'Vigilantes',
                    'url' => 'vigilante',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'can' => [
                        'VER VIGILANTE',
                        'CREAR VIGILANTE',
                        'EDITAR VIGILANTE',
                        'ELIMINAR VIGILANTE',
                    ],
                ],
                [
                    'text' => 'Importar CSV',
                    'url' => 'createImportarCSV',
                    'icon' => 'fas fa-fw fa-file-upload',
                    'can' => [
                        'IMPORTAR CSV' //Este permiso no esta todavia en los seeders
                    ],
                ],
            ],
        ],
        // Personas
        [
            'text' => 'Personas',
            'icon' => 'fas fa-fw fa-user',
            'url' => 'personas',
            'can' => [
                'VER PERSONA',
                'CREAR PERSONA',
                'EDITAR PERSONA',
                'ELIMINAR PERSONA',
            ],
        ],
        //Tomat Asistencias
        [
            'text' => 'Tomar Asistencia',
            'url' => 'asistencia',
            'icon' => 'fas fa-fw fa-check-square',
            'can' => [
                'TOMAR ASISTENCIA',
            ],
        ],
        // Gestión Académica
        [
            'text' => 'Gestión Académica',
            'icon' => 'fas fa-fw fa-graduation-cap',
            'submenu' => [
                [
                    'text' => 'Programas de Formación',
                    'url' => 'programa',
                    'icon' => 'fas fa-fw fa-graduation-cap',
                    'can' => [
                        'programa.index',
                        'programa.show',
                        'programa.create',
                        'programa.edit',
                        'programa.delete',
                        'programa.search',
                    ],
                ],
                [
                    'text' => 'Fichas de Caracterización',
                    'url' => 'fichaCaracterizacion',
                    'icon' => 'fas fa-fw fa-file-alt',
                    'can' => [
                        'VER FICHA CARACTERIZACION',
                        'CREAR FICHA CARACTERIZACION',
                        'EDITAR FICHA CARACTERIZACION',
                        'ELIMINAR FICHA CARACTERIZACION',
                        'GESTIONAR INSTRUCTORES FICHA',
                        'GESTIONAR DIAS FICHA',
                        'CAMBIAR ESTADO FICHA',
                    ],
                ],
                [
                    'text' => 'Jornadas de Formación',
                    'url' => 'jornada',
                    'icon' => 'fas fa-fw fa-calendar',
                    'can' => [
                        'VER JORNADA',
                        'CREAR JORNADA',
                        'EDITAR JORNADA',
                        'ELIMINAR JORNADA',
                    ],
                ],
            ],
        ],
        // Operaciones Académicas y Administrativas
        [
            'text' => 'Operaciones Académicas y Administrativas',
            'icon' => 'fas fa-fw fa-book',
            'submenu' => [
                [
                    'text' => 'Aprendices',
                    'icon' => 'fas fa-fw fa-user-graduate',
                    'can' => [
                        'VER APRENDIZ',
                    ],
                    'submenu' => [
                        [
                            'text' => 'Todos los Aprendices',
                            'url' => 'aprendices',
                            'icon' => 'fas fa-fw fa-users',
                            'can' => [
                                'VER APRENDIZ',
                            ],
                        ],
                        [
                            'text' => 'Crear Aprendiz',
                            'url' => 'aprendices/create',
                            'icon' => 'fas fa-fw fa-user-plus',
                            'can' => [
                                'CREAR APRENDIZ',
                            ],
                        ],
                    ],
                ],
                [
                    'text' => 'Administrar Asistencias',
                    'url' => 'asistencia',
                    'icon' => 'fas fa-fw fa-check-square',
                    'can' => [
                        'VER ASISTENCIA',
                        'CREAR ASISTENCIA',
                        'EDITAR ASISTENCIA',
                        'ELIMINAR ASISTENCIA',
                    ]
                ],
            ],
        ],
        // Consultas y Gestión de Carnet
        [
            'text' => 'Consultas y Gestión de Carnet',
            'icon' => 'fas fa-fw fa-search',
            'submenu' => [
                [
                    'text' => 'Consultas',
                    'url' => 'consulta',
                    'icon' => 'fas fa-fw fa-search',
                    'can' => [
                        'VER CONSULTA',
                        'CREAR CONSULTA',
                        'EDITAR CONSULTA',
                        'ELIMINAR CONSULTA',
                    ],
                ],
                [
                    'text' => 'Carnet',
                    'icon' => 'fas fa-fw fa-id-card',
                    'submenu' => [
                        [
                            'text' => 'Administrar Carnet',
                            'url' => 'administrar-carnet',
                            'icon' => 'fas fa-fw fa-tasks',
                            'can' => [
                                'VER ADMINISTRAR CARNET',
                                'CREAR ADMINISTRAR CARNET',
                                'EDITAR ADMINISTRAR CARNET',
                                'ELIMINAR ADMINISTRAR CARNET',
                            ],
                        ],
                        [
                            'text' => 'Crear Carnet',
                            'url' => 'carnet',
                            'icon' => 'fas fa-fw fa-plus-circle',
                            'can' => [
                                'VER CREAR CARNET',
                                'CREAR CREAR CARNET',
                                'EDITAR CREAR CARNET',
                                'ELIMINAR CREAR CARNET',
                            ],
                        ],
                    ],
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
