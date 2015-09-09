<?php
	
	require_once $modulePath . '/database/db_connect.php';
	$db = new DB_CONNECT();
	global $user;

$pathThisFile = $_SERVER['REQUEST_URI']; 

	//EID holen:
	$path = current_path();
	$explodedpath = explode("/", $path);
	$tag = $explodedpath[1];
	$tbl_event = "aae_data_event";//---r
	//DB-Abfrage aller Events, die an diesem Tag stattfinden---r
	$resultEvents = db_select($tbl_event, 'e')
	  ->fields('e', array( 
		'start',
		'ende',
		'name',
		'EID', 
	  ))
	  //->condition('start', db_like($this->currentDate.'%'), 'LIKE')
	  ->condition('start', $tag.'%', 'LIKE')
	  ->orderBy('name', 'ASC')
	  ->execute(); 
	foreach ($resultEvents as $row) {
		$events .= $row->start.' - '.$row->ende.' '.'<a href="?q=Eventprofil/'.$row->EID.'">'.$row->name.'</a><form action='.$pathThisFile.' method="POST" enctype="multipart/form-data"><input type="hidden" name="eventid" value="'.$row->EID.'"><input type="submit" class="event" id="icalSubmit" name="submit" value="Download"></form><br>';
	}

if (isset($_POST['submit'])) {
	$var = null;
	$event=null;
	$var .= "BEGIN:VCALENDAR\r\n";
	$var .= "PRODID;X-RICAL-TZSOURCE=TZINFO:-//com.denhaven2/NONSGML ri_cal gem//EN\r\n";
	$var .= "CALSCALE:GREGORIAN\n";
	$var .= "VERSION:2.0\n";
	
	$var .= "BEGIN:VEVENT\n";
	$var .= "UID:19970610T172345Z-AF23B2@example.com\n";
	//$var .= "DTSTAMP:19970610T172345Z\n";
	//$var .= "DTSTART:19970714T170000Z\n";
	//$var .= "DTEND:19970715T040000Z\n";
	$var .= "SUMMARY:Bastille Day Party\n";
	
	$eventID = $_POST['eventid'];
	$resultEvent = db_select($tbl_event, 'e')
	  ->fields('e', array( 
		'start',
		'ende',
		'name',
		'EID', 
	  ))
	  //->condition('start', db_like($this->currentDate.'%'), 'LIKE')
	  ->condition('EID', $eventID, '=')
	  ->execute(); 
	foreach ($resultEvent as $row) {
		$start = $row->start;
		$ende = $row->ende;
		$event = $row->name;
	}
	$var.="DTSTART:".makeiCalFormat($start)."\n";
	$var.="DTEND:".makeiCalFormat($ende)."\n";
	$var.="SUMMARY:".$event."\n";

	$var .= "END:VEVENT\n";
	
	    header('Content-Type: text/plain');    //  möglich, dass du hier auch x-type/octtype, text/plain wählen kannst
	    header('Content-Length: ' . strlen($var));
	    header('Content-Disposition: attachment; filename="'.$event.'.ics"');
	print $var;	
}

function makeiCalFormat($datum) {
	//yyyy-mm-dd hh:mm DB
	//yyyymmddThhmmss iCal
	$datum = str_replace(" ", "T", $datum);
	$datum = str_replace("-", "", $datum);
	$datum = str_replace(":", "", $datum);
	$datum .= "00";
	return $datum;
	
}


$profileHTML = <<<EOF
<p>$events</p>

EOF;

