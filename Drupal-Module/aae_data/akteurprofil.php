<?php
/**
 * @file akteurprofil.php
 * Zeigt das Profil eines Akteurs an.
 *
 * Ruth, 2015-06-06
 * Felix, 2015-07-21
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";
$tbl_adresse = "aae_data_adresse";
$tbl_hat_user = "aae_data_hat_user";
$tbl_event = "aae_data_event";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();

//AID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$akteur_id = $explodedpath[1];

//UID holen (ist in user gespeichert)
global $user;
$user_id = $user->uid;

//Prüfen ob Schreibrecht vorliegt
$resultUser = db_select($tbl_hat_user, 'u')
  ->fields('u', array(
    'hat_UID',
    'hat_AID',
  ))
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user_id, '=')
  ->execute();

$hat_recht = $resultUser->rowCount();

//Auswahl der Daten des Akteurs
$resultakteur = db_select($tbl_akteur, 'a')
  ->fields('a', array(
    'name',
    'email',
    'telefon',
    'url',
    'ansprechpartner',
    'funktion',
    'kurzbeschreibung',
    'oeffnungszeiten',
    'adresse',
    'bild',
  ))
  ->condition('AID', $akteur_id, '=')
  ->execute()
  ->fetchAll();

//-----------------------------------

$aResult[$hat_recht] = $hat_recht;  //Anzeige Edit-Button
$aResult[$aId] = $akteur_id;

foreach($resultakteur as $rId => $row){

	$aResult['row1'] = $row;

	$resultAdresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'ort',
	    'gps',
	  ))
	  ->condition('ADID', $row->adresse, '=')
	  ->execute()
    ->fetchAll();

    foreach($resultAdresse as $row2) {
     $aResult['row2'] = $row2; // Kleiner Fix, damit $row2 als Objekt abrufbar
    }
}

// Ziehe Informationen über Events vom Akteur

$resultEvents = db_select($tbl_event, 'e')
->fields('e')
->condition('veranstalter', $akteur_id, '=')
->execute()
->fetchAll();

foreach ($resultEvents as $eId => $row) {
 $aResult['events'][] = $row;
}

 // Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend

 if ($aResult['row2']->gps != '') {

 $kHelper = explode(' ', $aResult['row2']->gps, 2);
 $koordinaten = $kHelper[1].','.$kHelper[0];

 drupal_add_js('var map = L.mapbox.map("map", "matzelot.mn92ib5i").setView(['.$koordinaten.'], 16);',
 array('type' => 'inline', 'scope' => 'footer'));

 // Marker

 drupal_add_js('L.mapbox.featureLayer({
  type: "Feature",
  geometry: {
      type: "Point",
      coordinates: ['.str_replace(' ',',',$aResult['row2']->gps).']
    },
    properties: {
      title: "'.$aResult['row1']->name.'",
      description: "'.$aResult['row2']->strasse.' '.$aResult['row2']->nr.'",
      "marker-size": "large",
      "marker-color": "#1087bf"
    }
  }).addTo(map);', array('type' => 'inline', 'scope' => 'footer'));

 }


 ob_start(); // Aktiviert "Render"-modus

 include_once path_to_theme().'/templates/project.tpl.php';

 $profileHTML = ob_get_clean(); // Übergebe des gerenderten "project.tpl"
