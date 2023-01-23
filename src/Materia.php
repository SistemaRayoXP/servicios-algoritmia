<?php

class Materia
{
    public string $centro;
    public int $nrc;
    public string $clave;
    public string $nombre;
    public string $seccion;
    public int $creditos;
    public int $cupo;
    public int $disponible;
    public array $horarios;
    public string $profesor;
    public string $url;
    public bool $eliminada = false;

    function __construct(string $centro, int $nrc, string $clave, string $nombre,
                 string $seccion, int $creditos, int $cupo, int $disponible,
                 array $horarios, string $profesor = "", bool $eliminada = false,
                 string $url = "")
    {
        $this->centro = $centro;
        $this->nrc = $nrc;
        $this->clave = $clave;
        $this->nombre = mb_strtoupper($nombre);
        $this->seccion = $seccion;
        $this->creditos = $creditos;
        $this->cupo = $cupo;
        $this->disponible = $disponible;
        $this->horarios = $horarios;
        $this->profesor = mb_strtoupper($profesor);
        $this->url = $url;
        $this->eliminada = $eliminada;
    }

    function indiceValido(int $i = null, array $lista)
    {
        return (!is_null($i) && ($i > 0 && $i < sizeof($lista)));
    }

    function obtenerSesion($indiceHorario = null, bool $legible_por_humanos = true)
    {
        $sesion = [];
        if ($this->indiceValido($indiceHorario, $this->horarios)) {
            $horario = $this->horarios[$indiceHorario];
            $sesion = $horario->obtenerSesion();
        }
        else {
            foreach ($this->horarios as $horario) {
                if ($legible_por_humanos) {
                    $sesion[] = $horario->obtenerSesion();
                }
                else {
                    $sesion[] = $horario->sesion;
                }
            }
            if ($legible_por_humanos) {
                $sesion = array_unique($sesion);
            }
        }
        return $sesion;
    }

    function obtenerHoras($indiceHorario = null, bool $legible_por_humanos = true)
    {
        $horas = [];
        if ($this->indiceValido($indiceHorario, $this->horarios)) {
            $horario = $this->horarios[$indiceHorario];
            $horas = $horario->obtenerHoras();
        }
        else {
            foreach ($this->horarios as $horario) {
                if ($legible_por_humanos) {
                    $horas[] = $horario->obtenerHoras();
                }
                else {
                    $horas[] = $horario->horas;
                }
            }
            if ($legible_por_humanos) {
                $horas = array_unique($horas);
            }
        }

        return $horas;
    }

    function obtenerDias($indiceHorario = null, bool $legible_por_humanos = true)
    {
        $dias = [];
        if ($this->indiceValido($indiceHorario, $this->horarios)) {
            $horario = $this->horarios[$indiceHorario];
            $dias = $horario->obtenerDias();
        }
        else {
            foreach ($this->horarios as $horario) {
                if ($legible_por_humanos) {
                    $dias[] = $horario->obtenerDias();
                }
                else {
                    $dias[] = $horario->dias;
                }
            }
            if ($legible_por_humanos) {
                $dias = array_unique($dias);
            }
        }
        return $dias;
    }

    function obtenerEdificio($indiceHorario = null, bool $legible_por_humanos = true)
    {
        $edificios = [];
        if ($this->indiceValido($indiceHorario, $this->horarios)) {
            $horario = $this->horarios[$indiceHorario];
            $edificios = $horario->obtenerEdificio();
        }
        else {
            foreach ($this->horarios as $horario) {
                if ($legible_por_humanos) {
                    $edificios[] = $horario->obtenerEdificio();
                }
                else {
                    $edificios[] = $horario->edificio;
                }
            }
            if ($legible_por_humanos) {
                $edificios = array_unique($edificios);
            }
        }
        return $edificios;
    }

    function obtenerAula($indiceHorario = null, bool $legible_por_humanos = true)
    {
        $aulas = [];
        if ($this->indiceValido($indiceHorario, $this->horarios)) {
            $horario = $this->horarios[$indiceHorario];
            $aulas = $horario->obtenerAula();
        }
        else {
            foreach ($this->horarios as $horario) {
                if ($legible_por_humanos) {
                    $aulas[] = $horario->obtenerAula();
                }
                else {
                    $aulas[] = $horario->aula;
                }
            }
            if ($legible_por_humanos) {
                $aulas = array_unique($aulas);
            }
        }
        return $aulas;
    }

    function obtenerPeriodo($indiceHorario = null, bool $legible_por_humanos = true)
    {
        $periodos = [];
        if ($this->indiceValido($indiceHorario, $this->horarios)) {
            $horario = $this->horarios[$indiceHorario];
            $periodos = $horario->obtenerPeriodo();
        }
        else {
            foreach ($this->horarios as $horario) {
                if ($legible_por_humanos) {
                    $periodos[] = $horario->obtenerPeriodo();
                }
                else {
                    $periodos[] = $horario->periodo;
                }
            }
            if ($legible_por_humanos) {
                $periodos = array_unique($periodos);
            }
        }
        return $periodos;
    }

    function obtenerNombre()
    {
        return mb_convert_case($this->nombre, MB_CASE_TITLE_SIMPLE);
    }

    function obtenerProfesor()
    {
        return mb_convert_case($this->profesor, MB_CASE_TITLE_SIMPLE);
    }

    /**
     * Devuelve un diccionario con los detalles de la materia en formato legible por humanos
     *
     * @return void
     */
    function obtenerVersionHumana()
    {
        $mat_legible_por_humanos = [
            "centro" => $this->centro,
            "seccion" => $this->seccion,
            "materia" => $this->obtenerNombre(),
            "cupo" => $this->cupo,
            "disponible" => $this->disponible,
            "creditos" => $this->creditos,
            "nrc" => $this->nrc,
            "clave" => $this->clave,
            "sesion" => $this->obtenerSesion(),
            "horas" => $this->obtenerHoras(),
            "dias" => $this->obtenerDias(),
            "edificio" => $this->obtenerEdificio(),
            "aula" => $this->obtenerAula(),
            "periodo" => $this->obtenerPeriodo(),
            "profesor" => $this->obtenerProfesor(),
            "url" => $this->url,
            "eliminada" => $this->eliminada,
        ];
        return $mat_legible_por_humanos;
    }

    /**
     * Devuelve un diccionario con los detalles de la materia
     *
     * @return void
     */
    function obtenerArray()
    {
        $dictMateria = [
            "centro" => $this->centro,
            "seccion" => $this->seccion,
            "materia" => $this->nombre,
            "cupo" => $this->cupo,
            "disponible" => $this->disponible,
            "creditos" => $this->creditos,
            "nrc" => $this->nrc,
            "clave" => $this->clave,
            "sesion" => $this->obtenerSesion(null, false),
            "horas" => $this->obtenerHoras(null, false),
            "dias" => $this->obtenerDias(null, false),
            "edificio" => $this->obtenerEdificio(null, false),
            "aula" => $this->obtenerAula(null, false),
            "periodo" => $this->obtenerPeriodo(null, false),
            "profesor" => $this->obtenerProfesor(),
            "url" => $this->url,
            "eliminada" => $this->eliminada,
        ];
        return $dictMateria;
    }
}