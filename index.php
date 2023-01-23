<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/src/init.php');

$mainDoc = new HtmlTemplate();

$htmlString = $mainDoc->printHTML();
