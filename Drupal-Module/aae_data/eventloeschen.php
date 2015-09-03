<?php
/**
 * eventloeschen.php löscht ein Event aus der DB
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
$event_id = $explodedpath[1];

//DB-Tabellen
$tbl_event = "aae_data_event";
$tbl_hat_user = "aae_data_hat_user";
$tbl_akteur_events = "aae_data_akteur_hat_events";

//Sicherheitsschutz
if(!user_is_logged_in()){
  drupal_access_denied();
}

//Sicherheitsschutz, ob User entsprechende Rechte hat
$resultakteurid = db_select($tbl_akteur_events, 'e')//Den Akteur zum Event aus DB holen
  ->fields('e', array(
    'AID',
  ))
  ->condition('EID', $event_id, '=')
  ->execute();
$akteur_id = "";
$okay="";//gibt an, ob Zugang erlaubt wird oder nicht
foreach ($resultakteurid as $row) {
  $akteur_id = $row->AID;//Akteur speichern
  //Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
  $resultUser = db_select($tbl_hat_user, 'u')
    ->fields('u', array(
      'hat_UID',
      'hat_AID',
    ))
    ->condition('hat_AID', $akteur_id, '=')
    ->condition('hat_UID', $user_id, '=')
    ->execute();
  $hat_recht = $resultUser->rowCount();
  if($hat_recht == 1){//User gehört zu Akteur
	$okay = 1;//Zugang erlaubt
  }
}
//Abfrage, ob User Ersteller des Events ist:
$ersteller = db_select($tbl_event, 'e')
  ->fields('e', array(
    'ersteller',
  ))
  ->condition('ersteller', $user->uid, '=')
  ->execute();
$ist_ersteller = $ersteller->rowCount();
if($ist_ersteller == 1){
	$okay =1;
}

if(!array_intersect(array('administrator'), $user->roles)){
  if($okay != 1){
    drupal_access_denied();
  }
}


//-----------------------------------

//das wird ausgeführt, wenn auf "Löschen" gedrückt wird
if (isset($_POST['submit'])) {

  $event_id = $_POST['event_id'];
  require_once $modulePath . '/database/db_connect.php';
  //include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
  $db = new DB_CONNECT();

  //Event aus $tbl_akteur_events loeschen
  $eventloeschen = db_delete($tbl_akteur_events)
    ->condition('EID', $event_id, '=')
    ->execute();

  //Event aus DB loeschen
  $eventloeschen = db_delete($tbl_event)
    ->condition('EID', $event_id, '=')
    ->execute();

  header("Location: ?q=Events"); //Hier muss hin, welche Seite aufgerufen werden soll,
		//nach dem die Daten erfolgreich gespeichert wurden.

} else{

}

$pathThisFile = $_SERVER['REQUEST_URI'];

//Darstellung
$profileHTML = <<<EOF

<div class="alert-box" data-alert>
 <p><strong>Möchten Sie dieses Event wirklich löschen?</strong></p><br />
 <form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
   <input name="event_id" type="hidden" id="eventEIDInput" value="$event_id" />
   <a class="secondary button" href="javascript:history.go(-1)">Abbrechen</a>
   <input type="button submit"  id="eventSubmit" name="submit" value="Loeschen">
 </form>
<a href="#" class="close">&times;</a></div>
EOF;
