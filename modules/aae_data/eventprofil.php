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
   drupal_set_message('Dieses Event konnte nicht gefunden werden...');
   header("Location: ".$base_path."/events");

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
  include_once path_to_theme() . '/templates/single_event.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "events.tpl.php"

 }
} // end class aae_eventprofil
