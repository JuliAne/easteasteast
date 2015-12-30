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

 public function getAkteurKontakt($id) {

  $akteur_id = $this->clearContent($id);

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

 public function getAkteurAdresse($id) {

   $akteur_id = $this->clearContent($id);

   if (!is_numeric($akteur_id) || $akteur_id == 0) exit();

   $akteurAdresse = db_select($this->tbl_akteur, 'a')
    ->fields('a', array('adresse'))
    ->condition('AID', $akteur_id)
    ->execute()
    ->fetchAll();

   $resultAdresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad')
    ->condition('ADID', $akteurAdresse[0]->adresse, '=')
    ->execute()
    ->fetchAll();

    echo json_encode($resultAdresse[0]);

 }

 /**
  * @function getKalender()
  *
  * Dient dem Einblenden eines neuen Kalender-Monat's im Footer
  */

  function getKalender(){

    $modulePath = drupal_get_path('module', 'aae_data');
    include_once $modulePath . '/kalender.php';

    $kal = new kalender();
    echo $kal->run();


  }
}
?>
