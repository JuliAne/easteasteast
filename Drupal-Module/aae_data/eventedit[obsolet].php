<?php
//---------------------------------

  //Wenn $goodtogo true, ab in die DB mit den Daten
  if($freigabe == true){
	

$pathThisFile = $_SERVER['REQUEST_URI'];

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
  <input name="event_id" type="hidden" id="eventEIDInput" value="$event_id" />
  <!-- verstecktes Feld für bild -->
  <input name="bild_alt" type="hidden" id="bild_alt" value="$bild" />

  <label>Name (Pflichtfeld):</label>
  <input type="text" class="event" id="eventNameInput" name="name" value="$name" placeholder="$ph_name" required>$fehler_name
EOF;

if(array_intersect(array('administrator'), $user->roles)){
//alle Akteure abfragen, die in DB: nur Admin
  $resultakteure = db_select($tbl_akteur, 'a')
    ->fields('a', array(
      'AID',
	  'name',
    ))
    ->execute();
}else{
  //Akteure abfragen, die in DB und für welche User Schreibrechte hat
  $res = db_select($tbl_akteur, 'a');
  $res->join($tbl_hat_user, 'u', 'a.AID = u.hat_AID AND u.hat_UID = :uid', array(':uid' => $user->uid));
  $res->fields('a', array('AID','name'));
  $resultakteure=$res->execute();
}

$countakteure = $resultakteure->rowCount();
if($countakteure != 0){
  $profileHTML .= '<label>Veranstalter:</label>';
  //Dropdownliste zur Akteurauswahl
  $profileHTML .= '<select name="veranstalter" size="'.$countakteure.'" >';
  //$profileHTML .= '<select name="veranstalter" size="4" >';
  foreach ($resultakteure as $row) {
    $profileHTML .= '<option value="'.$row->AID.'">'.$row->name.'</option>';
  }
  $profileHTML .= '</select>';
}

//Bezirke abfragen, die in DB
$resultbezirke = db_select($tbl_bezirke, 'b')
  ->fields('b', array(
    'BID',
	'bezirksname',
  ))
  ->execute();
$countbezirke = $resultbezirke->rowCount();
//Dropdownliste zur Akteurauswahl
$profileHTML .= '<select name="ort" size="'.$countbezirke.'" >';
foreach ($resultbezirke as $row) {
  if($row->BID == $ort){
	$profileHTML .= '<option value="'.$row->BID.'" selected="selected" >'.$row->bezirksname.'</option>';
  }else{
	$profileHTML .= '<option value="'.$row->BID.'">'.$row->bezirksname.'</option>';
  }
}
$profileHTML .= '</select>';

$profileHTML .= <<<EOF
  <label>Geodaten:</label>
  <input type="text" class="event" id="eventGPSInput" name="gps" value="$gps" placeholder="$ph_gps">$fehler_gps


  <label>Website:</label>
  <input type="text" class="event" id="eventURLInput" name="url" value="$url" placeholder="$ph_url">$fehler_url


  <label>Beschreibung:</label>
  <textarea name="kurzbeschreibung" class="event" cols="45" rows="3" placeholder="$ph_kurzbeschreibung">$kurzbeschreibung</textarea>$fehler_kurzbeschreibung
  <label>Bild:</label><input type="file" class="event" id="eventBildInput" name="bild" value="$bild"/><br>
  <img src="$bild" title="Bisheriges Profilbild" width=250 ><br>

  <p>Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.<br>
Wir veröffentlichen alle Inhalte unter der Free cultural Licence „CC-By 4.0 international“ Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.“ Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.<br>
Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  <input type="submit" class="event" id="eventSubmit" name="submit" value="Speichern">
</form>
<a href="javascript:history.go(-1)">Abbrechen/Zurück</a>
EOF;
