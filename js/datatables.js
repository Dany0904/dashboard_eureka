var moodle_Wroot = moodleWroot;
document.addEventListener("DOMContentLoaded", function () {
  // Selecciona el enlace por su clase
  var perfilLink = document.querySelector(".dropdown-item");

  // Modifica el href con la variable moodle_Wroot
  if (perfilLink) {
    perfilLink.href = moodle_Wroot + "/user/profile.php";
  }
});
document.addEventListener("DOMContentLoaded", function () {
  // Seleccionar el enlace de salida por su ID único
  var logoutButton = document.getElementById("logoutButton");

  // Modificar el href dinámicamente
  if (logoutButton) {
    logoutButton.href = moodle_Wroot + "/?redirect=0";
  }
});
// table usuarios
$(document).ready(function () {
  $("#dataTable").DataTable({
    ajax: {
      url: moodle_Wroot + "/local/dashboard/ajax/get_total_users.php", // URL del AJAX
      type: "GET",
      dataType: "json",
      error: function (xhr, error, thrown) {
        console.log("Error al obtener datos del servidor:", error);
      },
    },
    columns: [
      { data: "Year" }, // Columna para el año
      { data: "TotalUsers" }, // Columna para el total de usuarios
    ],
    paging: true,
    pageLength: 10, // Establecer el número de registros por página a 5
    ordering: true,
    info: false,
    searching: true,
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "Mostrar _MENU_ registros",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "No hay datos disponibles en esta tabla",
      sInfo:
        "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
      sInfoPostFix: "",
      sSearch: "Buscar:",
      sUrl: "",
      sInfoThousands: ",",
      sLoadingRecords: "Cargando...",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
      oAria: {
        sSortAscending:
          ": Activar para ordenar la columna de manera ascendente",
        sSortDescending:
          ": Activar para ordenar la columna de manera descendente",
      },
      searchPlaceholder: "Buscar",
      search: "",
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excel",
        text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
        title: "Reporte Usuarios por Año",
        className: "btn-custom-excel",
      },
    ],
  });
  //tables de 30 dias
  $(document).ready(function () {
    var table = $("#dataTable_user_active").DataTable({
      ajax: {
        url: moodle_Wroot + "/local/dashboard/ajax/get_activity_course.php",
        type: "GET",
        dataType: "json",
        error: function (xhr, error, thrown) {
          console.log("Error al obtener datos del servidor:", error);
        },
      },
      columns: [{ data: "CourseName" }, { data: "Activity" }],
      paging: true,
      pageLength: 10, // Establecer el número de registros por página a 5
      ordering: true,
      info: false,
      searching: true,
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sInfoPostFix: "",
        sSearch: "Buscar:",
        sUrl: "",
        sInfoThousands: ",",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
        oAria: {
          sSortAscending:
            ": Activar para ordenar la columna de manera ascendente",
          sSortDescending:
            ": Activar para ordenar la columna de manera descendente",
        },
        searchPlaceholder: "Buscar",
        search: "",
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte Actividad en cursos - últimos 30 días",
          className: "btn-custom-excel",
        },
      ],
    });
  });

  //table cursos por año
  $("#dataTable_cursos_anio").DataTable({
    ajax: {
      url: moodle_Wroot + "/local/dashboard/ajax/get_total_courses.php", // Asegúrate de que la URL sea correcta
      type: "GET",
      dataType: "json",
      error: function (xhr, error, thrown) {
        console.log("Error al obtener datos del servidor:", error);
      },
    },
    columns: [
      { data: "Year", title: "Año" },
      { data: "TotalCourses", title: "Total de Cursos" },
      {
        data: "Year",
        title: "Acción",
        render: function (data, type, row) {
          return (
            '<button class="btn btn-primary btn-detalle" data-year="' +
            data +
            '">' +
            '<i class="fa fa-info-circle"></i> Ver Cursos</button>'
          );
        },
      },
    ],
    searching: false,
    info: false,
    paging: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "Mostrar _MENU_ registros",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "No hay datos disponibles en esta tabla",
      sInfo:
        "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
      sSearch: "Buscar:",
      sLoadingRecords: "Cargando...",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
        title: "Reporte de Cursos por Año",
        exportOptions: {
          columns: [0, 1], //exporta las columnas
        },
      },
    ],
  });

  // Evento de clic en el botón "Ver Cursos"
  $("#dataTable_cursos_anio tbody").on("click", ".btn-detalle", function () {
    var year = $(this).data("year"); // Obtener el año del botón
    $("#detalleCursosCard").show(); // Mostrar la segunda card
    cargarCursosPorAnio(year); // Llamar a la función que carga la segunda tabla
  });
  function cargarCursosPorAnio(year) {
    $("#detalleAnio").text(year); // Actualiza el título con el año seleccionado

    // Si la tabla ya existe, destruye la instancia anterior para evitar duplicados
    if ($.fn.DataTable.isDataTable("#dataTable_detalle_cursos")) {
      $("#dataTable_detalle_cursos").DataTable().destroy();
    }

    // Aplicar la animación para mostrar la card
    $("#detalleCursosCard").removeClass("show"); // Reinicia animación si ya estaba visible
    setTimeout(() => {
      $("#detalleCursosCard").addClass("show"); // Aplica la animación después de un breve tiempo
    }, 100);

    // Inicializa la DataTable con los cursos del año seleccionado
    $("#dataTable_detalle_cursos").DataTable({
      ajax: {
        url:
          moodle_Wroot +
          "/local/dashboard/ajax/get_courses_by_year.php?year=" +
          year,
        type: "GET",
        dataType: "json",
        error: function (xhr, error, thrown) {
          console.log("Error al obtener datos del servidor:", error);
        },
      },
      columns: [
        { data: "id", title: "ID Curso" },
        { data: "fullname", title: "Nombre del Curso" },
        { data: "timecreated", title: "Fecha de Creación" },
      ],
      searching: false,
      info: false,
      paging: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte de Cursos del año" + " " + year,
          exportOptions: {
            columns: [0, 1, 2],
          },
        },
      ],
    });
  }

  //tabla de cursos por categorias
  $("#dataTable_cursos_categoria").DataTable({
    ajax: {
      url:
        moodle_Wroot + "/local/dashboard/ajax/get_total_courses_categories.php",
      type: "GET",
      dataType: "json",
      error: function (xhr, error, thrown) {
        console.log("Error al obtener datos del servidor:", error);
      },
    },
    columns: [
      { data: "CategoryId", title: "ID Categoría" },
      { data: "CategoryName", title: "Nombre de la Categoría" },
      { data: "TotalCourses", title: "Total de Cursos" },
      {
        data: "CategoryId",
        title: "Ver",
        render: function (data, type, row) {
          return (
            '<button class="btn btn-primary btn-ver-cursos" data-idcategory="' +
            data +
            '">' +
            '<i class="fa fa-eye"></i> Ver</button>'
          );
        },
      },
    ],
    searching: true,
    info: false,
    paging: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "Mostrar _MENU_ registros",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "No hay datos disponibles en esta tabla",
      sInfo:
        "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
      sSearch: "Buscar:",
      sLoadingRecords: "Cargando...",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
        title: "Reporte de Cursos por Categoría",
        exportOptions: {
          columns: [0, 1, 2],
        },
      },
    ],
  });

  // Evento cuando se haga clic en el botón "Ver"
  $("#dataTable_cursos_categoria tbody").on(
    "click",
    ".btn-ver-cursos",
    function () {
      var idcategory = $(this).data("idcategory"); // Obtener ID de la categoría
      var categoryname = $(this).closest("tr").find("td:nth-child(2)").text(); // Obtener el nombre de la categoría desde la tabla
      // Mostrar la Card de la segunda tabla con animación
      $("#detalleCursosCard_cat").show();
      cargarCursosPorCategoria(idcategory, categoryname); // Llamar a la función que llena la segunda tabla
    },
  );

  function cargarCursosPorCategoria(idcategory, categoryname) {
    // Cambiar el título dinámicamente
    $("#detalleCat").text(categoryname);
    // Si la tabla ya existe, destruye la instancia anterior para evitar duplicados
    if ($.fn.DataTable.isDataTable("#dataTable_detalle_cursos_categoria")) {
      $("#dataTable_detalle_cursos_categoria").DataTable().destroy();
    }

    // Aplicar la animación para mostrar la card
    $("#detalleCursosCard_cat").removeClass("show"); // Reinicia animación si ya estaba visible
    setTimeout(() => {
      $("#detalleCursosCard_cat").addClass("show"); // Aplica la animación después de un breve tiempo
    }, 100);

    // Inicializa DataTable con AJAX
    $("#dataTable_detalle_cursos_categoria").DataTable({
      ajax: {
        url:
          moodle_Wroot +
          "/local/dashboard/ajax/get_category_detail.php?idcategory=" +
          idcategory,
        type: "GET",
        dataType: "json",
        error: function (xhr, error, thrown) {
          console.log("Error al obtener datos del servidor:", error);
        },
      },
      columns: [
        { data: "id", title: "ID Curso" },
        { data: "fullname", title: "Nombre del Curso" },
        { data: "summary", title: "Descripción" },
        { data: "startdate", title: "Fecha de Inicio" },
        { data: "enddate", title: "Fecha de Finalización" },
        {
          data: "id",
          title: "Acción",
          render: function (data, type, row) {
            return (
              '<button class="btn btn-info btn-ver-curso" data-idcurso="' +
              data +
              '">' +
              '<i class="fa fa-cog"></i> Configurar</button>'
            );
          },
        },
      ],
      searching: true,
      info: false,
      paging: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte de Cursos de la Categoría" + " " + categoryname,
          exportOptions: {
            columns: [0, 1, 2, 3, 4], //
          },
        },
      ],
    });

    // Evento cuando se haga clic en el botón "Ver"
    $("#dataTable_detalle_cursos_categoria tbody").on(
      "click",
      ".btn-ver-curso",
      function () {
        var idcurso = $(this).data("idcurso"); // Obtener ID del curso
        window.open(moodle_Wroot + "/course/edit.php?id=" + idcurso, "_blank"); // Redirigir a la configuración del curso
      },
    );
  }

  //tabla de cursos demandados
  $(document).ready(function () {
    // Inicializar DataTable de Cursos Demandados
    $("#dataTable_cursos_demandados").DataTable({
      ajax: {
        url: moodle_Wroot + "/local/dashboard/ajax/get_courses_demand.php",
        type: "GET",
        dataType: "json",
        error: function (xhr, error, thrown) {
          console.log("Error al obtener datos del servidor:", error);
        },
      },
      columns: [
        { data: "categoria", title: "Categoría" },
        { data: "nombre", title: "Nombre del Curso" },
        { data: "usuarios_inscritos", title: "Usuarios Inscritos" },
        { data: "fecha_inicio", title: "Fecha de Inicio" },
        { data: "fecha_fin", title: "Fecha de Finalización" },
        {
          data: "id",
          title: "Acciones",
          render: function (data, type, row) {
            return (
              '<button class="btn btn-info btn-detalle-curso" data-idcurso="' +
              data +
              '">' +
              '<i class="fas fa-chevron-circle-right"></i> Detalle</button>'
            );
          },
        },
      ],
      searching: true,
      info: false,
      paging: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte de Inscripciones en Cursos",
          exportOptions: {
            columns: [0, 1, 2, 3, 4],
          },
        },
      ],
    });

    // Evento cuando se haga clic en el botón "Detalle"
    $("#dataTable_cursos_demandados tbody").on(
      "click",
      ".btn-detalle-curso",
      function () {
        var idcurso = $(this).data("idcurso"); // Obtener ID del curso
        cargarDetallesCurso(idcurso); // Llamar función AJAX
        // Mostrar la card de detalles con animación
        $("#detalleCursoCarddeman").show();
      },
    );

    // Función AJAX para obtener los detalles del curso
    function cargarDetallesCurso(idcurso) {
      // Aplicar la animación para mostrar la card
      $("#detalleCursoCarddeman").removeClass("show"); // Reinicia animación si ya estaba visible
      setTimeout(() => {
        $("#detalleCursoCarddeman").addClass("show"); // Aplica la animación después de un breve tiempo
      }, 100);

      $.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_course_detail.php",
        type: "GET",
        data: { id: idcurso },
        dataType: "json",
        success: function (response) {
          if (response.error) {
            console.log("Error: " + response.error);
            return;
          }

          // Actualizar el título con el nombre del curso
          $("#cursoNombre").text(
            response.course.fullname + " (" + response.course.shortname + ")",
          );

          // Si la tabla ya existe, destruye y recarga
          if ($.fn.DataTable.isDataTable("#dataTable_participantes")) {
            $("#dataTable_participantes").DataTable().destroy();
          }

          // Llenar la segunda tabla con los participantes
          $("#dataTable_participantes").DataTable({
            data: response.participants,
            columns: [
              { data: "fullname", title: "Nombre Completo" },
              { data: "email", title: "Correo Electrónico" },
              { data: "progress", title: "Progreso (%)" },
              { data: "grade", title: "Calificación Final" },
              { data: "datecomplete", title: "Fecha de Finalización" },
              { data: "year", title: "Año de Finalización" },
              { data: "month", title: "Mes de Finalización" },
            ],
            searching: true,
            info: false,
            paging: true,
            lengthMenu: [
              [10, 25, 50, -1],
              [10, 25, 50, "Todos"],
            ],
            language: {
              sProcessing: "Procesando...",
              sLengthMenu: "Mostrar _MENU_ registros",
              sZeroRecords: "No se encontraron resultados",
              sEmptyTable: "No hay datos disponibles en esta tabla",
              sInfo:
                "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
              sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
              sSearch: "Buscar:",
              sLoadingRecords: "Cargando...",
              oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior",
              },
            },
            dom: "Bfrtip",
            buttons: [
              {
                extend: "excelHtml5",
                text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
                title:
                  "Reporte de Usuarios en Curso" +
                  " " +
                  response.course.fullname,
                exportOptions: {
                  columns: [0, 1, 2, 3, 4, 5, 6],
                },
              },
            ],
          });
        },
        error: function (xhr, error, thrown) {
          console.log("Error al obtener los datos del curso:", error);
        },
      });
    }
  });

  //cursos con mejores promedios
  $(document).ready(function () {
    // Inicializar DataTable de Cursos con Mejores Promedios
    $("#dataTable_cursos_promedios").DataTable({
      ajax: {
        url: moodle_Wroot + "/local/dashboard/ajax/get_promedio_courses.php",
        type: "GET",
        dataType: "json",
        error: function (xhr, error, thrown) {
          console.log("Error al obtener datos del servidor:", error);
        },
      },
      columns: [
        { data: "category", title: "Categoría" },
        { data: "fullname", title: "Nombre del Curso" },
        { data: "startdate", title: "Fecha de Inicio" },
        { data: "enddate", title: "Fecha de Finalización" },
        { data: "average", title: "Promedio de Calificación" },
        {
          data: "id",
          title: "Acciones",
          render: function (data, type, row) {
            return (
              '<button class="btn btn-info btn-detalle-curso" data-idcurso="' +
              data +
              '">' +
              '<i class="fas fa-eye"></i> Detalle</button>'
            );
          },
        },
      ],
      searching: true,
      info: false,
      paging: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte de Cursos con Promedio",
          exportOptions: {
            columns: [0, 1, 2, 3, 4],
          },
        },
      ],
    });

    // Evento cuando se haga clic en el botón "Detalle"
    $("#dataTable_cursos_promedios tbody").on(
      "click",
      ".btn-detalle-curso",
      function () {
        var idcurso = $(this).data("idcurso"); // Obtener ID del curso
        cargarActividadesCurso(idcurso); // Llamar función AJAX
        // Mostrar la card con la tabla de actividades
        $("#detalleActividadesCard_activities").show();
      },
    );

    // Función AJAX para obtener las actividades del curso
    function cargarActividadesCurso(idcurso) {
      // Aplicar la animación para mostrar la card
      $("#detalleActividadesCard_activities").removeClass("show"); // Reinicia animación si ya estaba visible
      setTimeout(() => {
        $("#detalleActividadesCard_activities").addClass("show"); // Aplica la animación después de un breve tiempo
      }, 100);
      $.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_activities_course.php",
        type: "GET",
        data: { id: idcurso },
        dataType: "json",
        success: function (response) {
          if (response.error) {
            console.log("Error: " + response.error);
            return;
          }

          // Actualizar el título con el nombre del curso
          $("#cursoNombre").text(response.course_name);

          // Si la tabla ya existe, destruye y recarga
          if ($.fn.DataTable.isDataTable("#dataTable_actividades")) {
            $("#dataTable_actividades").DataTable().destroy();
          }

          // Llenar la segunda tabla con las actividades del curso
          $("#dataTable_actividades").DataTable({
            data: response.data,
            columns: [
              { data: "modname", title: "Tipo de Actividad" },
              { data: "name", title: "Nombre de la Actividad" },
              { data: "completion", title: "Completado" },
              { data: "not_completed", title: "No Completado" },
            ],
            searching: true,
            info: false,
            paging: true,
            lengthMenu: [
              [10, 25, 50, -1],
              [10, 25, 50, "Todos"],
            ],
            language: {
              sProcessing: "Procesando...",
              sLengthMenu: "Mostrar _MENU_ registros",
              sZeroRecords: "No se encontraron resultados",
              sEmptyTable: "No hay datos disponibles en esta tabla",
              sInfo:
                "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
              sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
              sSearch: "Buscar:",
              sLoadingRecords: "Cargando...",
              oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior",
              },
            },
            dom: "Bfrtip",
            buttons: [
              {
                extend: "excelHtml5",
                text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
                title:
                  "Reporte de Actividades del Curso" +
                  " " +
                  response.course_name,
                exportOptions: {
                  columns: [0, 1, 2, 3],
                },
              },
            ],
          });
        },
        error: function (xhr, error, thrown) {
          console.log("Error al obtener las actividades del curso:", error);
        },
      });
    }
  });
  $(document).ready(function () {
    $("#dataTable_cursos_progreso").DataTable({
      processing: true,
      serverSide: true, // Usa paginación en el servidor
      ajax: {
        url: moodle_Wroot + "/local/dashboard/ajax/get_course_progress.php",
        type: "GET",
        dataType: "json",
        dataSrc: function (json) {
          console.log("Respuesta del servidor:", json); // 🔹 Depuración
          if (!json.data) {
            console.error(
              "Error: La respuesta del servidor no contiene 'data'",
            );
            return [];
          }
          return json.data;
        },
        error: function (xhr, error, thrown) {
          console.error("Error al obtener datos del servidor:", error);
        },
      },
      columns: [
        { data: "category", title: "Categoría" },
        { data: "fullname", title: "Nombre del Curso" },
        { data: "shortname", title: "Código del Curso" },
        { data: "startdate", title: "Fecha de Inicio" },
        { data: "enddate", title: "Fecha de Finalización" },
        { data: "progress", title: "Progreso del Curso" },
      ],
      searching: true,
      info: false,
      paging: true,
      pageLength: 10, // 🔹 Asegura que pagine correctamente
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte de Progreso en Cursos",
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5],
          },
        },
      ],
    });
  });

  // Tabla VS: usuarios que han finalizado el curso
  $(document).ready(function () {
    const $tbl = $("#dataTable_cursos_vs_usuarios");

    // Evita doble inicialización si vuelves a entrar a la vista.
    if ($.fn.DataTable.isDataTable($tbl)) {
      $tbl.DataTable().destroy();
      $tbl.empty();
    }

    $tbl.DataTable({
      destroy: true,
      processing: true,
      deferRender: true,
      responsive: true,
      stateSave: true,
      pageLength: 10,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      ajax: {
        url: moodle_Wroot + "/local/dashboard/ajax/get_vs_user_course.php",
        type: "GET",
        dataType: "json",
        cache: false,
        // Recomendado: si lo usas dentro de Moodle, manda sesskey y valida en PHP.
        data: function (d) {
          d.sesskey =
            typeof M !== "undefined" && M.cfg && M.cfg.sesskey
              ? M.cfg.sesskey
              : "";
        },
        dataSrc: function (json) {
          if (!json || !Array.isArray(json.data)) {
            console.error("Respuesta inesperada:", json);
            return [];
          }
          return json.data;
        },
        error: function (xhr, error, thrown) {
          console.error("Error AJAX DataTable:", {
            xhr,
            error,
            thrown,
            response: xhr.responseText,
          });
        },
      },
      columns: [
        { data: "category", title: "Categoría" },
        { data: "fullname", title: "Nombre del Curso" },
        {
          data: "participants",
          title: "Total de Participantes",
          className: "text-end",
        },
        { data: "completed", title: "Finalizado", className: "text-end" },
        {
          data: "not_completed",
          title: "No Finalizado",
          className: "text-end",
        },
      ],
      order: [
        [0, "asc"],
        [1, "asc"],
      ],
      searching: true,
      info: false,
      paging: true,
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "No hay datos disponibles en esta tabla",
        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          title: "Reporte de Finalización de Cursos",
          text: '<span class="btn btn-success btn-sm"><i class="fas fa-file-excel me-1"></i> Descargar Excel</span>',
          exportOptions: {
            columns: [0, 1, 2, 3, 4],
          },
        },
      ],
    });
  });

  //todos los usuarios
  $("#dataTable_todos_users").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: moodle_Wroot + "/local/dashboard/ajax/get_todos_user.php",
      type: "GET",
      dataSrc: "data",
    },
    columns: [
      { data: "ID", title: "ID" },
      { data: "Username", title: "Usuario" },
      { data: "FirstName", title: "Nombre" },
      { data: "LastName", title: "Apellido" },
      { data: "Email", title: "Correo" },
      { data: "CreatedAt", title: "Fecha de Registro" },
      {
        data: "ID",
        title: "Acción",
        render: function (data, type, row) {
          return `<button class="btn btn-primary btn-sm ver-progreso" 
                                data-userid="${data}" 
                                data-username="${row.FirstName} ${row.LastName}">
                            <i class="fas fa-chevron-circle-right"></i> Detalle
                        </button>`;
        },
      },
    ],
    info: false,
    paging: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      decimal: "",
      emptyTable: "No hay información disponible",
      info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
      infoEmpty: "Mostrando 0 a 0 de 0 usuarios",
      infoFiltered: "(Filtrado de _MAX_ usuarios en total)",
      lengthMenu: "Mostrar _MENU_ usuarios",
      loadingRecords: "Cargando...",
      processing: "Procesando...",
      search: "Buscar:",
      zeroRecords: "No se encontraron coincidencias",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
        title: "Reporte General de Usuarios Registrados",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5],
        },
      },
    ],
  });

  // Evento para cargar el progreso del usuario seleccionado y actualizar el título
  $("#dataTable_todos_users tbody").on("click", ".ver-progreso", function () {
    var userID = $(this).data("userid");
    var userName = $(this).data("username"); // Obtener el nombre del usuario
    $("#detalleCursosCard_users").show();
    // Actualizar el encabezado con el nombre del usuario
    $("#titulo-progreso").text(`Progreso del Usuario: ${userName}`);
    // Aplicar la animación para mostrar la card
    $("#detalleCursosCard_users").removeClass("show"); // Reinicia animación si ya estaba visible
    setTimeout(() => {
      $("#detalleCursosCard_users").addClass("show"); // Aplica la animación después de un breve tiempo
    }, 100);
    // Cargar la segunda DataTable con los cursos del usuario
    $("#dataTable_cursos_usuario").DataTable({
      destroy: true,
      processing: true,
      serverSide: false,
      ajax: {
        url: moodle_Wroot + "/local/dashboard/ajax/get_cursos_usuario.php",
        type: "GET",
        data: { userid: userID },
        dataSrc: "courses",
      },
      columns: [
        { data: "courseid", title: "ID Curso" },
        { data: "course_name", title: "Curso" },
        { data: "category_name", title: "Categoría" },
        { data: "grade", title: "Calificación" },
        { data: "aprobado", title: "Estado" },
        {
          data: "percentage",
          title: "Progreso",
          render: function (data, type, row) {
            return `
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: ${data}" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100">
                                ${data}
                            </div>
                        </div>
                    `;
          },
        },
        { data: "primer_ingreso", title: "Primer Ingreso" },
        { data: "ultimo_ingreso", title: "Último Ingreso" },
      ],
      info: false,
      paging: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
      language: {
        emptyTable: "No hay cursos disponibles para este usuario",
        info: "Mostrando _START_ a _END_ de _TOTAL_ cursos",
        infoEmpty: "Mostrando 0 a 0 de 0 cursos",
        lengthMenu: "Mostrar _MENU_ cursos",
        loadingRecords: "Cargando...",
        processing: "Procesando...",
        search: "Buscar:",
        zeroRecords: "No se encontraron coincidencias",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Anterior",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
          title: "Reporte de Progreso en Cursos del Usuario" + " " + userName,
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7],
          },
        },
      ],
    });
  });
  // Inicializar DataTable de todos los usuarios con calificaciones
  $("#dataTable_todos_users_grades").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: moodle_Wroot + "/local/dashboard/ajax/get_todos_user_grades.php",
      type: "GET",
      dataSrc: "data",
    },
    columns: [
      { data: "ID", title: "ID" },
      { data: "Username", title: "Usuario" },
      { data: "FirstName", title: "Nombre" },
      { data: "LastName", title: "Apellido" },
      { data: "Email", title: "Correo" },
      { data: "CreatedAt", title: "Fecha de Registro" },
      {
        data: "ID",
        title: "Acción",
        render: function (data, type, row) {
          return `<button class="btn btn-primary btn-sm ver-progreso" 
                                data-userid="${data}" 
                                data-username="${row.FirstName} ${row.LastName}">
                          <i class="far fa-arrow-alt-circle-right"></i> Detalle
                        </button>`;
        },
      },
    ],
    info: false,
    paging: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      emptyTable: "No hay información disponible",
      info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
      infoEmpty: "Mostrando 0 a 0 de 0 usuarios",
      infoFiltered: "(Filtrado de _MAX_ usuarios en total)",
      lengthMenu: "Mostrar _MENU_ usuarios",
      loadingRecords: "Cargando...",
      processing: "Procesando...",
      search: "Buscar:",
      zeroRecords: "No se encontraron coincidencias",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
        title: "Reporte General de Usuarios Registrados",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5],
        },
      },
    ],
  });

  // Evento para cargar los cursos y calificaciones del usuario seleccionado
  $("#dataTable_todos_users_grades tbody").on(
    "click",
    ".ver-progreso",
    function () {
      var userID = $(this).data("userid");
      var userName = $(this).data("username"); // Obtener el nombre del usuario
      $("#contenedor_cursos_usuario_calificaciones").show();
      // Actualizar el título con el nombre del usuario
      $("#titulo-progreso").text(`Cursos de: ${userName}`);
      // Aplicar la animación para mostrar la card
      $("#contenedor_cursos_usuario_calificaciones").removeClass("show"); // Reinicia animación si ya estaba visible
      setTimeout(() => {
        $("#contenedor_cursos_usuario_calificaciones").addClass("show"); // Aplica la animación después de un breve tiempo
      }, 100);
      // Cargar la segunda DataTable con los cursos del usuario
      $("#dataTable_cursos_usuario").DataTable({
        destroy: true, // Resetear tabla si ya está cargada
        processing: true,
        serverSide: false,
        ajax: {
          url: moodle_Wroot + "/local/dashboard/ajax/get_gardes_user.php",
          type: "GET",
          data: { userid: userID },
          dataSrc: "courses",
        },
        columns: [
          { data: "courseid", title: "ID Curso" },
          { data: "course_name", title: "Curso" },
          { data: "category_name", title: "Categoría" },
          { data: "grade", title: "Calificación" },
          { data: "aprobado", title: "Estado" },
          {
            data: "activities",
            title: "Detalle",
            render: function (data, type, row) {
              if (
                !row.activities ||
                !Array.isArray(row.activities) ||
                row.activities.length === 0
              ) {
                return `<button class="btn btn-secondary btn-sm" disabled> <i class="fas fa-exclamation-circle"></i> Sin Actividades</button>`;
              }

              let activitiesData;
              try {
                activitiesData = JSON.stringify(row.activities);
              } catch (error) {
                console.error(
                  "Error en JSON.stringify(row.activities):",
                  error,
                  row.activities,
                );
                return `<button class="btn btn-danger btn-sm" disabled>Error en actividades</button>`;
              }

              return `<button class="btn btn-info btn-sm ver-actividades" 
                    data-courseid="${row.courseid}" 
                    data-coursename="${row.course_name}" 
                    data-username="${row.username}" 
                    data-activities='${JSON.stringify(row.activities).replace(/'/g, "&apos;")}'>
                 <i class="far fa-arrow-alt-circle-right"></i> Ver Actividades
            </button>`;
            },
          },
        ],
        info: false,
        paging: true,
        lengthMenu: [
          [10, 25, 50, -1],
          [10, 25, 50, "Todos"],
        ],
        language: {
          emptyTable: "No hay cursos disponibles para este usuario",
          info: "Mostrando _START_ a _END_ de _TOTAL_ cursos",
          infoEmpty: "Mostrando 0 a 0 de 0 cursos",
          lengthMenu: "Mostrar _MENU_ cursos",
          loadingRecords: "Cargando...",
          processing: "Procesando...",
          search: "Buscar:",
          zeroRecords: "No se encontraron coincidencias",
          paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior",
          },
        },
        dom: "Bfrtip",
        buttons: [
          {
            extend: "excelHtml5",
            text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
            title: "Reporte del Estatus del Usuario" + " " + userName,
            exportOptions: {
              columns: [0, 1, 2, 3, 4],
            },
          },
        ],
      });
    },
  );

  // Evento para mostrar actividades del curso seleccionado
  $("#dataTable_cursos_usuario tbody").on(
    "click",
    ".ver-actividades",
    function () {
      var rawData = $(this).data("activities"); // Obtener las actividades
      var courseName = $(this).attr("data-coursename"); // Obtener nombre del curso
      var userName = $(this).attr("data-username"); // Obtener nombre del usuario

      console.log("Datos recibidos:", rawData);
      console.log("Curso:", courseName, "Usuario:", userName); // Depuración

      try {
        var activities = Array.isArray(rawData) ? rawData : JSON.parse(rawData);

        // Actualizar el título del modal con curso y usuario
        $("#modalTitle").html(
          `Actividades del Curso: <strong>${courseName}</strong> <br> Usuario: <strong>${userName}</strong>`,
        );

        $("#dataTable_actividades").DataTable({
          destroy: true,
          processing: true,
          data: activities,
          columns: [
            { data: "activity_name", title: "Actividad" },
            { data: "final_grade", title: "Calificación" },
            { data: "date_graded", title: "Fecha de Calificación" },
          ],
          info: false,
          paging: true,
          lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"],
          ],
          language: {
            emptyTable: "No hay actividades disponibles",
            info: "Mostrando _START_ a _END_ de _TOTAL_ actividades",
            infoEmpty: "Mostrando 0 a 0 de 0 actividades",
            lengthMenu: "Mostrar _MENU_ actividades",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron coincidencias",
            paginate: {
              first: "Primero",
              last: "Último",
              next: "Siguiente",
              previous: "Anterior",
            },
          },
          dom: "Bfrtip",
          buttons: [
            {
              extend: "excelHtml5",
              text: '<a href="#" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-cloud-download-alt"></i></span><span class="text">Descargar Excel</span></a>',
              title:
                "Reporte de Actividades en Curso" +
                " " +
                courseName +
                " " +
                "del Usuario" +
                " " +
                userName,
              exportOptions: {
                columns: [0, 1, 2],
              },
            },
          ],
        });

        $("#modalActividades").modal("show");
      } catch (error) {
        console.error(
          "Error al procesar las actividades:",
          error,
          "Datos recibidos:",
          rawData,
        );
        alert(
          "Error al procesar las actividades. Verifica la consola para más detalles.",
        );
      }
    },
  );
});

//Codigo nuevo implementacion Eureka

var percentChart = createGaugeChart("#kpiPercentChart", 0);
var gradeChart = createGaugeChart("#kpiAvgGradeChart", 0);

var percentChartQuiz = createGaugeChart("#kpiPercentQuizChart", 0);
var gradeChartQuiz = createGaugeChart("#kpiAvgGradeQuizChart", 0);

//table cursos por año
var tablaUsuarios = $("#tablaUsuarios").DataTable({
  ajax: {
    url: moodleWroot + "/local/dashboard/ajax/get_usuarios_cursos.php",
    type: "POST",
    data: function (d) {
      d.userid = $("#studentSelect").val() || 0;
      d.courseid = $("#cursoSelect").val() || null;
      d.sectionid = $("#sectionSelect").val() || 0;
      d.datestart = $("#dateStart").val();
      d.dateend = $("#dateEnd").val();
    },
    dataSrc: function (json) {
      $("#kpiTotal").text(json.kpis.totalh5p);
      $("#kpiDone").text(json.kpis.completed);
      $("#kpiPending").text(json.kpis.pending);
      percentChart.updateSeries([json.kpis.percentage]);

      gradeChart.updateSeries([
        json.kpis.avggrade !== null ? json.kpis.avggrade : 0
      ]);

      if (json.chart) {
        renderH5PChart(
          json.chart.labels,
          json.chart.compliance,
          json.chart.grades,
        );
      }

      if (json.sectionchart) {
        renderSectionComplianceChart(
          json.sectionchart.labels,
          json.sectionchart.compliance,
        );
      }

      return json.data;
    },
    error: function (xhr, status, error) {
      console.error("AJAX ERROR:", xhr.responseText);
    },
  },
  columns: [
    { data: 0, title: "Username" },
    { data: 1, title: "Nombre completo" },
    { data: 2, title: "Ciudad" },
    { data: 3, title: "Status" },
    { data: 4, title: "Último login" },
    { data: 5, title: "Total actividades" },
    { data: 6, title: "Actividades completadas" },
    { data: 7, title: "Actividades pendientes" },
    { data: 8, title: "Cobertura" },
    { data: 9, title: "Promedio de nota" },
  ],
  dom: "Bfrtip",
  buttons: [
    {
      extend: "excelHtml5",
      text: "Descargar Excel",
      title: "Reporte Preturnos",
      exportOptions: {
        columns: ":visible",
      },
    },
  ],
  language: {
    url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json",
  },
});

// Mostrar loader antes de enviar AJAX
tablaUsuarios.on("preXhr.dt", function () {
  $("#tableLoader").removeClass("d-none");
});

// Ocultar loader cuando termina completamente
tablaUsuarios.on("xhr.dt", function () {
  $("#tableLoader").addClass("d-none");
});


$(document).ready(function () {
  // Mostrar estado de carga
  $("#cursoSelect")
    .html('<option value="">Cargando cursos...</option>')
    .prop("disabled", true);

  $.ajax({
    url: moodleWroot + "/local/dashboard/ajax/get_cursos.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      $("#cursoSelect")
        .html('<option value="">Todos</option>')
        .prop("disabled", false);

      if (response.data) {
        response.data.forEach(function (curso) {
          $("#cursoSelect").append(
            `<option value="${curso.id}">${curso.fullname}</option>`,
          );
        });
      }
    },
    error: function (xhr) {
      $("#cursoSelect").html('<option value="">Error al cargar</option>');
      console.error("Error cargando cursos", xhr.responseText);
    },
  });
});

$("#cursoSelect").on("change", function () {
  tablaUsuarios.ajax.reload(null, false);
});

$("#dateStart, #dateEnd").on("change", function () {
  tablaUsuarios.ajax.reload();
});

$("#clearFilters").on("click", function () {
  const courseid = $("#cursoSelect").val();

  $("#dateStart").val("");
  $("#dateEnd").val("");

  if (!courseid) {
    // Estado inicial
    $("#studentSelect")
      .html('<option value="">Todos</option>')
      .prop("disabled", false);

    $("#sectionSelect")
      .html('<option value="">Seleccione un curso</option>')
      .prop("disabled", true);
  } else {
    // Dispara recarga natural
    $("#cursoSelect").trigger("change");
  }

  tablaUsuarios.ajax.reload();
});

$("#cursoSelect").on("change", function () {
  let courseid = $(this).val();

  $("#studentSelect")
    .html('<option value="">Seleccione un curso</option>')
    .prop("disabled", true);

  if (!courseid) {
    tablaUsuarios.ajax.reload(null, false);
    return;
  }

  // Estado cargando
  $("#studentSelect")
    .html('<option value="">Cargando estudiantes...</option>')
    .prop("disabled", true);

  $.ajax({
    url: moodleWroot + "/local/dashboard/ajax/get_estudiantes.php",
    type: "GET",
    dataType: "json",
    data: { courseid: courseid },
    success: function (response) {
      $("#studentSelect")
        .html('<option value="">Todos</option>')
        .prop("disabled", false);

      if (response.data) {
        response.data.forEach(function (u) {
          $("#studentSelect").append(
            `<option value="${u.id}">${u.fullname}</option>`,
          );
        });
      }

      tablaUsuarios.ajax.reload(null, false);
    },
    error: function () {
      $("#studentSelect")
        .html('<option value="">Error al cargar</option>')
        .prop("disabled", true);
    },
  });
});

$("#studentSelect").on("change", function () {
  tablaUsuarios.ajax.reload(null, false);
});

$("#cursoSelect").on("change", function () {
  let courseid = $(this).val();

  $("#sectionSelect")
    .html('<option value="">Cargando secciones...</option>')
    .prop("disabled", true);

  if (!courseid) {
    $("#sectionSelect")
      .html('<option value="">Seleccione un curso</option>')
      .prop("disabled", true);
    tablaUsuarios.ajax.reload();
    return;
  }

  $.ajax({
    url: moodleWroot + "/local/dashboard/ajax/get_sections.php",
    type: "GET",
    dataType: "json",
    data: { courseid: courseid },
    success: function (response) {

      $("#sectionSelect")
        .html('<option value="">Todas</option>')
        .prop("disabled", false);

      if (response.data) {
        response.data.forEach(function (section) {
          $("#sectionSelect").append(
            `<option value="${section.id}">
              ${section.name}
            </option>`
          );
        });
      }

      tablaUsuarios.ajax.reload();
    },
    error: function () {
      $("#sectionSelect")
        .html('<option value="">Error al cargar</option>')
        .prop("disabled", true);
    },
  });
});

$("#sectionSelect").on("change", function () {
  tablaUsuarios.ajax.reload();
});

var h5pChart;

function renderH5PChart(labels, compliance, grades) {
  if (h5pChart) {
    h5pChart.destroy();
  }

  var options = {
    chart: {
      height: 380,
      type: "line",
      zoom: {
        enabled: false,
      },
      stacked: false,
      toolbar: { show: false },
    },
    colors: ["#4e79a7", "#ff0000"],
    series: [
      {
        name: "% Cobertura",
        type: "column",
        data: compliance,
      },
      {
        name: "Promedio de calificación",
        type: "line",
        data: grades,
      },
    ],
    xaxis: {
      categories: labels,
      labels: { rotate: -45 },
    },
    yaxis: [
      {
        title: {
          text: "% Cobertura",
        },
      },
      {
        opposite: true,
        title: {
          text: "Promedio de calificación",
        },
        min: 0,
        max: 100, // AJUSTA si tu escala es diferente
      },
    ],
    plotOptions: {
      bar: {
        columnWidth: "55%",
        borderRadius: 4,
      },
    },
    stroke: {
      width: [0, 3],
      curve: "smooth",
    },
    markers: {
      size: 3,
      colors: ["#ff0000"],
      strokeWidth: 0,
      hover: {
        size: 8,
      },
    },
    dataLabels: {
      enabled: false,
    },
    tooltip: {
      shared: true,
      intersect: false,
      y: {
        formatter: function (val, { seriesIndex }) {
          return seriesIndex === 0 ? val + "%" : val;
        },
      },
    },
  };

  h5pChart = new ApexCharts(document.querySelector("#h5pBarChart"), options);

  h5pChart.render();
}

var sectionChart;

function renderSectionComplianceChart(labels, compliance) {
  if (sectionChart) {
    sectionChart.destroy();
  }

  var options = {
    chart: {
      height: 380,
      type: "bar",
      toolbar: { show: false },
    },
    series: [
      {
        name: "% Cobertura",
        data: compliance,
      },
    ],
    xaxis: {
      categories: labels,
      labels: {
        rotate: -45,
      },
    },
    yaxis: {
      min: 0,
      max: 100,
      title: {
        text: "% Cobertura",
      },
    },
    plotOptions: {
      bar: {
        columnWidth: "55%",
        borderRadius: 6,
      },
    },
    dataLabels: {
      enabled: true,
      formatter: (val) => val + "%",
    },
    tooltip: {
      y: {
        formatter: (val) => val + "%",
      },
    },
  };

  sectionChart = new ApexCharts(
    document.querySelector("#sectionComplianceChart"),
    options,
  );

  sectionChart.render();
}

var tablaSecciones = $("#tablaSecciones").DataTable({
  ajax: {
    url: moodleWroot + "/local/dashboard/ajax/get_secciones_curso.php",
    type: "POST",
    data: function () {
      return {
        courseid: $("#cursoSelect").val() || 0,
        actividad: $("#actividadSelect").val() || "hvp",
      };
    },
    dataSrc: function (json) {
      const data = json.data || [];

      // Arrays para ApexCharts de horas
      const secciones = data.map((d) => d.section);
      const horasProgramadas = data.map((d) => d.horas_programadas);
      const horasRealizadas = data.map((d) => d.horas_realizadas);

      // Actualizar gráfico de horas
      chartHoras.updateOptions({
        xaxis: { categories: secciones },
        series: [
          { name: "Horas Programadas", data: horasProgramadas },
          { name: "Horas Realizadas", data: horasRealizadas },
        ],
      });

      // Arrays para % de Cobertura
      const porcentajeCumplimiento = data.map((d) => {
        // Remover el % si tu JSON lo devuelve como "8%" y convertir a número
        return parseInt(d.porcentaje.replace("%", "")) || 0;
      });

      // Actualizar gráfico de % Cobertura
      chartPorcentaje.updateOptions({
        xaxis: { categories: secciones },
        series: [{ name: "% Cobertura", data: porcentajeCumplimiento }],
      });

      return data;
    },
  },
  columns: [
    { data: "section", title: "Sección" },
    { data: "lastmodified", title: "Última modificación" },
    { data: "objetivo", title: "Público objetivo", className: "text-center" },
    { data: "impactado", title: "Público impactado", className: "text-center" },
    { data: "falta", title: "Falta", className: "text-center" },
    {
      data: "horas_programadas",
      title: "Horas programadas",
      className: "text-center",
    },
    {
      data: "horas_realizadas",
      title: "Horas realizadas",
      className: "text-center",
    },
    { data: "porcentaje", title: "Q % abarcado", className: "text-center" },
  ],
  dom: "Bfrtip",
  buttons: [
    {
      extend: "excelHtml5",
      text: "Descargar Excel",
      title: "Reporte Horas",
      exportOptions: {
        columns: ":visible",
      },
    },
  ],
  paging: true,
  searching: true,
  ordering: true,
  order: [[0, "asc"]],
  language: { url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json" },
});

$("#cursoSelect").on("change", function () {
  const courseid = $(this).val();
  if (!courseid) {
    tablaSecciones.clear().draw();
    chartHoras.updateOptions({
      xaxis: { categories: [] },
      series: [
        { name: "Horas Programadas", data: [] },
        { name: "Horas Realizadas", data: [] },
      ],
    });
    return;
  }
  tablaSecciones.ajax.reload();
});

$("#actividadSelect").on("change", function () {
  const courseid = $("#cursoSelect").val();
  if (!courseid) return;
  tablaSecciones.ajax.reload();
});

var chartHoras = new ApexCharts(document.querySelector("#chartHoras"), {
  chart: { type: "bar", height: 400, stacked: false },
  plotOptions: { bar: { horizontal: false, columnWidth: "50%" } },
  series: [
    { name: "Horas Programadas", data: [] },
    { name: "Horas Realizadas", data: [] },
  ],
  xaxis: { categories: [] },
  yaxis: { title: { text: "Horas" }, min: 0 },
  tooltip: { y: { formatter: (val) => val + " h" } },
  colors: ["#1E90FF", "#32CD32"],
  legend: { position: "top" },
});

chartHoras.render();

var chartPorcentaje = new ApexCharts(
  document.querySelector("#chartPorcentaje"),
  {
    chart: { type: "bar", height: 400 },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "50%",
        dataLabels: { position: "top" },
      },
    },
    series: [
      {
        name: "% Cobertura",
        data: [], // se llenará dinámicamente
      },
    ],
    xaxis: {
      categories: [], // secciones
      title: { text: "Secciones" },
    },
    yaxis: {
      title: { text: "% Cobertura" },
      min: 0,
      max: 100,
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val + "%";
      },
      style: { fontSize: "12px", colors: ["#000"] },
    },
    tooltip: {
      y: { formatter: (val) => val + "%" },
    },
    colors: ["#FFA500"], // naranja
    legend: { show: false },
  },
);

chartPorcentaje.render();

var tablaUsuariosQuiz = $("#tablaUsuariosQuiz").DataTable({
  ajax: {
    url: moodleWroot + "/local/dashboard/ajax/get_usuarios_quiz.php",
    type: "POST",
    data: function (d) {
      d.userid = $("#studentSelectQuiz").val() || 0;
      d.courseid = $("#cursoSelectQuiz").val() || 0;
      d.sectionid = $("#sectionSelectQuiz").val() || 0;
      d.datestart = $("#dateStartQuiz").val();
      d.dateend = $("#dateEndQuiz").val();
    },
    dataSrc: function (json) {
      $("#kpiTotalQuiz").text(json.kpis.totalquiz);
      $("#kpiDoneQuiz").text(json.kpis.completed);
      $("#kpiPendingQuiz").text(json.kpis.pending);
      percentChartQuiz.updateSeries([json.kpis.percentage]);
      gradeChartQuiz.updateSeries([
        json.kpis.avggrade !== null ? json.kpis.avggrade : 0
      ]);

      if (json.chart) {
        renderH5PChartQuiz(
          json.chart.labels,
          json.chart.compliance,
          json.chart.grades,
        );
      }

      if (json.sectionchart) {
        renderSectionComplianceChartQuiz(
          json.sectionchart.labels,
          json.sectionchart.compliance,
        );
      }

      return json.data;
    },
    error: function (xhr, status, error) {
      console.error("AJAX ERROR:", xhr.responseText);
    },
  },
  columns: [
    { data: 0, title: "Username" },
    { data: 1, title: "Nombre completo" },
    { data: 2, title: "Ciudad" },
    { data: 3, title: "Status" },
    { data: 4, title: "Último login" },
    { data: 5, title: "Total actividades" },
    { data: 6, title: "Actividades completadas" },
    { data: 7, title: "Actividades pendientes" },
    { data: 8, title: "Cobertura" },
    { data: 9, title: "Promedio de nota" },
  ],
  dom: "Bfrtip",
  buttons: [
    {
      extend: "excelHtml5",
      text: "Descargar Excel",
      title: "Reporte Certificaciones",
      exportOptions: {
        columns: ":visible",
      },
    },
  ],
  language: {
    url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json",
  },
});

$("#dateStartQuiz, #dateEndQuiz").on("change", function () {
  tablaUsuariosQuiz.ajax.reload();
});

$("#sectionSelectQuiz").on("change", function () {

  $("#quizLoader").show();
  $("#tablaUsuariosQuiz").hide();

  tablaUsuariosQuiz.ajax.reload();
});

$("#clearFiltersQuiz").on("click", function () {
  const courseid = $("#cursoSelectQuiz").val();

  $("#dateStartQuiz").val("");
  $("#dateEndQuiz").val("");

  if (!courseid) {
    // Estado inicial
    $("#studentSelectQuiz")
      .html('<option value="">Todos</option>')
      .prop("disabled", false);

    $("#sectionSelectQuiz")
      .html('<option value="">Seleccione un curso</option>')
      .prop("disabled", true);
  } else {
    // Dispara recarga natural
    $("#cursoSelectQuiz").trigger("change");
  }

  tablaUsuariosQuiz.ajax.reload();
});

$("#tablaUsuariosQuiz").on("xhr.dt", function () {
  $("#quizLoader").hide();
  $("#tablaUsuariosQuiz").show();
});

$("#cursoSelectQuiz").on("change", function () {
  let courseid = $(this).val();

  $("#sectionSelectQuiz")
    .html('<option value="">Cargando secciones...</option>')
    .prop("disabled", true);

  if (!courseid) {
    $("#sectionSelectQuiz")
      .html('<option value="">Seleccione un curso</option>')
      .prop("disabled", true);

    tablaUsuariosQuiz.ajax.reload();
    return;
  }

  $.ajax({
    url: moodleWroot + "/local/dashboard/ajax/get_sections.php",
    type: "GET",
    dataType: "json",
    data: { courseid: courseid },
    success: function (response) {

      $("#sectionSelectQuiz")
        .html('<option value="">Todas</option>')
        .prop("disabled", false);

      if (response.data) {
        response.data.forEach(function (section) {
          $("#sectionSelectQuiz").append(
            `<option value="${section.id}">${section.name}</option>`
          );
        });
      }

      tablaUsuariosQuiz.ajax.reload();
    },
  });
});

$("#sectionSelectQuiz").on("change", function () {
  tablaUsuariosQuiz.ajax.reload();
});

$(document).ready(function () {
  // Estado inicial de carga
  $("#cursoSelectQuiz")
    .html('<option value="">Cargando cursos...</option>')
    .prop("disabled", true);

  $.ajax({
    url: moodleWroot + "/local/dashboard/ajax/get_cursos.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      $("#cursoSelectQuiz")
        .html('<option value="">Todos</option>')
        .prop("disabled", false);

      if (response.data) {
        response.data.forEach(function (curso) {
          $("#cursoSelectQuiz").append(
            `<option value="${curso.id}">${curso.fullname}</option>`,
          );
        });
      }
    },
    error: function (xhr) {
      $("#cursoSelectQuiz")
        .html('<option value="">Error al cargar</option>')
        .prop("disabled", true);

      console.error("Error cargando cursos", xhr.responseText);
    },
  });
});

$("#cursoSelectQuiz").on("change", function () {
  let courseid = $(this).val();

  $("#studentSelectQuiz")
    .html('<option value="">Seleccione un curso</option>')
    .prop("disabled", true);

  if (!courseid) {
    tablaUsuariosQuiz.ajax.reload(null, false);
    return;
  }

  // Estado cargando
  $("#studentSelectQuiz")
    .html('<option value="">Cargando estudiantes...</option>')
    .prop("disabled", true);

  $.ajax({
    url: moodleWroot + "/local/dashboard/ajax/get_estudiantes.php",
    type: "GET",
    dataType: "json",
    data: { courseid: courseid },
    success: function (response) {
      $("#studentSelectQuiz")
        .html('<option value="">Todos</option>')
        .prop("disabled", false);

      if (response.data) {
        response.data.forEach(function (u) {
          $("#studentSelectQuiz").append(
            `<option value="${u.id}">${u.fullname}</option>`,
          );
        });
      }

      tablaUsuariosQuiz.ajax.reload(null, false);
    },
    error: function () {
      $("#studentSelectQuiz")
        .html('<option value="">Error al cargar</option>')
        .prop("disabled", true);
    },
  });
});

$("#studentSelectQuiz").on("change", function () {
  tablaUsuariosQuiz.ajax.reload(null, false);
});

var h5pChartQuiz;

function renderH5PChartQuiz(labels, compliance, grades) {
  if (h5pChartQuiz) {
    h5pChartQuiz.destroy();
  }

  var options = {
    chart: {
      height: 380,
      type: "line",
      zoom: {
        enabled: false,
      },
      stacked: false,
      toolbar: { show: false },
    },
    colors: ["#4e79a7", "#ff0000"],
    series: [
      {
        name: "% Cobertura",
        type: "column",
        data: compliance,
      },
      {
        name: "Promedio de calificación",
        type: "line",
        data: grades,
      },
    ],
    xaxis: {
      categories: labels,
      labels: { rotate: -45 },
    },
    yaxis: [
      {
        title: {
          text: "% Cobertura",
        },
      },
      {
        opposite: true,
        title: {
          text: "Promedio de calificación",
        },
        min: 0,
        max: 100, // AJUSTA si tu escala es diferente
      },
    ],
    plotOptions: {
      bar: {
        columnWidth: "55%",
        borderRadius: 4,
      },
    },
    stroke: {
      width: [0, 3],
      curve: "smooth",
    },
    markers: {
      size: 3,
      colors: ["#ff0000"],
      strokeWidth: 0,
      hover: {
        size: 8,
      },
    },
    dataLabels: {
      enabled: false,
    },
    tooltip: {
      shared: true,
      intersect: false,
      y: {
        formatter: function (val, { seriesIndex }) {
          return seriesIndex === 0 ? val + "%" : val;
        },
      },
    },
  };

  h5pChartQuiz = new ApexCharts(
    document.querySelector("#h5pBarChartQuiz"),
    options,
  );

  h5pChartQuiz.render();
}

var sectionChartQuiz;

function renderSectionComplianceChartQuiz(labels, compliance) {
  if (sectionChartQuiz) {
    sectionChartQuiz.destroy();
  }

  var options = {
    chart: {
      height: 380,
      type: "bar",
      toolbar: { show: false },
    },
    series: [
      {
        name: "% Cobertura",
        data: compliance,
      },
    ],
    xaxis: {
      categories: labels,
      labels: {
        rotate: -45,
      },
    },
    yaxis: {
      min: 0,
      max: 100,
      title: {
        text: "% Cobertura",
      },
    },
    plotOptions: {
      bar: {
        columnWidth: "55%",
        borderRadius: 6,
      },
    },
    dataLabels: {
      enabled: true,
      formatter: (val) => val + "%",
    },
    tooltip: {
      y: {
        formatter: (val) => val + "%",
      },
    },
  };

  sectionChartQuiz = new ApexCharts(
    document.querySelector("#sectionComplianceChartQuiz"),
    options,
  );

  sectionChartQuiz.render();
}

function createGaugeChart(el, value) {

  var options = {
    series: [value],
    chart: {
      height: 160,
      type: "radialBar"
    },
    plotOptions: {
      radialBar: {
        startAngle: -90,
        endAngle: 90,
        hollow: {
          size: "55%"
        },
        track: {
          background: "#eee",
          strokeWidth: "100%"
        },
        dataLabels: {
          name: {
            show: false
          },
          value: {
            fontSize: "28px",
            fontWeight: 700,
            offsetY: 10,
            formatter: function(val) {
              return Math.round(val) + "%";
            }
          }
        }
      }
    },
    fill: {
      type: "gradient",
      gradient: {
        shade: "light",
        type: "horizontal",
        gradientToColors: ["#00c853"],
        stops: [0, 50, 75, 100],
        colorStops: [
          { offset: 0, color: "#ff0000" },
          { offset: 50, color: "#ff9800" },
          { offset: 75, color: "#ffeb3b" },
          { offset: 100, color: "#00c853" }
        ]
      }
    },
    stroke: {
      lineCap: "round"
    }
  };

  var chart = new ApexCharts(document.querySelector(el), options);
  chart.render();

  return chart;
}