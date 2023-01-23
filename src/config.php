<?php

class Configuracion
{
    static private function __obtenerRutaConfig()
    {
        return implode(DIRECTORY_SEPARATOR, [CARPETA_DATOS, 'config.json']);
    }

    static private function __cargar()
    {
        $config = [];
        $rutaConfig = self::__obtenerRutaConfig();
        if (file_exists($rutaConfig)) {
            $config = json_decode(file_get_contents($rutaConfig), true);
        }
        return $config;
    }

    static private function __guardar(array $config)
    {
        $archivo = fopen(self::__obtenerRutaConfig(), "w");
        fwrite($archivo, json_encode($config));
        fclose($archivo);
    }

    static public function obtener(string $clave)
    {
        $config = self::__cargar();
        $valor = isset($config[$clave]) ? $config[$clave] : null;
        return $valor;
    }

    static public function establecer(string $clave, $valor)
    {
        $config = self::__cargar();
        $config[$clave] = $valor;
        self::__guardar($config);
    }
}
