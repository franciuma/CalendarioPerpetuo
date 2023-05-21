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
let contadorFechas = 0;
//Array clave: valor con fecha: asignaturaId
const arrayFechaAsignatura = [];
const mapFechaGrupo = new Map();
//Si se encuentra en la vista /formulario/calendario carga la función
if(window.location.pathname == "/formulario/calendario"){
    $(function() {
        //Obtenemos del formulario de calendario las entidades
        const lecciones = JSON.parse(document.getElementById('lecciones').dataset.lecciones);
        const grupos = JSON.parse(document.getElementById('grupos').dataset.grupos);
        //Devuelve un array con todos los festivos
        const arrayFestivos = calcularFestivos();

        //Fechas del primer cuatrimestre
        const inicioPrimerCuatri = calcularFechaCalendario("primer cuatrimestre");
        const fechaFinPrimerCuatri = calcularFechaCalendario("exámenes finales primer cuatrimestre"); 

        //Fechas del segundo cuatrimestre
        const inicioSegundoCuatri = calcularFechaCalendario("segundo cuatrimestre");
        const fechaFinSegundoCuatri = calcularFechaCalendario("exámenes finales segundo cuatrimestre");

        //Recorrer los grupo
        grupos.forEach(function(grupo) {
            if(grupo.cuatrimestre == "Primero"){
                completaCuatrimestre(inicioPrimerCuatri, fechaFinPrimerCuatri, grupo);
            } else {
                completaCuatrimestre(inicioSegundoCuatri, fechaFinSegundoCuatri, grupo);
            }
        });

        function completaCuatrimestre(fechaInicio, fechaFin, grupo) {
            let diasTeoria = grupo.diasTeoria;
            let diasPractica = grupo.diasPractica;
            let fechaActual = new Date(fechaInicio);
            let grupoAsignaturaId = grupo.asignaturaId;
            //Filtramos lecciones por asignaturaId
            const leccionesFiltradas = lecciones.filter(function(leccion) {
                return leccion.asignaturaId === grupoAsignaturaId;
            });

            //Filtramos lecciones por teoria y practica
            const leccionesFiltradasTeoria = leccionesFiltradas.filter(function(leccion) {
                return leccion.modalidad === "Teorica";
            });

            const leccionesFiltradasPractica = leccionesFiltradas.filter(function(leccion) {
                return leccion.modalidad === "Practica";
            });

            //Creamos un array de días de la semana
            const diasSemana = {
                0: "Domingo",
                1: "Lunes",
                2: "Martes",
                3: "Miércoles",
                4: "Jueves",
                5: "Viernes",
                6: "Sábado"
            };

            let contLeccTeoria = 0;
            let contLeccPractica = 0;

            while (fechaActual <= fechaFin) {
                // Si coinciden los dias teoria, además no están completas y no es festivo, se incluye.
                if (diasTeoria.includes(diasSemana[fechaActual.getDay()]) 
                    && contLeccTeoria != leccionesFiltradasTeoria.length
                    && !esFestivo(fechaActual, arrayFestivos)
                    ) {
                    //Se incluye la fecha actual en formato Date para el setDates de datepicker
                    arrayFechaAsignatura.push({fecha: new Date(fechaActual), asignaturaId: grupoAsignaturaId});
                    //Formateamos la fecha para incluirla en el map
                    let fechaFormateada = formatearFecha(new Date(fechaActual));
                    // Incluimos en el map la entidad grupo y un valor esPractica: false
                    mapFechaGrupo.set(fechaFormateada+grupoAsignaturaId, {
                        ...grupo,
                        esPractica: false,
                        tituloSesion: leccionesFiltradasTeoria[contLeccTeoria].titulo
                    });
                    contLeccTeoria++;
                }
                // Si se incluyen dias practica, se añaden al array
                if (diasPractica.includes(diasSemana[fechaActual.getDay()])
                    && contLeccPractica != leccionesFiltradasPractica.length
                    && !esFestivo(fechaActual, arrayFestivos)
                    ) {
                    //Se incluye la fecha actual en formato Date para el setDates de datepicker
                    arrayFechaAsignatura.push({fecha: new Date(fechaActual), asignaturaId: grupoAsignaturaId});
                    //Formateamos la fecha para incluirla en el map
                    let fechaFormateada = formatearFecha(new Date(fechaActual));
                    // Incluimos en el map la entidad grupo y un valor esPractica: true
                    mapFechaGrupo.set(fechaFormateada+grupoAsignaturaId, {
                        ...grupo,
                        esPractica: true,
                        tituloSesion: leccionesFiltradasPractica[contLeccPractica].titulo
                    });
                    contLeccPractica++;
                }
                // Se actualiza la fecha actual
                fechaActual.setDate(fechaActual.getDate() + 1);
            }
        }

        function fechas_deshabilitadas(fecha) {
            if( esFestivo(fecha, arrayFestivos) ) {
                return false;
            } else {
                return true;
            }
        }

        //Manejamos el datepicker 
        $('.datepicker').datepicker({
            multidate: true,
            format: 'dd-mm-yy',
            language: 'es',
            weekStart: 1,
            startDate: new Date(),
            beforeShowDay: fechas_deshabilitadas,
        }).datepicker(
            //Establecemos las fechas de los grupos
            'setDate', arrayFechaAsignatura.map(indice => indice.fecha)
            );

        //Creamos una fila por cada fecha
        Object.keys(arrayFechaAsignatura).forEach(function(indice) {
            const fechaAsignatura = arrayFechaAsignatura[indice];
            const fecha = fechaAsignatura.fecha;
            const asignaturaId = fechaAsignatura.asignaturaId;
            const fechaStringFormato = formatearFecha(fecha); 
            contadorFechas++;
            const fila = crearFilaCalendario(fechaStringFormato, asignaturaId);
            $('#fechasTable tbody').append(fila);
        });

        $('.datepicker').on('changeDate', function(e) {
        const fechasTotales = e.dates.length;
            if (fechasTotales > contadorFechas) {
                contadorFechas++;
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

    function esFestivo(fechaActual, festivos) {
        return festivos.some(fecha => (
            fechaActual.getFullYear() === fecha.getFullYear() &&
            fechaActual.getMonth() === fecha.getMonth() &&
            fechaActual.getDate() === fecha.getDate()
        ));
    }
}

function calcularFechaCalendario(nombreFestivo) {
    const festivosCentro = JSON.parse(document.getElementById('festivosCentro').dataset.festivoscentro);
    const centro = document.getElementById('centro').dataset.centro;

    //Filtramos para buscar el nombreFestivo que se requiere
    const festivosCentroFiltrado = festivosCentro.filter(function(festivoCentro) {
        return festivoCentro.nombreFestivo == nombreFestivo
                && festivoCentro.nombreCentro == centro;
    });

    let claseDia;
    if(nombreFestivo.includes("finales")) {
        claseDia = crearFecha(festivosCentroFiltrado[0].final);
    } else {
        claseDia = crearFecha(festivosCentroFiltrado[0].inicio);
    }

    return claseDia;
}

function calcularFestivos() {
    //Obtenemos los festivos
    const festivosNacionales = JSON.parse(document.getElementById('festivosNacionales').dataset.festivosnacionales);
    const festivosLocales = JSON.parse(document.getElementById('festivosLocales').dataset.festivoslocales);
    const festivosCentro = JSON.parse(document.getElementById('festivosCentro').dataset.festivoscentro);
    const centro = document.getElementById('centro').dataset.centro;
    const provincia = document.getElementById('provincia').dataset.provincia;

    //Declaramos el array a devolver, el cual tendrá simplemente un array de todas las fechas festivas
    const festivos = [];

    //Obtenemos los festivosLocales en base a la provincia proporcionada
    const festivosLocalesFiltrado = festivosLocales.filter(function(festivoLocal) {
        return festivoLocal.provincia == provincia;
    });

    //Obtenemos los festivosCentro en base al centro proporcionado
    const festivosCentroFiltrado = festivosCentro.filter(function(festivoCentro) {
        return festivoCentro.nombreCentro == centro
        && !(festivoCentro.nombreFestivo).includes("cuatrimestre");
    });

    completaArrayFestivos(festivosNacionales, festivos);
    completaArrayFestivos(festivosLocalesFiltrado, festivos);
    completaArrayFestivos(festivosCentroFiltrado, festivos);

    return festivos;
}

function completaArrayFestivos(arrayFestivo, festivos) {
    //Recorremos los festivos nacionales
    arrayFestivo.forEach(function(festivo) {
        if(festivo.inicio == festivo.final) {
            festivos.push(crearFecha(festivo.inicio));
        } else {
            creaFestivosIntermedios(festivo.inicio, festivo.final, festivos);
        }
    });
}

function creaFestivosIntermedios(inicio, final, festivos) {
    let fechaActual = crearFecha(inicio);
    let fechaFin = crearFecha(final);
    
    while (fechaActual <= fechaFin) {
        festivos.push(new Date(fechaActual));
        fechaActual.setDate(fechaActual.getDate() + 1);
    }
}

//Pasa la fecha a formato "normal", como 12-11-23
function formatearFecha(fecha) {
    const fechaString = fecha.toLocaleDateString('es-ES');
    //Dividir la fecha por /
    const fechasPartes = fechaString.split('/');
    //El año lo ponemos a 2 digitos
    fechasPartes[2] = fechasPartes[2].substring(2);
    //Devolvemos la fecha con formato -
    return fechasPartes.join('-');
}

function crearFilaCalendario(fechaStringFormato, asignaturaId) {
    const clave = fechaStringFormato+asignaturaId;
    const asignaturas = obtenerAsignaturasSelect();
    const grupos = obtenerGrupoSelect();
    let esPractica = "";
    let asignatura = "";
    let inactivo = "";
    let grupo = "";
    let tituloSesion = "";
    if (mapFechaGrupo.has(clave)) {
        esPractica = mapFechaGrupo.get(clave).esPractica;
        asignatura = mapFechaGrupo.get(clave).asignatura;
        grupo = mapFechaGrupo.get(clave).letra;
        tituloSesion = mapFechaGrupo.get(clave).tituloSesion;
        // Si las fechas tienen un map asociado, ya estarán colocadas en el calendario. Estas serán inamovibles.
        inactivo = "disabled";
    }

    return $(`
        <tr id="fecha${fechaStringFormato}">
            <td><input type="text" class="fecha" name="fecha" value="${fechaStringFormato}" disabled></td>
            <td><input type="text" class="form-control nombre" name="nombre" id="nombre${fechaStringFormato}" value="${tituloSesion}"></td>
            <td>
                <select ${inactivo} type="text" class="form-control asignaturaCalendario" name="asignaturaCalendario" id="asignatura${fechaStringFormato}">
                    <option selected>${asignatura}</option>
                    ${asignaturas}
                </select>
            </td>
            <td>
                <select ${inactivo} type="text" class="form-control grupoCalendario" name="grupoCalendario" id="grupo${fechaStringFormato}">
                    <option selected>${grupo}</option>
                    ${grupos}
                </select>
            </td>
            <td>
                <select ${inactivo} class="form-control modalidad" name="modalidad" id="modalidad${fechaStringFormato}">
                    <option>Teorica</option>
                    <option ${esPractica ? "selected" : "Teoria"}>Practica</option>
                </select>
            </td>
            <td><button data-asignatura-id="${asignaturaId}" class="btn btn-primary permutar-fecha">Permutar</button></td>
            <td><button class="btn btn-danger eliminar-fecha">Eliminar</button></td>
        </tr>
    `);
}

function obtenerGrupoSelect(){
    //Obtenemos las letras de los grupos del template de formulario/calendario
    const grupos = JSON.parse(document.getElementById('grupos').dataset.grupos);
    let options = "";
    //Los recorremos y agregamos las opciones
    for (var i = 0; i < grupos.length; i++) {
        options += `<option>${grupos[i].letra}</option>`;
    }

    return options;
}

// Permutación de dos clases en previsualización de calendario
$(document).on('click', '.permutar-fecha', function(event) {
    event.preventDefault();
    // Obtener la fila y la fecha seleccionada
    const filaInicial = $(this).closest('tr');
    const asignaturaIdInicial = $(this).data('asignatura-id');
    const fechaInicial = filaInicial.find('input[name="fecha"]').val();
    const claveInicial = fechaInicial+asignaturaIdInicial;
    let valorInicialMap;
    //Buscamos el mapa inicial asociado
    if (mapFechaGrupo.has(claveInicial)) {
        valorInicialMap = mapFechaGrupo.get(claveInicial);
        mapFechaGrupo.delete(claveInicial);
    }

    $('.permutar-fecha').removeClass('permutar-fecha').addClass('destino-permutar-fecha').text('Destino permutación');
    //Quitamos los datos que tengamos de destino-permutar-fecha
    $(document).off('click', '.destino-permutar-fecha');

    $(document).on('click', '.destino-permutar-fecha', function(event) {
        event.preventDefault();
        // Obtener la fila y la fecha seleccionada
        const filaDestino = $(this).closest('tr');
        const asignaturaIdDestino = $(this).data('asignatura-id');
        const fechaDestino = filaDestino.find('input[name="fecha"]').val();
        const claveDestino = fechaDestino+asignaturaIdDestino;
        let valorDestinoMap;
        //Buscamos el mapa asociado
        if (mapFechaGrupo.has(claveDestino)) {
            valorDestinoMap = mapFechaGrupo.get(claveDestino);
            mapFechaGrupo.delete(claveDestino);
        }
        //Cambiamos los mapas inicial y destino
        mapFechaGrupo.set(claveDestino,valorInicialMap);
        mapFechaGrupo.set(claveInicial,valorDestinoMap);
        //Creamos las filas con los datos cambiados
        const fechaNuevaInicial = crearFilaCalendario(fechaInicial, asignaturaIdInicial);
        const fechaNuevaFinal = crearFilaCalendario(fechaDestino, asignaturaIdDestino);
        //Añadimos a la tabla las nuevas filas
        $('#fechasTable tbody').append(fechaNuevaInicial);
        $('#fechasTable tbody').append(fechaNuevaFinal);

        //Borramos las filas antiguas
        filaInicial.remove();
        filaDestino.remove();
        $('.destino-permutar-fecha').removeClass('destino-permutar-fecha').addClass('permutar-fecha').text('Permutar');

        // Mostrar el popup de permutación exitosa
        Swal.fire({
            title: 'Permutación exitosa',
            text: 'Las clases han sido agregadas al final de la página',
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#007BFF'
        });
    });
});

$(document).on('click', '.eliminar-fecha', function() {
    // Obtener la fila y la fecha seleccionada
    const fila = $(this).closest('tr');
    const fechaStringFormato = fila.find('input[name="fecha"]').val();

    // Crear un objeto Date a partir de la fecha seleccionada
    const fecha = crearFecha(fechaStringFormato);

    // Obtener las fechas actuales del datepicker
    const fechas = $('#datepickerInput').datepicker('getDates');

    // Crear un nuevo array con todas las fechas excepto la fecha seleccionada
    const nuevasFechas = fechas.filter(f => {
        // Comparar la fecha (año, mes y día)
        return f.getFullYear() !== fecha.getFullYear() ||
            f.getMonth() !== fecha.getMonth() ||
            f.getDate() !== fecha.getDate();
    });

    // Actualizar las fechas del datepicker
    $('#datepickerInput').datepicker('setDates', nuevasFechas);

    // Si no hay más fechas, limpiar el datepicker
    if (nuevasFechas.length === 0) {
        $('#datepickerInput').datepicker('setDates', '');
    }

    //Disminuimos el contador
    contadorFechas--;

    // Eliminar la fila de la tabla
    fila.remove();
});

//Pasa una fecha a tipo Date
function crearFecha(fechaStringFormato) {
    const [dia, mes, anio] = fechaStringFormato.split('-').map(Number);
    return new Date(anio + 2000, mes - 1, dia);
}

//Creamos el POST del formulario
$(document).on('click', '.crear-calendario', function() {
    const nombre = $('#nombreDelCentro').val();

    // Obtener los valores de las filas de la tabla
    const clases = [];
    $('#fechasTable tbody tr').each(function() {
        const fecha = $(this).find('.fecha').val();
        const nombre = $(this).find('.nombre').val();
        const modalidad = $(this).find('.modalidad').val();
        const asignaturaNombre = $(this).find('.asignaturaCalendario').val();
        clases.push({ fecha, nombre, modalidad, asignaturaNombre });
    });

    // Convertir el objeto a JSON
    const clasesJSON = JSON.stringify(clases);

    //Obtenemos el nombre del profesor via localStorage
    //Meter un if a nombreProfesor para que si no es profesor sea alumno.
    const nombreProfesor = localStorage.getItem('profesor');
    const provincia = localStorage.getItem('provincia');
    const centro = localStorage.getItem('centro');

    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/clase',{clasesJSON: clasesJSON},'/calendario?provincia='+ provincia + '&usuario='+ nombreProfesor + '&centro=' + centro); //parametros de URL
});

//LEER un calendario
$(document).on('click', '.ver-calendario', function() {
    const nombreProfesor = $('#nombreleerProfesor').val();
    // Enviar el objeto JSON a través de una petición AJAX
    window.location.replace('/calendario?usuario=' + nombreProfesor);
});

//Formulario profesor
let idGrupo = 0;
$(document).on('click', '.aniadir-fila-prof', function() {
    idGrupo++;
    const fila = crearFilaGrupo();
    $('#gruposTable tbody').append(fila);
    const multiselectConfig = {
        // Compatibilidad con bootstrap 5
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text" style="margin-right: 10px;"></span></button>',
        },
        buttonClass: 'boton-multiselect',
        selectAllText: 'Selecciona todo',
        includeSelectAllOption: true,
        allSelectedText: 'Todo seleccionado',
        nonSelectedText: 'Ningun día seleccionado',
        nSelectedText: 'Dias seleccionados'
    }

    $(`#diasTeoria${idGrupo}`).multiselect(multiselectConfig);
    $(`#diasPractica${idGrupo}`).multiselect(multiselectConfig);
});

function crearFilaGrupo() {
    var diasSemana = `<option>Lunes</option>
    <option>Martes</option>
    <option>Miércoles</option>
    <option>Jueves</option>
    <option>Viernes</option>`;

    const asignaturasOptions = obtenerAsignaturasSelect();

    return $(`
        <tr id="grupo${idGrupo}">
            <td><input type="text" class="form-control grupo" name="grupo" id="grupo${idGrupo}"></td>
            <td>
                <select type="text" class="form-control asignatura" name="asignatura" id="asignatura${idGrupo}">
                ${asignaturasOptions}
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

function obtenerAsignaturasSelect(){
    //Obtenemos las asignaturas del template de formulario/profesor
    const asignaturas = JSON.parse(decodeURIComponent(document.getElementById('asignaturas').dataset.asignaturas));
    let options = "";
    //Los recorremos y agregamos las opciones
    for (var i = 0; i < asignaturas.length; i++) {
        options += `<option>${asignaturas[i]}</option>`;
    }

    return options;
}

//Creamos el POST del formulario
$(document).on('click', '.crear-profesor', function() {
    const profesor = [];
    const nombre = $('#nombreProf').val();
    const primerapellido = $('#papellidoProf').val();
    const segundoapellido = $('#sapellidoProf').val();
    const despacho = $('#despacho').val();
    const correo = $('#correo').val();
    const tipo = 'Profesor';

    profesor.push({nombre, primerapellido, segundoapellido, despacho, correo, tipo});

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
    enviarPost('/manejar/posts/docente',{profesorGrupoJSON: profesorGrupoJSON},'/post/docente');
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
            <td><input type="number" class="form-control numLecciones" name="numLeccTeor" id="numLeccionesTeor${idAsignatura}" value="1"></td>
            <td><input type="number" class="form-control numLecciones" name="numLeccPrac" id="numLeccionesPrac${idAsignatura}" value="1"></td>
            <td><select class="form-control cuatrimestre" name="cuatrimestre" id="cuatrimestre${idAsignatura}">
            <option>Primero</option>
            <option>Segundo</option>
            </select>
            </td>
            <td><input type="text" class="form-control ntitulacion" name="ntitulacion" id="ntitulacion${idAsignatura}"></td>
            <td><button class="btn btn-primary aniadir-lecciones" data-id="${idAsignatura}">Añadir sesiones</button></td>
            <td><button class="btn btn-danger eliminar-asignatura">Eliminar</button></td>
        </tr>
    `);
}

let idLeccion = 0;
$(document).on('click', '.aniadir-lecciones', function(event) {
    event.preventDefault();
    const asignaturaId = $(this).data('id');
    const tabla = crearTablaLeccion(asignaturaId);
    $(`#asignatura${asignaturaId}`).after(tabla);
});

function crearTablaLeccion(asignaturaId) {
    var filas = '';
    let sesionTeoria = 0;
    let sesionPractica = 0;
    // Obtenemos los valores de leccionesPracticas y teoricas
    const numLeccionesTeoricas = $($(`#numLeccionesTeor${asignaturaId}`)).val();
    const numLeccionesPracticas = $($(`#numLeccionesPrac${asignaturaId}`)).val();
    //Agregamos el numero de filas sesion teorica
    for (var i = 0; i < numLeccionesTeoricas; i++) {
        sesionTeoria++;
        idLeccion++;
        filas += `<tr id="tabla${asignaturaId}leccion${idLeccion}" class="fila-leccion">
        <td><input type="text" class="form-control tituloLecc" name="tituloLecc" id="tituloLeccion${idLeccion}" value="Sesión teórica ${sesionTeoria}"></td>
        <td><select class="form-control modalidad" name="modalidad" id="modalidad${idLeccion}">
            <option>Teorica</option>
        </select></td>
        <td><button class="btn btn-danger eliminar-leccion">Eliminar</button></td>
        </tr>`;
    }

    //Agregamos el numero de filas sesionPractica
    for (var i = 0; i < numLeccionesPracticas; i++) {
        sesionPractica++;
        idLeccion++;
        filas += `<tr id="tabla${asignaturaId}leccion${idLeccion}" class="fila-leccion">
        <td><input type="text" class="form-control tituloLecc" name="tituloLecc" id="tituloLeccion${idLeccion}" value="Sesión práctica ${sesionPractica}"></td>
        <td>
        <select class="form-control modalidad" name="modalidad" id="modalidad${idLeccion}">
            <option>Practica</option>
        </select>
        </td>
        <td><button class="btn btn-danger eliminar-leccion">Eliminar</button></td>
        </tr>`;
    }

    idLeccion = 0;

    return $(`
        <div id="leccionesTabla${asignaturaId}">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título de lección</th>
                        <th>Modalidad</th>
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

//Creamos el POST del formulario
$(document).on('click', '.crear-asignatura', function() {

    // Obtener los valores de las filas de la tabla
    const asignaturas = [];
    let lecciones = [];
    $('#asignaturasTable tbody tr[id^="asignatura"]').each(function() {
        const nombre = $(this).find('.nombreAsig').val();
        const nombreTitulacion = $(this).find('.ntitulacion').val();
        const cuatrimestre = $(this).find('.cuatrimestre').val();
        $(this).next('div').find('.fila-leccion').each(function() {
            const titulo = $(this).find('.tituloLecc').val();
            const modalidad = $(this).find('.modalidad').val();
            lecciones.push({ titulo, modalidad })
        });
        asignaturas.push({ nombre, nombreTitulacion, cuatrimestre, lecciones });
        lecciones = [];
    });

    // Convertir el objeto a JSON
    const asignaturasJSON = JSON.stringify(asignaturas);
    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/asignatura',{asignaturasJSON: asignaturasJSON},'/post/asignatura');

});

//Formulario centro

$(document).on('click', '.previsualizar-calendario, .editar-calendario', function() {
    const centro = [];
    const nombreProvincia = $('#nombreDelCentroProvincia').val();
    const partesNombreProvincia = nombreProvincia.split('-');
    const nombre = partesNombreProvincia[0];
    const provincia = partesNombreProvincia[1];
    const profesor = $('#nombreDelProfesor').val();
    //Guardamos la variable en localStorage
    localStorage.setItem('provincia', provincia);
    localStorage.setItem('centro',nombre);
    localStorage.setItem('profesor',profesor);

    centro.push({nombre, provincia, profesor});

    const centroJSON = JSON.stringify({centro});

    if(provincia && nombre){
        //Poner en un futuro que el centro se cree con el admin y no en el post de centro
        enviarPost('/manejar/posts/centro',{centroJSON: centroJSON}, '/post/centro');
    } else {
        //Si no existen provincia y nombre es que están editando un calendario.
        enviarPost('/manejar/posts/centro',{centroJSON: centroJSON}, '/formulario/calendario');
    }
});

//Formulario festivos de centro admin
// Datepicker de festivosCentro
$('#festivosCentroTable tbody').on('focus', '.datepicker-festivo-centro', function() {
    $(this).datepicker({
        format: 'dd-mm-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

$(document).on('click', '.seleccionar-festivos-centro', function() {
    // Mostrar el popup de centro seleccionado
    Swal.fire({
        title: 'Centro seleccionado',
        text: 'Ya puedes ver los festivos asociados al centro',
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#007BFF'
    });
});

let idFestivoCentro = 0;
$(document).on('click', '.aniadir-festivos-centro', function() {
    idFestivoCentro++;
    const fila = crearFilaFestivo(idFestivoCentro, "centro");
    $('#festivosCentroTable tbody').append(fila);
});

function crearFilaFestivo(idFestivo, tipoDeFestivo) {
    return $(`
        <tr class="fila-festivo-${tipoDeFestivo}" id="festivoCentro${idFestivo}">
            <td><input type="text" class="form-control nombreFestivo${tipoDeFestivo}" name="nombreFestivo${tipoDeFestivo}" id="nombreFestivo${tipoDeFestivo}${idFestivo}"></td>
            <td><input type="text" class="form-control inicioFestivo${tipoDeFestivo} datepicker-festivo-${tipoDeFestivo}" name="inicioFestivo${tipoDeFestivo}" id="inicioFestivo${tipoDeFestivo}${idFestivo}"></td>
            <td><input type="text" class="form-control finalFestivo${tipoDeFestivo} datepicker-festivo-${tipoDeFestivo}" name="finalFestivo${tipoDeFestivo}" id="finalFestivo${tipoDeFestivo}${idFestivo}"></td>
            <td><button class="btn btn-danger eliminar-festivo-${tipoDeFestivo}">Eliminar</button></td>
        </tr>
    `);
}

$(document).on('click', '.guardar-festivos-centro', function() {
    // Obtener los valores de las filas de la tabla
    const nombreCentro = $('#nombreCentroFestivo').val();;
    const festivosCentro = [];
    $('#festivosCentroTable tbody tr').each(function() {
        const nombre = $(this).find('.nombreFestivocentro').val();
        let inicio = $(this).find('.inicioFestivocentro').val();
        let final = $(this).find('.finalFestivocentro').val();
        //Modificamos la fecha, para recibir el formato dd-mm-%AN% o dd-mm-%AC% siendo AN año anterior y AC año actual.
        inicio = modificarFecha(inicio);
        final = modificarFecha(final);
        festivosCentro.push({ nombre, inicio, final });
    });
    // Convertir el objeto a JSON
    const festivoscentroJSON = JSON.stringify(festivosCentro);

    const datosPost = {
        nombreCentro: nombreCentro,
        festivoscentroJSON: festivoscentroJSON
    }
    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/festivoscentro', datosPost,'/menu/administrador');

    // Mostrar el popup de añadido festivo centro correctamente
    mostrarPopUp("festivo/s añadido/s");
});

function modificarFecha(fecha) {
    //Dividimos la fecha y lo pasamos a entero para poder compararlo.
    const partes = fecha.split("-"); // Dividir la cadena en partes utilizando el guion como separador

    const dia = partes[0];
    const mes = parseInt(partes[1], 10); // Convertir el mes a número entero

    //Si el mes de la fecha es posterior a 8 (agosto) es el año pasado, si no es el actual.
    let anio;
    if(mes > 8) {
        anio = "%AN%";
    } else {
        anio = "%AC%";
    }

    return [dia, mes.toString(), anio].join("-");
}

$(document).on('click', '.crear-centro', function() {
    mostrarPopUp("Centro añadido correctamente");
});

function mostrarPopUp(titulo) {
    Swal.fire({
        title: titulo,
        text: 'Todo ha salido correctamente',
        icon: 'success',
        showConfirmButton: false,
        showCancelButton: false,
    });
}

// Formulario festivos nacionales admin
// Datepicker de festivosNacionales
$('#festivosNacionalesTable tbody').on('focus', '.datepicker-festivo-nacional', function() {
    $(this).datepicker({
        format: 'dd-mm-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

let idFestivosNacionales = 0;
$(document).on('click', '.aniadir-festivos-nacional', function() {
    idFestivosNacionales++;
    const fila = crearFilaFestivo(idFestivosNacionales, "nacional");
    $('#festivosNacionalesTable tbody').append(fila);
});

$(document).on('click', '.guardar-festivos-nacional', function() {
    // Obtener los valores de las filas de la tabla
    const festivosNacionales = [];
    $('#festivosNacionalesTable tbody tr').each(function() {
        const nombre = $(this).find('.nombreFestivonacional').val();
        let inicio = $(this).find('.inicioFestivonacional').val();
        let final = $(this).find('.finalFestivonacional').val();
        //Modificamos la fecha, para recibir el formato dd-mm-%AN% o dd-mm-%AC% siendo AN año anterior y AC año actual.
        inicio = modificarFecha(inicio);
        final = modificarFecha(final);
        festivosNacionales.push({ nombre, inicio, final });
    });
    // Convertir el objeto a JSON
    const festivosnacionalesJSON = JSON.stringify(festivosNacionales);

    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/festivosnacionales', {festivosnacionalesJSON: festivosnacionalesJSON},'/menu/administrador');

    // Mostrar el popup de añadido festivo centro correctamente
    mostrarPopUp("festivo/s nacional/es añadido/s");
});

// Formulario festivos locales admin
// Datepicker de festivosLocales
$('#festivosLocalesTable tbody').on('focus', '.datepicker-festivo-local', function() {
    $(this).datepicker({
        format: 'dd-mm-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

$(document).on('click', '.seleccionar-festivos-local', function() {
    // Mostrar el popup de centro seleccionado
    Swal.fire({
        title: 'Centro seleccionado',
        text: 'Ya puedes ver los festivos asociados al centro',
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#007BFF'
    });
});

let idFestivosLocales = 0;
$(document).on('click', '.aniadir-festivos-local', function() {
    idFestivosLocales++;
    const fila = crearFilaFestivo(idFestivosLocales, "local");
    $('#festivosLocalesTable tbody').append(fila);
});

$(document).on('click', '.guardar-festivos-local', function() {
    // Obtener los valores de las filas de la tabla
    const provincia = $('#nombreFestivoLocal').val();
    const festivosLocales = [];
    $('#festivosLocalesTable tbody tr').each(function() {
        const nombre = $(this).find('.nombreFestivolocal').val();
        let inicio = $(this).find('.inicioFestivolocal').val();
        let final = $(this).find('.finalFestivolocal').val();
        //Modificamos la fecha, para recibir el formato dd-mm-%AN% o dd-mm-%AC% siendo AN año anterior y AC año actual.
        inicio = modificarFecha(inicio);
        final = modificarFecha(final);
        festivosLocales.push({ nombre, inicio, final });
    });
    // Convertir el objeto a JSON
    const festivoslocalesJSON = JSON.stringify(festivosLocales);

    const datosPost = {
        provincia: provincia,
        festivoslocalesJSON: festivoslocalesJSON
    }

    // Enviar el objeto JSON a través de una petición AJAX
    enviarPost('/manejar/posts/festivoslocales', datosPost,'/menu/administrador');

    // Mostrar el popup de añadido festivo centro correctamente
    mostrarPopUp("festivo/s local/es añadido/s");
});

//Eliminar filas de tablas
$(document).on('click', '.eliminar-festivo-local, .eliminar-grupo, .eliminar-asignatura, .eliminar-festivo-centro, .eliminar-festivo-nacional', function() {
    // Obtener la fila
    const fila = $(this).closest('tr');
    fila.remove();
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