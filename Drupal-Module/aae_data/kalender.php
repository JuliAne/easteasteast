<?php
/**
 * Stellt einen Kalender fuer die Events dar
 */
include 'kalendarKlasse.php';
$calendar = new Calendar();
$content = $calendar->show();

$profileHTML = <<<EOF
EOF;

$profileHTML .= $content;
