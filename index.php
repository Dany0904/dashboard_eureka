<?php

// Incluir las bibliotecas necesarias de Moodle
global $DB;
require_once(__DIR__ . '/../../config.php');
require_login();

// Verifica si el usuario está logueado y tiene el rol de administrador del sitio, manager o es manager de curso
$context_system = context_system::instance();

if (
    !isloggedin() || isguestuser() ||
    (!has_capability('moodle/site:config', $context_system) &&
        !has_capability('moodle/role:manager', $context_system) &&
        !has_capability('moodle/course:manageactivities', context_course::instance(SITEID)))
) {

    redirect('/', 'No tienes permiso para ver esta sección', 'error', 0);
}

//obtenemos el username del usuario logueado
global $USER;
global $CFG;
$user_activo = "";
if (isloggedin() && !isguestuser()) {
    $user_activo = $USER->username;
} else {
    echo "No hay usuario autenticado.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- PASAR PHP A JAVASCRIPT -->
    <script>
        var moodleWroot = "<?php echo $CFG->wwwroot; ?>"; // URL base de Moodle
    </script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Dashboard <sup>Moodle</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Inicio</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="./detalle_preturnos.php">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Reporte de preturnos</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="./detalle_quiz.php">
                    <i class="fas fa-fw fa-award"></i>
                    <span>Reporte de certificaciones</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="./detalle_horas.php">
                    <i class="fas fa-fw fa-clock"></i>
                    <span>Reporte por horas</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Opciones:</h6>
                        <a class="collapse-item" href="./detalle_usuarios_anio.php">Usuarios por Año</a>
                        <a class="collapse-item" href="./detalle_usuarios_activos.php">Usuarios Activos</a>
                        <a class="collapse-item" href="./detalle_vs.php">Finalizado / No Finalizado</a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages2"
                    aria-expanded="true" aria-controls="collapsePages2">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Cursos</span>
                </a>
                <div id="collapsePages2" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Opciones:</h6>
                        <a class="collapse-item" href="./detalle_cursos_anio.php">Cursos por Año</a>
                        <a class="collapse-item" href="./detalle_cursos_categorias.php">Cursos por Categorías</a>
                        <a class="collapse-item" href="./detalle_cursos_demandados.php">Cursos Demandados</a>
                        <a class="collapse-item" href="./detalle_cursos_promedio.php">Cursos (Promedios)</a>
                        <a class="collapse-item" href="./detalle_cursos_progreso.php">Cursos (Progreso %)</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="./detalle_calificaciones.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Reporte de Calificaciones</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="./detalle_progreso_by_user.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Reporte de Progreso (%)</span></a>
            </li>



            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>


                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($user_activo); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Salir
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Usuarios Activos -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card stat-card-primary shadow h-100">
                                <div class="card-body">
                                    <div class="col mr-2">
                                        <div class="text-xs text-uppercase mb-1">Usuarios</div>
                                        <div id="totalUsuarios" class="h5 mb-0">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Usuarios en Línea -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card stat-card-warning shadow h-100">
                                <div class="card-body">
                                    <div class="col mr-2">
                                        <div class="text-xs text-uppercase mb-1">Usuarios en Línea</div>
                                        <div id="totalUsuariosEnLinea" class="h5 mb-0">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cursos -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card stat-card-success shadow h-100">
                                <div class="card-body">
                                    <div class="col mr-2">
                                        <div class="text-xs text-uppercase mb-1">Cursos</div>
                                        <div id="totalCursos" class="h5 mb-0">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-book"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categorías -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card stat-card-info shadow h-100">
                                <div class="card-body">
                                    <div class="col mr-2">
                                        <div class="text-xs text-uppercase mb-1">Categorías</div>
                                        <div id="total_categorias" class="h5 mb-0">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>

                    <!-- Row Usuarios y cursos -->
                    <div class="row">

                        <!-- Usuarios Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold">Usuarios por Año</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="usuariosChart"></div>
                                </div>
                                <a href="./detalle_usuarios_anio.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>

                        <!-- Cursos Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Cursos por Año</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="cursosChart"></div>
                                </div>
                                <a href="./detalle_cursos_anio.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Row Cursos por Categorías y Cursos demandados -->
                    <div class="row">

                        <!-- Cursos por Categorías Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Cursos por Categorías</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="categoriasChart"></div>
                                </div>
                                <a href="./detalle_cursos_categorias.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>


                        <!-- Cursos Demandados Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Cursos Demandados</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="inscritosChart"></div>
                                </div>
                                <a href="./detalle_cursos_demandados.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>
                    </div>


                    <!-- Row Cursos con mejores promedios y usuarios activos-->
                    <div class="row">

                        <!-- Cursos con mejores promedios Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Cursos con mejores Promedios</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="promedioChart"></div>
                                </div>
                                <a href="./detalle_cursos_promedio.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>


                        <!-- usuarios activos en cursos Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Usuarios activos en Cursos</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="actividadcursesChart"></div>
                                </div>
                                <a href="./detalle_usuarios_activos.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>
                    </div>


                    <!-- Row Cursos con mayor progreso y Finalizado vs No finalizado-->
                    <div class="row">

                        <!-- Cursos con mejores promedios Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Cursos con mayor Progreso (%)</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="topprogress"></div>
                                </div>
                                <a href="./detalle_cursos_progreso.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>


                        <!-- Usuarios Activos en Cursos Chart -->
                        <div class="col-lg-6 col-12">
                            <div class="card custom-card mb-4">
                                <!-- Card Header -->
                                <div class="card-header custom-card-header">
                                    <h6 class="m-0 font-weight-bold">Finalizado vs No Finalizado</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div id="chartBarGroupVs" aria-label="Gráfico de Finalizado vs No Finalizado" role="img"></div>
                                </div>
                                <a href="./detalle_vs.php" class="btn btn-primary btn-icon-split" style="background-color:#2949c2 !important; border:none; width: fit-content; margin: 12px auto;">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </span>
                                    <span class="text">Ver detalle</span>
                                </a>
                            </div>
                        </div>


                    </div>


                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Dashboard Subitus 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal de Cierre de Sesión -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">¿Seguro que deseas Salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Haz clic en "Salir" si deseas regresar a la "Página Principal".</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <!-- Botón HTML con ID único -->
                    <a id="logoutButton" class="btn btn-primary" href="#">
                        Salir
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script src="vendor/apexcharts-bundle/dist/apexcharts.min.js"></script>


    <script src="js/functions.js"></script>


</body>

</html>
<style>
    /* 🔥 Tarjeta con borde animado */
    .custom-card {
        border-radius: 15px;
        box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        background: #fff;
        transition: all 0.4s ease-in-out;
        position: relative;
        border: 2px solid transparent;
    }

    /* 🔥 Efecto de borde neón al pasar el mouse */
    .custom-card:hover {
        transform: translateY(-6px);
        box-shadow: 0px 12px 30px rgba(0, 0, 0, 0.25);
        border: 2px solid #2949c2;
        /* Naranja */
    }

    /* 💎 Encabezado con degradado vibrante */
    .custom-card-header {
        background-color: #2949c2 !important;
        /* Gris */
        color: white;
        padding: 16px 22px;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        border-bottom: 3px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }

    /* ✨ Sombra en el texto del encabezado */
    .custom-card-header h6 {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* ✨ Efecto de iluminación al hacer hover */
    .custom-card:hover .custom-card-header {
        /* Naranja */
        border-bottom: 3px solid #2949c2;
    }

    /* 📊 Estilo del cuerpo de la tarjeta */
    .custom-card .card-body {
        padding: 25px;
        background: #f4f7fc;
        transition: all 0.3s ease-in-out;
    }

    /* 📊 Área de la gráfica con efecto brillante */
    .chart-area {
        padding: 15px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: inset 0px 3px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        transition: all 0.3s ease-in-out;
    }

    /* ✨ Transición en elementos internos */
    .custom-card .card-body * {
        transition: all 0.3s ease-in-out;
    }

    /* 🔥 Estilo base de las tarjetas */
    .stat-card {
        border-radius: 12px;
        box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        background: #fff;
        transition: all 0.4s ease-in-out;
        position: relative;
        padding: 20px;
        border-left: 4px solid transparent;
    }

    /* 🎨 Efecto de borde degradado dinámico */
    .stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, #2949c2 100%);
        /* Gris */
        transition: all 0.3s ease-in-out;
    }

    /* 🔥 Hover: Eleva la tarjeta y resalta el borde */
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0px 12px 30px rgba(0, 0, 0, 0.25);
    }

    /* ✨ Efecto de resplandor en el borde al hacer hover */
    .stat-card:hover::before {
        width: 8px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.2) 0%, #2949c2 100%);
        /* Naranja */
    }

    /* 📊 Estilo del contenido interno */
    .stat-card .card-body {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* 🎯 Títulos de las tarjetas */
    .stat-card .text-uppercase {
        font-size: 0.85rem;
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #54565b;
        /* Gris */
    }

    /* 📈 Número principal */
    .stat-card .h5 {
        font-size: 1.75rem;
        font-weight: bold;
        color: #2e2e2e;
    }

    /* ✨ Nuevo efecto en los íconos */
    .stat-card .col-auto i {
        font-size: 2.5rem;
        padding: 10px;
        border-radius: 10px;
        transition: box-shadow 0.3s ease-in-out;
    }

    /* ✨ Efecto de pulso más suave */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0px 0px 10px rgba(245, 130, 32, 0.3);
            /* Naranja */
        }

        100% {
            transform: scale(1.05);
            box-shadow: 0px 0px 20px rgba(245, 130, 32, 0.6);
            /* Naranja */
        }
    }

    /* 🟦 Colores vibrantes para cada tipo de tarjeta */
    .stat-card-primary {
        border-left-color: #2949c2;
        /* Gris */
    }

    .stat-card-warning {
        border-left-color: #2949c2;
        /* Naranja */
    }

    .stat-card-success {
        border-left-color: #2949c2;
    }

    .stat-card-info {
        border-left-color: #2949c2;
    }

    /* 💡 Hover cambia el color del borde */
    .stat-card-primary:hover {
        border-left-color: #3a3b3e;
        /* Gris más oscuro */
    }

    .stat-card-warning:hover {
        border-left-color: #3a3b3e;
        /* Naranja más oscuro */
    }

    .stat-card-success:hover {
        border-left-color: #3a3b3e;
    }

    .stat-card-info:hover {
        border-left-color: #3a3b3e;
    }

    /*COLOR DE BARRA*/
    #accordionSidebar {
        background-color: #2949c2 !important;
    }

    .sidebar-dark .nav-item.active .nav-link i {
        color: #fff !important;
    }

    .stat-card .col-auto i{
        color: #2949c2 !important;
    }
</style>