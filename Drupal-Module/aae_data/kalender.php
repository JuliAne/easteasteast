<?php
/*
	$calendar = new Calendar();
	$content = $calendar->show();
	*/	
	include 'calendar.php';
	$calendar = new Calendar();
	$content = $calendar->show();
	
$profileHTML = <<<EOF
<p>Hallo</p>

EOF;

	$profileHTML .= $content;
	$profileHTML .= '<p>Hallo</p>';