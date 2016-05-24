<?php
/**
 * eventprofil.php zeigt das Profil eines Events an.
 */

namespace Drupal\AaeData;

class eventprofil extends aae_data_helper {

 var $akteur_id = '';
 var $isOwner = 0;

 public function __construct(){

  global $user;
  require_once('models/events.php');
  $this->event = new events();

 }

 public function run(){

  $explodedpath = explode("/", current_path());
  $event_id = $this->clearContent($explodedpath[1]);

  //Sicherheitsschutz, ob User entsprechende Rechte hat
  $resultAkteurId = db_select($this->tbl_akteur_events, 'e')
   ->fields('e', array('AID'))
   ->condition('EID', $event_id, '=')
   ->execute()
   ->fetchObject();
   
  global $user;

  // Show "Edit"-Button?
  $this->akteur_id = $resultAkteurId->AID;
  $resultUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u')
   ->condition('hat_AID', $this->akteur_id, '=')
   ->condition('hat_UID', $user->uid, '=')
   ->execute();
   
  if ($resultUser->rowCount() == 1 || array_intersect(array('administrator'), $user->roles)) $this->isOwner = 1;

  $resultEvent = $this->event->getEvents(array('EID' => $event_id), 'complete');
  $resultEvent = $resultEvent[0];
  
  if (empty($resultEvent)) {
  // Event nicht vorhanden

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Dieses Event konnte nicht gefunden werden...'));
   header("Location: ". $base_url ."/events");

  }

  $map = false;

  if (!empty($resultEvent->adresse->gps_lat)) {
   $this->addMapContent($resultEvent->adresse->gps_lat.','.$resultEvent->adresse->gps_long, array('name' => $resultEvent->name, 'strasse' => $resultEvent->adresse->strasse, 'nr' => $resultEvent->adresse->nr));
   # works?
   $map = true;
  }

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/eventprofil.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "eventprofil.tpl.php"

 } // end public function run()

 /**
  * @function removeEvent
  * Removes an event from DB
  * TODO: Put DB-Zeugs into $this->event->removeEvent()
  */

 public function removeEvent(){
 
  global $user;
  $user_id = $user->uid;

  $explodedpath = explode("/", current_path());
  $event_id = $this->clearContent($explodedpath[1]);

  if (!user_is_logged_in())
    drupal_access_denied();

  // Sicherheitsschutz, ob User entsprechende Rechte hat
  $resultAkteurEvent = db_select($this->tbl_akteur_events, 'e')
   ->fields('e')
   ->condition('EID', $event_id)
   ->execute()
   ->fetchObject();

   $akteur_id = $resultAkteurEvent->AID;

   // Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
   $resultUser = db_select($this->tbl_hat_user, 'u')
    ->fields('u')
    ->condition('hat_AID', $akteur_id, '=')
    ->condition('hat_UID', $user_id, '=')
    ->execute();

   $this->isOwner = ($resultUser->rowCount()) ? 1 : 0;

   if (!$this->isOwner) {
    if (!array_intersect(array('administrator'), $user->roles)) {
     drupal_access_denied();
    }
   }

//-----------------------------------

  if (isset($_POST['submit'])) {

   $this->event->removeEvent($event_id);

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Das Event wurde gelöscht.'));
   header("Location: ". $base_url."/events");
   // Und "Tschö mit ö..."!

 } else {

 $pathThisFile = $_SERVER['REQUEST_URI'];

 return '<div class="callout row">
 <h4><strong>'.t('Möchten Sie dieses Event wirklich löschen?').'</strong></h4><br />
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
    $start = new \DateTime($row->start_ts);
 	  $ende  = new \DateTime($row->ende_ts);
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
