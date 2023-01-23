<?php

require("CatalogoMaterias.php");

function obtenerCicloActual()
{
    $cadenaCiclo = date("Y");
    $cadenaCiclo .= intval(date("M")) <= 6 ? "10" : "20";
    return $cadenaCiclo;
}

function cargarXml()
{
    $rutaPlanes = CARPETA_DATOS . '/planes.xml';
    $xml = new DOMDocument();
    $xml->load($rutaPlanes);

    return $xml;
}

function cargarPlanes()
{
    $planes = ['p' => []];
    $xml = cargarXml();
    $tagsPlanes = $xml->getElementsByTagName("plan");

    foreach ($tagsPlanes as $tP) {
        $idP = $tP->getAttribute('id');
        $planes['p'][$idP] = $idP;
        $planes[$idP] = ['nombre' => $idP, 'mallas' => ['m' => []]];
        $tagsMallas = $tP->getElementsByTagName("malla");

        foreach ($tagsMallas as $tM) {
            $nombreM = $tM->getAttribute('nombre');
            $idM = $tM->getAttribute('id');
            $planes[$idP]['mallas']['m'][$idM] = $nombreM;
            $planes[$idP]['mallas'][$idM] = ['nombre' => $nombreM, 'semestres' => ['s' => []]];
            $tagsSemestres = $tM->getElementsByTagName("semestre");

            foreach ($tagsSemestres as $tS) {
                $numero = intval($tS->getAttribute("numero"));
                $materias = $tS->getElementsByTagName("materia");
                $planes[$idP]['mallas'][$idM]['semestres']['s'][$numero] = $numero;
                $planes[$idP]['mallas'][$idM]['semestres'][$numero] = [
                    'numero' => strval($numero), 'materias' => []
                ];

                foreach ($materias as $tag) {
                    $planes[$idP]['mallas'][$idM]['semestres'][$numero]['materias'][] = $tag->nodeValue;
                }
            }
            asort($planes[$idP]['mallas'][$idM]['semestres']['s']);
        }
    }

    return $planes;
}

function cargarSemestre(string $idPlan, string $idMalla, int $numSemestre)
{
    $plan = null;
    $malla = null;
    $semestre = null;

    $xml = cargarXml();
    $tagsPlanes = $xml->getElementsByTagName("plan");

    foreach ($tagsPlanes as $tag) {
        if ($tag->getAttribute('id') == $idPlan) {
            $plan = $tag;
        }
    }

    if ($plan) {
        $tagsMallas = $plan->getElementsByTagName("malla");

        foreach ($tagsMallas as $tag) {
            if ($tag->getAttribute('id') == $idMalla) {
                $malla = $tag;
            }
        }

        if ($malla) {
            $tagsSemestres = $malla->getElementsByTagName("semestre");
            foreach ($tagsSemestres as $tag) {
                $numero = intval($tag->getAttribute("numero"));
                if ($numero == $numSemestre) {
                    $materias = $tag->getElementsByTagName("materia");
                    foreach ($materias as $tag) {
                        $semestre[$tag->nodeValue] = $tag->nodeValue;
                    }
                }
            }
        }
    }

    return $semestre;
}

function cargarOferta(string $plan, string $malla, string $semestre)
{
    $ciclo = obtenerCicloActual();
    $nombreCatalogo = sprintf("%s_%s.json", $plan, $ciclo);
    $rutaCatalogo = implode(DIRECTORY_SEPARATOR, [CARPETA_DATOS, $nombreCatalogo]);
    $materias = cargarSemestre($plan, $malla, $semestre);
    $catalogo = json_decode(file_get_contents($rutaCatalogo), true);

    return array_intersect_key($catalogo, $materias);
}

/**
 * Obtiene un objeto curso
 *
 * @param string $plan
 * @param string $nrc
 * @return \Materia
 */
function cargarCurso(string $plan, string $nrc)
{
    $catalogo = new CatalogoMaterias($plan);

    return $catalogo->obtener($nrc);
}
