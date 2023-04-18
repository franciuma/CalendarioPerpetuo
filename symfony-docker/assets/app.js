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

// Formulario de Calendario
let contador = 0;
$(function() {
    $('.datepicker').datepicker({
        multidate: true,
        format: 'dd-mm-yy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    }).on('changeDate', function(e) {
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
});

function crearFilaGrupo() {
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
        grupo.push({letra,asignaturaNombre});
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
    $('#asignaturasTable tbody').append(fila);
});

function crearFilaAsignatura() {
    return $(`
        <tr id="asignatura${idAsignatura}">
            <td><input type="text" class="form-control nombreAsig" name="nombreAsig" id="nombreAsignatura${idAsignatura}"</td>
            <td><button class="btn btn-danger eliminar-asignatura">Eliminar</button></td>
        </tr>
    `);
}

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