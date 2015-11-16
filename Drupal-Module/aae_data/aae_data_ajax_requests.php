<?php

/** Klasse zum handeln von Ajax-request's. Sollte eine funktion zu groß
 *  werden, kann diese natürlich gerne in eine eigene Datei ausgelagert werden
 */

Class aae_data_ajax_requests extends aae_data_helper {

/**
 * @function akteurkontakt()
 *
 * Gibt div wider, welcher Kontaktinformationen zum Akteur beinhaltet.
 * Aufgerufen über das Akteurprofil
 */

 public function getAkteurKontakt() {

  $path = current_path();
  $explodedpath = explode("/", $path);
  $akteur_id = $this->clearContent($explodedpath[2]);

  if (!is_numeric($akteur_id)) exit();

  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('name','email','telefon','ansprechpartner','funktion'))
   ->condition('AID', $akteur_id, '=')
   ->execute()
   ->fetchAll();

  include_once path_to_theme() . '/templates/ajax.akteurkontakt.tpl.php';

 }

 /**
  * @function getAkteurAdresse()
  *
  * Gibt Adresse eines Akteurs wieder, welche (wie in eventformular) zur
  * dynamischen Anzeige dient
  */

 public function getAkteurAdresse() {

   $path = current_path();
   $explodedpath = explode("/", $path);
   $akteur_id = $this->clearContent($explodedpath[2]);

   if (!is_numeric($akteur_id) || $akteur_id == 0) exit();

   $akteurAdresse = db_select($this->tbl_akteur, 'a')
    ->fields('a', array('adresse'))
    ->condition('AID', $akteur_id)
    ->execute()
    ->fetchAll();

   $resultAdresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad')
    ->condition('', $akteurAdresse->adresse, '=')
    ->execute()
    ->fetchAll();

    // Get bezirk...

   echo $resultAdresse;

 }
}
?>
