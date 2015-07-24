<?php
/**
 * akteurformular.php stellt ein Formular dar,
 * in welches alle Informationen über einen Akteur
 * eingetragen werden können.
 * Einziges Pflichtfeld ist bisher der Name und die Emailadresse.
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-04
 * Felix, 2015-07-24
 */

//Eingeloggter User
global $user;
$user_id = $user->uid;

//Sicherheitsschutz
if(!user_is_logged_in()){
  drupal_access_denied();
}

//DB-Tabellen
$tbl_hat_Sparte = "aae_data_hat_sparte";
$tbl_adresse = "aae_data_adresse";
$tbl_akteur = "aae_data_akteur";
$tbl_kategorie = "aae_data_kategorie";
$tbl_hat_user = "aae_data_hat_user";
$tbl_bezirke = "aae_data_bezirke";

//-----------------------------------

//Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
//$tbl_akteur
$name = "";
$adresse = "";//Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
$email = "";
$telefon = "";
$url = "";
$ansprechpartner = "";
$funktion = "";
$bild = "";
$beschreibung = "";
$oeffnungszeiten = "";

//$tbl_adresse
$strasse = "";
$nr = "";
$adresszusatz = "";
$plz = "";
$ort = "";
$gps = "";

//$tbl_hat_Sparte
//$kategorie = array();

//Speicherort fuer Bilder
$bildpfad = "var/www/drupal/images/";

//-----------------------------------

//Variable zur Kategoriebestimmung
//$sent = "";
//Variable zur Freigabe: muss true sein
$freigabe = true;

//Fehlervariablen
$fehler_name = "";
$fehler_email = "";
$fehler_telefon = "";
$fehler_url = "";
$fehler_ansprechpartner = "";
$fehler_funktion = "";
$fehler_bild = "";
$fehler_beschreibung = "";
$fehler_oeffnungszeiten = "";
$fehler_strasse = "";
$fehler_nr = "";
$fehler_adresszusatz = "";
$fehler_plz = "";
$fehler_ort = "";
$fehler_gps = "";

//-----------------------------------

//Variablen, welche Texte in den Formularfeldern halten
//$tbl_akteur
$ph_name = "Name des Vereins/ der Organisation";
$ph_email = "E-mail Addresse";
$ph_telefon = "Telefonnummer";
$ph_url = "Website";
$ph_ansprechpartner = "Kontaktperson";
$ph_funktion = "Funktion der Kontaktperson";
$ph_bild = "Dateiname mit Endung";
$ph_beschreibung = "Beschreibung";
$ph_oeffnungszeiten = "Öffnungszeiten";

//$tbl_adresse
$ph_strasse = "Strasse";
$ph_nr = "Hausnummer";
$ph_adresszusatz = "Adresszusatz";
$ph_plz = "PLZ";
$ph_ort = "Bezirk";
$ph_gps = "GPS-Addresskoordinaten";

//$tbl_hat_Sparte

//-----------------------------------

//das wird ausgeführt, wenn auf "Speichern" gedrückt wird
if (isset($_POST['submit'])) {

  //Wertezuweisung
  $name = $_POST['name'];
  $email = $_POST['email'];
  $telefon = $_POST['telefon'];
  $url = $_POST['url'];
  $ansprechpartner = $_POST['ansprechpartner'];
  $funktion = $_POST['funktion'];
  if(isset($_POST['bild'])){
    $bild = $_POST['bild'];
  }
  $kurzbeschreibung = $_POST['kurzbeschreibung'];
  $oeffnungszeiten = $_POST['oeffnungszeiten'];

  $strasse = $_POST['strasse'];
  $nr = $_POST['nr'];
  $adresszusatz = $_POST['adresszusatz'];
  $plz = $_POST['plz'];
  $ort = $_POST['ort'];
  $gps = $_POST['gps'];

  //$kategorie = $_POST['kategorie'];

//-------------------------------------
  //Check-Klauseln

  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0){
    //Feld nicht ausgefüllt
    $fehler_name = "Bitte einen Organisationsnamen eingeben!";
	$freigabe = false;
  }
  //Ckeck, ob Email angegeben wurde
  if(strlen($email) == 0){
    //Feld nicht ausgefüllt
    $fehler_email = "Bitte eine Emailadresse eingeben!";
	$freigabe = false;
  }

  //überflüssige Leerzeichen am Anfang entfernen
  $name=trim($name);
  $email = trim($email);
  $telefon = trim($telefon);
  $url = trim($url);
  $ansprechpartner = trim($ansprechpartner);
  $funktion = trim($funktion);
  $bild = trim($bild);
  $beschreibung = trim($beschreibung);
  $oeffnungszeiten = trim($oeffnungszeiten);
  $strasse = trim($strasse);
  $nr = trim($nr);
  $adresszusatz = trim($adresszusatz);
  $plz = trim($plz);
  $ort = trim($ort);
  $gps = trim($gps);

  //und alle Tags entfernen (Hacker)
  $name=strip_tags($name);
  $email = strip_tags($email);
  $telefon = strip_tags($telefon);
  $url = strip_tags($url);
  $ansprechpartner = strip_tags($ansprechpartner);
  $funktion = strip_tags($funktion);
  $bild = strip_tags($bild);
  $beschreibung = strip_tags($beschreibung);
  $oeffnungszeiten = strip_tags($oeffnungszeiten);
  $strasse = strip_tags($strasse);
  $nr = strip_tags($nr);
  $adresszusatz = strip_tags($adresszusatz);
  $plz = strip_tags($plz);
  $ort = strip_tags($ort);
  $gps = strip_tags($gps);

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.
  if (strlen($name) > 100){
	$fehler_name = "Bitte geben Sie einen kuerzeren Namen an oder verwenden Sie ein Kuerzel.";
	$freigabe = false;
  }
  if (strlen($email) > 100){
	$fehler_email = "Bitte geben Sie eine kuerzere Emailadresse an.";
	$freigabe = false;
  }
  if (strlen($telefon) > 100){
	$fehler_telefon = "Bitte geben Sie eine kuerzere Telefonnummer an.";
	$freigabe = false;
  }
  if (strlen($url) > 100){
	$fehler_url = "Bitte geben Sie eine kuerzere URL an.";
	$freigabe = false;
  }
  if (strlen($ansprechpartner) > 100){
	$fehler_ansprechpartner = "Bitte geben Sie einen kuerzeren Ansprechpartner an.";
	$freigabe = false;
  }
  if (strlen($funktion) > 100){
	$fehler_funktion = "Bitte geben Sie eine kuerzere Funktion an.";
	$freigabe = false;
  }
  if (strlen($beschreibung) > 500){
	$fehler_beschreibung = "Bitte geben Sie eine kuerzere Beschreibung an.";
	$freigabe = false;
  }
  if (strlen($oeffnungszeiten) > 200){
	$fehler_oeffnungszeiten = "Bitte geben Sie kuerzere Oeffnungszeiten an.";
	$freigabe = false;
  }
  if (strlen($strasse) > 100){
	$fehler_strasse = "Bitte geben Sie einen kuerzeren Strassennamen an.";
	$freigabe = false;
  }
  if (strlen($nr) > 100){
	$fehler_nr = "Bitte geben Sie eine kuerzere Nummer an.";
	$freigabe = false;
  }
  if (strlen($adresszusatz) > 100){
	$fehler_adresszusatz = "Bitte geben Sie einen kuerzeren Adresszusatz an.";
	$freigabe = false;
  }
  if (strlen($plz) > 100){
	$fehler_plz = "Bitte geben Sie eine kuerzere PLZ an.";
	$freigabe = false;
  }
  if (strlen($gps) > 100){
	$fehler_gps = "Bitte geben Sie kuerzere GPS-Daten an.";
	$freigabe = false;
  }

  //Wenn Bilddatei ausgewählt wurde...
  if($_FILES){
	$bildname = $_FILES['bild']['name'];

	if($bildname != ""){
	  if move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname);
    else echo $_FILES['bild']['error'];
	  $bild = "images/".$bildname;
    echo '<br />'.$bild;
	}

  }

//---------------------------------

  //Wenn $goodtogo true, ab in die DB mit den Daten
  if($freigabe == true){
	require_once $modulePath . '/database/db_connect.php';
	//include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
	$db = new DB_CONNECT();
	//Das Ergebnis von db_insert()->...->execute(); ist die ID, von diesem Eintrag

	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$resultadresse = db_select($tbl_adresse, 'a')
	  ->fields('a', array(
	    'ADID',
		'gps',
	  ))
	  ->condition('strasse', $strasse, '=')
	  ->condition('nr', $nr, '=')
	  ->condition('adresszusatz', $adresszusatz, '=')
	  ->condition('plz', $plz, '=')
	  ->condition('bezirk', $ort, '=')
	  ->execute();

	//wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
    $i = $resultadresse->rowCount();
	if($i == 0){//Adresse nicht vorhanden
	  $adresse = db_insert($tbl_adresse)
	    ->fields(array(
		  'strasse' => $strasse,
		  'nr' => $nr,
		  'adresszusatz' => $adresszusatz,
		  'plz' => $plz,
		  'bezirk' => $ort,
		  'gps' => $gps,
		))
		->execute();
	}else {//Adresse bereits vorhanden
	  foreach ($resultadresse as $row) {
	    //Abfrage, ob GPS-Angaben gemacht wurden
	    if(strlen($gps) != 0 && strlen($row->gps) == 0 ){//ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
	      //Update der Adresse
	      $adresse_updated = db_update($tbl_adresse)
	 	    ->fields(array(
			  'gps' => $gps,
	        ))
	        ->condition('ADID', $row->ADID, '=')
	        ->execute();
	    }
	    $adresse = $row->ADID;//Adress-ID merken
	  }
	}

    //tbl_akteur INSERT!!!
	$akteur_id = db_insert($tbl_akteur)
   	  ->fields(array(
		'name' => $name,
		'adresse' => $adresse,
		'email' => $email,
		'telefon' => $telefon,
		'url' => $url,
		'ansprechpartner' => $ansprechpartner,
		'funktion' => $funktion,
		'bild' => $bild,
		'beschreibung' => $beschreibung,
		'oeffnungszeiten' => $oeffnungszeiten,
	  ))
	  ->execute();

	//tbl_hat_user insert
	db_insert($tbl_hat_user)
	  ->fields(array(
	    'hat_UID' => $user_id,
	    'hat_AID' => $akteur_id,
	  ))
	  ->execute();


	//$tbl_hat-Sparte
    /*
	foreach ($kategorie as $row) {
	  //Kategorie noch nicht zugeordnet
	  //tbl_hat_Sparte INSERT!!!
	  $hat_sparte_insert = db_insert($tbl_hat_Sparte)
	 	->fields(array(
	      'kategorie' => $row1->kategorie_id,
		))
		->condition('akteur_id', $akteur_id, '=')
	    ->execute();
	}
	*/

	header("Location: ?q=Akteurprofil/".$akteur_id);
  // Leite weiter auf das neu erstellte Profil
  }

} else {
 //Formular wird zum ersten Mal aufgerufen: nichts tun
}

$pathThisFile = $_SERVER['REQUEST_URI'];

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>

  <label>Name (Pflichtfeld):</label>
  <input type="text" class="akteur" id="akteurNameInput" name="name" value="$name" placeholder="$ph_name" required>$fehler_name

  <label>Addresse:</label>
  <label>Straße:</label>
  <input type="text" class="akteur" id="akteurStrasseInput" name="strasse" value="$strasse" placeholder="$ph_strasse">$fehler_strasse
  <label>Nr.:</label>
  <input type="text" class="akteur" id="akteurNrInput" name="nr" value="$nr" placeholder="$ph_nr">$fehler_nr
  <label>Adresszusatz:</label>
  <input type="text" class="akteur" id="akteurAdresszusatzInput" name="adresszusatz" value="$adresszusatz" placeholder="$ph_adresszusatz">$fehler_adresszusatz
  <label>PLZ:</label>
  <input type="text" class="akteur" id="akteurPLZInput" name="plz" value="$plz" placeholder="$ph_plz">$fehler_plz
  <label>Bezirk:</label>
  <!--<input type="text" class="akteur" id="akteurOrtInput" name="ort" value="$ort" placeholder="$ph_ort">$fehler_ort-->
EOF;

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
  $profileHTML .= '<option value="'.$row->BID.'">'.$row->bezirksname.'</option>';
}
$profileHTML .= '</select>';

$profileHTML .= <<<EOF
  <label>Geodaten:</label>
  <input type="text" class="akteur" id="akteurGPSInput" name="gps" value="$gps" placeholder="$ph_gps">$fehler_gps
  <label>Öffnungszeiten:</label>
  <input type="text" class="akteur" id="akteurOeffnungszeitenInput" name="oeffnungszeiten" value="$oeffnungszeiten" placeholder="$ph_oeffnungszeiten">$fehler_oeffnungszeiten

  <label>Kontakt:</label>
  <label>Ansprechpartner:</label>
  <input type="text" class="akteur" id="akteurAnsprechpartnerInput" name="ansprechpartner" value="$ansprechpartner" placeholder="$ph_ansprechpartner">$fehler_ansprechpartner
  <label>Rolle des Ansprechpartners:</label>
  <input type="text" class="akteur" id="akteurFunktionInput" name="funktion" value="$funktion" placeholder="$ph_funktion">$fehler_funktion
  <label>Telefonnummer:</label>
  <input type="text" class="akteur" id="akteurTelefonInput" name="telefon" value="$telefon" placeholder="$ph_telefon">$fehler_telefon
  <label>Website:</label>
  <input type="text" class="akteur" id="akteurURLInput" name="url" value="$url" placeholder="$ph_url">$fehler_url
  <label>Emailaddresse (Pflichtfeld):</label>
  <input type="email" class="akteur" id="akteurEmailInput" name ="email" value="$email" placeholder="$ph_email">$fehler_email<br>

  <label>Beschreibung:</label>
  <textarea name="beschreibung" class="akteur" cols="45" rows="3" placeholder="$ph_beschreibung">$beschreibung</textarea>$fehler_beschreibung
  <label>Bild:</label>
  <input type="file" class="akteur" id="akteurBildInput" name="bild" /><br>

  <!--<label>Sparten:</label>
  <input type="hidden" name="sent" value="yes">-->

  <input type="submit" class="akteure" id="akteureSubmit" name="submit" value="Speichern">
</form>
EOF;
