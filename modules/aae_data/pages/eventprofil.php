<?php
/**
 * @file eventprofil.php
 */

namespace Drupal\AaeData;

class eventprofil extends events {

 var $event_id;
 var $akteur_id = '';
 var $isOwner = false;
 var $showMap = false;
 var $resultEvent;

 public function __construct(){
   
  parent::__construct();

  $explodedpath = explode("/", current_path());
  $this->event_id = $this->clearContent($explodedpath[1]);

 }

 public function run(){

  $eventResult = reset($this->getEvents(array('EID' => $this->event_id), 'complete'));
  
  if (empty($eventResult)) {

   // Event not existing
   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Dieses Event konnte nicht gefunden werden...'));
   header("Location: ". $base_url ."/events");

  }
  
  $this->__setSingleEventVars($eventResult);
  $this->isOwner = $this->isAuthorized($this->event_id);

  if (!empty($this->adresse->gps_lat)) {
  
   $this->showMap = true;
   $koordinaten = $this->adresse->gps_lat.','.$this->adresse->gps_long;
   $this->addMapContent($koordinaten, array(
    'gps' => $koordinaten,
    'name' => $this->name,
    'strasse' => $this->adresse->strasse,
    'nr' => $this->adresse->nr
   ));
  
  }

  return $this->render('/templates/eventprofil.tpl.php');

 } // end public function run()

 /**
  * @function removeEvent
  * Returns interface for removing an event
  */

 public function removeEvent(){

  if (!user_is_logged_in() || !$this->isAuthorized($this->event_id, $this->user_id)){
   drupal_access_denied();
   drupal_exit();
  }

  if (isset($_POST['submit'])) {

   $this->__removeEvent($this->event_id);

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Das Event wurde gelöscht.'));
   header("Location: ". $base_url."/events");
   // Und "Tschö mit ö..."!

 } else {

 $pathThisFile = $_SERVER['REQUEST_URI'];

 return '<div class="callout row" style="padding:1rem !important;"><div class="large-12 columns">
 <h3>'.t('Möchten Sie dieses Event wirklich löschen?').'</h3><br />
 <form action='.$pathThisFile.' method="POST" enctype="multipart/form-data">
   <input name="event_id" type="hidden" id="eventEIDInput" value="'.$this->event_id.'" />
   <a class="secondary button" href="javascript:history.go(-1)">'.t('Abbrechen').'</a>
   <input type="submit" class="button" id="eventSubmit" name="submit" value="'.t('Löschen').'">
 </form></div></div>';

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
    #->fetchAll(); ???

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

   header('Content-Type: text/ics');
   header('Content-Length: ' . strlen($var));
   header('Content-Disposition: attachment; filename="' . $name . '.ics"');
   # TODO line above: escape $name / remove whitespaces

   echo $var;
   drupal_exit();

 }

} // end class aae_eventprofil
