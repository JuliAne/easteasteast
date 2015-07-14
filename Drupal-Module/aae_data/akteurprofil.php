<?php
/**
 * @file akteurprofil.php
 * Zeigt das Profil eines Akteurs an.
 *
 * Ruth, 2015-06-06
 * Felix, 2015-06-13
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";
$tbl_adresse = "aae_data_adresse";
$tbl_hat_user = "aae_data_hat_user";

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

 $kHelper = explode(' ', $aResult['row2']->gps, -1);
 $koordinaten = $kHelper[1].','.$kHelper[0];
 echo $koordinaten;

 drupal_add_js('var map = L.mapbox.map("map", "matzelot.mn92ib5i").setView(['.$koordinaten.'], 16);',
 array('type' => 'inline', 'scope' => 'footer'));

 // ADD MARKER!

 ob_start(); // Aktiviere "Render"-modus

 include_once path_to_theme().'/templates/project.tpl.php';

 $profileHTML = ob_get_clean(); // Übergebe gerendertes "project.tpl"
