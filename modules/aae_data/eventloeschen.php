<?php
/**
 * @file eventloeschen.php
 * Löscht ein Event aus der DB
 */

Class eventloeschen extends aae_data_helper {

 public function run(){

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

   if ($resultUser->rowCount()) {
	  $okay = 1;
   }
  }

  // Abfrage, ob User Ersteller des Events ist:
  $ersteller = db_select($this->tbl_event, 'e')
   ->fields('e', array('ersteller'))
   ->condition('ersteller', $user->uid, '=')
   ->execute();

  if ($ersteller->rowCount()) {
   $okay = 1;
  }

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
   drupal_set_message('Das Event wurde gelöscht.');
   header("Location: ".base_path()."events");
   // Und "Tschö mit ö..."!

 } else {

 $pathThisFile = $_SERVER['REQUEST_URI'];

 return '<div class="callout">
 <h4><strong>Möchten Sie dieses Event wirklich löschen?</strong></h4><br />
 <form action='.$pathThisFile.' method="POST" enctype="multipart/form-data">
   <input name="event_id" type="hidden" id="eventEIDInput" value="'.$event_id.'" />
   <a class="secondary button" href="javascript:history.go(-1)">Abbrechen</a>
   <input type="submit" class="button" id="eventSubmit" name="submit" value="Löschen">
 </form></div>';
  }
 }
} // end class eventloeschen
