<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/src/init.php');

function crearBotonWhatsApp(string $url, string $nrc, DOMElement $padre, DOMDocument $doc)
{
    if ($url) {
        $padre->appendChild($doc->createTextNode(' '));
        $enlaceWhatsApp = crearElementoDOM($doc, 'a', null, [
            'href' => $url,
            'class' => 'btn btn-success',
        ], $padre);
        crearTextoConIcono('Grupo de WhatsApp', 'fab fa-whatsapp', $doc, $enlaceWhatsApp);

        $agregarWhatsApp = crearElementoDOM($doc, "button", null, [
            'class' => 'btn btn-outline-secondary text-body grupo-agregable',
            'data-nrc' => $nrc,
            'data-tipo-adicion' => 'actualizar',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#dialogoAdicionGrupo',
            'title' => 'Actualizar enlace',
        ], $padre);
        crearIcono('fas fa-redo', $doc, $agregarWhatsApp);
    } else {
        $agregarWhatsApp = crearElementoDOM($doc, "button", null, [
            'class' => 'btn btn-outline-secondary text-body grupo-agregable',
            'data-nrc' => $nrc,
            'data-tipo-adicion' => 'nuevo',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#dialogoAdicionGrupo',
        ], $padre);
        crearTextoConIcono('Agregar enlace', 'fas fa-plus', $doc, $agregarWhatsApp);
    }
}

function crearDialogoAdicion(DOMElement $padre, DOMDocument $dom)
{
    $modal = crearElementoDOM($dom, "div", null, [
        'class' => 'modal fade',
        'tabindex' => '-1',
        'id' => 'dialogoAdicionGrupo',
        'aria-labelledby' => 'tituloDialogoAdicionGrupo',
    ], $padre);
    $dialogoModal = crearElementoDOM($dom, "div", null, ['class' => 'modal-dialog'], $modal);
    $formulario = crearElementoDOM($dom, "form", null, ['class' => 'modal-content', 'method' => 'POST'], $dialogoModal);

    $encabezadoModal = crearElementoDOM($dom, "div", null, ['class' => 'modal-header'], $formulario);
    $tituloModal = crearElementoDOM($dom, "h1", null, [
        'class' => 'modal-title fs-5', 'id' => 'tituloDialogoAdicionGrupo'
    ], $encabezadoModal);
    crearTextoConIcono(
        "Agregar grupo de WhatsApp",
        "fab fa-whatsapp",
        $dom,
        $tituloModal,
        'span',
        ['id' => 'textoDialogoAdicionGrupo']
    );
    crearElementoDOM($dom, "button", null, [
        'type' => "button",
        'class' => 'btn-close',
        'data-bs-dismiss' => 'modal',
        'aria-label' => 'Cerrar',
    ], $encabezadoModal);

    $cuerpoModal = crearElementoDOM($dom, "div", null, ['class' => 'modal-body'], $formulario);

    $grupoInputsNrc = crearElementoDOM($dom, "div", null, ['class' => 'row-col d-none'], $cuerpoModal);
    $grupoInputsEnlace = crearElementoDOM($dom, "div", null, ['class' => 'row-col'], $cuerpoModal);

    crearElementoDOM($dom, "label", "NRC", ['class' => 'form-label'], $grupoInputsNrc);
    $contenedorInputNrc = crearElementoDOM($dom, "div", null, ['class' => 'input-group mb-2'], $grupoInputsNrc);
    crearElementoDOM(
        $dom,
        "i",
        null,
        ['class' => 'fas fa-fingerprint'],
        crearElementoDOM($dom, "span", null, ['class' => 'input-group-text'], $contenedorInputNrc)
    );
    crearElementoDOM(
        $dom,
        "input",
        null,
        [
            'type' => 'text',
            'class' => 'form-control',
            'id' => 'nrc',
            'name' => 'nrc',
            'placeholder' => 'Número de referencia del curso',
            'autocomplete' => "off",
        ],
        $contenedorInputNrc
    );

    crearElementoDOM($dom, "label", "Ingresa el enlace al grupo de WhatsApp", ['class' => 'form-label'], $grupoInputsEnlace);
    $contenedorInputEnlace = crearElementoDOM($dom, "div", null, ['class' => 'input-group mb-2'], $grupoInputsEnlace);
    crearElementoDOM(
        $dom,
        "i",
        null,
        ['class' => 'fas fa-link'],
        crearElementoDOM($dom, "span", null, ['class' => 'input-group-text'], $contenedorInputEnlace)
    );
    crearElementoDOM(
        $dom,
        "input",
        null,
        [
            'type' => 'text',
            'class' => 'form-control',
            'id' => 'enlace',
            'name' => 'enlace',
            'placeholder' => 'Ej. https://chat.whatsapp.com/abcxyz123789',
            'pattern' => '^(?:https:\/\/)?(?:www\.)?chat\.whatsapp\.com\/[\d\w]+$',
            'required' => '',
            'autocomplete' => 'off',
        ],
        $contenedorInputEnlace
    );

    $pieModal = crearElementoDOM($dom, "div", null, ['class' => 'modal-footer'], $formulario);
    $botonEnviar = crearElementoDOM($dom, 'button', null, [
        'type' => 'submit',
        'class' => 'btn btn-success',
    ], $pieModal);
    crearTextoConIcono("Añadir grupo", "fas fa-plus", $dom, $botonEnviar);
}

function crearVistaOferta(array $materias, string $plan, DOMElement $padre, DOMDocument $dom)
{
    $contenedorOferta = crearElementoDOM(
        $dom,
        'div',
        null,
        ['class' => 'accordion accordion-flush', 'id' => ID_OFERTA],
        $padre
    );

    foreach ($materias as $clave => $cursos) {
        if ($cursos) {
            $contenedorMateria = crearElementoDOM($dom, "div", null, ['class' => 'accordion-item'], $contenedorOferta);
            $tituloAcordeon = "";
            $cabezaAcordeon = crearElementoDOM(
                $dom,
                "h2",
                null,
                ['class' => 'accordion-header text-center bg-light', 'id' => "$clave-head"],
                $contenedorMateria
            );
            $botonAcordeon = crearElementoDOM(
                $dom,
                "button",
                null,
                [
                    'type' => 'button',
                    'class' => 'accordion-button collapsed',
                    'data-bs-toggle' => 'collapse',
                    'data-bs-target' => "#$clave-collapse",
                    'aria-controls' => "$clave-collapse",
                    'aria-expanded' => 'false',
                ],
                $cabezaAcordeon
            );

            $acordeonColapso = crearElementoDOM(
                $dom,
                "div",
                null,
                [
                    'class' => 'accordion-collapse collapse text-center',
                    'id' => "$clave-collapse",
                    'aria-labelledby' => "$clave-head",
                    'data-bs-parent' => ID_OFERTA,
                ],
                $contenedorMateria
            );
            $cuerpoAcordeon = crearElementoDOM(
                $dom,
                "div",
                null,
                [
                    'class' => 'accordion-body row px-0',
                    'style' => 'margin: inherit !important',
                ],
                $acordeonColapso
            );

            foreach ($cursos as $c) {
                $curso = cargarCurso($plan, $c['nrc']);
                if (!$tituloAcordeon) {
                    $tituloAcordeon = "{$curso->clave} - {$curso->obtenerNombre()}";
                }
                
                $contenedor = crearElementoDOM(
                    $dom,
                    'div',
                    null,
                    ['class' => 'px-0 px-md-2 py-2 col-md-6 col-lg-4 col-xxl-3 d-grid'],
                    $cuerpoAcordeon
                );

                $tarjeta = crearElementoDOM(
                    $dom,
                    'div',
                    null,
                    ['class' => 'card text-center'],
                    $contenedor
                );

                crearElementoDOM(
                    $dom,
                    'div',
                    "{$curso->seccion} - NRC: {$curso->nrc}",
                    ['class' => 'card-header bg-tertiary'],
                    $tarjeta
                );

                $tarjetaCuerpo = crearElementoDOM($dom, 'div', null, ['class' => 'card-body bg-light-subtle'], $tarjeta);
                crearElementoDOM($dom, 'h5', $curso->obtenerProfesor(), ['class' => 'card-title'], $tarjetaCuerpo);

                if ($curso->obtenerDias() || $curso->obtenerHoras() || $curso->obtenerEdificio() || $curso->obtenerAula()) {
                    if ($curso->obtenerDias() || $curso->obtenerHoras()) {
                        $subtextoHorario = crearElementoDOM($dom, 'div', null, ['class' => 'card-text my-2'], $tarjetaCuerpo);
                        if ($curso->obtenerDias()) {
                            $dias = crearElementoDOM($dom, 'div', null, [], $subtextoHorario);
                            crearTextoConIcono(implode(", ", $curso->obtenerDias()), 'fas fa-calendar', $dom, $dias);
                        }
                        if ($curso->obtenerHoras()) {
                            $horas = crearElementoDOM($dom, 'div', null, [], $subtextoHorario);
                            crearTextoConIcono(implode(", ", $curso->obtenerHoras()), 'fas fa-clock', $dom, $horas);
                        }
                    }

                    if ($curso->obtenerEdificio() || $curso->obtenerAula()) {
                        $subtextoEdificio = crearElementoDOM($dom, 'div', null, ['class' => 'card-text my-2'], $tarjetaCuerpo);
                        if ($curso->obtenerEdificio()) {
                            $edificio = crearElementoDOM($dom, 'div', null, [], $subtextoEdificio);
                            crearTextoConIcono(implode(", ", $curso->obtenerEdificio()), 'fas fa-building', $dom, $edificio);
                        }
                        if ($curso->obtenerAula()) {
                            $aula = crearElementoDOM($dom, 'div', null, [], $subtextoEdificio);
                            crearTextoConIcono(implode(", ", $curso->obtenerAula()), 'fas fa-chalkboard', $dom, $aula);
                        }
                    }
                } else {
                    $info = crearElementoDOM($dom, 'div', null, [], $tarjetaCuerpo);
                    crearTextoConIcono("Sin información de esta clase.", 'fas fa-question-circle', $dom, $info);
                }

                $tarjetaPies = crearElementoDOM($dom, 'div', null, ['class' => 'card-footer bg-outline-success'], $tarjeta);
                crearBotonWhatsApp($curso->url, $curso->nrc, $tarjetaPies, $dom);
            }
            $botonAcordeon->appendChild($dom->createTextNode($tituloAcordeon));
        }
    }
}

function crearFormularioOferta(array $planes, array $selecciones, DOMElement $padre, DOMDocument $dom)
{
    $formulario = crearElementoDOM($dom, "form", null, ['class' => 'col'], $padre);
    $filaSelectores = crearElementoDOM($dom, "div", null, ['class' => 'row mb-2'], $formulario);
    $columnaPlan = crearElementoDOM($dom, "div", null, ['class' => 'col form-floating'], $filaSelectores);
    $columnaPlan->appendChild(
        crearSelectDesdeArray(
            $dom,
            $planes['p'],
            'plan',
            $selecciones['plan'],
            'Carrera',
            'Selecciona tu carrera'
        )
    );

    $columnaMalla = crearElementoDOM($dom, "div", null, ['class' => 'col form-floating'], $filaSelectores);
    $columnaMalla->appendChild(
        crearSelectDesdeArray(
            $dom,
            $planes[$selecciones['plan']]['mallas']['m'],
            'malla',
            $selecciones['malla'],
            'Malla',
            'Selecciona una malla curricular'
        )
    );

    $columnaSemestre = crearElementoDOM($dom, "div", null, ['class' => 'col form-floating'], $filaSelectores);
    $columnaSemestre->appendChild(
        crearSelectDesdeArray(
            $dom,
            $planes[$selecciones['plan']]['mallas'][$selecciones['malla']]['semestres']['s'],
            'semestre',
            $selecciones['semestre'],
            'Semestre',
            'Selecciona tu semestre actual'
        )
    );

    $filaBuscar = crearElementoDOM($dom, "div", null, ['class' => 'row mb-2'], $formulario);
    $contenedorBoton = crearElementoDOM($dom, "div", null, ['class' => 'col'], $filaBuscar);
    crearElementoDOM(
        $dom,
        'button',
        'Buscar',
        ['type' => 'submit', 'class' => 'btn btn-outline-primary w-100'],
        $contenedorBoton
    );
}

function obtenerSelecciones(array $planes)
{
    $seleccion = [
        'accion' => 'mostrarOferta',
        'mostrarBusqueda' => true,
        'nrc' => '',
    ];

    if (isset($_GET['plan']) && isset($_GET['malla']) && isset($_GET['semestre'])) {
        $seleccion['plan'] = $_GET['plan'];
        $seleccion['malla'] = $_GET['malla'];
        $seleccion['semestre'] = $_GET['semestre'];
    } else {
        $seleccion['accion'] = 'mostrarBusqueda';
        $seleccion['plan'] = array_key_first(
            $planes['p']
        );
        $seleccion['malla'] = array_key_first(
            $planes[$seleccion['plan']]['mallas']['m']
        );
        $seleccion['semestre'] = array_key_first(
            $planes[$seleccion['plan']]['mallas'][$seleccion['malla']]['semestres']['s']
        );
    }

    if (isset($_GET['agregar'])) {
        $seleccion['accion'] = 'mostrarAgregar';
        $seleccion['nrc'] = $_GET['agregar'];
        $seleccion['mostrarBusqueda'] = false;
    }

    return $seleccion;
}

function construirDocumento()
{
    $htmlTemplate = new HtmlTemplate("Grupos");
    $dom = $htmlTemplate->getDOMDocument();
    $htmlTemplate->addHeadElement(crearElementoDOM($dom, "script", file_get_contents("index.js")));
    $mainTag = $htmlTemplate->getMainElement();
    $materias = [];
    $planes = cargarPlanes();
    $selecciones = obtenerSelecciones($planes);

    crearElementoDOM($dom, "h1", "Grupos de WhatsApp", ['class' => 'fw-light'], $mainTag);
    crearFormularioOferta($planes, $selecciones, $mainTag, $dom);

    if ($selecciones['accion'] == 'mostrarOferta') {
        $materias = cargarOferta($selecciones['plan'], $selecciones['malla'], $selecciones['semestre']);
        crearVistaOferta($materias, $selecciones['plan'], $mainTag, $dom);
    }
    crearDialogoAdicion($mainTag, $dom);

    $htmlTemplate->printHTML();
}

function peticion()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            isset($_GET['plan']) &&
            isset($_POST['nrc']) &&
            isset($_POST['enlace']) &&
            isset($_POST['accion']) &&
            $_POST['accion'] == 'agregarGrupo'
        ) {
            $catalogo = new CatalogoMaterias($_GET['plan']);
            $respuesta = [
                'exito' => true,
                'mensaje' => null,
                'codigo' => 0,
                'datos' => null,
            ];

            $regexNrc = "/^\d{6}$/";
            $regexEnlace = "/^(?:https:\/\/)?(?:www\.)?chat\.whatsapp\.com\/[\w\d]+$/";

            $nrc = $_POST['nrc'];
            $enlace = $_POST['enlace'];

            if (preg_match($regexNrc, $nrc)) {
                // El NRC es válido, solo contiene 6 dígitos numéricos
                if (preg_match($regexEnlace, $enlace)) {
                    // El enlace es válido, es un enlace de WhatsApp
                    $materiaSeleccionada = $catalogo->obtener($nrc);
                    $materiaSeleccionada->url = $enlace;
                    if ($catalogo->actualizar($materiaSeleccionada) === false) {
                        $respuesta['exito'] = false;
                        $respuesta['codigo'] = 3;
                        $respuesta['mensaje'] = 'No se pudo guardar el grupo';
                    }
                } else {
                    // El enlace no es válida, no es un enlace de WhatsApp
                    $respuesta['exito'] = false;
                    $respuesta['codigo'] = 2;
                    $respuesta['mensaje'] = 'El enlace no es un enlace de WhatsApp, o bien, no está escrito correctamente';
                }
            } else {
                // El NRC es válido, no contiene solo 6 dígitos numéricos
                $respuesta['exito'] = false;
                $respuesta['codigo'] = 1;
                $respuesta['mensaje'] = 'El NRC no es válido, o bien, no está escrito correctamente';
            }

            respuestaJson($respuesta);
        } else {
            die('Error en solicitud');
        }

    } else {
        construirDocumento();
    }
}

peticion();

