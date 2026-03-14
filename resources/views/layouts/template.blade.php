<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title> @yield('title') </title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/LogoDr.ico') }}" />
    <link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/js/loader.js') }}"></script>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('plugins/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/dashboard/dash_2.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('fonts/line-awesome/css/line-awesome.min.css') }}">


    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr.css') }}">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/elements/infobox.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/switches.css') }}">
    <link href="{{ asset('plugins/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/flatpickr/material_red.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('assets/css/elements/color_library.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/users/user-profile.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <livewire:styles />
    @livewireStyles
</head>

<body class="alt-menu sidebar-noneoverflow">
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->
    <header>
        <nav class="header navbar  navbar-expand-lg navbar-dark " style="background-color:  #e05454  ;">
            <a class="navbar-brand ml-2 mr-0 mr-md-3 text-white" href="{{ url('home') }}"><b>ExisteSys</b></a>

            <button class="navbar-toggler" style="color: #fdfefe;" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon text-light"></span>
            </button>

            <div class="collapse navbar-collapse ml-5" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    @hasanyrole('gerente')
                        <li class="nav-item dropdown" style="font-size: .75rem;">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <b>Maestros</b>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="font-size: .70rem;">
                                <a class="dropdown-item" href="{{ url('products') }}"
                                    style="font-size: .70rem;">Productos y Servicios</a>
                                <div class="dropdown-divider" style="font-size: .70rem;"></div>
                                <a class="dropdown-item" href="{{ url('classes') }}" style="font-size: .70rem;">Clases
                                    Inventario</a>
                                <a class="dropdown-item" href="{{ url('lines') }}" style="font-size: .70rem;">Líneas
                                    Inventario</a>
                                <div class="dropdown-divider" style="font-size: .70rem;"></div>
                                <a class="dropdown-item" href="{{ url('warehouses') }}"
                                    style="font-size: .70rem;">Bodegas</a>
                                <a class="dropdown-item" href="{{ url('destinos') }}"
                                    style="font-size: .70rem;">Destinos (consumos)</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('proveedores') }}"
                                    style="font-size: .70rem;">Proveedores</a>
                            </div>
                        </li>
                    @endhasanyrole
                    @hasanyrole('bodega')
                        <li class="nav-item dropdown" style="font-size: .75rem;">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Maestros
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="font-size: .70rem;">
                                <a class="dropdown-item" href="{{ url('products') }}"
                                    style="font-size: .70rem;">Productos y Servicios</a>
                                <div class="dropdown-divider" style="font-size: .70rem;"></div>
                                <a class="dropdown-item" href="{{ url('proveedores') }}"
                                    style="font-size: .70rem;">Proveedores</a>
                            </div>
                        </li>
                    @endhasanyrole


                    @hasanyrole('gerente|bodega')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Inventarios
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('compras') }}" style="font-size: .75rem;">Compra
                                    de Producto</a>
                                <a class="dropdown-item" href="{{ url('gastos') }}" style="font-size: .75rem;">Salida
                                    Consumo/Gasto</a>
                                <a class="dropdown-item" href="{{ url('ajustes') }}"
                                    style="font-size: .75rem;">Entrada-Salida x Ajuste</a>
                                <div class="dropdown-divider" style="font-size: .75rem;"></div>
                                <a class="dropdown-item" href="{{ url('traslados') }}"
                                    style="font-size: .75rem;">Traslado de Producto</a>
                                <div class="dropdown-divider" style="font-size: .75rem;"></div>
                                <a class="dropdown-item" href="{{ url('movimientos') }}"
                                    style="font-size: .75rem;">Visor Movimientos</a>
                                <div class="dropdown-divider" style="font-size: .75rem;"></div>
                                <div class="dropdown-divider" style="font-size: .75rem;"></div>
                                <a class="dropdown-item" href="{{ url('fisico') }}" style="font-size: .75rem;">
                                    <FONT COLOR ="PURPLE">Conteo Fisico</FONT>
                                </a>
                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('gerente')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Solicitudes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('solicitastock') }}">Stock a Bodega</a>
                                <div class="dropdown-divider" style="font-size: .75rem;"></div>
                                <a class="dropdown-item" href="{{ url('solicitagasto') }}">Para Gasto/Consumo</a>
                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('bodega')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Solicitudes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('solicitastock') }}">Stock a Bodega</a>
                                <div class="dropdown-divider" style="font-size: .75rem;"></div>
                                <a class="dropdown-item" href="{{ url('solicitagasto') }}">Para Gasto/Consumo</a>
                            </div>
                        </li>
                    @endhasanyrole


                    @hasanyrole('solicitante')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Solicitudes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('solicitagasto') }}">Para Gasto/Consumo</a>

                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('gerente')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Autorizaciones
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('autorizasol') }}">Autorizar Solicitudes</a>
                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('gerente|bodega')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Despacho
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('visordespacho') }}">Despachar Solicitudes</a>
                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('gerente|bodega')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Reportes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('controlreportes') }}">Reportes de control</a>
                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('gerente')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Solicitudes (Admin)
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="font-size: .70rem;">
                                <a class="dropdown-item" href="{{ route('solicitudes.admin') }}"
                                    style="font-size: .70rem;">Ver todas las solicitudes</a>
                                <div class="dropdown-divider" style="font-size: .70rem;"></div>
                            </div>
                        </li>
                    @endhasanyrole

                    @hasanyrole('gerente|bodega|solicitante')
                        <li class="nav-item dropdown">
                            <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                                id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Mis Solicitudes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="font-size: .70rem;">
                                <a class="dropdown-item" href="{{ route('mis-solicitudes') }}"
                                    style="font-size: .70rem;">Ver mis solicitudes</a>
                                <div class="dropdown-divider" style="font-size: .70rem;"></div>
                            </div>
                        </li>
                    @endhasanyrole



                    <li class="nav-item dropdown">
                        <a style="font-size: .75rem;" class="nav-link dropdown-toggle text-white" href="#"
                            id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            Salir
                        </a>
                        <div class="dropdown-menu  mr-auto" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ url('clave') }}">Cambiar Clave</a>
                            <div class="dropdown-divider"></div>
                            <form id="form1" class="form-horizontal" method="POST"
                                action="{{ route('logout') }}">
                                {{ csrf_field() }}
                            </form>
                            <a class="dropdown-item" onclick="document.getElementById('form1').submit();"
                                href="javascript:void(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Cerrar Sesión</a>
                        </div>
                    </li>
                </ul>

            </div>
            <a class="navbar-brand ml-3 mr-3 mr-md-3 text-light nav-item" href="#">
                <span style="font-size: .70rem;"
                    class="text-center"><small>User:{{ Auth::user()->name }}</small></span></a>
        </nav>


    </header>


    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN CONTENT PART  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                @yield('content')

            </div>
            <div class="ml-3 mr-3">
                @include('footer.footer')
            </div>

        </div>
        <!--  END CONTENT PART  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('plugins/notification/snackbar/snackbar.min.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>

    <script src="{{ asset('plugins/flatpickr/flatpickr_es.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            App.init();
            //OLD WAY
            /*
            $(".flatpickr").flatpickr({
                enableTime: false,
                dateFormat: "d-m-Y",
                'locale': 'es'
            });
            */
        });

        $(document).ready(function() {
            $("input, select").change(function() {
                concambios = true;
            });
        });


        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });

        //NEW WAY
        $(document).on("focus", ".flatpickr", function() {
            $(this).flatpickr({
                enableTime: true,
                dateFormat: "d-m-Y",
                'locale': 'es'
            });

        })
    </script>




    @yield('scripts')

    @livewireScripts


    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>

    <script src="{{ asset('js/toast.js') }}"></script>


    <script>
        //escuchamos eventos 200ok
        window.livewire.on('msgok', msgText => {
            toastr.success(msgText, "Info");
        })
        //escuchamos eventos de error
        window.livewire.on('msg-error', msgText => {
            toastr.error(msgText.toUpperCase(), "Alerta!");
        })
    </script>

</body>

</html>
