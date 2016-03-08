<?php
/**
 * eventprofil.php zeigt das Profil eines Events an.
 *
 */

class aae_eventprofil extends aae_data_helper {

 public function run(){

  global $user;

  $explodedpath = explode("/", current_path());
  $eventId = $this->clearContent($explodedpath[1]);

  //Prüfen, wer Schreibrechte hat
  //Sicherheitsschutz, ob User entsprechende Rechte hat

  $resultAkteurId = db_select($this->tbl_akteur_events, 'e')
   ->fields('e', array( 'AID' ))
   ->condition('EID', $eventId, '=')
   ->execute();

  $akteurId = "";
  $okay = ""; // Gibt an, ob Zugang erlaubt wird oder nicht

  foreach ($resultAkteurId as $row) {

   $akteurId = $row->AID; //Akteur speichern

   //Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
   $resultUser = db_select($this->tbl_hat_user, 'u')
    ->fields('u', array(
      'hat_UID',
      'hat_AID',
    ))
    ->condition('hat_AID', $akteurId, '=')
    ->condition('hat_UID', $user->uid, '=')
    ->execute();

   if ($resultUser->rowCount() == 1) {
    $okay = 1; //Zugang erlaubt
   }
  }

 //Abfrage, ob User Ersteller des Events ist:
 $ersteller = db_select($this->tbl_event, 'e')
  ->fields('e', array( 'ersteller' ))
  ->condition('ersteller', $user->uid, '=')
  ->execute();

 if ($ersteller->rowCount() == 1 || array_intersect(array('administrator'), $user->roles)) $okay = 1;

  $event = db_select($this->tbl_event, 'a')
   ->fields('a')
   ->condition('EID', $eventId, '=');

  $resultEvent = $event->execute()->fetchAssoc();

  if (empty($resultEvent)) {
  // Event nicht vorhanden

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Dieses Event konnte nicht gefunden werden...'));
   header("Location: ". $base_url ."/events");

  }

  // Hack: add times to $resultEvent-object
  $resultEvent = (object)$resultEvent;
  $resultEvent->start = new DateTime($resultEvent->start_ts);
  $resultEvent->ende = new DateTime($resultEvent->ende_ts);
  $resultEvent->created = new DateTime($resultEvent->created);
  $resultEvent = (object)$resultEvent;

  $akteurId = db_select($this->tbl_akteur_events, 'ae')
   ->fields('ae', array('AID'))
   ->condition('EID', $eventId, '=')
   ->execute()
   ->fetchAssoc();

  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a',array('AID','name'))
   ->condition('AID', $akteurId['AID'], '=')
   ->execute()
   ->fetchAssoc();

  //Selektion der Tags
  $resultSparten = db_select($this->tbl_event_sparte, 's')
   ->fields('s', array( 'hat_KID' ))
   ->condition('hat_EID', $eventId, '=')
   ->execute();

  $countSparten = $resultSparten->rowCount();
  $sparten = array();


  if ($countSparten != 0) {

   foreach ($resultSparten as $row) {
    $resultSpartenName = db_select($this->tbl_sparte, 'sp')
	  ->fields('sp')
	  ->condition('KID', $row->hat_KID, '=')
	  ->execute();

	  foreach ($resultSpartenName as $row1) {
	   $sparten[] = $row1;
	  }
  }
 }

 //Ersteller (USER!) aus DB holen
 $ersteller = db_select("users", 'u')
  ->fields('u', array('name' ))
  ->condition('uid', $resultEvent->ersteller, '=')
  ->execute();

 //Adresse des Akteurs
 $resultAdresse = db_select($this->tbl_adresse, 'b')
  ->fields('b', array())
  ->condition('ADID', $resultEvent->ort, '=')
  ->execute();

  foreach ($resultAdresse as $adresse) {
   $resultAdresse = $adresse; // Kleiner Fix, um EIN Objekt zu generieren
  }

  //Bezirksnamen
  $resultBezirk = db_select($this->tbl_bezirke, 'z')
   ->fields('z', array( 'bezirksname' ))
   ->condition('BID', $resultAdresse->bezirk, '=')
   ->execute();

  foreach ($resultBezirk as $bezirk) {
   $resultBezirk = $bezirk; // Kleiner Fix, um EIN Objekt zu generieren
  }

  $map = false;

  if (!empty($resultAdresse->gps)) {
    $this->addMapContent($resultAdresse->gps, array('gps' => $resultAdresse->gps, 'name' => $resultEvent->name, 'strasse' => $resultAdresse->strasse, 'nr' => $resultAdresse->nr));
    $map = true;
  }

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/eventprofil.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "eventprofil.tpl.php"

} // end public function run()

 /**
  * @function removeEvent
  * Removes an event from DB
  */

 public function removeEvent(){

  global $user;
  $user_id = $user->uid;

  $okay = 0;

  $explodedpath = explode("/", current_path());
  $event_id = $this->clearContent($explodedpath[1]);

  if (!user_is_logged_in()) {
   drupal_access_denied();
  }

  //Sicherheitsschutz, ob User entsprechende Rechte hat
  $resultAkteurEvent = db_select($this->tbl_akteur_events, 'e')
   ->fields('e')
   ->condition('EID', $event_id, '=')
   ->execute();

  foreach ($resultAkteurEvent as $row) {

   $akteur_id = $row->AID;

   //Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
   $resultUser = db_select($this->tbl_hat_user, 'u')
    ->fields('u')
    ->condition('hat_AID', $akteur_id, '=')
    ->condition('hat_UID', $user_id, '=')
    ->execute();

    $okay = ($resultUser->rowCount()) ? 1 : 0;
  }

  // Abfrage, ob User Ersteller des Events ist:
   $ersteller = db_select($this->tbl_event, 'e')
   ->fields('e', array('ersteller'))
   ->condition('ersteller', $user->uid, '=')
   ->execute();

   $okay = ($ersteller->rowCount()) ? 1 : 0;

  if (!array_intersect(array('administrator'), $user->roles) || !$okay) {
   drupal_access_denied();
  }

//-----------------------------------

  if (isset($_POST['submit'])) {

   $resultEvent = db_select($this->tbl_event, 'e')
    ->fields('e', array('bild'))
    ->condition('EID', $event_id, '=')
    ->execute()
    ->fetchAssoc();

   db_delete($this->tbl_akteur_events)
    ->condition('EID', $event_id, '=')
    ->execute();

   db_delete($this->tbl_event)
    ->condition('EID', $event_id, '=')
    ->execute();

   db_delete($this->tbl_event_sparte)
   ->condition('hat_EID', $event_id, '=')
   ->execute();

   // remove profile-image (if possible)
   $bild = end(explode('/', $resultEvent['bild']));

   if (file_exists($this->short_bildpfad.$bild)) {
    unlink($this->short_bildpfad.$bild);
   }

   menu_link_delete(NULL, 'eventprofil/'.$event_id);

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Das Event wurde gelöscht.'));
   header("Location: ". $base_url." events");
   // Und "Tschö mit ö..."!

 } else {

 $pathThisFile = $_SERVER['REQUEST_URI'];

 return '<div class="callout row">
 <h4><strong>Möchten Sie dieses Event wirklich löschen?</strong></h4><br />
 <form action='.$pathThisFile.' method="POST" enctype="multipart/form-data">
   <input name="event_id" type="hidden" id="eventEIDInput" value="'.$event_id.'" />
   <a class="secondary button" href="javascript:history.go(-1)">Abbrechen</a>
   <input type="submit" class="button" id="eventSubmit" name="submit" value="Löschen">
 </form></div>';

  }
 } // end public function removeEvent()

 public function ics_download(){

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

} // end class aae_eventprofil
