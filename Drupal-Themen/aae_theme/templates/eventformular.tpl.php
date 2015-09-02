<h3>Event anlegen</h3>
<div class="divider"></div>

<form action='<?= $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

  <label>Name (Pflichtfeld):</label>
  <?= $fehler_name; ?>
  <input type="text" class="event" id="eventNameInput" name="name" value="<?= $name; ?>" placeholder="<?= $ph_name; ?>" required>


<?php if(array_intersect(array('administrator'), $user->roles)){
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

$profileHTML .= '
  <label>Datum (Pflichtfeld):</label>
  <input type="text" class="event" id="eventStartdatumInput" name="start" value="$start" placeholder="$ph_start">$fehler_start
  <label>von (Uhrzeit; ganztägig: keine Uhrzeit angeben):</label>
  <input type="text" class="event" id="eventZeitvonInput" name="zeit_von" value="$zeit_von" placeholder="$ph_zeit_von">$zeit_von
  <label>bis (Uhrzeit; ganztägig: keine Uhrzeit angeben):</label>
  <input type="text" class="event" id="eventZeitbisInput" name="zeit_bis" value="$zeit_bis" placeholder="$ph_zeit_bis">$zeit_bis
  <label>Ende (Datum):</label>
  <input type="text" class="event" id="eventEnddatumInput" name="ende" value="$ende" placeholder="$ph_ende">$fehler_ende

  <label>Addresse:</label>
  <label>Straße:</label>
  <input type="text" class="event" id="eventStrasseInput" name="strasse" value="$strasse" placeholder="$ph_strasse">$fehler_strasse
  <label>Nr.:</label>
  <input type="text" class="event" id="eventNrInput" name="nr" value="$nr" placeholder="$ph_nr">$fehler_nr
  <label>Adresszusatz:</label>
  <input type="text" class="event" id="eventAdresszusatzInput" name="adresszusatz" value="$adresszusatz" placeholder="$ph_adresszusatz">$fehler_adresszusatz
  <label>PLZ:</label>
  <input type="text" class="event" id="eventPLZInput" name="plz" value="$plz" placeholder="$ph_plz">$fehler_plz
  <label>Bezirk:</label>$fehler_ort
  <!--<input type="text" class="event" id="eventOrtInput" name="ort" value="$ort" placeholder="$ph_ort">$fehler_ort-->';

//Bezirke abfragen, die in DB
$resultbezirke = db_select($tbl_bezirke, 'b')
  ->fields('b', array(
    'BID',
	'bezirksname',
  ))
  ->execute();
$countbezirke = $resultbezirke->rowCount();
//Dropdownliste zur Bezirkauswahl
$profileHTML .= '<select name="ort" size="'.$countbezirke.'" >';
$profileHTML .= '<option value="" selected="selected">Bezirk auswählen</option>';
foreach ($resultbezirke as $row) {
  $profileHTML .= '<option value="'.$row->BID.'">'.$row->bezirksname.'</option>';
}
$profileHTML .= '</select>'; echo $profileHTML; ?>

  <label>Geodaten:</label>
  <input type="text" class="event" id="eventGPSInput" name="gps" value="$gps" placeholder="$ph_gps">$fehler_gps


  <label>Website:</label>
  <input type="text" class="event" id="eventURLInput" name="url" value="$url" placeholder="$ph_url">$fehler_url


  <label>Beschreibung:</label>
  <textarea name="kurzbeschreibung" class="event" cols="45" rows="3" placeholder="$ph_kurzbeschreibung">$kurzbeschreibung</textarea>$fehler_kurzbeschreibung
  <label>Bild:</label>
  <input type="file" class="event" id="eventBildInput" name="bild" /><br>

  <label>Tags:</label>
  <input type="text" class="event" id="eventSpartenInput" name="sparten" value="$sparten" placeholder="$ph_sparten">$fehler_sparten
  <p>Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.<br>
Wir veröffentlichen alle Inhalte unter der Free cultural Licence „CC-By 4.0 international“ Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.“ Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.<br>
Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  <input type="submit" class="event" id="eventSubmit" name="submit" value="Speichern">
</form>
<a href="javascript:history.go(-1)">Abbrechen/Zurück</a>
