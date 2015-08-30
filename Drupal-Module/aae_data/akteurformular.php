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
$tbl_hat_sparte = "aae_data_hat_sparte";
$tbl_adresse = "aae_data_adresse";
$tbl_akteur = "aae_data_akteur";
$tbl_sparte = "aae_data_kategorie";
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

//Tags:
$sparten="";

//Speicherort fuer Bilder
$bildpfad = "/home/swp15-aae/drupal/sites/default/files/styles/large/public/field/image/";
$short_bildpfad = "sites/default/files/styles/large/public/field/image/";

//-----------------------------------

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
$fehler_sparten = "";

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
$ph_sparten = "Tags kommasepariert eingeben!";

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
  $beschreibung = $_POST['beschreibung'];
  $oeffnungszeiten = $_POST['oeffnungszeiten'];

  $strasse = $_POST['strasse'];
  $nr = $_POST['nr'];
  $adresszusatz = $_POST['adresszusatz'];
  $plz = $_POST['plz'];
  $ort = $_POST['ort'];
  $gps = $_POST['gps'];

  $sparten = $_POST['sparten'];
  $explodedsparten = "";
  if($sparten != ""){
	$explodedsparten = explode(",", $sparten);
  }

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

  //Tags:
  if($sparten != ""){
	$countsparten = count($explodedsparten);
	$i = 0;
	while($i < $countsparten){
	  $explodedsparten[$i] = trim($explodedsparten[$i]);
	  $explodedsparten[$i] = strip_tags($explodedsparten[$i]);
	  $i = $i+1;
	}
  }

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
	  if (!move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname)) {
      echo 'Error: Konnte Bild nicht hochladen. Bitte informieren Sie den Administrator. Bildname: <br />'.$bildname;
      exit();
    }
	  $bild = base_path().$short_bildpfad.$bildname;
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
		'ersteller' => $user->uid,
	  ))
	  ->execute();

	//tbl_hat_user insert
	db_insert($tbl_hat_user)
	  ->fields(array(
	    'hat_UID' => $user_id,
	    'hat_AID' => $akteur_id,
	  ))
	  ->execute();

	//falls Tags angegeben wurden
	if($sparten != ""){
	  $sparte_id = "";
      $countsparten = count($explodedsparten);
	  $i = 0;
	  while($i < $countsparten){
		//1. Prüfen, ob Tag bereits in Tabelle $tbl_sparte
		$resultsparte = db_select($tbl_sparte, 's')
		  ->fields('s', array(
		    'KID',
		  ))
		  ->condition('kategorie', $explodedsparten[$i], '=')
		  ->execute();
		$countresult = $resultsparte->rowCount();
		if($countresult == 0){//nein: Tag in $tbl_sparte einfügen
		  $sparte_id = db_insert($tbl_sparte)
		    ->fields(array(
		      'kategorie' => $explodedsparten[$i],
			))
			->execute();
		}else{//ja: KID des Tags holen
		  foreach ($resultsparte as $row) {
			$sparte_id = $row->KID;
		  }
		}
		//2. Akteur+Tag in Tabelle $tbl_hat_sparte einfügen
		$insertakteursparte = db_insert($tbl_hat_sparte)
		  ->fields(array(
		    'hat_AID' => $akteur_id,
		    'hat_KID' => $sparte_id,
		  ))
		  ->execute();
	    $i = $i+1;
	  }
	}

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
//Dropdownliste zur Bezirksauswahl
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

  <label>Tags:</label>
  <input type="text" class="akteur" id="akteurSpartenInput" name="sparten" value="$sparten" placeholder="$ph_sparten">$fehler_sparten
  <p>Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.<br>
Wir veröffentlichen alle Inhalte unter der Free cultural Licence „CC-By 4.0 international“ Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.“ Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.<br>
Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  <input type="submit" class="akteure" id="akteureSubmit" name="submit" value="Speichern">
</form>
EOF;


/*-----------------------------------

FELIX: Die folgenden drei Zeilen sorgen dafür, dass die Theme-Datei zur Ausgabe des
       Akteursformulars eingebunden wird. Dort sind alle Variablen wie hier
       verfügbar. Bitte auskommentieren, sobald eine Methode zur einheitlichen Auswertung
       der POST-Daten gefunden wurde :)

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/akteurformular.tpl.php';

$profileHTML = ob_get_clean(); // Übergabe des gerenderten "project.tpl" */
