<?php
/**
 * displayAkteure.php listet alle Akteure auf.
 * TODO: Verlinkung zum Akteurprofil!!!
 *
 * Ruth, 2015-06-25
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
//include $modulePath . '/templates/utils/rest_helper.php';

$db = new DB_CONNECT();

//Auswahl aller Akteure (nur Name) in alphabetischer Reihenfolge
$result = db_select($tbl_akteur, 'a')
  ->fields('a', array(
    'label',
  ))
  ->orderBy('label', 'ASC')
  ->execute();

//Ausgabe
$profileHTML = <<<EOF
EOF;

foreach($result as $row){
  $profileHTML .= '<p>'.$row->label.'</p>';
}
