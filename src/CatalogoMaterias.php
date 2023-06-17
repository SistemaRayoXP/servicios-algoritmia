<?php
// Un administrador del JSON que genera el CatalogoMaterias de Python

require("Materia.php");
require("Horario.php");

class CatalogoMaterias
{
    # Ruta al catálogo de materias
    private string $rutaCatalogoMaterias;
    private array $materias;
    private string $idPlan;

    function __construct(string $idPlan)
    {
        $this->idPlan = $idPlan;
        $this->rutaCatalogoMaterias = $this->obtenerRutaActualCatalogoMaterias();
        $this->cargar();
    }

    function __clasificar__(array $materias)
    {
        $materiasClasificadas = [];
        foreach ($materias as $mat) {
            if (!array_key_exists($mat->clave, $materiasClasificadas)) {
                $materiasClasificadas[$mat->clave] = [];
            }

            $materiasClasificadas[$mat->clave][$mat->nrc] = $mat;
        }

        return $materiasClasificadas;
    }

    function __aplanarMaterias__()
    {
        $materiasAplanadas = [];

        foreach ($this->materias as $materia) {
            foreach ($materia as $clase) {
                $materiasAplanadas[$clase->nrc] = $clase;
            }
        }

        return $materiasAplanadas;
    }

    function __crearMateriaDesdeArray__(array $arraymateria)
    {
        $horarios = [];
        $centro = $arraymateria["centro"];

        for ($i = 0; $i < sizeof($arraymateria["sesion"]); $i++) {
            $sesion = $arraymateria["sesion"][$i];
            $horas = $arraymateria["horas"][$i];
            $dias = $arraymateria["dias"][$i];
            $edif = $arraymateria["edificio"][$i];
            $aula = $arraymateria["aula"][$i];
            $periodo = $arraymateria["periodo"][$i];
            $horarios[] = new Horario($centro, $sesion, $horas, $dias, $edif, $aula, $periodo);
        }

        return new Materia(
            $centro,
            $arraymateria["nrc"],
            $arraymateria["clave"],
            $arraymateria["materia"],
            $arraymateria["seccion"],
            $arraymateria["creditos"],
            $arraymateria["cupo"],
            $arraymateria["disponible"],
            $horarios,
            $arraymateria["profesor"],
            array_key_exists("eliminada", $arraymateria) ? $arraymateria["eliminada"] : false,
            $arraymateria["url"]
        );
    }

    function __materiasAArray__()
    {
        $dictMaterias = [];

        foreach ($this->materias as $clave => $materia) {
            $dictMaterias[$clave] = [];

            foreach ($materia as $curso) {
                $dictMateria = $curso->obtenerArray();
                $dictMaterias[$clave][$curso->nrc] = $dictMateria;
            }
        }
        return $dictMaterias;
    }


    function obtenerNrcsCatalogo()
    {
        return array_keys($this->__aplanarMaterias__());
    }

    static function obtenerCicloActual()
    {
        $cadenaCiclo = date("Y");
        $cadenaCiclo .= intval(date("m")) < 6 ? "10" : "20";
        return $cadenaCiclo;
    }

    function obtenerRutaActualCatalogoMaterias()
    {
        $cadenaCatalogo = sprintf("%s_%s.json", $this->idPlan, CatalogoMaterias::obtenerCicloActual());
        $rutaCatalogo = implode(DIRECTORY_SEPARATOR, [CARPETA_DATOS, $cadenaCatalogo]);
        return $rutaCatalogo;
    }

    function cargar(string $ruta = null)
    {
        $ruta = $ruta ? $ruta : $this->rutaCatalogoMaterias;

        if (file_exists($ruta)) {
            $listaMaterias = [];

            $dictMaterias = json_decode(file_get_contents($this->rutaCatalogoMaterias), true);

            foreach ($dictMaterias as $clave => $materia) {
                foreach ($materia as $clase) {
                    try {
                        $materia = $this->__crearMateriaDesdeArray__($clase);
                        $listaMaterias[] = $materia;
                    } catch (\Throwable $th) {
                        trigger_error("Error cargando NRC#{$clase['nrc']}", E_WARNING);
                    }
                }
            }
        }

        $this->materias = $this->__clasificar__($listaMaterias);
    }

    function actualizar(Materia $materia)
    {
        $this->materias[$materia->clave][$materia->nrc] = $materia;
        return $this->guardar();
    }

    function guardar(string $ruta = null)
    {
        $guardadoCorrecto = true;

        try {
            $dictMaterias = $this->__materiasAArray__();
            $ruta = is_null($ruta) ? $this->rutaCatalogoMaterias : $ruta;

            $json = json_encode($dictMaterias, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            if (($guardadoCorrecto = file_put_contents($ruta, $json)) === false) {
                throw new Exception("Error al guardar las materias en disco");
            }
        } catch (Exception $e) {
            $guardadoCorrecto = false;
            // echo "Error: " . $e->getMessage() . "\n";
            // echo "Detalles:\n";
            // echo $e->getTraceAsString();
        }

        return $guardadoCorrecto;
    }

    /**
     * Obtiene un objeto materia con el NRC dado
     *
     * @param integer $nrc Número de Referencia del Curso
     * @return Materia El objeto materia para consulta
     */
    function obtener(int $nrc)
    {
        $catalogoMaterias = $this->__aplanarMaterias__();

        if (array_search($nrc, array_keys($catalogoMaterias)) === false)
            throw new RangeException("El NRC '$nrc' no se encuentra en el catálogo '{$this->rutaCatalogoMaterias}'", 1);

        return $catalogoMaterias[$nrc];
    }

    function obtenerPorClave(string $clave)
    {
        return $this->materias[$clave];
    }

    /**
     * Obtiene todas las materias del catálogo
     *
     * @param boolean $clasificar Clasificar las secciones por clave.
     * @return array Un dict de objetos materia accesibles por NRC
     */
    function obtenerTodo(bool $clasificar = true)
    {
        $materiasCatalogo = $this->materias;

        if (!$clasificar)
            $materiasCatalogo = $this->__aplanarMaterias__();

        return $materiasCatalogo;
    }
}
