<?php


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    date_default_timezone_set('America/Monterrey');
    
    require_once("controller/ClassOps.php");
    require_once(__DIR__ . '/../config/ContratistaContexto.php');

    session_start();
    $Contratista = $_SESSION['Contratista'];
    requerirSesionAlmacen(true);
    $idContratistas = obtenerIdContratistaContexto();
    
    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';
    $mes_anterior = date('Y-m-d', strtotime('-1 month'));
    
?>
<!DOCTYPE html>
<html lang="es">    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CONTRATISTAS - ALMACEN</title>

    <!-- CARGA DE ICONOS Y FUENTES -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">

    <!-- ESTILOS BASE DE LA PLANTILLA -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y RESPONSIVIDAD -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

    <style>
        /* TARJETA CONTRATISTA */
        .cont-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 4px 16px rgba(0,0,0,.07);
            transition: all .3s ease;
            cursor: pointer;
        }
        /* HOVER DE LA TARJETA */
        .cont-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0,0,0,.13);
            border-color: #003d99;
        }
        /* TARJETA SELECCIONADA */
        .cont-card.selected {
            border: 2px solid #003d99;
            background: #eff6fc;
        }
        /* AVATAR DEL CONTRATISTA */
        .cont-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007BFF, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        /* NOMBRE DEL CONTRATISTA */
        .cont-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #3a3b45;
        }
        /* ---- PANEL PRINCIPAL ---- */
        .panel-folios {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 4px 16px rgba(0,0,0,.07);
        }
        .panel-folios .panel-header {
            background: linear-gradient(90deg,#f8f9fc,#fff);
            border-bottom: 1px solid #e3e6f0;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.5rem;
        }

        /* ---- PLACEHOLDER ---- */
        .placeholder-tac {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #b7bac3;
        }
        .placeholder-tac i { font-size: 4rem; margin-bottom: 1rem; }
        .placeholder-tac p  { font-size: 1rem; }

        /* ---- SPINNER ---- */
        .loading-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.7);
            z-index: 10;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        .modal-xxl {
            max-width: 85vw !important;
        }

        /* ESTILO PARA LAS CELDAS NUMÉRICAS */
        #tac_series_tabla td.dt-type-numeric,
        #tac_series_tabla th.dt-type-numeric {
            text-align: center !important;
        }

        .modal-header-gradient {
             background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
        }

        @media (max-width: 768px) {
            
            h1{
                font-size: 15px;
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>

</head>

<body id="page-top">

    <!-- CONTENEDOR PRINCIPAL DE LA PÁGINA -->
    <div id="wrapper">

        <!-- SIDEBAR IMPORTADA DESDE COMPONENTES -->
        <?php include_once(__DIR__ . '/../components/SidebarAlmacen.php'); ?>

        <!-- CONTENEDOR DEL ÁREA DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- CONTENIDO PRINCIPAL -->
            <div id="content">

                <!-- TOPBAR CON FILTROS Y ACCIONES GLOBALES -->
                <?php
                    $defaultFecha1 = $_POST['Fecha1'] ?? date('Y-m-d', strtotime('-3 month'));
                    $defaultFecha2 = $_POST['Fecha2'] ?? date('Y-m-d');
                    $topbar_onchange = 'CargarFolios';
                    include_once(__DIR__ . '/../components/TopbarContratistas.php');
                ?>

                <!-- INICIO DEL CONTENIDO DE LA PÁGINA -->
                <div class="container-fluid">

                    <!-- TITULO DEL MODULO -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h2 class="h3 mb-0 text-gray-800">
                            Navegacion de Numeros de Serie <i class="fa-brands fa-tailwind-css"></i>
                            <button type="button" class="btn btn-link btn-sm p-0 ml-1 text-info" data-toggle="modal" data-target="#modalLeyendaNavegacion" aria-label="Ver significado de los colores" title="Significado de los colores">
                                <i class="fas fa-question-circle" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm ml-2" id="btn-vista-masiva" title="Consultar todos los folios del periodo">
                                <i class="fas fa-layer-group mr-1"></i> Vista masiva
                            </button>
                        </h2>
                        <?php $selectorCallback = 'CargarFolios'; include_once(__DIR__ . '/../components/SelectorContratista.php'); ?>
                    </div>

                    <!-- CONTENEDOR PRINCIPAL -->
                    <div class="row">

                        <!-- COLUMNA IZQUIERDA: LISTA DE FOLIOS ASIGNADOS -->
                        <div class="col-md-3 col-lg-2 mb-4" id="columna-folios">

                            <div class="panel-folios h-100">

                                <div class="panel-header d-flex align-items-center justify-content-between">
                                    <!-- TITULO DE LA COLUMNA -->
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-users mr-1"></i> Vales Entregados
                                    </h6>
                                    <!-- CONTADOR DE FOLIOS -->
                                    <span id="badge-total-cont" class="badge badge-primary badge-pill">0</span>
                                </div>

                                <!-- BUSCADOR LOCAL -->
                                <div class="p-2 border-bottom">
                                    <input type="text" id="buscadorFolio" class="form-control form-control-sm"
                                        placeholder="Buscar folio...">
                                </div>

                                <!-- CONTENEDOR DE LAS TARJETAS DE FOLIOS -->
                                <div id="lista-folios" class="p-2" style="max-height:65vh; overflow-y:auto;">
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p class="mt-2">Cargando...</p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- COLUMNA DERECHA: FOLIOS DEL CONTRATISTA -->
                        <div class="col-md-9 col-lg-10 mb-4" id="columna-contenido-tabla">
                            <div class="panel-folios position-relative">
                                <div class="panel-header d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-success" id="titulo-panel-folios">
                                        <i class="fas fa-file-alt mr-1"></i> Selecciona un vale
                                    </h6>
                                </div>

                                <!-- SPINNER OVERLAY -->
                                <div class="loading-overlay" id="spinnerFolios">
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p class="mt-2">Cargando...</p>
                                    </div>
                                </div>

                                <div id="contenido-folios" class="p-3">
                                    <div class="placeholder-tac">
                                        <i class="fas fa-hand-point-left"></i>
                                        <p>Selecciona un vale para ver sus Numeros de serie asignadas</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- FIN DEL CONTENIDO -->

            </div>
            <!-- FIN DEL CONTENIDO PRINCIPAL -->

            <!-- PIE DE PÁGINA DEL PORTAL -->
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>

        </div>
        <!-- FIN DEL CONTENEDOR DEL CONTENIDO -->

    </div>
    <!-- FIN DEL CONTENEDOR PRINCIPAL -->

    <!-- BOTÓN PARA VOLVER AL INICIO -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- MODAL DE CIERRE DE SESION -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    <div class="modal fade" id="modalLeyendaNavegacion" tabindex="-1" role="dialog" aria-labelledby="modalLeyendaNavegacionTitulo" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white border-0">
                    <h5 class="modal-title" id="modalLeyendaNavegacionTitulo">Significado de los colores</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p><span class="badge badge-info mr-2">Azul</span> La serie esta navegando correctamente.</p>
                    <p><span class="badge mr-2" style="background-color:#17365d;color:#fff;">Azul oscuro</span> Navegando con observacion: se recupero el numero de serie o se valido una posible captura incorrecta en Coordiapp.</p>
                    <p><span class="badge badge-dark mr-2">Negro</span> Perdida de navegacion: el ultimo registro ECI tiene DID_ACTIVO=0 y el anterior tenia DID_ACTIVO=1.</p>
                    <p><span class="badge badge-danger mr-2">Rojo</span> Tiene una liquidacion de mas de 14 dias y no aparece Navegando. Requiere seguimiento.</p>
                    <p><span class="badge badge-secondary mr-2">Gris</span> Existe Navegacion, pero algun telefono de TAC o Coordiapp no coincide.</p>
                    <p><span class="badge mr-2" style="background-color:#fd7e14;color:#212529;">Naranja</span> Sigue pendiente la validacion de los 3 dias y ya pasaron mas de 3 semanas desde la liquidacion.</p>
                    <p><span class="badge badge-warning mr-2">Amarillo</span> Los telefonos coinciden, pero la Navegacion aun no cumple 3 dias desde la liquidacion.</p>
                    <p><span class="badge badge-success mr-2" style="background-color:#28a745;">Verde</span> Hay TAC o Coordiapp reciente, pero aun no aparece Navegacion.</p>
                    <p><span class="badge badge-light border mr-2">Blanco</span> No hay Liquidacion para clasificar la serie.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>

    <script>

        var FolioActivo = null;
        var FoliosData = [];
        var FiltroFolio = '';
        var modoMasivo = false;

        $(document).ready(function(){
            CargarFolios();
        });


        // CARGAR LISTA DE FOLIOS
        function CargarFolios() {
            if (modoMasivo) {
                cargarVistaMasiva();
                return;
            }

            // OBTENER LAS FECHAS DE LOS FILTROS
            var fecha_inicio = $('#Fecha1').val();
            var fecha_fin    = $('#Fecha2').val();

            // MOSTRAR UN SPINNER MIENTRAS SE CARGAN LOS FOLIOS
            $('#lista-folios').html(
                '<div class="text-center text-muted py-5">' +
                '<i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Cargando...</p></div>'
            );
            // INICIALIZAR CONTADOR DE FOLIOS EN 0
            $('#badge-total-cont').text(0);
            // REALIZAR LA PETICIÓN AJAX PARA OBTENER LOS FOLIOS DISPONIBLES
            $.ajax({
                url: 'modal/Navegacion_Folios.php',
                type: 'GET',
                dataType: 'json',
                cache: false,
                data: { fecha_inicio, fecha_fin },
                success: function (data) {
                    FoliosData = data;
                    // ACTUALIZAR CONTADOR DE FOLIOS
                    $('#badge-total-cont').text(data.length);
                    // RENDERIZAR LAS TARJETAS DE FOLIOS
                    renderizarTarjetas(data);

                    // Si había uno activo, mantenerlo seleccionado y recargar sus folios
                    if (FolioActivo) {
                        var sigue = data.find(function(f){ return String(f.Folio_Salida_ST) === String(FolioActivo.id); });
                        if (sigue) {
                            seleccionarFolio(FolioActivo.id, FolioActivo.nombre);
                        } else {
                            FolioActivo = null;
                            $('#contenido-folios').html(
                                '<div class="placeholder-tac">' +
                                '<i class="fas fa-hand-point-left"></i>' +
                                '<p>Selecciona un vale para ver los numeros de serie</p></div>'
                            );
                            $('#titulo-panel-folios').html('<i class="fas fa-file-alt mr-1"></i> Selecciona un vale');
                        }
                    }
                },
                error: function () {
                    $('#lista-folios').html(
                        '<div class="alert alert-danger m-2">Error al cargar los folios</div>'
                    );
                }
            });
        }



        //  RENDERIZAR TARJETAS DE FOLIOS
        function renderizarTarjetas(lista) {
            var filtro = String(FiltroFolio || '').toLowerCase().trim();
            var listaFiltrada = lista.filter(function (folio) {
                return String(folio.Folio_Salida_ST || '').toLowerCase().indexOf(filtro) !== -1;
            });

            if (listaFiltrada.length === 0) {
                $('#lista-folios').html(
                    '<div class="text-center text-muted py-5">' +
                    (filtro ? 'No se encontraron folios' : 'No hay vales en el periodo') +
                    '</div>'
                );
                return;
            }

            var html = '';
            listaFiltrada.forEach(function (folio) {
                var numeroFolio = String(folio.Folio_Salida_ST || '');
                var cantidadSeries = Number(folio.CantidadSeries || 0);
                var isActive = (FolioActivo && String(FolioActivo.id) === numeroFolio) ? 'selected' : '';
                html +=
                    '<div class="cont-card p-3 mb-2 d-flex align-items-center ' + isActive + '" ' +
                    '     data-folio="' + escapeHtml(numeroFolio) + '" ' +
                    '     onclick="seleccionarFolio(\'' + escapeJs(numeroFolio) + '\')">' +
                    '  <div class="cont-avatar mr-3"><i class="fas fa-file-alt"></i></div>' +
                    '  <div>' +
                    '    <div class="cont-name">Folio ' + escapeHtml(numeroFolio) + '</div>' +
                    '    <small class="text-muted">' + cantidadSeries + ' numero' + (cantidadSeries === 1 ? '' : 's') + ' de serie</small>' +
                    '  </div>' +
                    '</div>';
            });

            $('#lista-folios').html(html);
        }

        function filtrarTarjetas(valor) {
            FiltroFolio = String(valor || '');
            renderizarTarjetas(FoliosData);
        }

        // FILTRAR FOLIOS EN TIEMPO REAL, INCLUSO CUANDO LAS TARJETAS SE RECARGAN POR AJAX.
        $(document).on('input', '#buscadorFolio', function () {
            filtrarTarjetas(this.value);
        });

        function cargarVistaMasiva() {
            modoMasivo = true;
            FolioActivo = null;
            var fecha_inicio = $('#Fecha1').val();
            var fecha_fin = $('#Fecha2').val();

            $('#columna-folios').hide();
            $('#columna-contenido-tabla').removeClass('col-md-9 col-lg-10').addClass('col-12');
            $('#btn-vista-masiva').html('<i class="fas fa-arrow-left mr-1"></i> Regresar a folios');
            $('#btn-vista-masiva').attr('title', 'Regresar a la búsqueda por folio');
            $('#titulo-panel-folios').html('<span><i class="fas fa-layer-group mr-1"></i> Navegacion masiva</span>');
            $('#spinnerFolios').css('display', 'flex');
            $('#contenido-folios').css('opacity', '0.4');

            $.ajax({
                url: 'modal/Navegacion_Masiva.php',
                type: 'POST',
                data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
                success: function (html) {
                    $('#contenido-folios').html(html).css('opacity', '1');
                    $('#spinnerFolios').hide();
                    $('.cont-card').removeClass('selected');
                },
                error: function () {
                    $('#contenido-folios').html(
                        '<div class="alert alert-danger m-3">Error al cargar la vista masiva</div>'
                    ).css('opacity', '1');
                    $('#spinnerFolios').hide();
                }
            });
        }

        function regresarABusquedaFolios() {
            modoMasivo = false;
            FolioActivo = null;
            $('#columna-contenido-tabla').removeClass('col-12').addClass('col-md-9 col-lg-10');
            $('#columna-folios').show();
            $('#btn-vista-masiva').html('<i class="fas fa-layer-group mr-1"></i> Vista masiva');
            $('#btn-vista-masiva').attr('title', 'Consultar todos los folios del periodo');
            $('#titulo-panel-folios').html('<i class="fas fa-file-alt mr-1"></i> Selecciona un vale');
            $('#contenido-folios').html(
                '<div class="placeholder-tac">' +
                '<i class="fas fa-hand-point-left"></i>' +
                '<p>Selecciona un vale para ver sus numeros de serie asignadas</p></div>'
            );
        }

        $(document).on('click', '#btn-vista-masiva', function () {
            if (modoMasivo) {
                regresarABusquedaFolios();
            } else {
                cargarVistaMasiva();
            }
        });

        // SELECCIONAR FOLIO
        function seleccionarFolio(id) {
            var folio = String(id);
            var folioSeleccionado = FoliosData.find(function (item) {
                return String(item.Folio_Salida_ST) === folio;
            }) || {};
            var fechaFolio = folioSeleccionado.Fecha_Salida_ST || 'Sin fecha';
            FolioActivo = { id: folio, nombre: 'Folio ' + folio };

            // MARCAR LA TARJETA ACTIVA
            $('.cont-card').removeClass('selected');
            $('.cont-card[data-folio="' + escapeHtml(folio) + '"]').addClass('selected');

            $('#titulo-panel-folios').html(
                '<span><i class="fas fa-file-alt mr-1"></i> Folio ' + escapeHtml(folio) + '</span>' +
                '<span class="fecha-folio">Fecha de asignacion: ' + escapeHtml(fechaFolio) + '</span>'
            );

            $('#spinnerFolios').css('display', 'flex');
            $('#contenido-folios').css('opacity', '0.4');

            $.ajax({
                url: 'modal/Navegacion_Tabla.php',
                type: 'POST',
                data: { folio: folio },
                success: function (html) {
                    $('#contenido-folios').html(html).css('opacity', '1');
                    $('#spinnerFolios').hide();
                },
                error: function () {
                    $('#contenido-folios').html(
                        '<div class="alert alert-danger m-3">Error al cargar el detalle del folio</div>'
                    ).css('opacity', '1');
                    $('#spinnerFolios').hide();
                }
            });
        }

        function escapeHtml(valor) {
            return $('<div>').text(valor).html();
        }

        function escapeJs(valor) {
            return String(valor).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        }
    </script>

</body>
</html>

