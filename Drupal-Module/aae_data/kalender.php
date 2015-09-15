<?php
/**
 * Stellt einen Kalender fuer die Events dar
 */
include 'calendar.php';
$calendar = new Calendar();
$content = $calendar->show();

$profileHTML = <<<EOF
EOF;
	
$profileHTML .= $content;
