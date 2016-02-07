<?php
/**
 * Moeglichkeit, die einzelnen Events als .ics-Datei (iCal-Format)
 * herunterzuladen.
 * TODO: Missing Paramter "Categories"???
 */

Class ics_download extends aae_data_helper {

 public function run(){

  global $user;

  $pathThisFile = $_SERVER['REQUEST_URI'];

  $explodedpath = explode("/", current_path());
  $eventID = $this->clearContent($explodedpath[1]);

  $resultEvent = db_select($this->tbl_event, 'e')
   ->fields('e')
   ->condition('EID', $eventID)
   ->execute();

  $var = null;
  $event = null;

  //Generierung der .ics-Datei
  $var .= "BEGIN:VCALENDAR\r\n";
  $var .= "PRODID;X-RICAL-TZSOURCE=TZINFO:-//com.denhaven2/NONSGML ri_cal gem//EN\r\n";
  $var .= "CALSCALE:GREGORIAN\n";
  $var .= "VERSION:2.0\n";
  $var .= "BEGIN:VEVENT\n";

  foreach ($resultEvent as $row) {
   $start = new DateTime($row->start_ts);
	 $ende  = new DateTime($row->ende_ts);
	 $name = $row->name;
	 $ort = $row->ort;
	 $eid = $row->EID;
   $beschreibung = $row->kurzbeschreibung;
  }

  $resultAdresse = db_select($this->tbl_adresse, 'a')
   ->fields('a', array(
    'strasse',
	  'nr',
	  'adresszusatz',
	  'plz',
	 ))
   ->condition('ADID', $ort)
   ->execute();

  $var .= "UID:". $eid . "@leipziger-ecken.de\n";
  $var .= "DTSTART:" . $start->format('Ymd\THis') . "\n";
  $var .= "DTEND:" . $ende->format('Ymd\THis') . "\n";
  $var .= "SUMMARY:" . $name . " am ". $start->format('d.m.Y') ."\n";
  $var .= "DESCRIPTION:" . $beschreibung . "\n";
  foreach ($resultAdresse as $row) {
    $ad = $row->strasse . ' ' . $row->nr . '\; ' . $row->plz . ' Leipzig';
  }
  $var .= "LOCATION:" . $ad . "\n";
  $var .= "END:VEVENT\n";
  $var .= "END:VCALENDAR\n";

  header('Content-Type: text/plain');
  header('Content-Length: ' . strlen($var));
  header('Content-Disposition: attachment; filename="' . $name . '.ics"');

  echo $var;
  exit();

 }
}
