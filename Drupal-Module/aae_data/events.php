<?php
/**
 * events.php listet alle Events auf.
 *
 * Ruth, 2015-07-10
 */

//-----------------------------------

$tbl_events = "aae_data_event";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();

//Auswahl aller Akteure (nur Name) in alphabetischer Reihenfolge
$resultevents = db_select($tbl_events, 'a')
  ->fields('a', array(
    'name',
    'EID',
  ))
  ->orderBy('name', 'ASC')
  ->execute();

//-----------------------------------

//Ausgabe
$profileHTML = <<<EOF
EOF;

foreach($resultevents as $row){
  //$profileHTML .= '<p>'.$row->name.'</p>';
  $profileHTML .= '<a href="?q=Eventprofil/'.$row->EID.'">'.$row->name.'</a><br>';
}
