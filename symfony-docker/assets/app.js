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

$(function() {
    let contador = 0;
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
            const fechaString = fecha.toLocaleDateString('es-ES');
            // Darle formato
            const fechaStringFormato = fechaString.split('/').join('-');
            // Crear una nueva fila de entrada
            const fila = $('<tr id="fecha' + fechaStringFormato + 
            '"><td><input type="text" value="' + fechaStringFormato + 
            '"></td><td><button class="btn btn-danger eliminar-fecha">Eliminar</button></td></tr>'
            );
            // Agregar la nueva fila
            $('#fechasTable tbody').append(fila);
        }
    });
});

$(document).on('click', '.eliminar-fecha', function() {
    const nuevasFechas = [];

    const fila = $(this).closest('tr');
    const fechaStringFormato = fila.find('input[type="text"]').val();
    const [dia, mes, anio] = fechaStringFormato.split('-').map(Number);

    const fecha = new Date(anio, mes - 1, dia);
    const fechas = $('#datepickerInput').datepicker('getDates');

    for (const f of fechas) {
        if (f.toISOString() !== fecha.toISOString()) {
            nuevasFechas.push(f);
        }
    }
    $('#datepickerInput').datepicker('setDates', nuevasFechas);
    if (nuevasFechas.length === 0) {
        $('#datepickerInput').datepicker('setDates', '');
    }
    console.log(nuevasFechas.length);
    fila.remove();
});