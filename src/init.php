<?php
// ini_set('display_errors', true);
// ini_set('display_startup_errors', true);

define('CARPETA_DATOS', $_SERVER['DOCUMENT_ROOT'] . '/src/datos');
define('ID_OFERTA', 'oferta');
define('EJECUCION_DESDE_WEB', true);

require("config.php");
require("html.php");
require("HtmlTemplate.php");
require("oferta.php");
require("cron.php");

// El host actual no permite agregar cron desde consola
// if (!Configuracion::obtener('CRON_ESTABLECIDO')) {
//     verificarYEstablecerEjecucionCron();
// }
