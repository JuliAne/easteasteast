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

// Paginator: Auf welcher Seite befinden wir uns?
$explodedPath = explode("/", current_path());
$pageNr = $explodedPath[1];

//Auswahl aller Akteure (nur Name) in alphabetischer Reihenfolge
$resultAkteure = db_select($tbl_akteur, 'a')
  ->fields('a', array(
	'AID',
  'name',
  'kurzbeschreibung',
  'bild'
  ))
  ->orderBy('name', 'ASC')
  ->execute(); // Limit?

//-----------------------------------

$itemsCount = $resultAkteure->rowCount();

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/akteure.tpl.php';

$profileHTML = ob_get_clean(); // Übergebe des gerenderten "project.tpl"
