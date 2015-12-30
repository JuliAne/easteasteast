<?php
/**
 * Zeigt das Profil eines Akteurs an.
 */

class aae_akteurprofil extends aae_data_helper {

 public function run(){

  $explodedpath = explode("/", current_path());
  $akteur_id = $this->clearContent($explodedpath[1]);

  global $user;
  $user_id = $user->uid;

  //Prüfen ob Schreibrecht vorliegt
  $resultUser = db_select($this->tbl_hat_user, 'u')
  ->fields('u')
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user_id, '=')
  ->execute();

  // Anzeige Edit-Button?
  $hat_recht = $resultUser->rowCount();

  //Auswahl der Daten des Akteurs
  $resultakteur = db_select($this->tbl_akteur, 'a')
   ->fields('a')
   ->condition('AID', $akteur_id, '=')
   ->execute()
   ->fetchAll();

  if (empty($resultakteur)) {
  // Akteur nicht vorhanden, beame ihn zur Akteure-Seite

   if (session_status() == PHP_SESSION_NONE) session_start();
   $_SESSION['sysmsg'][] = 'Dieses Akteurprofil konnte nicht gefunden werden...';
   header("Location: ".$base_path."/Akteure");
  }

 foreach ($resultakteur as $row) {

  $aResult['row1'] = $row;
  $resultAdresse = db_select($this->tbl_adresse, 'b')
   ->fields('b')
	 ->condition('ADID', $row->adresse, '=')
	 ->execute()
   ->fetchAll();

  foreach ($resultAdresse as $row2) {
    $aResult['row2'] = $row2; // Kleiner Fix, damit $row2 als Objekt abrufbar
  }

}

  // Ziehe Informationen über Events vom Akteur
  $events = db_select($this->tbl_akteur_events, 'ae')
  ->fields('ae')
  ->condition('AID', $akteur_id, '=')
  ->execute()
  ->fetchAll();

  foreach ($events as $event) {

   $aResult['events'][] = db_select($this->tbl_event, 'e')
   ->fields('e')
   ->condition('EID', $event->EID, '=')
   ->execute()
   ->fetchAll();

  }

  // Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend

  if (!empty($aResult['row2']->gps)) {

    $koordinaten = $aResult['row2']->gps;

    $this->addMapContent($koordinaten, array('gps' => $koordinaten, 'name' => $aResult['row1']->name, 'strasse' => $aResult['row2']->strasse, 'nr' => $aResult['row2']->nr));
  }

  $kategorien = db_select($this->tbl_hat_sparte, 'a')
  ->fields('a', array('hat_KID'))
  ->condition('hat_AID', $akteur_id, '=')
  ->execute()
  ->fetchAll();

  if (!empty($kategorien)) {

  foreach($kategorien as $kategorie) {

   $resulttags[] = db_select($this->tbl_sparte, 't')
   ->fields('t')
   ->condition('KID', $kategorie->hat_KID, '=')
   ->execute()
   ->fetchAll();

  }
 }

 ob_start(); // Aktiviert "Render"-modus
 include_once path_to_theme() . '/templates/single_akteur.tpl.php';
 return ob_get_clean(); // Übergabe des gerenderten "single_akteur.tpl"

 }
} // end class aae_akteurprofil

?>
