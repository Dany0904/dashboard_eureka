var moodle_Wroot = moodleWroot;
document.addEventListener("DOMContentLoaded", function() {
    // Selecciona el enlace por su clase
    var perfilLink = document.querySelector(".dropdown-item");

    // Modifica el href con la variable moodle_Wroot
    if (perfilLink) {
        perfilLink.href = moodle_Wroot + "/user/profile.php";
    }
    
});
document.addEventListener("DOMContentLoaded", function() {
    // Seleccionar el enlace de salida por su ID único
    var logoutButton = document.getElementById("logoutButton");

    // Modificar el href dinámicamente
    if (logoutButton) {
        logoutButton.href = moodle_Wroot + "/?redirect=0";
    }
});
// Función para generar colores aleatorios en formato RGBA
function generateRandomColors(count) {
    let colors = [];
    for (let i = 0; i < count; i++) {
        let r = Math.floor(Math.random() * 256);
        let g = Math.floor(Math.random() * 256);
        let b = Math.floor(Math.random() * 256);
        colors.push(`rgba(${r}, ${g}, ${b}, 0.6)`);
    }
    return colors;
}
// Esperar a que el DOM esté listo
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_total_users.php", 
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            
            // Verificar si la respuesta tiene el total de usuarios
            if (response.totalUsuarios && typeof response.totalUsuarios === "object") {
                let total = response.totalUsuarios.total ?? 0; // Evitar valores indefinidos
                $("#totalUsuarios").text(total);
            } else {
                console.error("totalUsuarios no encontrado en la respuesta");
            }
            
            // Preparar datos para ApexCharts
            var labels = [];
            var dataValues = [];
            
            jQuery.each(response.data, function(index, value) {
                labels.push(value.Year);
                dataValues.push(value.TotalUsers);
            });

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.usuariosChartInstance) {
                window.usuariosChartInstance.destroy();
            }

            // Configurar ApexCharts
            var options = {
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: { show: true }
                },
                series: [{
                    name: "Total Usuarios",
                    data: dataValues
                }],
                xaxis: {
                    categories: labels,
                    title: { text: "Año" }
                },
                yaxis: {
                    title: { text: "Cantidad de Usuarios" }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                markers: {
                    size: 5,
                    colors: ["#4e73df"],
                    strokeWidth: 2
                },
                tooltip: {
                    theme: "light",
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString(); // Formato de números
                        }
                    }
                },
                colors: ["#4e73df"]
            };

            // Renderizar la gráfica en el elemento con ID 'usuariosChart'
            window.usuariosChartInstance = new ApexCharts(document.querySelector("#usuariosChart"), options);
            window.usuariosChartInstance.render();
        }
    });
});


// Gráfica de total de cursos con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_total_courses.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            // Verificar si la respuesta tiene el total de cursos
            if (response.totalCourses && response.totalCourses.total) {
                jQuery("#totalCursos").text(response.totalCourses.total);
            }

            // Preparar datos para ApexCharts
            var labels = [];
            var dataValues = [];

            jQuery.each(response.data, function(index, value) {
                labels.push(value.Year);
                dataValues.push(Math.round(value.TotalCourses)); // Asegurar valores enteros
            });

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.cursosChartInstance) {
                window.cursosChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico de barras
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: true }
                },
                series: [{
                    name: "Total de Cursos",
                    data: dataValues
                }],
                xaxis: {
                    categories: labels,
                    title: { text: "Año" }
                },
                yaxis: {
                    title: { text: "Cantidad de Cursos" },
                    min: 0
                },
                colors: ["#008FFB"],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toLocaleString(); // Formateo de números
                    }
                },
                tooltip: {
                    theme: "light",
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString(); // Formato de números
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: "60%"
                    }
                }
            };

            // Renderizar la gráfica en el contenedor
            window.cursosChartInstance = new ApexCharts(document.querySelector("#cursosChart"), options);
            window.cursosChartInstance.render();
        }
    });
});


// Gráfica de cursos por categoría con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_total_courses_categories.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            // Actualizar el total de categorías en el HTML
            $('#total_categorias').text(response.total_categories);

            // Preparar datos para ApexCharts
            var labels = [];
            var dataValues = [];

            jQuery.each(response.data, function(index, value) {
                labels.push(value.CategoryName);
                dataValues.push(value.TotalCourses);
            });

            // Generar colores aleatorios para cada categoría
            var colors = generateRandomColors(labels.length);

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.categoriasChartInstance) {
                window.categoriasChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico tipo Doughnut (Pastel)
            var options = {
                chart: {
                    type: 'donut',
                    height: 350,
                    toolbar: { show: true }
                },
                series: dataValues,
                labels: labels,
                colors: colors,
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%"; // Mostrar porcentaje con un decimal
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + " cursos"; // Formato del tooltip
                        }
                    }
                }
            };

            // Renderizar la gráfica en el contenedor
            window.categoriasChartInstance = new ApexCharts(document.querySelector("#categoriasChart"), options);
            window.categoriasChartInstance.render();
        }
    });
});

// Gráfica de cursos demandados (inscripciones) con ApexCharts
// Gráfica de cursos demandados (inscripciones) con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_total_inscripciones.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            if (!response.data || response.data.length === 0) {
                console.warn("No hay datos disponibles para la gráfica de inscripciones.");
                return;
            }

            // Preparar datos para ApexCharts
            var labels = [];
            var dataValues = [];

            jQuery.each(response.data, function(index, value) {
                labels.push(value.CourseName);
                dataValues.push(value.TotalEnrollments);
            });

            console.log("Valores para la gráfica:", dataValues); // Verificar distribución de datos

            // Generar colores aleatorios para cada curso
            var colors = generateRandomColors(labels.length);

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.enrollmentsChartInstance) {
                window.enrollmentsChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico tipo Pie con tooltip corregido
            var options = {
                chart: {
                    type: 'pie',
                    height: 350,
                    toolbar: { show: true }
                },
                series: dataValues,
                labels: labels,
                colors: colors,
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + " usuarios inscritos"; // Formato del tooltip
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return val.toFixed(1) + "%"; // Mostrar porcentaje con un decimal
                    },
                    dropShadow: {
                        enabled: true
                    }
                },
                stroke: {
                    width: 1
                },
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                        donut: {
                            size: "70%"
                        }
                    }
                }
            };

            // Renderizar la gráfica en el contenedor
            window.enrollmentsChartInstance = new ApexCharts(document.querySelector("#inscritosChart"), options);
            window.enrollmentsChartInstance.render();
        }
    });
});
// Gráfica de promedio de cursos con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_top_courses_promedio.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            if (!response.data || response.data.length === 0) {
                console.warn("No hay datos disponibles para la gráfica de promedios.");
                return;
            }

            // Preparar datos para ApexCharts
            var labels = [];
            var dataValues = [];

            jQuery.each(response.data, function(index, value) {
                labels.push(value.fullname);
                dataValues.push(Math.round(value.average)); // ✅ Redondeo al número entero más cercano
            });

            console.log("Valores para la gráfica:", dataValues); // Verificar distribución de datos

            // Generar colores aleatorios para cada barra
            var colors = generateRandomColors(labels.length);

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.promedioChartInstance) {
                window.promedioChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico de barras verticales
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: true }
                },
                series: [{
                    name: "Promedio del curso",
                    data: dataValues
                }],
                xaxis: {
                    categories: labels,
                    title: { text: "Cursos" }
                },
                yaxis: {
                    title: { text: "Promedio" },
                    min: 0
                },
                colors: colors,
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: "60%"
                    }
                }
            };

            // Renderizar la gráfica en el contenedor
            window.promedioChartInstance = new ApexCharts(document.querySelector("#promedioChart"), options);
            window.promedioChartInstance.render();
        }
    });
});
// Gráfica de usuarios activos por curso con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_activity_course.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            if (!response.data || response.data.length === 0) {
                console.warn("No hay datos disponibles para la gráfica de usuarios activos.");
                return;
            }

            // Preparar datos para ApexCharts
            var labels = [];
            var dataValues = [];

            jQuery.each(response.data, function(index, value) {
                labels.push(value.CourseName);  // Nombre del curso
                dataValues.push(value.Activity);  // Usuarios activos
            });

            console.log("Valores para la gráfica:", dataValues); // Verificar distribución de datos

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.activityCoursesChartInstance) {
                window.activityCoursesChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico de línea
            var options = {
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: { show: true }
                },
                series: [{
                    name: "Usuarios activos",
                    data: dataValues
                }],
                xaxis: {
                    categories: labels,
                    title: { text: "Cursos" }
                },
                yaxis: {
                    title: { text: "Cantidad de Usuarios" },
                    min: 0
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                markers: {
                    size: 5,
                    colors: ["#4e73df"],
                    strokeWidth: 2
                },
                colors: ["#4e73df"]
            };

            // Renderizar la gráfica en el contenedor
            window.activityCoursesChartInstance = new ApexCharts(document.querySelector("#actividadcursesChart"), options);
            window.activityCoursesChartInstance.render();
        }
    });
});

// Gráfica de progreso de curso (%) con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_top_progress.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.error("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            if (!response.data || response.data.length === 0) {
                console.warn("No se encontraron datos de progreso.");
                return;
            }

            // Procesar datos para la gráfica
            var labels = [];
            var dataValues = [];

            jQuery.each(response.data, function(index, value) {
                labels.push(value.fullname); // Nombre del curso

                // Extraer el progreso y convertirlo a número eliminando el '%'
                let progressValue = parseFloat(value.pprogress.replace('%', '').trim()) || 0;
                dataValues.push(progressValue);
            });

            console.log("Valores para la gráfica:", dataValues); // Verificar valores

            // Generar colores aleatorios para cada barra
            var colors = generateRandomColors(labels.length);

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.topProgressChartInstance) {
                window.topProgressChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico de barras
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: true }
                },
                series: [{
                    name: "Progreso (%)",
                    data: dataValues
                }],
                xaxis: {
                    categories: labels,
                    title: { text: "Cursos" }
                },
                yaxis: {
                    title: { text: "Progreso (%)" },
                    min: 0,
                    max: 100,
                    labels: {
                        formatter: function(value) {
                            return value + "%"; // Agregar % en el eje Y
                        }
                    }
                },
                colors: colors,
                tooltip: {
                    y: {
                        formatter: function(value, { seriesIndex, w }) {
                            return `${w.globals.labels[seriesIndex]}: ${value}%`;
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: "60%"
                    }
                }
            };

            // Renderizar la gráfica en el contenedor
            window.topProgressChartInstance = new ApexCharts(document.querySelector("#topprogress"), options);
            window.topProgressChartInstance.render();
        }
    });
});

// Gráfica Finalizado vs No Finalizado con ApexCharts
jQuery(document).ready(function() {
    jQuery.ajax({
        url: moodle_Wroot + "/local/dashboard/ajax/get_vs.php",
        type: "GET",
        dataType: "json",
        error: function(xhr, error, thrown) {
            console.log("Error al obtener datos del servidor:", error);
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);

            if (!response.labels_course || response.labels_course.length === 0) {
                console.warn("No hay datos disponibles para la gráfica Finalizado vs No Finalizado.");
                return;
            }

            // Preparar datos para ApexCharts
            var labels = response.labels_course;
            var dataCompleted = response.data_complete;
            var dataNotCompleted = response.data_notcomplete;

            console.log("Valores para la gráfica:", dataCompleted, dataNotCompleted); // Verificar valores

            // Verificar si ya existe una instancia del gráfico y destruirla
            if (window.comparisonChartInstance) {
                window.comparisonChartInstance.destroy();
            }

            // Configurar ApexCharts para gráfico de barras agrupadas
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: true }
                },
                series: [
                    {
                        name: "No Finalizado",
                        data: dataNotCompleted
                    },
                    {
                        name: "Finalizado",
                        data: dataCompleted
                    }
                ],
                xaxis: {
                    categories: labels,
                    title: { text: "Cursos" }
                },
                yaxis: {
                    title: { text: "Usuarios" },
                    min: 0
                },
                colors: ["#A81010", "#1A7204"], // Rojo para no finalizado, verde para finalizado
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "60%",
                        dataLabels: {
                            position: 'top'
                        }
                    }
                }, tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + " usuarios"; // Formato del tooltip
                        }
                    }
                },
            };

            // Renderizar la gráfica en el contenedor
            window.comparisonChartInstance = new ApexCharts(document.querySelector("#chartBarGroupVs"), options);
            window.comparisonChartInstance.render();
        }
    });
});


//obtener usuarios en lienea
$(document).ready(function() {
function obtenerUsuariosEnLinea() {
    $.ajax({
        url: moodle_Wroot+"/local/dashboard/ajax/get_users_enline.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.error) {
                console.log("Error: " + response.error);
                return;
            }
            $("#totalUsuariosEnLinea").text(response.total_online_users);
        },
        error: function(xhr, error, thrown) {
            console.log("Error al obtener usuarios en línea:", error);
        }
    });
}

// Llamar a la función cada 10 segundos
$(document).ready(function() {
    obtenerUsuariosEnLinea();
    setInterval(obtenerUsuariosEnLinea, 10000);
});
});