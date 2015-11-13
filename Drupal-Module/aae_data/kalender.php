<?php
/**
 * Stellt einen Kalender fuer die Events dar
 */

$modulePath = drupal_get_path('module', 'aae_data');
include $modulePath . '/kalendarKlasse.php';
 
$calendar = new Calendar();
$content = $calendar->show();

$profileHTML = <<<EOF
EOF;

$profileHTML .= $content;
