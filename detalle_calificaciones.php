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

    <title>Dashboard | Cursos</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="vendor/datatables/buttons.bootstrap4.min.css">

    <!-- stylo de botones -->
    <link href="css/boton_datatables.css" rel="stylesheet">

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
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary" style="color: #2949c2 !important;">Calificaciones</h6>
                        </div>
                        <div class="card-body">
                            <section class="section_general">
                                <div class="filters">
                                    <div class="filter-group date-filters">
                                        <div class="sub-filter-group">
                                            <label>
                                                Curso
                                                <select id="cursoSelectCal" class="form-control">
                                                    <option value="">Todos</option>
                                                </select>
                                            </label>

                                             <label>
                                                Estudiante
                                                <select id="studentSelectCal" class="form-control">
                                                    <option value="">Todos</option>
                                                </select>
                                            </label>

                                           <!--  <label>
                                                Sección
                                                <select id="sectionSelectCal" class="form-control" disabled>
                                                    <option value="">Seleccione un curso</option>
                                                </select>
                                            </label> -->
                                        </div>

                                       

                                       <!--  <div class="sub-filter-group">
                                            <label>
                                                Fecha inicio
                                                <input type="date" id="dateStartCal" class="form-control">
                                            </label>

                                            <label>
                                                Fecha fin
                                                <input type="date" id="dateEndCal" class="form-control">
                                            </label>
                                        </div> -->

                                        <div style="margin-top: 1rem;">
                                            <button id="clearFiltersCal" class="btn btn-secondary">
                                                Limpiar filtros
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </section>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable_todos_users_grades" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <div class="card mt-4" id="contenedor_cursos_usuario_calificaciones" style="display: none;">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary" id="titulo-progreso" style="color: #2949c2 !important;"></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable_cursos_usuario" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalActividades" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <center>
                                        <p class="modal-title m-0 font-weight-bold text-primary" id="modalTitle" style="color: #2949c2 !important;">Actividades del Curso</p>
                                    </center>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable_actividades" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Actividad</th>
                                                    <th>Calificación</th>
                                                    <th>Fecha de Calificación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                    <a id="logoutButton" class="btn btn-primary" href="#" style="background: #2949c2; border-color: #2949c2;">
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

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="vendor/datatables/dataTables.buttons.min.js"></script>
    <script src="vendor/datatables/buttons.bootstrap4.min.js"></script>
    <script src="vendor/datatables/buttons.html5.min.js"></script>
    <script src="vendor/datatables/buttons.print.min.js"></script>

    <!-- Librería para exportar a Excel -->
    <script src="vendor/datatables/jszip.min.js"></script>

    <!-- inicializacion de tables -->
    <script src="js/datatables.js"></script>

</body>

</html>
<style>
    /* Ocultar la segunda Card inicialmente */
    #contenedor_cursos_usuario_calificaciones {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
    }

    /* Clase para mostrar la Card con animación */
    #contenedor_cursos_usuario_calificaciones.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }
</style>
<style>
    /* -------------------------- */
    /* 📌 ESTILOS PARA EL MODAL   */
    /* -------------------------- */

    /* Centrar el modal y hacer que sea responsivo */
    .modal-dialog {
        max-width: 80%;
        /* Ajustar el ancho del modal */
        margin: auto;
        /* Centrado horizontal */
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 100px);
        /* Altura adaptable a la pantalla */
    }

    /* Ajustar el contenido del modal */
    .modal-content {
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        /* Limitar la altura total */
        width: 100%;
        overflow: hidden;
        /* Evitar desbordamientos */
        border-radius: 8px;
        /* Bordes redondeados */
    }

    /* -------------------------- */
    /* 📌 CUERPO DEL MODAL        */
    /* -------------------------- */

    /* Ajustar la altura del modal y permitir scroll si es necesario */
    .modal-body {
        width: 100%;
        max-height: 70vh;
        /* Evitar que el modal sea demasiado alto */
        overflow-y: auto;
        /* Agregar scroll vertical si el contenido es largo */
        padding: 15px;
    }

    /* -------------------------- */
    /* 📌 ESTILOS PARA LA TABLA   */
    /* -------------------------- */



    /* Asegurar que la DataTable se ajuste correctamente */
    #dataTable_actividades {
        width: 100% !important;
        table-layout: auto;
        /* Ajustar el tamaño de las celdas automáticamente */
        white-space: nowrap;
        /* Evitar que el texto se rompa */
    }

    /* -------------------------- */
    /* 📌 MEJORAS VISUALES        */
    /* -------------------------- */

    /* Mejorar la visualización del título del modal */
    .modal-header {
        background: #f8f9fc;
        /* Fondo ligero */
        border-bottom: 1px solid #ddd;
        padding: 15px;
        font-weight: bold;
        text-align: center;
        width: 100%;
    }

    /* Botón de cerrar */
    .modal-header .close {
        color: #333;
        font-size: 22px;
        opacity: 0.8;
        transition: 0.3s;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    /* -------------------------- */
    /* 📌 MEJORAS EN SCROLL       */
    /* -------------------------- */

    /* Personalizar la barra de desplazamiento (solo en WebKit) */
    .modal-body::-webkit-scrollbar,
    .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-thumb,
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 4px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover,
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }
</style>
<style>
    /* 🎨 Estilo base de la tabla */
    .table {
        width: 100%;
        border-collapse: separate;
        /* 🔥 Esto permite que border-radius funcione */
        border-spacing: 0;
        /* Elimina espacios entre celdas */
        border-radius: 1rem;
        /* Aplica esquinas redondeadas a toda la tabla */
        overflow: hidden;
        /* Evita que los bordes se corten */
    }

    /* 🔥 Encabezado con degradado y esquinas redondeadas en la parte superior */
    .table thead {
        background: linear-gradient(135deg, #007bff, #0048a3);
        color: white;
        text-transform: uppercase;
        font-weight: bold;
        letter-spacing: 1px;
        border-bottom: 3px solid #0048a3;
    }

    /* 🟦 Redondear las esquinas superiores del encabezado */
    .table thead tr:first-child th:first-child {
        border-top-left-radius: 1rem;
    }

    .table thead tr:first-child th:last-child {
        border-top-right-radius: 1rem;
    }

    /* 📊 Celdas de la tabla */
    .table tbody tr {
        border-bottom: 1px solid #e6e9ef;
    }

    /* 🔥 Redondear las esquinas inferiores de la tabla */
    .table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 1rem;
    }

    .table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 1rem;
    }

    thead tr {
        background-color: #54565b !important;
    }

    /*COLOR DE BARRA*/
    #accordionSidebar {
        background-color: #2949c2 !important;
    }

    .sidebar-dark .nav-item.active .nav-link i {
        color: #fff !important;
    }

    .stat-card .col-auto i {
        color: #2949c2 !important;
    }

    .section_general {
        width: 85vw;
    }

    @media (min-width: 768px) {
        .section_general {
            width: 100%;
        }
    }

    .section_general {
        padding: 16px;
        background: #f9fafb;
        border-radius: 12px;
    }

    .filters {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Curso / Estudiante */
    .filter-group {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-group label {
        display: flex;
        flex-direction: column;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        min-width: 220px;
        width: 100%;
    }

    .filter-group select {
        margin-top: 6px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
    }

    /* Separador */
    .filters-divider {
        border: none;
        height: 1px;
        background: #e5e7eb;
        margin: 4px 0;
    }

    /* Fechas */
    .date-filters {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        padding: 16px;
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .date-field {
        display: flex;
        flex-direction: column;
        min-width: 180px;
    }

    .date-field label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #374151;
    }

    .date-field input[type="date"] {
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
    }

    /* Botones */
    .date-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 160px;
    }

    .date-actions button {
        padding: 10px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .date-actions button#applyFilter,
    .date-actions button#applyFilterQuiz {
        background: #2563eb;
        color: white;
    }

    .date-actions button.secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .date-actions button:hover {
        opacity: 0.9;
    }

    .date-field-contain {
        width: 100%;
    }

    @media (min-width: 768px) {
        .filters {
            flex-direction: row;
        }

        .date-filters {
            width: 100%;
        }

        .date-field-contain {
            width: 50%;
        }

        .date-actions {
            margin: auto;
        }
    }

    .sub-filter-group {
        display: flex;
        width: 100%;
        flex-direction: column;
    }

    @media (min-width: 768px) {
        .sub-filter-group {
            gap: 1rem;
            flex-direction: row;
        }
    }
</style>