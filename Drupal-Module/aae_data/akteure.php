<?php
/**
 * akteure.php listet alle Akteure auf.
 *
 * Ruth, 2015-07-06
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();

//Auswahl aller Akteure (nur Name) in alphabetischer Reihenfolge
$resultakteure = db_select($tbl_akteur, 'a')
  ->fields('a', array(
	'AID',
    'name',
  ))
  ->orderBy('name', 'ASC')
  ->execute();

//-----------------------------------

//Ausgabe
$profileHTML = <<<EOF
EOF;

//Abfrage, ob Besucher der Seite eingeloggt ist:
if(user_is_logged_in()){//Link für Generierung eines neuen Akteurs anzeigen
  $profileHTML .= '<a href="akteur_bearbeiten.php">Neuen Akteur hinzufügen!</a><br>';
}

foreach($resultakteure as $row){
  $profileHTML .= '<a href="akteurprofil.php?AID='.$row->AID.'">'.$row->name.'</a><br>';
}
