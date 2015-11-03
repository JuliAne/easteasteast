<?php
/**
 * ajax.akteurkontakt.php
 *
 * Wird via AJAX-request aufgerufen und gibt div wider, welcher
 * Kontaktinformationen zum Akteur beinhaltet.
 */

global $user;
$user_id = $user->uid;

$path = current_path();
$explodedpath = explode("/", $path);
$akteur_id = $explodedpath[1];

if (!is_numeric($akteur_id)) exit();

$tbl_akteur = "aae_data_akteur";

$resultAkteur = db_select($tbl_akteur, 'a')
 ->fields('a', array('name','email','telefon','ansprechpartner','funktion'))
 ->condition('AID', $akteur_id, '=')
 ->execute()
 ->fetchAll();

include_once path_to_theme() . '/templates/ajax.akteurkontakt.tpl.php';

?>
