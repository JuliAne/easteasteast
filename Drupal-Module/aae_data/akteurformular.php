<?php
/**
 * akteur_bearbeiten.php stellt ein Formular dar,
 * in welches alle Informationen über einen Akteur
 * eingetragen werden können.
 * Sind bereits Informationen in der DB zu dem Akteur gespeichert,
 * werden diese angezeigt und können bearbeitet werden.
 * Einziges Pflichtfeld ist bisher der Name (orgname).
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-04
 */

//Eingeloggter User
global $user;

//DB-Tabellen
$tbl_hat_Sparte = "aae_data_hat_sparte";
$tbl_adresse = "aae_data_adresse";
$tbl_akteur = "aae_data_akteur";
$tbl_kategorie = "aae_data_kategorie";

//-----------------------------------

//Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
$name = "";
$adresse = "";//Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
$email = "";
$telefon = "";
$url = "";
$ansprechpartner = "";
$funktion = "";
$gps = "";
$bild = "";
$kurzbeschreibung = "";
$oeffnungszeiten = "";

$strasse = "";
$nr = "";
$adresszusatz = "";
$plz = "";
$ort = "";

//Tags??!!??
$kategorie = array();

//Speicherort fuer Bilder
$bildpfad = "bilder/";

//-----------------------------------

$akteur_id = "";
$sent = "";

//Variable zur Freigabe: muss true sein
$goodtogo = true;
//Fehlervariablen
$fail_name = "";

//Variablen, welche Texte in den Formularfeldern halten
$ph_name = "Name des Vereins/ der Organisation";
$ph_email = "E-mail Addresse";
$ph_telefon = "Telefonnummer";
$ph_url = "Website";
$ph_ansprechpartner = "Kontaktperson";
$ph_funktion = "Funktion der Kontaktperson";
$ph_gps = "GPS-Addresskoordinaten";
$ph_bild = "Dateiname mit Endung";
$ph_kurzbeschreibung = "Kurzbeschreibung";
$ph_oeffnungszeiten = "Öffnungszeiten";

$ph_strasse = "Strasse";
$ph_nr = "Hausnummer";
$ph_adresszusatz = "Adresszusatz";
$ph_plz = "PLZ";
$ph_ort = "Ort";
//Tags??!!??

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
  $gps = $_POST['gps'];
  //$bild = $_POST['bild'];
  $kurzbeschreibung = $_POST['kurzbeschreibung'];
  $oeffnungszeiten = $_POST['oeffnungszeiten'];

  $strasse = $_POST['strasse'];
  $nr = $_POST['nr'];
  $adresszusatz = $_POST['adresszusatz'];
  $plz = $_POST['plz'];
  $ort = $_POST['ort'];
  //Tags??!!??
  //$kategorie = $_POST['kategorie'];
	
  //-------------------------------------
  //Check-Klauseln
	
  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0){
    //Feld nicht ausgefüllt
    $fail_name = "Bitte einen Organisationsnamen eingeben!";
	$goodtogo = false;
  }
  /*
  if($_FILES){
	$bildname = $_FILES['bild']['name'];
	if($bildname != ""){
	  move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname);
	  $bild = $bildname;
	}
  }
  */
  //---------------------------------
  //Wenn $goodtogo true, ab in die DB mit den Daten
  if($goodtogo == true){
	require_once $modulePath . '/database/db_connect.php';
	//include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
	//Betroffene Tabellen der Datenbank
	$db = new DB_CONNECT();
	//Das Ergebnis von db_insert()->...->execute(); ist die ID, von diesem Eintrag
	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$resultadress = db_select($tbl_adresse, 'a')
	  ->fields('a', array(
	    'strasse',
		'nr',
		'adresszusatz',
		'plz',
		'ort',
	  ))
	  ->condition('strasse', $strasse, '=')
	  ->condition('nr', $nr, '=')
	  ->condition('adresszusatz', $adresszusatz, '=')
	  ->condition('plz', $plz, '=')
	  ->condition('ort', $ort, '=')
	  ->execute();
	//wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
	$i = 0;
	foreach($resultadress as $row){
	  $i = $i + 1;
	}
	if($i == 0){//Adresse nicht vorhanden
	  $adresse = db_insert($tbl_adresse)
		->fields(array(
		  'strasse' => $strasse,
		  'nr' => $nr,
		  'adresszusatz' => $adresszusatz,
		  'plz' => $plz,
		  'ort' => $ort,
	    ))
		->execute();
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
		'gps' => $gps,
		'bild' => $bild,
		'kurzbeschreibung' => $kurzbeschreibung,
		'oeffnungszeiten' => $oeffnungszeiten,
	  ))
	  ->execute();
	//Tags??!!??
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
		
	header("Location: ?q=Akteure"); //Hier muss hin, welche Seite aufgerufen werden soll,
	  //nach dem die Daten erfolgreich gespeichert wurden.
	}
	
}else{

}

$pathThisFile = $_SERVER['REQUEST_URI']; 

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
  <label>Name:</label>
  <input type="text" class="akteur" id="akteurNameInput" name="name" value="$name" placeholder="$ph_name" required>$fail_name

  <label>Addresse:</label>
  <label>Straße:</label>
  <input type="text" class="akteur" id="akteurStrasseInput" name="strasse" value="$strasse" placeholder="$ph_strasse">
  <label>Nr.:</label>
  <input type="text" class="akteur" id="akteurNrInput" name="nr" value="$nr" placeholder="$ph_nr">
  <label>Adresszusatz:</label>
  <input type="text" class="akteur" id="akteurAdresszusatzInput" name="adresszusatz" value="$adresszusatz" placeholder="$ph_adresszusatz">
  <label>PLZ:</label>
  <input type="text" class="akteur" id="akteurPLZInput" name="plz" value="$plz" placeholder="$ph_plz">
  <label>Stadt:</label>
  <input type="text" class="akteur" id="akteurOrtInput" name="ort" value="$ort" placeholder="$ph_ort">
  <label>Geodaten:</label>
  <input type="text" class="akteur" id="akteurGPSInput" name="gps" value="$gps" placeholder="$ph_gps">
  <label>Öffnungszeiten:</label>
  <input type="text" class="akteur" id="akteurOeffnungszeitenInput" name="oeffnungszeiten" value="$oeffnungszeiten" placeholder="$ph_oeffnungszeiten">
	
  <label>Kontakt:</label>
  <label>Ansprechpartner:</label>
  <input type="text" class="akteur" id="akteurAnsprechpartnerInput" name="ansprechpartner" value="$ansprechpartner" placeholder="$ph_ansprechpartner">
  <label>Rolle des Ansprechpartners:</label>
  <input type="text" class="akteur" id="akteurFunktionInput" name="funktion" value="$funktion" placeholder="$ph_funktion">
  <label>Telefonnummer:</label>
  <input type="text" class="akteur" id="akteurTelefonInput" name="telefon" value="$telefon" placeholder="$ph_telefon">
  <label>Website:</label>
  <input type="text" class="akteur" id="akteurURLInput" name="url" value="$url" placeholder="$ph_url">
  <label>Emailaddresse:</label>
  <input type="email" class="akteur" id="akteurEmailInput" name ="email" value="$email" placeholder="$ph_email"><br>

  <label>Beschreibung:</label>
  <textarea name="kurzbeschreibung" class="akteur" cols="45" rows="3" placeholder="$ph_kurzbeschreibung">$kurzbeschreibung</textarea>
  <!--<label>Bild: $bild</label>
  <input type="file" class="akteur" id="akteurBildInput" name="bild" placeholder="$ph_bild" />-->
	
  <!--<label>Sparten:</label>
  <input type="hidden" name="sent" value="yes">-->

  <input type="submit" class="akteure" id="akteureSubmit" name="submit" value="Speichern">
</form>
EOF;

