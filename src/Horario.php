<?php

class Horario
{
    private string $centro;
    public string $sesion;
    public string $horas;
    public string $dias;
    public string $edificio;
    public string $aula;
    public string $periodo;
    const _SEPARADOR_DATOS = '|';

    function __construct(
        string $centro,
        string $sesion,
        string $horas,
        string $dias,
        string $edificio,
        string $aula,
        string $periodo
    ) {
        $this->centro = $centro;
        $this->sesion = $sesion;
        $this->horas = $horas;
        $this->dias = $dias;
        $this->edificio = $this->_limpiarEdificio($edificio, $centro);
        $this->aula = $aula;
        $this->periodo = $periodo;
    }

    function _limpiarEdificio(string $edificio, string $centro)
    {
        if (mb_strstr($edificio, "Virtual")) {
            $edificio = trim(explode("Virtual", $edificio)[-1]);
            $edificio = "{$centro}ESV{$edificio}";
        } elseif (mb_strstr($edificio, "Edificio")) {
            $edificio = trim(explode("Edificio", $edificio)[-1]);
            $edificio = "{$centro}ED{$edificio}";
        }
        return $edificio;
    }

    function obtenerSesion()
    {
        $sesion = $this->sesion;
        $sesion = is_int($sesion) ? $sesion : intval(mb_substr($sesion, 1));
        return $sesion;
    }

    function obtenerHoras()
    {
        $horas = $this->horas;
        $textoHoras = $horas;

        if (!(mb_stristr($textoHoras, "-") && mb_stristr($textoHoras, ":"))) {
            $horasIni = sprintf("%02d:%02d", intval(mb_substr($horas, 0, 2)), intval(mb_substr($horas, 2, 2)));
            $horasFin = sprintf("%02d:%02d", intval(mb_substr($horas, 5, 2)), intval(mb_substr($horas, 7, 2)));
            $textoHoras = "{$horasIni} - {$horasFin}";
        }

        return $textoHoras;
    }

    function obtenerDias()
    {
        $dias = [];
        $diasTexto = $this->dias;

        if (mb_stristr($diasTexto, ".")) {
            $diasClave = [
                "L" => "Lunes",
                "M" => "Martes",
                "I" => "Miércoles",
                "J" => "Jueves",
                "V" => "Viernes",
                "S" => "Sábado"
            ];

            $diasSIIAU = preg_replace("/[\.\s]+/", "", $diasTexto);

            foreach (str_split($diasSIIAU) as $letra) {
                if (array_key_exists($letra, $diasClave)) {
                    $dias[] = $diasClave[$letra];
                }
            }
            $diasTexto = implode(", ", $dias);
        }

        return $diasTexto;
    }

    function obtenerEdificio()
    {
        $edificio = $this->edificio;
        $centro = $this->centro;
        if (!(mb_strstr($edificio, "Edificio") || mb_strstr($edificio, "Virtual"))) {
            preg_match("/^{$centro}ESV|^VIRTU\w*/", $edificio, $coincVirt);
            if ($coincVirt) {
                $edificio = preg_replace("/^{$centro}ESV|^VIRTU\w*/", "", $edificio);
                $edificio = "Virtual {$edificio}";
            } else {
                $edificio = preg_replace("/^{$centro}(ED)?/", "", $edificio);
                $edificio = "Edificio {$edificio}";
            }
        }
        return $edificio;
    }

    function obtenerAula()
    {
        $aula = $this->aula;
        $textoAula = $aula;
        preg_match("/([A-Za-z]+)(\d+)/", $aula, $partes_aula);
        if ($partes_aula) {
            $alfa = $partes_aula[1];
            $num = intval($partes_aula[2]);
            $textoAula = sprintf("%s%02d", $alfa, $num);
        }

        return $textoAula;
    }


    function obtenerPeriodo()
    {
        return $this->periodo;
    }

    function __toString()
    {
        $datos = [
            $this->centro,
            $this->sesion,
            $this->horas,
            $this->dias,
            $this->edificio,
            $this->aula,
            $this->periodo,
        ];

        $salida = implode(self::_SEPARADOR_DATOS, $datos);

        return $salida;
    }
}
