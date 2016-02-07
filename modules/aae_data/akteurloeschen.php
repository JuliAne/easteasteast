<?php
/**
 * @file akteurloeschen.php
 * Löscht einen Akteur aus der DB
 */

Class akteurloeschen extends aae_data_helper {

 public function run(){

  global $user;
  $user_id = $user->uid;

  $explodedpath = explode("/", current_path());
  $akteur_id = $this->clearContent($explodedpath[1]);

  if(!user_is_logged_in()){
   drupal_access_denied();
  }

  //Prüfen ob Schreibrecht vorliegt
  $resultUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u', array('hat_UID', 'hat_AID'))
   ->condition('hat_AID', $akteur_id, '=')
   ->condition('hat_UID', $user_id, '=')
   ->execute();

  $hat_recht = $resultUser->rowCount();

  if (!array_intersect(array('redakteur','administrator'), $user->roles)) {
   if ($hat_recht != 1) {
    drupal_access_denied();
   }
  }

  $resultAkteur = db_select($this->tbl_akteur, 'a')
  ->fields('a', array('name','bild'))
  ->condition('AID', $akteur_id, '=')
  ->execute()
  ->fetchAssoc();

//-----------------------------------

 if (isset($_POST['submit'])) {

  $resultEvents = db_select($this->tbl_akteur_events, 'ae')
   ->fields('ae')
   ->condition('AID', $akteur_id, '=')
   ->execute()
   ->fetchAll();

  foreach($resultEvents as $event){
   db_delete($this->tbl_event)
    ->condition('EID', $event->EID, '=')
    ->execute();
  }

  db_delete($this->tbl_akteur_events)
    ->condition('AID', $akteur_id, '=')
    ->execute();

  db_delete($this->tbl_hat_user)
    ->condition('hat_AID', $akteur_id, '=')
    ->execute();

  db_delete($this->tbl_akteur)
    ->condition('AID', $akteur_id, '=')
    ->execute();

  db_delete($this->tbl_hat_sparte)
   ->condition('hat_AID', $akteur_id, '=')
   ->execute();

  // remove profile-image (if possible)
  $bild = end(explode('/', $resultAkteur['bild']));

  if (file_exists($this->short_bildpfad.$bild)) {
   unlink($this->short_bildpfad.$bild);
  }

  if (session_status() == PHP_SESSION_NONE) session_start();
  $_SESSION['sysmsg'][] = 'Der Akteur wurde gelöscht.';
  header("Location: ".base_path()."Akteure");

} else {

 $pathThisFile = $_SERVER['REQUEST_URI'];

 return '<div class="callout">
  <h3>Möchten Sie den Akteur <strong>'.$resultAkteur['name'].'</strong> wirklich löschen?</h3><br />
  <form action="'.$pathThisFile.'" method="POST" enctype="multipart/form-data">
    <input name="akteur_id" type="hidden" id="eventEIDInput" value="'.$akteur_id.'" />
    <a class="secondary button" href="javascript:history.go(-1)">Abbrechen</a>
    <input type="submit" class="button" id="akteurSubmit" name="submit" value="Löschen">
  </form>
  </div>';

  }
 } // end function run()
} // end class akteurloeschen
