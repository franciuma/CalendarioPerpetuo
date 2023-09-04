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

// Formulario calendario
let contadorFechas = 0;
//Array clave: valor con fecha: asignaturaId
const arrayFechaAsignatura = [];
const mapFechaGrupo = new Map();

if(
    window.location.pathname == "/formulario/calendario" ||
    window.location.pathname == "/formulario/trasladar/calendario" ||
    window.location.pathname == "/formulario/editar/calendario"
    ){
    const paginaAnterior = document.referrer;

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

        // Si se está editando el calendario
        if (paginaAnterior.includes("/editar/calendario")) {
            //Obtenemos las clases
            const clases = JSON.parse(document.getElementById('clases').dataset.clases);
            clases.forEach(function(clase) {
                const fecha = clase.fecha;
                const asignaturaId = clase.asignaturaId;
                const grupoLetra = clase.letraGrupo;
                const horario = clase.horario;
                const asignaturaNombre = clase.asignaturaNombre;
                const nombre = clase.nombre;
                const enlace = clase.enlace;
                const modalidad = clase.modalidad;
                const claveMap = fecha+asignaturaId+grupoLetra+horario;
                let esPractica = true;
                if(modalidad == "Teorica") {
                    esPractica = false;
                }

                //Colocar las clases
                arrayFechaAsignatura.push({fecha: crearFecha(fecha), asignaturaId: asignaturaId, grupoLetra: grupoLetra, horario: horario});
                //Buscamos su grupo y lo metemos en el map
                grupos.forEach(function(grupo) {
                    if(grupo.asignatura == asignaturaNombre && grupo.letra == grupoLetra && grupo.horario == horario){
                        //Lo metemos en el map
                        mapFechaGrupo.set(claveMap, {
                            ...grupo,
                            esPractica,
                            tituloSesion: nombre,
                            enlace: enlace
                        });
                        //Le asignamos la variable grupo
                        grupo = mapFechaGrupo.get(claveMap);
                    }
                });
            });
        } else {
            //Si se está creando o trasladando el calendario
            //Recorrer los grupos
            grupos.forEach(function(grupo) {
                if(grupo.cuatrimestre == "Primero"){
                    completaCuatrimestre(inicioPrimerCuatri, fechaFinPrimerCuatri, grupo);
                } else {
                    completaCuatrimestre(inicioSegundoCuatri, fechaFinSegundoCuatri, grupo);
                }
            });
        }

        /**
         * Dada la fechaInicio, fechaFin y el grupo, completa la fecha de las clases.
         * @param {Date} fechaInicio 
         * @param {Date} fechaFin 
         * @param {grupo} grupo 
         */
        function completaCuatrimestre(fechaInicio, fechaFin, grupo) {
            let diasTeoria = grupo.diasTeoria;
            let diasPractica = grupo.diasPractica;
            let fechaActual = new Date(fechaInicio);
            let grupoAsignaturaId = grupo.asignaturaId;
            let grupoLetra = grupo.letra;
            let horario = grupo.horario;

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

            let leccionesFiltradas;
            let leccionesFiltradasTeoria;
            let leccionesFiltradasPractica;
            let sonClase = false;
            //En caso de trasladar calendario, en vez de utilizar las lecciones usaremos los títulos de las clases.
            if(paginaAnterior.includes("/trasladar")) {
                //Obtenemos las clases del grupo
                const clases = JSON.parse(document.getElementById('clases').dataset.clases);
                //Filtramos clases por asignaturaId
                leccionesFiltradas = clases.filter(function(clase) {
                    return clase.asignaturaId === grupoAsignaturaId;
                });

                //Filtramos clases por teoria y practica
                leccionesFiltradasTeoria = leccionesFiltradas.filter(function(clase) {
                    return clase.modalidad === "Teorica";
                });
                leccionesFiltradasPractica = leccionesFiltradas.filter(function(clase) {
                    return clase.modalidad === "Practica";
                });
                sonClase = true;
            } else {
                //Filtramos lecciones por asignaturaId
                leccionesFiltradas = lecciones.filter(function(leccion) {
                    return leccion.asignaturaId === grupoAsignaturaId;
                });

                //Filtramos lecciones por teoria y practica
                leccionesFiltradasTeoria = leccionesFiltradas.filter(function(leccion) {
                    return leccion.modalidad === "Teorica";
                });
                leccionesFiltradasPractica = leccionesFiltradas.filter(function(leccion) {
                    return leccion.modalidad === "Practica";
                });
            }

            while (fechaActual <= fechaFin) {
                //Si viene de clases tituloSesion será .nombre y si viene de lecciones será .titulo
                let tituloSesionTeoria = obtenerTituloSesion(leccionesFiltradasTeoria, contLeccTeoria, sonClase ? 'nombre' : 'titulo');
                let tituloSesionPractica = obtenerTituloSesion(leccionesFiltradasPractica, contLeccPractica, sonClase ? 'nombre' : 'titulo');

                // Si coinciden los dias teoria, además no están completas y no es festivo, se incluye.
                if (diasTeoria.includes(diasSemana[fechaActual.getDay()]) 
                    && contLeccTeoria != leccionesFiltradasTeoria.length
                    && !esFestivo(fechaActual, arrayFestivos)
                    ) {
                    //Se incluye la fecha actual en formato Date para el setDates de datepicker
                    arrayFechaAsignatura.push({fecha: new Date(fechaActual), asignaturaId: grupoAsignaturaId, grupoLetra: grupoLetra, horario: horario});
                    //Formateamos la fecha para incluirla en el map
                    let fechaFormateada = formatearFecha(new Date(fechaActual));
                    // Incluimos en el map la entidad grupo y un valor esPractica: false
                    mapFechaGrupo.set(fechaFormateada+grupoAsignaturaId+grupoLetra+horario, {
                        ...grupo,
                        esPractica: false,
                        tituloSesion: tituloSesionTeoria
                    });
                    contLeccTeoria++;
                }
                // Si se incluyen dias practica, se añaden al array
                if (diasPractica.includes(diasSemana[fechaActual.getDay()])
                    && contLeccPractica != leccionesFiltradasPractica.length
                    && !esFestivo(fechaActual, arrayFestivos)
                    ) {
                    //Se incluye la fecha actual en formato Date para el setDates de datepicker
                    arrayFechaAsignatura.push({fecha: new Date(fechaActual), asignaturaId: grupoAsignaturaId, grupoLetra: grupoLetra, horario: horario});
                    //Formateamos la fecha para incluirla en el map
                    let fechaFormateada = formatearFecha(new Date(fechaActual));
                    // Incluimos en el map la entidad grupo y un valor esPractica: true
                    mapFechaGrupo.set(fechaFormateada+grupoAsignaturaId+grupoLetra+horario, {
                        ...grupo,
                        esPractica: true,
                        tituloSesion: tituloSesionPractica
                    });
                    contLeccPractica++;
                }
                // Se actualiza la fecha actual
                fechaActual.setDate(fechaActual.getDate() + 1);
            }
        }

        function obtenerTituloSesion(lecciones, indice, propiedad) {
            if (indice < lecciones.length) {
                return lecciones[indice][propiedad];
            }
            return '';
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
            const grupoLetra = fechaAsignatura.grupoLetra;
            const horario = fechaAsignatura.horario;
            const fechaStringFormato = formatearFecha(fecha);
            contadorFechas++;
            const fila = crearFilaCalendario(fechaStringFormato, asignaturaId, grupoLetra, horario);
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
    const curso = JSON.parse(document.getElementById('curso').dataset.curso);
    let festivosCentroFiltrado;
    if(curso != "") {
        //Filtramos para buscar el nombreFestivo que se requiere y el curso
        festivosCentroFiltrado = festivosCentro.filter(function(festivoCentro) {
            let inicioFestivo = crearFecha(festivoCentro.inicio);
            let mesInicioFestivo = inicioFestivo.getMonth();
            let anioFestivo = inicioFestivo.getFullYear().toString();
            let anioInicioFestivo = anioFestivo.substring(anioFestivo.length - 2);
            if(mesInicioFestivo>=8 && anioInicioFestivo == curso[0] || mesInicioFestivo>=0 && anioInicioFestivo == curso[1]) {
                return festivoCentro.nombreFestivo == nombreFestivo
                && festivoCentro.nombreCentro == centro;
            }
        });
    } else {
        //Filtramos para buscar el nombreFestivo que se requiere
        festivosCentroFiltrado = festivosCentro.filter(function(festivoCentro) {
            return festivoCentro.nombreFestivo == nombreFestivo
                && festivoCentro.nombreCentro == centro;
        });
    }

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
    const curso = JSON.parse(document.getElementById('curso').dataset.curso);
    //Declaramos el array a devolver, el cual tendrá simplemente un array de todas las fechas festivas
    const festivos = [];

    //Filtraremos todos los festivos en base al curso académico
    festivosNacionales.filter(function(festivoNacional) {
        let inicioFestivo = crearFecha(festivoNacional.inicio);
        let mesInicioFestivo = inicioFestivo.getMonth();
        let anioFestivo = inicioFestivo.getFullYear().toString();
        let anioInicioFestivo = anioFestivo.substring(anioFestivo.length - 2);
        if(mesInicioFestivo>=8 && anioInicioFestivo == curso[0] || mesInicioFestivo>=0 && anioInicioFestivo == curso[1]) {
            festivos.push(inicioFestivo);
        }
    });

    //Obtenemos los festivosLocales en base a la provincia proporcionada y curso
    festivosLocales.filter(function(festivoLocal) {
        let inicioFestivo = crearFecha(festivoLocal.inicio);
        let mesInicioFestivo = inicioFestivo.getMonth();
        let anioFestivo = inicioFestivo.getFullYear().toString();
        let anioInicioFestivo = anioFestivo.substring(anioFestivo.length - 2);
        if((mesInicioFestivo>=8 && anioInicioFestivo == curso[0] || mesInicioFestivo>=0 && anioInicioFestivo == curso[1])
            && festivoLocal.provincia == provincia
        ) {
            festivos.push(inicioFestivo);
        }
    });

    //Obtenemos los festivosCentro en base al centro proporcionado y curso
    festivosCentro.filter(function(festivoCentro) {
        let inicioFestivo = crearFecha(festivoCentro.inicio);
        let mesInicioFestivo = inicioFestivo.getMonth();
        let anioFestivo = inicioFestivo.getFullYear().toString();
        let anioInicioFestivo = anioFestivo.substring(anioFestivo.length - 2);
        if((mesInicioFestivo>=8 && anioInicioFestivo == curso[0] || mesInicioFestivo>=0  && anioInicioFestivo == curso[1])
            && festivoCentro.nombreCentro == centro
            && !(festivoCentro.nombreFestivo).includes("cuatrimestre")
        ) {
            festivos.push(inicioFestivo);
        }
    });

    return festivos;
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

function crearFilaCalendario(fechaStringFormato, asignaturaId, grupoLetra, horario) {
    const clave = fechaStringFormato+asignaturaId+grupoLetra+horario;
    const asignaturas = obtenerAsignaturasSelect();
    const grupos = obtenerGrupoSelect();
    let esPractica = "";
    let asignatura = "";
    let inactivo = "";
    let grupo = "";
    let tituloSesion = "";
    let enlace = "";
    if (mapFechaGrupo.has(clave)) {
        esPractica = mapFechaGrupo.get(clave).esPractica;
        asignatura = mapFechaGrupo.get(clave).asignatura;
        grupo = mapFechaGrupo.get(clave).letra;
        tituloSesion = mapFechaGrupo.get(clave).tituloSesion;
        enlace = mapFechaGrupo.get(clave).enlace ? mapFechaGrupo.get(clave).enlace : "";

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
                    <option ${esPractica ? '' : 'selected'}></option>
                    <option ${esPractica === true ? 'selected' : ''}>Practica</option>
                    <option ${esPractica === false ? 'selected' : ''}>Teorica</option>
                </select>
            </td>
            <td>
                <select ${inactivo} class="form-control horario" name="horarioCalendario" id="horario${fechaStringFormato}">
                    <option ${horario ? '' : 'selected'}></option>
                    <option ${horario === 'Mañana' ? 'selected' : ''}>Mañana</option>
                    <option ${horario === 'Tarde' ? 'selected' : ''}>Tarde</option>
                </select>
            </td>
            <td><input type="text" class="form-control enlace" name="enlace" id="enlace${fechaStringFormato}" placeholder="Enlace a la clase" value="${enlace}"></td>
            <td><button data-asignatura-id="${asignaturaId}" type="button" class="btn btn-primary permutar-fecha">Permutar</button></td>
            <td><button class="btn btn-danger eliminar-fecha">Eliminar</button></td>
        </tr>
    `);
}

function obtenerGrupoSelect(tipo){
    const grupos = JSON.parse(document.getElementById('grupos').dataset.grupos);
    let options = "";
    //Los recorremos y agregamos las opciones
    for (var i = 0; i < grupos.length; i++) {
        options += `<option>${grupos[i].letra}</option>`;
    }

    return options;
}

// Permutación de dos clases en previsualización de calendario
$(document).on('click', '.permutar-fecha', function() {
    // Obtener la fila y la fecha seleccionada
    const filaInicial = $(this).closest('tr');
    const posicionFilaInicial = filaInicial.index();
    const asignaturaIdInicial = $(this).data('asignatura-id');
    const fechaInicial = filaInicial.find('input[name="fecha"]').val();
    const grupoLetraInicial = filaInicial.find('select[name="grupoCalendario"]').val();
    const horarioInicial = filaInicial.find('select[name="horarioCalendario"]').val();
    const claveInicial = fechaInicial+asignaturaIdInicial+grupoLetraInicial+horarioInicial;
    let valorInicialMap;
    //Buscamos el mapa inicial asociado
    if (mapFechaGrupo.has(claveInicial)) {
        valorInicialMap = mapFechaGrupo.get(claveInicial);
        mapFechaGrupo.delete(claveInicial);
    }

    $('.permutar-fecha').removeClass('permutar-fecha').addClass('destino-permutar-fecha').text('Destino permutación');
    $(this).removeClass('destino-permutar-fecha').addClass('cancelar-permutacion').text('Cancelar Permutación').addClass('btn-danger');

    //Si toca a cancelar permutación
    $(document).off('click', '.cancelar-permutacion').on('click', '.cancelar-permutacion', function() {
        $('.destino-permutar-fecha').removeClass('destino-permutar-fecha').addClass('permutar-fecha').text('Permutar').removeAttr('style');
        $(this).removeClass('cancelar-permutacion').addClass('permutar-fecha').text('Permutar').removeClass('btn-danger');
        //Volvemos a crear el mapa borrado
        mapFechaGrupo.set(claveInicial, valorInicialMap);
    });
    //Reseteamos los datos que tengamos de destino-permutar-fecha
    $(document).off('click', '.destino-permutar-fecha');

    $(document).on('click', '.destino-permutar-fecha', function() {
        // Obtener la fila y la fecha seleccionada
        const filaDestino = $(this).closest('tr');
        const posicionFilaDestino = filaDestino.index();
        const asignaturaIdDestino = $(this).data('asignatura-id');
        const fechaDestino = filaDestino.find('input[name="fecha"]').val();
        const grupoLetraDestino = filaDestino.find('select[name="grupoCalendario"]').val();
        const horarioDestino = filaDestino.find('select[name="horarioCalendario"]').val();
        const claveDestino = fechaDestino+asignaturaIdDestino+grupoLetraDestino+horarioDestino;
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
        const fechaNuevaInicial = crearFilaCalendario(fechaInicial, asignaturaIdInicial, grupoLetraInicial, horarioInicial);
        const fechaNuevaFinal = crearFilaCalendario(fechaDestino, asignaturaIdDestino, grupoLetraDestino, horarioDestino);
        //Obtenemos los lugares de las filas
        const nuevaFilaInicial = $('#fechasTable tbody tr').eq(posicionFilaInicial);
        const nuevaFilaDestino = $('#fechasTable tbody tr').eq(posicionFilaDestino);
        //Añadimos a la tabla las nuevas filas
        nuevaFilaDestino.after(fechaNuevaFinal);
        nuevaFilaInicial.after(fechaNuevaInicial);

        //Borramos las filas antiguas
        filaInicial.remove();
        filaDestino.remove();
        $('.destino-permutar-fecha').removeClass('destino-permutar-fecha').addClass('permutar-fecha').text('Permutar');

        // Mostrar el popup de permutación exitosa
        Swal.fire({
            title: 'Permutación exitosa',
            text: 'Permutación exitosa',
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

//Al darle a añadir clases, se selecciona el datepicker
$(document).on('click', '.aniadir-clase-calendario', function() {
    $('#datepickerInput').datepicker('show');
});

//Creamos el POST del formulario
$(document).on('click', '.crear-calendario', function() {
    // Obtener grupos
    const grupos = JSON.parse(document.getElementById('grupos').dataset.grupos);
    // Obtener los valores de las filas de la tabla
    const clases = [];
    $('#fechasTable tbody tr').each(function() {
        const fecha = $(this).find('.fecha').val();
        const nombre = $(this).find('.nombre').val();
        const modalidad = $(this).find('.modalidad').val();
        const asignaturaNombre = $(this).find('.asignaturaCalendario').val();
        const grupoLetra = $(this).find('.grupoCalendario').val();
        const horario = $(this).find('.horario').val();
        const enlace = $(this).find('.enlace').val();
        const asignaturaId = obtenerAsignaturaId(asignaturaNombre);
        const claveMap = fecha+asignaturaId+grupoLetra+horario;
        let grupo;
        //Obtenemos el grupo de cada fila
        if(mapFechaGrupo.has(claveMap)){
            grupo = mapFechaGrupo.get(claveMap);
        } else {
            //Se ha metido nuevo, por lo que hay que buscar su grupo y meterlo en el map
            grupos.forEach(function(grupoFor) {
                if(grupoFor.asignatura == asignaturaNombre && grupoFor.letra == grupoLetra && grupoFor.horario == horario){
                    //Lo metemos en el map
                    mapFechaGrupo.set(claveMap, {
                        ...grupoFor,
                    });
                    //Le asignamos la variable grupo
                    grupo = mapFechaGrupo.get(claveMap);
                }
            });
        }

        clases.push({ fecha, nombre, modalidad, enlace, asignaturaNombre, grupo });
    });

    // Convertir el objeto a JSON
    const clasesJSON = JSON.stringify(clases);

    //Obtenemos el nombre del profesor via localStorage
    const nombreProfesor = localStorage.getItem('profesor');
    const provincia = localStorage.getItem('provincia');
    const centro = localStorage.getItem('centro');
    // Enviar el objeto JSON a través de una petición AJAX
    if(window.location.pathname == "/formulario/trasladar/calendario") {
        enviarPost('/manejar/posts/clase',{clasesJSON: clasesJSON},'/trasladar/calendario?provincia='+ provincia + '&usuario='+ nombreProfesor + '&centro=' + centro); //parametros de URL
    } else {
        //Si se traslada el calendario viene de /formulario/trasladar/calendario
        enviarPost('/manejar/posts/clase',{clasesJSON: clasesJSON},'/calendario?provincia='+ provincia + '&usuario='+ nombreProfesor + '&centro=' + centro); //parametros de URL

    }
});

//LEER un calendario
$(document).on('click', '.Ver-calendario', function() {
    const nombreProfesor = $('#nombreleerProfesor').val();
    //Leemos la url actual
    const urlActual = window.location.pathname;
    // Enviar el objeto JSON a través de una petición AJAX
    if(urlActual.includes("alumno")) {
        window.location.replace('/ver/calendario/alumno?usuario=' + nombreProfesor);
    } else {
        window.location.replace('/ver/calendario?usuario=' + nombreProfesor);
    }
});

//ELIMINAR un calendario
$(document).on('click', '.Eliminar-calendario', function() {
    const nombreProfesor = $('#nombreleerProfesor').val();
    // Enviar el objeto JSON a través de una petición AJAX
    window.location.replace('/eliminar/calendario?usuario='+ nombreProfesor);
});

//Formulario profesor
let idGrupo = 0;
//Si se está editando un profesor
if(window.location.pathname == "/editar/docente" || window.location.pathname == "/editar/docente/admin") {
    //Obtenemos los grupos y creamos sus filas
    const grupos = JSON.parse(document.getElementById('grupos').dataset.grupos);
    crearFilasExistentesGrupo(grupos);
}

//Si se está editando un alumno
if(window.location.pathname == "/editar/alumno") {
    //Obtenemos los grupos y creamos sus filas
    const grupos = JSON.parse(document.getElementById('gruposAlumno').dataset.grupos);
    crearFilasExistentesGrupoAlum(grupos);
}

$(document).on('click', '.aniadir-fila-prof, .aniadir-fila-alum', function() {
    idGrupo++;
    let fila;
    if($(this).hasClass('aniadir-fila-prof')) {
        fila = crearFilaGrupo();
    } else {
        fila = crearFilaGrupoAlum();
    }
    $('#gruposTable tbody').append(fila);
    configurarMultiSelect();
});

function configurarMultiSelect()
{
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
}

function crearFilasExistentesGrupo(grupos) {
    var diasSemana = `<option>Lunes</option>
    <option>Martes</option>
    <option>Miércoles</option>
    <option>Jueves</option>
    <option>Viernes</option>`;

    const asignaturasOptions = obtenerAsignaturasSelect();
    let fila;

    grupos.forEach(grupo => {
        idGrupo++;
        fila = $(`
        <tr id="grupo${grupo.id}">
            <td><input type="text" class="form-control grupo" name="grupo" id="grupo${idGrupo}" value="${grupo.letra}" disabled></td>
            <td>
                <select type="text" class="form-control asignatura" name="asignatura" id="asignatura${idGrupo}" disabled>
                <option selected>${grupo.asignatura}</option>
                ${asignaturasOptions}
                </select>
            </td>
            <td>
                <select class="form-control horario" name="horario" id="horario${idGrupo}" disabled>
                    <option selected>${grupo.horario}</option>
                    <option>Mañana</option>
                    <option>Tarde</option>
                </select>
            </td>
            <td>
                <select class="form-control diasTeoria" name="diasTeoria" id="diasTeoria${idGrupo}" multiple="multiple">
                    <option selected>${grupo.diasTeoria}</option>
                    ${diasSemana}
                </select>
            </td>
            <td>
                <select class="form-control diasPractica" name="diasPractica" id="diasPractica${idGrupo}" multiple="multiple">
                    <option selected>${grupo.diasPractica}</option>
                    ${diasSemana}
                </select>
            </td>
            <td><button class="btn btn-danger eliminar-grupo">Eliminar</button></td>
        </tr>
    `);
    $('#gruposTable tbody').append(fila);
    configurarMultiSelect();
    });
}

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
                <option selected>-- Seleccione la asignatura --</option>
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

function obtenerAsignaturasSelect() {
    //Obtenemos las asignaturas del template de formulario/profesor
    const asignaturas = JSON.parse(decodeURIComponent(document.getElementById('asignaturas').dataset.asignaturas));
    let options = "";
    //Los recorremos y agregamos las opciones
    for (let i = 0; i < asignaturas.length; i++) {
        if(window.location.pathname.includes("/calendario")) {
            options += `<option>${asignaturas[i].asignatura}</option>`;
        } else {
            options += `<option>${asignaturas[i].asignatura} - ${asignaturas[i].centro}</option>`;
        }
    }

    return options;
}

function obtenerAsignaturaId(asignaturaNombre) {
    //Cogemos las asignaturas de formulario/calendario
    const asignaturas = JSON.parse(document.getElementById('asignaturas').dataset.asignaturas);
    //Buscamos el nombre de asignatura y devolvemos su id
    for (let i = 0; i < asignaturas.length; i++) {
        if (asignaturas[i].asignatura == asignaturaNombre) {
            return asignaturas[i].id;
        }
    }
}

//Creamos el POST del formulario
$(document).on('click', '.crear-profesor, .editar-profesor', function() {
    let error;
    const profesor = [];
    const nombre = $('#nombreProf').val();
    const primerapellido = $('#papellidoProf').val();
    const segundoapellido = $('#sapellidoProf').val();
    const despacho = $('#despacho').val();
    const correo = $('#correo').val();
    const tipo = 'Profesor';

    const camposObligatorios = [
        { nombre: 'nombre', valor: nombre },
        { nombre: 'primer apellido', valor: primerapellido },
        { nombre: 'segundo apellido', valor: segundoapellido }
    ];

    if($(this).hasClass('crear-profesor')) {
        const profesores = JSON.parse(document.getElementById('profesores').dataset.profesores);
        profesores.forEach(function(profesor) {
            const nombreCompleto = nombre+" "+primerapellido+" "+segundoapellido;
            if(profesor.nombreCompleto === nombreCompleto) {
                alertaPersonalizada("Docente ya existente","", "error");
                error = true;
                return;
            } 
        });
    }

    if(manejarErroresVacios(camposObligatorios) || error) {
        return;
    }

    profesor.push({nombre, primerapellido, segundoapellido, despacho, correo, tipo});

    const grupo = [];
    // Obtener los valores de las filas de la tabla
    $('#gruposTable tbody tr').each(function() {
        const letra = $(this).find('.grupo').val();
        const asignaturaNombreCentro = $(this).find('.asignatura').val();
        const asignaturaNombre = asignaturaNombreCentro.split("-")[0];
        const horario = $(this).find('.horario').val();
        let diasTeoria = $(this).find('.diasTeoria').val();
        let diasPractica = $(this).find('.diasPractica').val();

        // Verificar si diasTeoria y diasPractica contienen una coma y están rodeadas por comillas
        if ( diasTeoria.some(dias => dias.includes(',') && !dias.startsWith('"') && !dias.endsWith('"'))) {
            diasTeoria = diasTeoria[0].split(',').map(dia => dia.trim());
        }

        if ( diasPractica.some(dias => dias.includes(',') && !dias.startsWith('"') && !dias.endsWith('"'))) {
            diasPractica = diasPractica[0].split(',').map(dia => dia.trim());
        }

        const camposObligatorios = [
            { nombre: 'grupo', valor: letra },
            { nombre: 'asignatura', valor: asignaturaNombre}
        ];
    
        if(manejarErroresVacios(camposObligatorios)) {
            error = true;
            return;
        }

        grupo.push({letra, asignaturaNombre, diasTeoria, diasPractica, horario});
    });

    if(error) {
        return;
    }

    const datos = {
        profesor: profesor,
        grupos: grupo
    };
    const usuarioGrupoJSON = JSON.stringify(datos);

    const urlActual = window.location.pathname;
    // Enviar el objeto JSON a través de una petición AJAX
    let usuario = "";
    if(urlActual.includes("admin")) {
        usuario = "/admin";
    }

    // Enviar el objeto JSON a través de una petición AJAX
    if(window.location.pathname == "/editar/docente"+usuario) {
        const profesorId = document.getElementById('profesorid').dataset.profesorid;
        enviarPost('/manejar/posts/usuarioGrupo',{usuarioGrupoJSON: usuarioGrupoJSON},'/post/docente/editado'+usuario+'?profesor='+profesorId);
    } else {
        enviarPost('/manejar/posts/usuarioGrupo',{usuarioGrupoJSON: usuarioGrupoJSON},'/post/docente'+usuario);
    }
});

$(document).on('click', '.crear-alumno, .editar-alumno', function() {
    let error;
    const alumno = [];
    const dni = $('#dniAlum').val();
    const tipo = 'Alumno';

    const camposObligatorios = [
        { nombre: 'dni', valor: dni }
    ];

    if($(this).hasClass('crear-alumno')) {
        const alumnos = JSON.parse(document.getElementById('alumnos').dataset.alumnos);
        alumnos.forEach(function(alumno) {
            if(alumno.dni === dni) {
                alertaPersonalizada("Alumno ya existente","", "error");
                error = true;
                return;
            } 
        });
    }

    if(manejarErroresVacios(camposObligatorios) || error) {
        return;
    }

    alumno.push({dni, tipo});

    const grupo = [];
    // Obtener los valores de las filas de la tabla
    $('#gruposTable tbody tr').each(function() {
        const grupoNombre = $(this).find('.grupoAlum').val();

        const camposObligatorios = [
            { nombre: 'grupo', valor: grupoNombre },
        ];
    
        if(manejarErroresVacios(camposObligatorios)) {
            error = true;
            return;
        }

        const partes = grupoNombre.split("-");

        const letra = partes[0];
        const asignaturaNombre = partes[1];
        const horario = partes[2];

        grupo.push({letra, asignaturaNombre, horario});
    });

    if(error) {
        return;
    }

    const datos = {
        alumno: alumno,
        grupos: grupo
    };
    const usuarioGrupoJSON = JSON.stringify(datos);

    // Enviar el objeto JSON a través de una petición AJAX
    if(window.location.pathname == "/editar/alumno") {
        const alumnoId = document.getElementById('alumnoid').dataset.alumnoid;
        enviarPost('/manejar/posts/usuarioGrupo',{usuarioGrupoJSON: usuarioGrupoJSON},'/post/alumno/editado?alumno='+alumnoId);
    } else {
        enviarPost('/manejar/posts/usuarioGrupo',{usuarioGrupoJSON: usuarioGrupoJSON},'/post/alumno');
    }
});

function crearFilaGrupoAlum() {
    const gruposOptions = obtenerGrupoSelect();

    return $(`
        <tr id="grupo${idGrupo}">
            <td>
                <select type="text" class="form-control grupoAlum" name="grupoAlum" id="grupoAlum${idGrupo}">
                <option selected>-- Seleccione el grupo --</option>
                ${gruposOptions}
                </select>
            </td>
            <td><button class="btn btn-danger eliminar-grupo">Eliminar</button></td>
        </tr>
    `);
}

function crearFilasExistentesGrupoAlum(grupos) {
    const gruposOptions = obtenerGrupoSelect("editar alumno");
    let fila;

    grupos.forEach(grupo => {
        idGrupo++;
        fila = $(`
        <tr id="grupo${idGrupo}">
            <td>
                <select type="text" class="form-control grupoAlum" name="grupoAlum" id="grupoAlum${idGrupo}" disabled>
                <option selected>${grupo.letra}</option>
                ${gruposOptions}
                </select>
            </td>
            <td><button class="btn btn-danger eliminar-grupo">Eliminar</button></td>
        </tr>
    `);
    $('#gruposTable tbody').append(fila);
    });
}

//Si se está editando una asignatura
if(window.location.pathname == "/editar/asignatura") {
    //Obtenemos la asignatura y lecciones y creamos sus filas
    const asignatura = JSON.parse(document.getElementById('asignatura').dataset.asignatura);
    const lecciones = JSON.parse(document.getElementById('lecciones').dataset.lecciones);
    crearFilasExistentesAsignatura(asignatura, lecciones);
}

function crearFilasExistentesAsignatura(asignatura, lecciones) {
    const idAsignatura = JSON.parse(document.getElementById('asignaturaid').dataset.asignaturaid);
    const titulacionEscogida = asignatura.titulacion;
    const optionTitulacion = obtenerTitulacionSelect();
    const optionTitulacionFiltrado = optionTitulacion.replace(`<option>${titulacionEscogida}</option>`, "");
    let filasLeccion = "";
    let filaAsignatura;
    let idLeccion = 0;

    filaAsignatura = $(`
        <tr class="fila-asignatura" id="asignatura${idAsignatura}">
            <td><input type="text" class="form-control nombreAsig" name="nombreAsig" id="nombreAsignatura${idAsignatura}" value="${asignatura.asignatura}"></td>
            <td><input type="text" class="form-control abrevAsig" name="abrevAsig" id="abrevAsignatura${idAsignatura}" value="${asignatura.abreviatura}"></td>
            <td>
            <select class="form-control cuatrimestre" name="cuatrimestre" id="cuatrimestre${idAsignatura}">
                <option ${asignatura.cuatrimestre === 'Primero' ? 'selected' : ''}>Primero</option>
                <option ${asignatura.cuatrimestre === 'Segundo' ? 'selected' : ''}>Segundo</option>
            </select>
            </td>
            <td>
                <select class="form-control ntitulacion" name="ntitulacion" id="ntitulacion${idAsignatura}">
                <option selected>${asignatura.titulacion}</option>
                ${optionTitulacionFiltrado}
                </select>
            </td>
        </tr>
    `);

    for (let i = 0; i < lecciones.length; i++) {
        idLeccion++;
        filasLeccion += `<tr id="tabla${idAsignatura}leccion${idLeccion}" class="fila-leccion">
        <td><input type="text" class="form-control tituloLecc" name="tituloLecc" id="tituloLeccion${idLeccion}" value="${lecciones[i].titulo}"></td>
        <td>
            <select class="form-control modalidad" name="modalidad" id="modalidad${idLeccion}">
                <option ${lecciones[i].modalidad === 'Teorica' ? 'selected' : ''}>Teorica</option>
                <option ${lecciones[i].modalidad === 'Practica' ? 'selected' : ''}>Practica</option>
            </select>
        </td>
        <td><input type="text" class="form-control abrevtituloLecc" name="abrevtituloLecc" id="abrevtituloLecc${idLeccion}" value="${lecciones[i].abreviatura ? lecciones[i].abreviatura : ''}"
        ></td>
        </tr>`;
    }

    idLeccion = 0;

    $("#asignaturasTable tbody").append(filaAsignatura);
    $("#leccionesTable tbody").append(filasLeccion);
}

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
    const optionTitulacion = obtenerTitulacionSelect();
    return $(`
        <tr class="fila-asignatura" id="asignatura${idAsignatura}">
            <td><input type="text" class="form-control nombreAsig" name="nombreAsig" id="nombreAsignatura${idAsignatura}"></td>
            <td><input type="text" class="form-control abrevAsig" name="abrevAsig" id="abrevAsignatura${idAsignatura}"></td>
            <td><input type="number" class="form-control numLecciones" name="numLeccTeor" id="numLeccionesTeor${idAsignatura}" value="1"></td>
            <td><input type="number" class="form-control numLecciones" name="numLeccPrac" id="numLeccionesPrac${idAsignatura}" value="1"></td>
            <td>
            <select class="form-control cuatrimestre" name="cuatrimestre" id="cuatrimestre${idAsignatura}">
                <option>Primero</option>
                <option>Segundo</option>
            </select>
            </td>
            <td>
                <select class="form-control ntitulacion" name="ntitulacion" id="ntitulacion${idAsignatura}">
                <option selected></option>
                ${optionTitulacion}
                </select>
            </td>
            <td><button class="btn btn-primary aniadir-lecciones" type="button" data-id="${idAsignatura}">Añadir sesiones</button></td>
            <td><button class="btn btn-danger eliminar-asignatura" type="button">Eliminar</button></td>
        </tr>
    `);
}

function obtenerTitulacionSelect(){
    //obtenemos las titulaciones desde /formulario/asignatura
    const titulaciones = JSON.parse(document.getElementById('titulaciones').dataset.titulaciones);
    let options = "";
    //Los recorremos y agregamos las opciones
    for (var i = 0; i < titulaciones.length; i++) {
        options += `<option>${titulaciones[i]}</option>`;
    }

    return options;
}

let idLeccion = 0;
$(document).on('click', '.aniadir-lecciones', function() {
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
        <td><input type="text" class="form-control abrevtituloLecc" name="abrevtituloLecc" id="abrevtituloLecc${idLeccion}" placeholder="Opcional"></td>
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
        <td><input type="text" class="form-control abrevtituloLecc" name="abrevtituloLecc" id="abrevtituloLecc${idLeccion}" placeholder="Opcional"></td>
        <td><button class="btn btn-danger eliminar-leccion">Eliminar</button></td>
        </tr>`;
    }

    idLeccion = 0;

    return $(`
        <div id="leccionesTabla${asignaturaId}">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título de sesión</th>
                        <th>Modalidad</th>
                        <th>Abreviatura</th>
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

//Creamos el POST del formulario asignatura
$(document).on('click', '.crear-asignatura, .editar-asignatura', function() {
    const crearAsignatura = $(this).hasClass('crear-asignatura');
    // Obtener los valores de las filas de la tabla
    const asignaturas = [];
    let lecciones = [];
    let error;
    $('#asignaturasTable tbody tr[id^="asignatura"]').each(function() {
        const nombre = $(this).find('.nombreAsig').val();
        const abreviatura = $(this).find('.abrevAsig').val();
        const nombreTitulacion = $(this).find('.ntitulacion').val();
        const cuatrimestre = $(this).find('.cuatrimestre').val();
        const camposObligatorios = [
            { nombre: 'nombre', valor: nombre },
            { nombre: 'titulacion', valor: nombreTitulacion },
        ];

        if(manejarErroresVacios(camposObligatorios)) {
            error = true;
            return;
        }

        if(crearAsignatura) {
            $(this).next('div').find('.fila-leccion').each(function() {
                const titulo = $(this).find('.tituloLecc').val();
                const modalidad = $(this).find('.modalidad').val();
                const abreviatura = $(this).find('.abrevtituloLecc').val();
                lecciones.push({ titulo, modalidad, abreviatura })
            });
        } else {
            $('#leccionesTable tbody tr').each(function() {
                const titulo = $(this).find('.tituloLecc').val();
                const modalidad = $(this).find('.modalidad').val();
                const abreviatura = $(this).find('.abrevtituloLecc').val();
                lecciones.push({ titulo, modalidad, abreviatura })
            });
        }
        asignaturas.push({ nombre, abreviatura, nombreTitulacion, cuatrimestre, lecciones });
        lecciones = [];
    });

    if(error) {
        return;
    }

    // Convertir el objeto a JSON
    const asignaturasJSON = JSON.stringify(asignaturas);
    // Enviar el objeto JSON a través de una petición AJAX
    if(window.location.pathname == "/editar/asignatura") {
        const asignaturaId = JSON.parse(document.getElementById('asignaturaid').dataset.asignaturaid);
        enviarPost('/manejar/posts/asignatura', {asignaturasJSON: asignaturasJSON},'/post/asignatura/editada?asignatura='+asignaturaId);
    } else {
        enviarPost('/manejar/posts/asignatura',{asignaturasJSON: asignaturasJSON},'/post/asignatura');
    }
});

//Formulario centro
$(document).on('click', '.previsualizar-calendario', function() {
    const centro = [];
    const profesor = $('#nombreDelProfesor').val();
    const nombreCentroProvincia = $('#nombreDelCentroProvincia').val();
    const partesNombreProvincia = nombreCentroProvincia.split('-');
    const nombre = partesNombreProvincia[0];
    const provincia = partesNombreProvincia[1];
    //Guardamos la variable en localStorage
    localStorage.setItem('provincia', provincia);
    localStorage.setItem('centro',nombre);
    localStorage.setItem('profesor',profesor);

    centro.push({nombre, provincia, profesor});

    const centroJSON = JSON.stringify(centro);

    enviarPost('/manejar/posts/centro',{centroJSON: centroJSON}, '/post/centro');
});

//Formulario editar calendario
$(document).on('click', '.editar-calendario, .trasladar-calendario', function() {
    let profesor;
    if($(this).hasClass('editar-calendario')) {
        profesor = $('#nombreDelProfesorEditado').val();
    } else {
        profesor = $('#nombreDelProfesorTrasladado').val();
    }
    //En caso de trasladar, recogemos el curso académico
    const curso = $('#cursoacademico').val();
    //Mandamos por AJAX a un controlador que nos devuelva el centro y provincia de un profesor en caso de editar un calendario
    $.ajax({
        url: '/obtener/info/profesor',
        method: 'GET',
        data: { profesor: profesor },
        success: function(response) {
            const nombreCentroProvincia = response;
            editarCalendario(nombreCentroProvincia, profesor, curso);
            // Maneja la respuesta del servidor aquí
            console.log(response);
        },
        error: function() {
            // Maneja el error si la llamada AJAX falla
        }
    });
});

function editarCalendario(nombreCentroProvincia, profesor, curso)
{
    const centro = [];
    const partesNombreProvincia = nombreCentroProvincia.split('-');
    const nombre = partesNombreProvincia[0];
    const provincia = partesNombreProvincia[1];
    const editar = true;
    centro.push({nombre, provincia, profesor, editar, curso});
    const centroJSON = JSON.stringify(centro);
    //Si no hay curso se está editando, en otro caso se está trasladando.
    localStorage.setItem('profesor',profesor);
    localStorage.setItem('provincia',provincia);
    localStorage.setItem('centro',nombre);
    if(!curso) {
        enviarPost('/manejar/posts/centro',{centroJSON: centroJSON}, '/formulario/editar/calendario');
    } else {
        enviarPost('/manejar/posts/centro',{centroJSON: centroJSON}, '/formulario/trasladar/calendario');
    }
}


//Formulario festivos de centro admin
// Datepicker de festivosCentro
$('#festivosCentroTable tbody').on('focus', '.datepicker-festivo-centro', function() {
    $(this).datepicker({
        format: 'd-m-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

$('#festivosCentroEditarTable tbody').on('focus', '.datepicker-festivo-centro', function() {
    $(this).datepicker({
        format: 'd-m-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

//Si se está editando un festivo de centro
if(window.location.pathname == "/editar/festivo/centro") {
    //Obtenemos la asignatura y lecciones y creamos sus filas
    const festivoCentro = JSON.parse(document.getElementById('festivosCentro').dataset.festivocentro);
    const fila = crearFilasExistentesFestivos(festivoCentro, "centro");
    $('#festivosCentroEditarTable tbody').append(fila);
}

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
            <td><input type="text" class="form-control nombreFestivo${tipoDeFestivo}" name="nombreFestivo${tipoDeFestivo}" id="nombreFestivo${tipoDeFestivo}${idFestivo}" placeholder="Ej: Dia de la paz"></td>
            <td><input type="text" class="form-control inicioFestivo${tipoDeFestivo} datepicker-festivo-${tipoDeFestivo}" name="inicioFestivo${tipoDeFestivo}" id="inicioFestivo${tipoDeFestivo}${idFestivo}"></td>
            <td><input type="text" class="form-control finalFestivo${tipoDeFestivo} datepicker-festivo-${tipoDeFestivo}" name="finalFestivo${tipoDeFestivo}" id="finalFestivo${tipoDeFestivo}${idFestivo}"></td>
            <td><button class="btn btn-danger eliminar-festivo-${tipoDeFestivo}">Eliminar</button></td>
        </tr>
    `);
}

$(document).on('click', '.guardar-festivos-centro, .editar-festivos-centro', function() {
    // Obtener los valores de las filas de la tabla
    let nombreCentro = $('#nombreCentroFestivo').val();
    const festivosCentro = [];

    let tabla;
    if($(this).hasClass('guardar-festivos-centro')) {
        tabla = '#festivosCentroTable tbody tr';
    } else {
        tabla = '#festivosCentroEditarTable tbody tr';
    }

    $(tabla).each(function() {
        const id = $(this).find('.idFestivocentro').val();
        const nombre = $(this).find('.nombreFestivocentro').val();
        let inicio = $(this).find('.inicioFestivocentro').val();
        let final = $(this).find('.finalFestivocentro').val();
        //Modificamos la fecha, para recibir el formato dd-mm-%AN% o dd-mm-%AC% siendo AN año anterior y AC año actual.
        inicio = modificarFecha(inicio);
        final = modificarFecha(final);

        if(id) {
            nombreCentro = $(this).find('.datoFestivocentro').val();
            festivosCentro.push({ id, nombre, inicio, final });
        } else {
            festivosCentro.push({ nombre, inicio, final });
        }
    });
    // Convertir el objeto a JSON
    const festivoscentroJSON = JSON.stringify(festivosCentro);

    const datosPost = {
        nombreCentro: nombreCentro,
        festivoscentroJSON: festivoscentroJSON
    }

    if($(this).hasClass('guardar-festivos-centro')) {
        // Enviar el objeto JSON a través de una petición AJAX
        enviarPost('/manejar/posts/festivoscentro', datosPost,'/menu/periodos/centro/admin');
        // Mostrar el popup de añadido festivo centro correctamente
        mostrarPopUp("festivo/s añadido/s");
    } else {
        // Enviar el objeto JSON a través de una petición AJAX
        enviarPost('/post/editar/festivo/centro', datosPost,'/menu/periodos/centro/admin');
        // Mostrar el popup de añadido festivo centro correctamente
        mostrarPopUp("periodo de centro editado");
    }
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
    const centros = JSON.parse(document.getElementById('centros').dataset.centros);
    const nombreCentro = $('.nombreDelCentro').val();
    const nombreProvincia = $('.nombreDeProvincia').val();

    const camposObligatorios = [
        { nombre: 'nombre del centro', valor: nombreCentro },
        { nombre: 'nombre de provincia', valor: nombreProvincia }
    ];

    if(manejarErroresVacios(camposObligatorios)){
        return;
    }
    //Comprobamos que el centro no exista
    const centroFormulario = nombreCentro+'-'+nombreProvincia;
    centros.forEach(function(centro) {
        if(centroFormulario == centro) {
            alertaPersonalizada("Centro ya existente", "", "error");
            return;
        } 
    });
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
        format: 'd-m-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

$('#festivosNacionalesEditarTable tbody').on('focus', '.datepicker-festivo-nacional', function() {
    $(this).datepicker({
        format: 'd-m-yyyy',
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

$(document).on('click', '.guardar-festivos-nacional, .editar-festivo-nacional', function() {
    // Obtener los valores de las filas de la tabla
    const festivosNacionales = [];
    let tabla;

    if($(this).hasClass('guardar-festivos-nacional')) {
        tabla = '#festivosNacionalesTable tbody tr';
    } else {
        tabla = '#festivosNacionalesEditarTable tbody tr';
    }

    $(tabla).each(function() {
        const id = $(this).find('.idFestivonacional').val();
        const nombre = $(this).find('.nombreFestivonacional').val();
        let inicio = $(this).find('.inicioFestivonacional').val();
        let final = $(this).find('.finalFestivonacional').val();
        //Modificamos la fecha, para recibir el formato dd-mm-%AN% o dd-mm-%AC% siendo AN año anterior y AC año actual.
        inicio = modificarFecha(inicio);
        final = modificarFecha(final);
        if(id) {
        festivosNacionales.push({ id, nombre, inicio, final });
        } else {
            festivosNacionales.push({ nombre, inicio, final });
        }
    });

    // Convertir el objeto a JSON
    const festivosnacionalesJSON = JSON.stringify(festivosNacionales);

    if($(this).hasClass('guardar-festivos-nacional')) {
        // Enviar el objeto JSON a través de una petición AJAX
        enviarPost('/manejar/posts/festivosnacionales', {festivosnacionalesJSON: festivosnacionalesJSON},'/menu/periodos/nacionales/admin');
        // Mostrar el popup de añadido festivo centro correctamente
        mostrarPopUp("periodo/s nacional/es añadido/s");
    } else {
        enviarPost('/post/editar/festivo/nacional', {festivosnacionalesJSON: festivosnacionalesJSON},'/menu/periodos/nacionales/admin');
        mostrarPopUp("periodo nacional editado");
    }
});

//Si se está editando un festivo nacional
if(window.location.pathname == "/editar/festivo/nacional") {
    //Obtenemos la asignatura y lecciones y creamos sus filas
    const festivoNacional = JSON.parse(document.getElementById('festivosNacionales').dataset.festivonacional);
    const fila = crearFilasExistentesFestivos(festivoNacional, "nacional");
    $('#festivosNacionalesEditarTable tbody').append(fila);
}

function crearFilasExistentesFestivos(festivo, tipoDeFestivo) {

    const formatearFecha = (fechaStr) => {
        const [dia, mes, anio] = fechaStr.split('-');
        return `${dia.padStart(2, '0')}-${mes.padStart(2, '0')}-${anio.padStart(4, '20')}`;
    };
    
    const festivoInicio = formatearFecha(festivo.inicio);
    const festivoFinal = formatearFecha(festivo.final);

    let dato;
    if(tipoDeFestivo == "local") {
        dato = festivo.provincia;
    } else if (tipoDeFestivo == "centro") {
        dato = festivo.centro;
    }
    
    return $(`
        <tr class="fila-festivo-${tipoDeFestivo}" id="festivoCentro${festivo.id}">
            <td><input hidden type="text" class="form-control idFestivo${tipoDeFestivo}" name="idFestivo${tipoDeFestivo}" id="idFestivo${tipoDeFestivo}" value="${festivo.id}"></td>
            <td><input type="text" class="form-control nombreFestivo${tipoDeFestivo}" name="nombreFestivo${tipoDeFestivo}" id="nombreFestivo${tipoDeFestivo}${festivo.id}" value="${festivo.nombre}" disabled></td>
            <td><input type="text" class="form-control inicioFestivo${tipoDeFestivo} datepicker-festivo-${tipoDeFestivo}" name="inicioFestivo${tipoDeFestivo}" id="inicioFestivo${tipoDeFestivo}${festivo.id}" value="${festivoInicio}"></td>
            <td><input type="text" class="form-control finalFestivo${tipoDeFestivo} datepicker-festivo-${tipoDeFestivo}" name="finalFestivo${tipoDeFestivo}" id="finalFestivo${tipoDeFestivo}${festivo.id}" value="${festivoFinal}"></td>
            <td><input hidden type="text" class="form-control datoFestivo${tipoDeFestivo}" name="datoFestivo${tipoDeFestivo}" id="datoFestivo${tipoDeFestivo}" value=${dato}></td>
        </tr>
    `);
}

// Formulario festivos locales admin
// Datepicker de festivosLocales
$('#festivosLocalesTable tbody').on('focus', '.datepicker-festivo-local', function() {
    $(this).datepicker({
        format: 'd-mm-yyyy',
        language: 'es',
        weekStart: 1,
        startDate: new Date()
    });
});

$('#festivosLocalesEditarTable tbody').on('focus', '.datepicker-festivo-local', function() {
    $(this).datepicker({
        format: 'd-mm-yyyy',
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

//Si se está editando un festivo local
if(window.location.pathname == "/editar/festivo/local") {
    //Obtenemos la asignatura y lecciones y creamos sus filas
    const festivoLocal = JSON.parse(document.getElementById('festivosLocales').dataset.festivolocal);
    const fila = crearFilasExistentesFestivos(festivoLocal, "local");
    $('#festivosLocalesEditarTable tbody').append(fila);
}

$(document).on('click', '.guardar-festivos-local, .editar-festivo-local', function() {
    // Obtener los valores de las filas de la tabla
    let provincia = $('#nombreFestivoLocal').val();
    const festivosLocales = [];
    let tabla;
    if($(this).hasClass('guardar-festivos-local')) {
        tabla = '#festivosLocalesTable tbody tr';
    } else {
        tabla = '#festivosLocalesEditarTable tbody tr';
    }

    $(tabla).each(function() {
        const id = $(this).find('.idFestivolocal').val();
        const nombre = $(this).find('.nombreFestivolocal').val();
        let inicio = $(this).find('.inicioFestivolocal').val();
        let final = $(this).find('.finalFestivolocal').val();
        //Modificamos la fecha, para recibir el formato dd-mm-%AN% o dd-mm-%AC% siendo AN año anterior y AC año actual.
        inicio = modificarFecha(inicio);
        final = modificarFecha(final);
        if(id) {
            provincia = $(this).find('.datoFestivolocal').val();
            festivosLocales.push({ id, nombre, inicio, final });
        } else {
            festivosLocales.push({ nombre, inicio, final });
        }
    });
    // Convertir el objeto a JSON
    const festivoslocalesJSON = JSON.stringify(festivosLocales);

    const datosPost = {
        provincia: provincia,
        festivoslocalesJSON: festivoslocalesJSON
    }

    if($(this).hasClass('guardar-festivos-local')) {
        // Enviar el objeto JSON a través de una petición AJAX
        enviarPost('/manejar/posts/festivoslocales', datosPost,'/menu/periodos/locales/admin');
        // Mostrar el popup de añadido festivo centro correctamente
        mostrarPopUp("festivo/s local/es añadido/s");
    } else {
        enviarPost('/post/editar/festivo/local', datosPost,'/menu/periodos/locales/admin');
        mostrarPopUp("periodo local editado");
    }
});

//Formulario titulación
let idTitulacion = 0;
$(document).on('click', '.aniadir-fila-titulacion', function() {
    idTitulacion++;
    const fila = crearFilaTitulacion();
    $('#titulacionesTable tbody').append(fila);
});

//Editar titulacion
if(window.location.pathname == "/editar/titulacion") {
    //Obtenemos la titulación a editar
    const titulacion = JSON.parse(document.getElementById('titulacion').dataset.titulacion);
    idTitulacion++;
    const fila = crearFilaTitulacion(titulacion);
    $('#titulacionesTable tbody').append(fila);
}

function crearFilaTitulacion(titulacion) {
    const optionsCentro = obtenerCentroSelect();
    return $(`
        <tr class="fila-titulacion" id="titulacion${idTitulacion}">
            <td><input type="text" class="form-control nombreTitul" name="nombreTitul" id="nombreTitul${idTitulacion}" value="${titulacion?.nombre ?? ''}"></td>
            <td><input type="text" class="form-control abrevTitul" name="abrevTitul" id="abrevTitul${idTitulacion}" value="${titulacion?.abreviatura ?? ''}"></td>
            <td>
                <select class="form-control centroTitul" name="centroTitul" id="centroTitul${idTitulacion}">
                    <option selected>${titulacion?.centro ?? ''}</option>
                    ${optionsCentro}
                </select>
            </td>
            <td><button class="btn btn-danger eliminar-titulacion">Eliminar</button></td>
        </tr>
    `);
}

function obtenerCentroSelect(){
    //obtenemos los centros desde /formulario/titulacion
    const centros = JSON.parse(document.getElementById('centros').dataset.centros);
    let options = "";
    //Los recorremos y agregamos las opciones
    for (var i = 0; i < centros.length; i++) {
        options += `<option>${centros[i].nombreProvincia}</option>`;
    }

    return options;
}

$(document).on('click', '.crear-titulacion, .editar-titulacion', function() {
    // Obtener los valores de las filas de la tabla
    const titulaciones = [];
    let error;
    $('#titulacionesTable tbody tr').each(function() {
        const nombreTitulacion = $(this).find('.nombreTitul').val();
        const abreviatura = $(this).find('.abrevTitul').val();
        const centro = $(this).find('.centroTitul').val();

        const camposObligatorios = [
            { nombre: 'nombre', valor: nombreTitulacion },
            { nombre: 'abreviatura', valor: abreviatura },
            { nombre: 'centro', valor: centro },
        ];

        if(manejarErroresVacios(camposObligatorios)) {
            error = true;
            return;
        }

        titulaciones.push({ nombreTitulacion, abreviatura, centro });
    });

    if(error) {
        return;
    }

    // Convertir el objeto a JSON
    const titulacionesJSON = JSON.stringify(titulaciones);

    // Enviar el objeto JSON a través de una petición AJAX
    if(window.location.pathname == "/editar/titulacion") {
        const titulacion = JSON.parse(document.getElementById('titulacion').dataset.titulacion);
        enviarPost('/manejar/posts/titulaciones', {titulacionesJSON: titulacionesJSON},'/post/titulacion/editado?titulacion='+titulacion.id);
    } else {
        enviarPost('/manejar/posts/titulaciones', {titulacionesJSON: titulacionesJSON},'/post/titulacion');
    }
});

//Eliminar filas de tablas
$(document).on('click', '.eliminar-festivo-local, .eliminar-grupo, .eliminar-asignatura, .eliminar-festivo-centro, .eliminar-festivo-nacional, .eliminar-titulacion', function() {
    // Obtener la fila
    const fila = $(this).closest('tr');
    fila.remove();
});

//Claves para el menú docente y administrador
$(document).on('click', '.menu-profesor, .menu-admin, .menu-alumno', function() {
    let contrasenia;
    let usuario;

    if($(this).hasClass('menu-profesor')) {
        contrasenia = "docente";
        usuario = "Docente";
    } else if($(this).hasClass('menu-admin')) {
        contrasenia = "admin";
        usuario = "Admin";
    } else {
        contrasenia = "alumno";
        usuario = "Alumno";
    }

    Swal.fire({
        title: 'Clave',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off',
            placeholder: 'Clave'
        },
        showCancelButton: true,
        confirmButtonText: 'Entrar',
        cancelButtonText: 'Cancelar',
        preConfirm: (clave) => {
            if (clave == contrasenia && usuario == "Docente") {
                window.location.replace("/menu/calendario/docente");
                return Swal.fire({
                    icon: 'success',
                    title: 'Clave válida',
                    text: '¡Bienvenido al menú docente!',
                });
            } else if (clave == contrasenia && usuario == "Admin") {
                window.location.replace("/menu/docentes/admin");
                return Swal.fire({
                    icon: 'success',
                    title: 'Clave válida',
                    text: '¡Bienvenido al menú administrador!',
                });
            } else if(clave == contrasenia && usuario == "Alumno"){
                window.location.replace("/menu/alumno");
                return Swal.fire({
                    icon: 'success',
                    title: 'Clave válida',
                    text: '¡Bienvenido al menú alumno!',
                });
            } else {
                return Swal.fire({
                    icon: 'error',
                    title: 'Clave incorrecta',
                    text: 'Por favor, ponga la clave correcta.',
                });
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
});

function manejarErroresVacios(camposObligatorios, excepcion)
{
    for (const campo of camposObligatorios) {
        if (campo.valor === "" || campo.valor.includes("--")) {
            alertaPersonalizada("Error en " + campo.nombre, "El campo " + campo.nombre + " es obligatorio", "error");
            return true;
        }
    }
    return false;
}

function alertaPersonalizada(titulo, texto, icono)
{
    Swal.fire({
        title: titulo,
        text: texto,
        icon: icono,
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#007BFF'
    });
}

function enviarPost(url, data, href) {
    $.ajax({
        url: url, // ruta donde enviar la petición POST
        type: 'POST',
        data: data, // los datos a enviar, en este caso el objeto JSON
        success: function(response) {
            console.log(response); // loguear la respuesta del servidor (opcional)
            if (href) {
                window.location.replace(href);
            } else {
                window.location.reload();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown); // loguear el error (opcional)
        }
    });
}