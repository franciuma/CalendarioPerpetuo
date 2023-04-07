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

let contador = 0;
$(function() {
    $('.datepicker').datepicker({
        multidate: true,
        format: 'dd-mm-yyyy',
        language: 'es',
        weekStart: 1
    }).on('changeDate', function(e) {
        const fechasTotales = e.dates.length;

        if (fechasTotales > contador) {
            contador++;
            // Obtener la fecha seleccionada
            const fecha = e.date;
            // Formatear la fecha como una cadena
            const fechaStringFormato = formatearFecha(fecha);
            // Crear una nueva fila
            const fila = crearFila(fechaStringFormato);
            $('#fechasTable tbody').append(fila);
        } 
        //meter que si se da boton eliminar, se baje el contador 1.
    });
});

function formatearFecha(fecha) {
    const fechaString = fecha.toLocaleDateString('es-ES');
    return fechaString.split('/').join('-');
}

function crearFila(fechaStringFormato) {
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
    const fechaStringFormato = fila.find('input[type="text"]').val();

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
    return new Date(anio, mes - 1, dia);
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
    $.ajax({
        url: 'http://localhost:8000/formulario', // ruta donde enviar la petición POST
        type: 'POST',
        data: {
            clasesJSON: clasesJSON,
            provincia: provincia,
            centro: centro
        }, // los datos a enviar, en este caso el objeto JSON
        success: function(response) {
            console.log(response); // loguear la respuesta del servidor (opcional)
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown); // loguear el error (opcional)
        }
    });
});
