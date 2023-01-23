<?php
define('CARPETA_DATOS', $_SERVER['DOCUMENT_ROOT'] . '/src/datos');
define('URL_AGREGAR_GRUPO', "/grupos/?agregar=%s");
define('ID_OFERTA', 'oferta');

require("html.php");
require("HtmlTemplate.php");
require("oferta.php");
