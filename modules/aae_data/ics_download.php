<?php
/**
 * Moeglichkeit die einzelnen Events als .ics-Datei (iCal-Format)
 * herunterzuladen.
 */

global $user;

$pathThisFile = $_SERVER['REQUEST_URI'];

//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$tag = $explodedpath[1];
$tbl_event = "aae_data_event";
$tbl_adresse = "aae_data_adresse";

//DB-Abfrage aller Events, die an diesem Tag stattfinden
$resultEvents = db_select($tbl_event, 'e')
  ->fields('e', array(
    'start',
	'ende',
	'name',
	'EID',
  ))
  ->condition('start', $tag.'%', 'LIKE')
  ->orderBy('name', 'ASC')
  ->execute();

if (isset($_POST['submit'])) {

  $var = null;
  $event=null;
  //Generierung der .ics-Datei
  $var .= "BEGIN:VCALENDAR\r\n";
  $var .= "PRODID;X-RICAL-TZSOURCE=TZINFO:-//com.denhaven2/NONSGML ri_cal gem//EN\r\n";
  $var .= "CALSCALE:GREGORIAN\n";
  $var .= "VERSION:2.0\n";
  $var .= "BEGIN:VEVENT\n";

  $eventID = $_POST['eventid'];
  $resultEvent = db_select($tbl_event, 'e')
   ->fields('e')
	 ->condition('EID', $eventID, '=')
	 ->execute();

  foreach ($resultEvent as $row) {
    $start = $row->start;
	$ende = $row->ende;
	$event = $row->name;
	$ort = $row->ort;
	$eid = $row->EID;
  $beschreibung = $row->kurzbeschreibung;
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

  $var .= "UID:" . makeiCalFormat($start) . makeiCalFormat($ende) . $eid . "@leipziger-ecken.de\n";
  $var .= "DTSTART:" . makeiCalFormat($start) . "\n";
  $var .= "DTEND:" . makeiCalFormat($ende) . "\n";
  $var .= "SUMMARY:" . $event . "\n";
  $var .= "DESCRIPTION:" . $beschreibung . "\n";

  foreach ($resultAdresse as $row) {
    $ad = $row->strasse . ' ' . $row->nr . '\; ' . $row->plz . ' Leipzig';
  }
  $var .= "LOCATION:" . $ad . "\n";
  $var .= "END:VEVENT\n";

  header('Content-Type: text/plain');
  header('Content-Length: ' . strlen($var));
  header('Content-Disposition: attachment; filename="' . $event . '.ics"');
  print $var;
}

/**
 * Datumsformatierung fuer iCal
 */
function makeiCalFormat($datum) {
  //yyyy-mm-dd hh:mm DB (ist)
  //yyyymmddThhmmss iCal (soll)
  $datum = str_replace(" ", "T", $datum);
  $datum = str_replace("-", "", $datum);
  $datum = str_replace(":", "", $datum);
  $datum .= "00";
  return $datum;
}
