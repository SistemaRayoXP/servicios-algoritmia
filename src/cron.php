<?php

require_once("config.php");

/**
 * Realiza las tareas de la web programadas
 *
 * @return void
 */
function ejecutarTareasProgramadas()
{
    $tareas = [
        "python3 src/python/get_offer.py"
    ];

    foreach ($tareas as $comando) {
        var_dump(shell_exec($comando));
    }
}

/**
 * Verificar si el archivo de tareas programadas
 * se encuentra establecida en el crontab del sistema
 *
 * @param integer $intento
 * @return void
 */
function verificarYEstablecerEjecucionCron(int $intento = 0)
{
    $entradasCrontab = shell_exec("crontab -l");
    $cronLocal = "30 7,9,11,13,15,17 * 1-2,6-8,12 1-6 " . __FILE__;
    $comando = sprintf('echo "%s" | crontab -', $cronLocal);

    if ($entradasCrontab !== null && strstr($entradasCrontab, $cronLocal)) {
        Configuracion::establecer('CRON_ESTABLECIDO', true);
    } else {
        if ($intento < 3) {
            shell_exec($comando);
            verificarYEstablecerEjecucionCron($intento);
        }
    }
}

/**
 * No llamar a esta funcion; no esta implementada
 *
 * @return void
 */
function eliminarCron()
{
    shell_exec('crontab -r');
    Configuracion::establecer('CRON_ESTABLECIDO', false);
}

function iniciarCron()
{
    // verificarYEstablecerEjecucionCron();
    ejecutarTareasProgramadas();
}

if (!defined('EJECUCION_DESDE_WEB')) {
    iniciarCron();
}
