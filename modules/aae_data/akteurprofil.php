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
  if ($resultUser->rowCount() == 1 || array_intersect(array('administrator'), $user->roles)) $hat_recht = 1;


  //Auswahl der Daten des Akteurs
  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a')
   ->condition('AID', $akteur_id, '=')
   ->execute()
   ->fetchAll();

  if (empty($resultAkteur)) {
  // Akteur nicht vorhanden, beame ihn zur Akteure-Seite

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message('Dieses Akteurprofil konnte nicht gefunden werden...');
   header("Location: ".$base_path."akteure");

 } else {

  foreach ($resultAkteur as $row) {

   $aResult['row1'] = $row;
   $resultAdresse = db_select($this->tbl_adresse, 'b')
    ->fields('b')
    ->condition('ADID', $row->adresse, '=');

   $aResult['adresse'] = $resultAdresse->execute()->fetchObject();

  }

  // Ziehe Informationen über Events vom Akteur

  $events = db_query('
   SELECT * FROM {aae_data_event} AS e JOIN {aae_data_akteur_hat_event} AS he
   WHERE he.EID = e.EID AND he.AID = :aid
   ORDER BY start_ts DESC',
   array(':aid' => $akteur_id));

  $resultEvents = $events->fetchAll();

  // Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend

  if (!empty($aResult['adresse']->gps)) {

    $koordinaten = $aResult['adresse']->gps;
    $this->addMapContent($koordinaten, array(
     'gps' => $koordinaten,
     'name' => $aResult['adresse']->name,
     'strasse' => $aResult['adresse']->strasse,
     'nr' => $aResult['adresse']->nr
    ));

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
 }
} // end class aae_akteurprofil

?>
