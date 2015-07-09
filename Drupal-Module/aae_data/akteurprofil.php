<?php
/**
 * akteurprofil.php zeigt das Profil eines Akteurs an.
 *
 * Ruth, 2015-06-06
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
$hat_recht = empty($resultUser);

//uebermittelte ID des Akteurs
//$akteur_id = $_GET['AID']

//Auswahl aller Akteure (nur Name) in alphabetischer Reihenfolge
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
  ->execute();

//-----------------------------------

//Ausgabe
$profileHTML = <<<EOF
EOF;

//Anzeige Edit Button
if($hat_recht){
  $profileHTML .= '<a href="?q=Akteuredit">Akteur bearbeiten</a><br><br>';
}

foreach($resultakteur as $row){
	$profileHTML .= '<h1>'.$row->name.'</h1>';
	
	$profileHTML .= '<h4>Adresse:</h4>';
	//Adresse des Akteurs
	$resultadresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'ort',
	    'gps',
	  ))
	  ->condition('ADID', $row->adresse, '=')
	  ->execute();
	foreach ($resultadresse as $row1) {
		$profileHTML .= $row1->strasse.' '.$row1->nr.'<br>';
		$profileHTML .= $row1->plz.' '.$row1->ort.'<br>';
		$profileHTML .= 'GPS: '.$row1->gps.'<br>';
	}
	
	$profileHTML .= '<h4>Kontakt:</h4>';
	if($row->ansprechpartner != "") { $profileHTML .= $row->ansprechpartner.'<br>'; }
	if($row->funktion != "") { $profileHTML .= $row->funktion.'<br>'; }
	if($row->email != "") { $profileHTML .= $row->email.'<br>'; }
	if($row->telefon != "") { $profileHTML .= $row->telefon.'<br>'; }
	if($row->url != "") { $profileHTML .= $row->url.'<br>'; }
	if($row->kurzbeschreibung != "") { 
      $profileHTML .= '<h4>Beschreibung:</h4>';
	  $profileHTML .= $row->kurzbeschreibung.'<br>';
	}
	if($row->bild != "") { 
	  $profileHTML .= '<img src="/'.$row->bild.'" >'; }
	
}
