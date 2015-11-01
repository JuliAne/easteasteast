<?php
/**
 * @file akteurprofil.php
 * Zeigt das Profil eines Akteurs an.
 *
 * Ruth, 2015-06-06
 * Felix, 2015-07-23
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";
$tbl_adresse = "aae_data_adresse";
$tbl_hat_user = "aae_data_akteur_hat_user";
$tbl_event = "aae_data_event";
$tbl_akteur_hat_events = "aae_data_akteur_hat_event";
$tbl_sparte = "aae_data_sparte";
$tbl_akteur_hat_sparte = "aae_data_akteur_hat_sparte";

//-----------------------------------

//AID holen
$explodedpath = explode("/", current_path());
$akteur_id = $explodedpath[1];

//UID holen (ist in user gespeichert)
global $user;
$user_id = $user->uid;

//Prüfen ob Schreibrecht vorliegt
$resultUser = db_select($tbl_hat_user, 'u')
  ->fields('u')
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user_id, '=')
  ->execute();

// Anzeige Edit-Button?
$hat_recht = $resultUser->rowCount();

//Auswahl der Daten des Akteurs
$resultakteur = db_select($tbl_akteur, 'a')
  ->fields('a')
  ->condition('AID', $akteur_id, '=')
  ->execute()
  ->fetchAll();

if (empty($resultakteur)) {
  // Akteur nicht vorhanden, beame ihn zur Akteure-Seite

  if (session_status() == PHP_SESSION_NONE) session_start();

  $_SESSION['sysmsg'][] = 'Akteurprofil konnte nicht gefunden werden...';

  header("Location: ".$base_path."/Akteure");
}

//-----------------------------------

foreach ($resultakteur as $rId => $row) {
  $aResult['row1'] = $row;
  $resultAdresse = db_select($tbl_adresse, 'b')
    ->fields('b')
	->condition('ADID', $row->adresse, '=')
	->execute()
    ->fetchAll();
  foreach ($resultAdresse as $row2) {
    $aResult['row2'] = $row2; // Kleiner Fix, damit $row2 als Objekt abrufbar
  }
}

// Ziehe Informationen über Events vom Akteur
$resultEvents = db_select($tbl_event, 'e');
$resultEvents->join($tbl_akteur_hat_events, 'b', 'e.EID = b.EID');
$resultEvents
  ->fields('e')
  ->condition('b.AID', $akteur_id, '=')
  ->execute()
  ->fetchAll();

foreach ($resultEvents as $row) {
  $aResult['events'][] = $row;
}

// Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend
if ($aResult['row2']->gps != '') {
  $kHelper = explode(' ', $aResult['row2']->gps, 2);
  $koordinaten = $kHelper[1] . ',' . $kHelper[0];

  drupal_add_js('var map = L.mapbox.map("map", "matzelot.mn92ib5i").setView([' . $koordinaten . '], 16);', array('type' => 'inline', 'scope' => 'footer'));

  // Marker
  drupal_add_js('L.mapbox.featureLayer({
    type: "Feature",
    geometry: {
      type: "Point",
      coordinates: [' . str_replace(' ',',',$aResult['row2']->gps) . ']
    },
    properties: {
      title: "' . $aResult['row1']->name . '",
      description: "' . $aResult['row2']->strasse . ' ' . $aResult['row2']->nr . '",
      "marker-size": "large",
      "marker-color": "#1087bf"
    }
  }).addTo(map);', array('type' => 'inline', 'scope' => 'footer'));
}

$kategorien = db_select($tbl_akteur_hat_sparte, 'a')
 ->fields('a', array('hat_KID'))
 ->condition('hat_AID', $akteur_id, '=')
 ->execute()
 ->fetchAll();

if (!empty($kategorien)) {

 foreach($kategorien as $kategorie) {

  $results[] = db_select($tbl_sparte, 't')
  ->fields('t')
  ->condition('KID', $kategorie->hat_KID, '=')
  ->execute()
  ->fetchAll();

 }

 //print_r($results);
}

ob_start(); // Aktiviert "Render"-modus
include_once path_to_theme() . '/templates/single_akteur.tpl.php';
$profileHTML = ob_get_clean(); // Übergebe des gerenderten "single_akteur.tpl"
