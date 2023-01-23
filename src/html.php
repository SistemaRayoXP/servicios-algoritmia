<?php

function crearElementoDOM(
    DOMDocument $doc,
    string $tag,
    string $contenido = null,
    array $atributos = [],
    DOMElement $padre = null
) {
    $nodo = null;
    if ($contenido) {
        $nodo = $doc->createElement($tag, $contenido);
    } else {
        $nodo = $doc->createElement($tag);
    }
    foreach ($atributos as $clave => $valor) {
        $nodo->setAttribute($clave, $valor);
    }

    if ($padre) {
        $padre->appendChild($nodo);
    }

    return $nodo;
}

function crearSelectDesdeArray(
    DOMDocument $doc,
    array $opciones,
    string $idSelect,
    string $opcionSeleccionada = "",
    string $texto = "",
    string $textoAria = ""
) {
    $texto = $texto ? $texto : $idSelect;
    $textoAria = $textoAria ? $textoAria : $texto;

    $contenedorSelect = $doc->createElement("div");
    $contenedorSelect->setAttribute('class', 'form-floating');

    $elementoSelect = $doc->createElement("select");
    $elementoSelect->setAttribute('class', 'form-select');
    $elementoSelect->setAttribute('id', $idSelect);
    $elementoSelect->setAttribute('name', $idSelect);
    $elementoSelect->setAttribute('aria-label', $textoAria);
    $elementoSelect->setAttribute('title', $textoAria);
    $contenedorSelect->appendChild($elementoSelect);

    foreach ($opciones as $id => $etiqueta) {
        $opcionSelect = $doc->createElement("option", $etiqueta);
        $opcionSelect->setAttribute('value', $id);
        if ($opcionSeleccionada == $id) {
            $opcionSelect->setAttribute('selected', '');
        }
        $elementoSelect->appendChild($opcionSelect);
    }

    $etiquetaSelect = $doc->createElement("label", $texto);
    $etiquetaSelect->setAttribute('for', $idSelect);
    $contenedorSelect->appendChild($etiquetaSelect);

    return $contenedorSelect;
}

function crearIcono(string $claseIcono, DOMDocument $doc, DOMElement $padre = null)
{
    $icono = crearElementoDOM($doc, 'i', null, ['class' => $claseIcono]);
    if ($padre) {
        $padre->appendChild($icono);
    }

    return $icono;
}

function crearTexto(string $texto, DOMDocument $doc, DOMElement $padre = null)
{
    $texto = $doc->createTextNode($texto);
    if ($padre) {
        $padre->appendChild($texto);
    }

    return $texto;
}

function crearTextoConIcono(
    string $texto,
    string $iconoFA,
    DOMDocument $doc,
    DOMNode $padre = null,
    string $tag = null,
    array $atributos = []
) {
    crearIcono($iconoFA, $doc, $padre);
    if ($tag) {
        $elemento = crearElementoDOM($doc, $tag, null, $atributos, $padre);
        crearTexto(' ' . $texto, $doc, $elemento);
    } else {
        $elemento = crearTexto(' ' . $texto, $doc, $padre);
    }
    return $elemento;
}

function respuestaJson($datos)
{
    header('Content-Type: text/json');
    print json_encode($datos, JSON_NUMERIC_CHECK);
    die;
}
