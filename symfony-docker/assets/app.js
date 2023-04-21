/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
require('bootstrap');

// start the Stimulus application
import './bootstrap';

//importamos css de datepicker
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.css';

//importamos css del multiselect
import 'bootstrap-multiselect/dist/css/bootstrap-multiselect.css';

// Formulario de Calendario
let contador = 0;
$(function() {
    $('.datepicker').datepicker({
        multidate: true,
        format: 'dd-mm-yy',
        language: 'es',
        weekStart: 1,
        startDate: new Date(),
    }).datepicker('setDate', [new Date(2024, 1, 1), new Date(2024, 1, 1)]) // Setear dos fechas
    .on('changeDate', function(e) {
        const fechasTotales = e.dates.length;

        if (fechasTotales > contador) {
            contador++;
            // Obtener la fecha seleccionada
            const fecha = e.date;
            // Formatear la fecha como una cadena
            const fechaStringFormato = formatearFecha(fecha);
            // Crear una nueva fila
            const fila = crearFilaCalendario(fechaStringFormato);
            $('#fechasTable tbody').append(fila);
        } 
    });
});

function formatearFecha(fecha) {
    const fechaString = fecha.toLocaleDateString('es-ES');
    //Dividir la fecha por /
    const fechasPartes = fechaString.split('/');
    //El año lo ponemos a 2 digitos
    fechasPartes[2] = fechasPartes[2].substring(2);
    //Devolvemos la fecha con formato -
    return fechasPartes.join('-');
}

function crearFilaCalendario(fechaStringFormato) {
    return $(`
        <tr id="fecha${fechaStringFormato}">
            <td><input type="text" class="fecha" name="fecha" value="${fechaStringFormato}"></td>
            <td><input type="text" class="form-control nombre" name="nombre" id="nombre${fechaStringFormato}" placeholder="Tema 0: Introducción de la asignatura"></td>
            <td>
                <select class="form-control modalidad" name="modalidad" id="modalidad${fechaStringFormato}">
                    <option>Teorica</option>
                    <option>Practica</option>
                </select>
            </td>
            <td><button class="btn btn-danger eliminar-fecha">Eliminar</button></td>
        </tr>
    `);
}

$(document).on('click', '.eliminar-fecha', function() {
    // Obtener la fila y la fecha seleccionada
    const fila = $(this).closest('tr');
    const fechaStringFormato = fila.find('input[name="fecha"]').val();

    // Crear un objeto Date a partir de la fecha seleccionada
    const fecha = crearFecha(fechaStringFormato);

    // Obtener las fechas actuales del datepicker
    const fechas = $('#datepickerInput').datepicker('getDates');

    // Crear un nuevo array con todas las fechas excepto la fecha seleccionada
    const nuevasFechas = fechas.filter(f => f.toISOString() !== fecha.toISOString());

    // Actualizar las fechas del datepicker
    $('#datepickerInput').datepicker('setDates', nuevasFechas);

    // Si no hay más fechas, limpiar el datepicker
    if (nuevasFechas.length === 0) {
        $('#datepickerInput').datepicker('setDates', '');
    }

    //Disminuimos el contador
    contador--;

    // Eliminar la fila de la tabla
    fila.remove();
});

function crearFecha(fechaStringFormato) {
    const [dia, mes, anio] = fechaStringFormato.split('-').map(Number);
    return new Date(anio + 2000, mes - 1, dia);
}

//Creamos el POST del formulario
$(document).on('click', '.crear-calendario', function() {
    const provincia = $('#nombreDeProvincia').val();
    const centro = $('#nombreDelCentro').val();

    // Obtener los valores de las filas de la tabla
    const clases = [];
    $('#fechasTable tbody tr').each(function() {
        const fecha = $(this).find('.fecha').val();
        const nombre = $(this).find('.nombre').val();
        const modalidad = $(this).find('.modalidad').val();
        clases.push({ fecha, nombre, modalidad });
    });

    // Convertir el objeto a JSON
    const clasesJSON = JSON.stringify(clases);

    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/clase',{clasesJSON: clasesJSON},'http://localhost:8000/calendario?provincia=' + provincia + '&centro=' + centro); //parametros de URL');
});

//Formulario profesor
let idGrupo = 0;
$(document).on('click', '.aniadir-fila-prof', function() {
    idGrupo++;
    const fila = crearFilaGrupo();
    $('#gruposTable tbody').append(fila);
    $(`#diasTeoria${idGrupo}`).multiselect({
        // Compatibilidad con bootstrap 5
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text" style="margin-right: 10px;"></span></button>',
        },
        buttonClass: 'boton-multiselect',
        includeSelectAllOption: true,
        allSelectedText: 'Todo seleccionado',
        nonSelectedText: 'Ningun día seleccionado',
        nSelectedText: 'Dias seleccionados'
    });
    $(`#diasPractica${idGrupo}`).multiselect({
        // Compatibilidad con bootstrap 5
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text" style="margin-right: 10px;"></span></button>',
        },
        buttonClass: 'boton-multiselect',
        includeSelectAllOption: true,
        allSelectedText: 'Todo seleccionado',
        nonSelectedText: 'Ningun día seleccionado',
        nSelectedText: 'Dias seleccionados'
    });
});

function crearFilaGrupo() {
    var diasSemana = `<option>Lunes</option>
    <option>Martes</option>
    <option>Miercoles</option>
    <option>Jueves</option>
    <option>Viernes</option>`;

    var options = '';
    //Obtenemos las asignaturas del template de formulario/profesor
    const asignaturas = JSON.parse(decodeURIComponent(document.getElementById('asignaturas').dataset.asignaturas));
    //Los recorremos y agregamos las opciones
    for (var i = 0; i < asignaturas.length; i++) {
        options += `<option>${asignaturas[i]}</option>`;
    }
    return $(`
        <tr id="grupo${idGrupo}">
            <td><input type="text" class="form-control grupo" name="grupo" id="grupo${idGrupo}"></td>
            <td>
                <select type="text" class="form-control asignatura" name="asignatura" id="asignatura${idGrupo}">
                ${options}
                </select>
            </td>
            <td>
                <select class="form-control horario" name="horario" id="horario${idGrupo}">
                    <option>Mañana</option>
                    <option>Tarde</option>
                </select>
            </td>
            <td>
                <select class="form-control diasTeoria" name="diasTeoria" id="diasTeoria${idGrupo}" multiple="multiple">
                    ${diasSemana}
                </select>
            </td>
            <td>
                <select class="form-control diasPractica" name="diasPractica" id="diasPractica${idGrupo}" multiple="multiple">
                    ${diasSemana}
                </select>
            </td>
            <td><button class="btn btn-danger eliminar-grupo">Eliminar</button></td>
        </tr>
    `);
}

$(document).on('click', '.eliminar-grupo', function() {
    // Obtener la fila
    const fila = $(this).closest('tr');
    fila.remove();
});

//Creamos el POST del formulario
$(document).on('click', '.crear-profesor', function() {
    const profesor = [];
    const nombre = $('#nombreProf').val();
    const primerapellido = $('#papellidoProf').val();
    const segundoapellido = $('#sapellidoProf').val();
    const despacho = $('#aula').val();
    const correo = $('#correo').val();

    profesor.push({nombre,primerapellido,segundoapellido,despacho,correo});

    const grupo = [];
    // Obtener los valores de las filas de la tabla
    $('#gruposTable tbody tr').each(function() {
        const letra = $(this).find('.grupo').val();
        const asignaturaNombre = $(this).find('.asignatura').val();
        const horario = $(this).find('.horario').val();
        const diasTeoria = $(this).find('.diasTeoria').val();
        const diasPractica = $(this).find('.diasPractica').val();

        grupo.push({letra, asignaturaNombre, diasTeoria, diasPractica, horario});
    });

    const datos = {
        profesor: profesor,
        grupos: grupo
    };
    const profesorGrupoJSON = JSON.stringify(datos);

    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/docente',{profesorGrupoJSON: profesorGrupoJSON},'http://localhost:8000/post/docente');
});

//Formulario Asignatura
let idAsignatura = 0;
$(document).on('click', '.aniadir-fila-asig', function() {
    idAsignatura++;
    const fila = crearFilaAsignatura();
    // Buscamos la última fila de asignatura en la tabla
    const ultimaFilaAsignatura = $('#asignaturasTable tbody .fila-asignatura').last();
    // Buscamos si hay una tabla de lecciones después de la última fila de asignatura
    const tablaLecciones = ultimaFilaAsignatura.next('div');
    // Si hay una tabla de lecciones después de la última fila de asignatura
    if (tablaLecciones.length) {
        // Agregamos la nueva fila después de esta tabla
        tablaLecciones.after(fila);
    // Si no hay una tabla de lecciones después de la última fila de asignatura pero sí hay una última fila de asignatura
    } else if (ultimaFilaAsignatura.length) {
        // Agregamos la nueva fila después de esta última fila de asignatura
        ultimaFilaAsignatura.after(fila);
    } else {
        // Si no hay ninguna fila, la agregamos
        $('#asignaturasTable tbody').append(fila);
    }
});

function crearFilaAsignatura() {
    return $(`
        <tr class="fila-asignatura" id="asignatura${idAsignatura}">
            <td><input type="text" class="form-control nombreAsig" name="nombreAsig" id="nombreAsignatura${idAsignatura}"></td>
            <td><button class="btn btn-primary aniadir-lecciones" data-id="${idAsignatura}">Añadir lecciones</button></td>
            <td><input type="number" class="form-control numLecciones" name="numLecc" id="numLecciones${idAsignatura}" value="1"></td>
            <td><button class="btn btn-danger eliminar-asignatura">Eliminar</button></td>
        </tr>
    `);
}

let idLeccion = 0;
let idLeccionTabla = 0;
$(document).on('click', '.aniadir-lecciones', function(event) {
    event.preventDefault();
    const asignaturaId = $(this).data('id');
    const tabla = crearTablaLeccion(asignaturaId);
    $(`#asignatura${asignaturaId}`).after(tabla);
});

function crearTablaLeccion(asignaturaId) {
    var filas = '';
    idLeccionTabla++;
    let tema = 0;
    //Agregamos el numero de filas
    for (var i = 0; i < $($(`#numLecciones${asignaturaId}`)).val(); i++) {
        tema++;
        idLeccion++;
        filas += `<tr id="tabla${idLeccionTabla}leccion${idLeccion}">
        <td><input type="text" class="form-control tituloLecc" name="tituloLecc" id="tituloLeccion${idLeccion}" value="Tema ${tema}"</td>
        <td><button class="btn btn-danger eliminar-leccion">Eliminar</button></td>
        </tr>`;
    }

    return $(`
        <div id="leccionesTabla${idLeccionTabla}">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título de lección</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    ${filas}
                </tbody>
            </table>
        </div>
    `);
}

$(document).on('click', '.eliminar-leccion', function() {
    // Obtener la fila
    const fila = $(this).closest('tr');
    // Obtener la tabla
    const tabla = fila.closest('div');
    // Eliminar la fila
    fila.remove();
    // Verificar si quedan filas en la tabla
    if (tabla.find('tbody tr').length === 0) {
        // Si no quedan filas, eliminar la tabla
        tabla.remove();
    }
});

$(document).on('click', '.eliminar-asignatura', function() {
    // Obtener la fila
    const fila = $(this).closest('tr');
    fila.remove();
});

//Creamos el POST del formulario
$(document).on('click', '.crear-asignatura', function() {
    // Obtener los valores de las filas de la tabla
    const asignaturas = [];
    $('#asignaturasTable tbody tr').each(function() {
        const nombre = $(this).find('.nombreAsig').val();
        asignaturas.push({ nombre });
    });

    // Convertir el objeto a JSON
    const asignaturasJSON = JSON.stringify(asignaturas);
    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/asignatura',{asignaturasJSON: asignaturasJSON},'http://localhost:8000/post/asignatura');

});

function enviarPost(url, data, href) {
    $.ajax({
        url: url, // ruta donde enviar la petición POST
        type: 'POST',
        data: data, // los datos a enviar, en este caso el objeto JSON
        success: function(response) {
            console.log(response); // loguear la respuesta del servidor (opcional)
            window.location.replace(href);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown); // loguear el error (opcional)
        }
    });
}