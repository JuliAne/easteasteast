<?php
/**
 * akteurloeschen.php löscht einen Akteur aus der DB
 * (nach vorheriger Abfrage).
 *
 * Ruth, 2015-07-20
 */

//Eingeloggter User
global $user;
$user_id = $user->uid;

//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$akteur_id = $explodedpath[1];

//DB-Tabellen
$tbl_hat_user = "aae_data_akteur_hat_user";
$tbl_akteur_events = "aae_data_akteur_hat_event";
$tbl_akteur = "aae_data_akteur";

//Sicherheitsschutz
if(!user_is_logged_in()){
  drupal_access_denied();
}

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

if (!array_intersect(array('redakteur','administrator'), $user->roles)) {
  if ($hat_recht != 1) {
    drupal_access_denied();
  }
}
//AKteurnamen ermitteln
$akteur = "";
$resultakteur = db_select($tbl_akteur, 'a')
  ->fields('a', array(
    'name',
  ))
  ->condition('AID', $akteur_id, '=')
  ->execute();
foreach ($resultakteur as $row) {
  $akteur = $row->name;
}

//-----------------------------------

//das wird ausgeführt, wenn auf "Löschen" gedrückt wird
if (isset($_POST['submit'])) {

  $akteur_id = $_POST['akteur_id'];

  //Akteur aus $tbl_akteur_events loeschen
  $akteureventloeschen = db_delete($tbl_akteur_events)
    ->condition('AID', $akteur_id, '=')
    ->execute();
  //Akteur aus $tbl_hat_user loeschen
  $akteuruserloeschen = db_delete($tbl_hat_user)
    ->condition('hat_AID', $akteur_id, '=')
    ->execute();

  //Akteur aus DB loeschen
  $akteurloeschen = db_delete($tbl_akteur)
    ->condition('AID', $akteur_id, '=')
    ->execute();

  // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
  if (session_status() == PHP_SESSION_NONE) session_start();
  $_SESSION['sysmsg'][] = 'Der Akteur wurde gelöscht.';
  header("Location: ".base_path()."Akteure");
  
} else {

}

$pathThisFile = $_SERVER['REQUEST_URI'];

//Darstellung
$profileHTML = <<<EOF
<div class="alert-box" data-alert>
  <p>Möchten Sie den Akteur <strong>$akteur</strong> wirklich löschen?</p><br />
  <form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
    <input name="akteur_id" type="hidden" id="eventEIDInput" value="$akteur_id" />
    <a class="secondary button" href="javascript:history.go(-1)">Abbrechen</a>
    <input type="submit" class="button" id="akteurSubmit" name="submit" value="Löschen">
  </form>
</div>
EOF;
