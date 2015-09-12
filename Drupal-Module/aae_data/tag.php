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
<<<<<<< HEAD
	$tbl_adresse = "aae_data_adresse";
	//DB-Abfrage aller Events, die an diesem Tag stattfinden
=======
	//DB-Abfrage aller Events, die an diesem Tag stattfinden---r
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
	$resultEvents = db_select($tbl_event, 'e')
	  ->fields('e', array( 
		'start',
		'ende',
		'name',
		'EID', 
	  ))
<<<<<<< HEAD
=======
	  //->condition('start', db_like($this->currentDate.'%'), 'LIKE')
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
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
<<<<<<< HEAD
=======
	$var .= "UID:19970610T172345Z-AF23B2@example.com\n";
	//$var .= "DTSTAMP:19970610T172345Z\n";
	//$var .= "DTSTART:19970714T170000Z\n";
	//$var .= "DTEND:19970715T040000Z\n";
	$var .= "SUMMARY:Bastille Day Party\n";
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
	
	$eventID = $_POST['eventid'];
	$resultEvent = db_select($tbl_event, 'e')
	  ->fields('e', array( 
		'start',
		'ende',
		'name',
		'EID', 
<<<<<<< HEAD
		'ort',
	  ))
	  ->condition('EID', $eventID, '=')
	  ->execute(); 
	
=======
	  ))
	  //->condition('start', db_like($this->currentDate.'%'), 'LIKE')
	  ->condition('EID', $eventID, '=')
	  ->execute(); 
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
	foreach ($resultEvent as $row) {
		$start = $row->start;
		$ende = $row->ende;
		$event = $row->name;
<<<<<<< HEAD
		$ort = $row->ort;
		$eid = $row->EID;
	}
	
	$resultAdresse = db_select($tbl_adresse, 'a')
	  ->fields('a', array(
	    'strasse',
		'nr',
		'adresszusatz',
		'plz',
	  ))
	  ->condition('ADID', $ort, "=")
	  ->execute();
	
	$var .= "UID:".makeiCalFormat($start).makeiCalFormat($ende).$eid."@leipziger-ecken.de\n";
	$var.="DTSTART:".makeiCalFormat($start)."\n";
	$var.="DTEND:".makeiCalFormat($ende)."\n";
	$var.="SUMMARY:".$event."\n";
	foreach ($resultAdresse as $row) {
		$ad = $row->strasse.' '.$row->nr.'\; '.$row->plz.' Leipzig';
	}
	$var.= "LOCATION:".$ad."\n";
	$var .= "END:VEVENT\n";
	
	    header('Content-Type: text/calendar');
=======
	}
	$var.="DTSTART:".makeiCalFormat($start)."\n";
	$var.="DTEND:".makeiCalFormat($ende)."\n";
	$var.="SUMMARY:".$event."\n";

	$var .= "END:VEVENT\n";
	
	    header('Content-Type: text/plain');    //  möglich, dass du hier auch x-type/octtype, text/plain wählen kannst
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
	    header('Content-Length: ' . strlen($var));
	    header('Content-Disposition: attachment; filename="'.$event.'.ics"');
	print $var;	
}

function makeiCalFormat($datum) {
<<<<<<< HEAD
	//yyyy-mm-dd hh:mm DB (ist)
	//yyyymmddThhmmss iCal (soll)
=======
	//yyyy-mm-dd hh:mm DB
	//yyyymmddThhmmss iCal
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
	$datum = str_replace(" ", "T", $datum);
	$datum = str_replace("-", "", $datum);
	$datum = str_replace(":", "", $datum);
	$datum .= "00";
	return $datum;
<<<<<<< HEAD
=======
	
>>>>>>> d94444151509392ff8d89cb01ebe69812a03470a
}


$profileHTML = <<<EOF
<p>$events</p>

EOF;

