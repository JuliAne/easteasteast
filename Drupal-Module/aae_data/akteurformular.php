<?php
/**
 * akteurformular.php stellt ein Formular dar,
 * in welches alle Informationen über einen Akteur
 * eingetragen UND bearbeitet werden koennen.
 * Einzige Pflichtfelder sind bisher Name, Emailadresse und Bezirk.
 *
 * Die Klasse akteurformular wird in aae_data.module initialisiert (s. __construct)
 * und via ->run() aufgerufen.
 *
 * Ruth, 2015-07-04
 * Felix, 2015-09-04
 *
 * TODO: - Tagspeicherung+User hinzufuegen funktioniert nach Umbau nicht mehr -> neu implementieren
 *       - Mehr Security-Stuff muss hier rein, ggf. "phpsec" einbinden
 *
 */

Class akteurformular {

  //DB-Tabellen
  var $tbl_hat_sparte = "aae_data_hat_sparte";
  var $tbl_adresse = "aae_data_adresse";
  var $tbl_akteur = "aae_data_akteur";
  var $tbl_sparte = "aae_data_kategorie";
  var $tbl_hat_user = "aae_data_hat_user";
  var $tbl_bezirke = "aae_data_bezirke";

  //$tbl_akteur
  var $name = "";
  var $adresse = ""; //Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
  var $email = "";
  var $telefon = "";
  var $url = "";
  var $ansprechpartner = "";
  var $funktion = "";
  var $bild = "";
  var $beschreibung = "";
  var $oeffnungszeiten = "";

  //$tbl_adresse
  var $strasse = "";
  var $nr = "";
  var $adresszusatz = "";
  var $plz = "";
  var $ort = "";
  var $gps = "";

  //Tags:
  var $sparten = "";
  var $all_sparten = ''; // Ermöglicht Tokenizer-plugin im Frontend

  //Speicherort fuer Bilder
  var $bildpfad = "/var/www/virtual/grinch/leipziger-ecken.de/sites/default/files/styles/large/public/field/image/";
  var $short_bildpfad = "sites/default/files/styles/large/public/field/image/";

  var $akteur_id = "";
  var $user_id = "";
  var $fehler = array();
  var $freigabe = true; // Variable zur Freigabe: muss true sein

  //Variablen, welche Texte in den Formularfeldern halten ("Placeholder")
  //$tbl_akteur
  var $ph_name = "Name des Vereins/ der Organisation";
  var $ph_email = "E-mail Addresse";
  var $ph_telefon = "Telefonnummer";
  var $ph_url = "Website";
  var $ph_ansprechpartner = "Kontaktperson";
  var $ph_funktion = "Funktion der Kontaktperson";
  var $ph_bild = "Dateiname mit Endung";
  var $ph_beschreibung = "Beschreibung";
  var $ph_oeffnungszeiten = "Öffnungszeiten";

  //$tbl_adresse
  var $ph_strasse = "Strasse";
  var $ph_nr = "Hausnummer";
  var $ph_adresszusatz = "Adresszusatz";
  var $ph_plz = "PLZ";
  var $ph_ort = "Bezirk";
  var $ph_gps = "GPS-Addresskoordinaten";

  //$tbl_hat_Sparte
  var $ph_sparten = "Tags kommasepariert eingeben!";
  var $explodedsparten = "";
  var $countsparten = "";
  var $sparte_id = "";

  var $resultbezirke = "";
  var $target = "";
  var $modulePath;

  //-----------------------------------

  function __construct($action) {

    //Sicherheitsschutz
    if (!user_is_logged_in()) {
      drupal_access_denied();
    }

    $this->modulePath = drupal_get_path('module', 'aae_data');
    global $user;
    $this->user_id = $user->uid;

    // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
    if ($action == 'update') {
      $this->target = 'update';
    }
  } // END Constructor

  /**
   *  Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
   *  @returns $profileHTML;
   */
  public function run() {

    $path = current_path();
    $explodedpath = explode("/", $path);
    $this->akteur_id = $this->clearContent($explodedpath[1]);

    $output = '';

    if (isset($_POST['submit'])) {
      if ($this->akteurCheckPost()) {
	    if ($this->target == 'update') {
	      $this->akteurUpdaten();
	    } else {
		  $this->akteurSpeichern();
	    }
        $output = $this->akteurDisplay();
      } else {
	    $output = $this->akteurDisplay();
      }
    } else {
      // Was passiert, wenn Seite zum ersten mal gezeigt wird?
      // Lade Feld-Werte via ID (akteurGetFields) und gebe diese aus
      if ($this->target == 'update') {
	    $this->akteurGetFields();
      }
      $output = $this->akteurDisplay();
    }

    return $output;
  }

  /**
   *  Einfache Funktion zum Filtern von POST-Daten. Gerne erweiterbar.
   */
  private function clearContent($trimTag) {
    $clear = trim($trimTag);
    return strip_tags($clear);
  }

  /**
   * Wird ausgefuehrt, wenn auf "Speichern" geklickt wird
   * @return $this->freigabe
   */
  private function akteurCheckPost() {
    //Wertezuweisung
    $this->name = $this->clearContent($_POST['name']);
    $this->email = $this->clearContent($_POST['email']);
    $this->telefon = $this->clearContent($_POST['telefon']);
    $this->url = $this->clearContent($_POST['url']);
    $this->ansprechpartner = $this->clearContent($_POST['ansprechpartner']);
    $this->funktion = $this->clearContent($_POST['funktion']);
    if (isset($_POST['bild'])) {
	  $this->bild = $_POST['bild'];
    }
    $this->beschreibung = $this->clearContent($_POST['beschreibung']);
    $this->oeffnungszeiten = $this->clearContent($_POST['oeffnungszeiten']);
    $this->strasse = $this->clearContent($_POST['strasse']);
    $this->nr = $this->clearContent($_POST['nr']);
    $this->adresszusatz = $this->clearContent($_POST['adresszusatz']);
    $this->plz = $this->clearContent($_POST['plz']);
    $this->ort = $this->clearContent($_POST['ort']);
    $this->gps = $this->clearContent($_POST['gps']);
    $this->sparten = $this->clearContent($_POST['sparten']);
    $this->explodedsparten = "";
    if ($this->sparten != "") {
	  $this->explodedsparten = explode(",", $this->sparten);
    }

    //-------------------------------------
    //Check-Klauseln

    //Check, ob ein Name eingegeben wurde:
    if (strlen($this->name) == 0) {
      $this->fehler['name'] = "Bitte einen Organisationsnamen eingeben!";
      $this->freigabe = false;
    }
    //Check, ob Email angegeben wurde
    if (strlen($this->email) == 0) {
      $this->fehler['email'] = "Bitte eine Emailadresse eingeben!";
	   $this->freigabe = false;
    }
    //Check, ob Bezirk angegeben wurde
    if (strlen($this->ort) == 0) {
      $this->fehler['ort'] = "Bitte einen Bezirk auswählen!";
	  $this->freigabe = false;
    }

    //Tags:
    if ($this->sparten != "") {
      $this->countsparten = count($this->explodedsparten);
	  $i = 0;
      while ($i < $this->countsparten) {
	    $this->explodedsparten[$i] = $this->clearContent($explodedsparten[$i]);
	    $i++;
	  }
    }

    //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.
    if (strlen($this->name) > 100) {
	  $this->fehler['name'] = "Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.";
	  $this->freigabe = false;
    }

    if (strlen($this->email) > 100) {
	  $this->fehler['email'] = "Bitte geben Sie eine kürzere Emailadresse an.";
	  $this->freigabe = false;
    }

    if (strlen($this->telefon) > 100) {
 	  $this->fehler['telefon'] = "Bitte geben Sie eine kürzere Telefonnummer an.";
	  $this->freigabe = false;
    }

    if (strlen($this->url) > 100) {
	  $this->fehler['url'] = "Bitte geben Sie eine kürzere URL an.";
	  $this->freigabe = false;
    }

    if (strlen($this->ansprechpartner) > 100){
	  $this->fehler['ansprechpartner'] = "Bitte geben Sie einen kürzeren Ansprechpartner an.";
	  $this->freigabe = false;
    }

    if (strlen($this->funktion) > 100) {
	  $this->fehler['funktion'] = "Bitte geben Sie eine kürzere Funktion an.";
      $this->freigabe = false;
    }

    if (strlen($this->beschreibung) > 500) {
	  $this->fehler['beschreibung'] = "Bitte geben Sie eine kürzere Beschreibung an.";
	  $this->freigabe = false;
    }

    if (strlen($this->oeffnungszeiten) > 200) {
	  $this->fehler['oeffnungszeiten'] = "Bitte geben Sie kürzere Oeffnungszeiten an.";
	  $this->freigabe = false;
    }

    if (strlen($this->strasse) > 100) {
 	  $this->fehler['strasse'] = "Bitte geben Sie einen kürzeren Strassennamen an.";
	  $this->freigabe = false;
    }

    if (strlen($this->nr) > 100) {
	  $this->fehler['nr'] = "Bitte geben Sie eine kürzere Nummer an.";
	  $this->freigabe = false;
    }

    if (strlen($this->adresszusatz) > 100) {
	  $this->fehler['adresszusatz'] = "Bitte geben Sie einen kürzeren Adresszusatz an.";
	  $this->freigabe = false;
    }

    if (strlen($this->plz) > 100) {
	  $this->fehler['plz '] = "Bitte geben Sie eine kürzere PLZ an.";
	  $this->freigabe = false;
    }

    if (strlen($this->gps) > 100) {
	  $this->fehler['gps'] = "Bitte geben Sie kürzere GPS-Daten an.";
	  $this->freigabe = false;
    }

    return $this->freigabe;
  } // END akteurCheckPost


  /**
   * Schreibt Daten in DB
   */
  private function akteurSpeichern() {

	//Abfrage, ob Adresse bereits in Adresstabelle
	$resultadresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array( 'ADID', 'gps' ))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

	//wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
	if ($resultadresse->rowCount() == 0) {
      //Adresse nicht vorhanden
	  $this->adresse = db_insert($this->tbl_adresse)
	    ->fields(array(
	      'strasse' => $this->strasse,
	      'nr' => $this->nr,
	      'adresszusatz' => $this->adresszusatz,
	      'plz' => $this->plz,
	      'bezirk' => $this->ort,
	      'gps' => $this->gps,
	   ))
	   ->execute();
	} else {
      //Adresse bereits vorhanden
	  foreach ($resultadresse as $row) {
	    //Abfrage, ob GPS-Angaben gemacht wurden
	    if (strlen($this->gps) != 0 && strlen($row->gps) == 0 ) {
          //ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
	      //Update der Adresse
	      $adresse_updated = db_update($this->tbl_adresse)
	 	    ->fields(array( 'gps' => $this->gps ))
	        ->condition('ADID', $row->ADID, '=')
	        ->execute();
	    }
	    $this->adresse = $row->ADID; //Adress-ID merken
	  }
	}

    //Wenn Bilddatei ausgewählt wurde...
    if ($_FILES) {
      $bildname = $_FILES['bild']['name'];
      if ($bildname != "") {
	    if (!move_uploaded_file($_FILES['bild']['tmp_name'], $this->bildpfad . $bildname)) {
          echo 'Error: Konnte Bild nicht hochladen. Bitte informieren Sie den Administrator. Bildname: <br />' . $bildname;
          exit();
        }
        $this->bild = base_path() . $this->short_bildpfad . $bildname;
      }
    }

    //tbl_akteur INSERT!!!
	$this->akteur_id = db_insert($this->tbl_akteur)
   	  ->fields(array(
	    'name' => $this->name,
		'adresse' => $this->adresse,
		'email' => $this->email,
		'telefon' => $this->telefon,
		'url' => $this->url,
		'ansprechpartner' => $this->ansprechpartner,
		'funktion' => $this->funktion,
		'bild' => $this->bild,
		'beschreibung' => $beschreibung,
		'oeffnungszeiten' => $oeffnungszeiten,
		'ersteller' => $this->user_uid,
	  ))
	  ->execute();

    //tbl_hat_user insert
	db_insert($this->tbl_hat_user)
	  ->fields(array(
	    'hat_UID' => $this->user_id,
	    'hat_AID' => $this->akteur_id,
	  ))
	  ->execute();

	//falls Tags angegeben wurden
	if ($this->sparten != "") {
	  $this->sparte_id = "";
      $this->countsparten = count($explodedsparten);
	  $i = 0;
      while ($i < $this->countsparten) {
		//1. Prüfen, ob Tag bereits in Tabelle $tbl_sparte
		$resultsparte = db_select($this->tbl_sparte, 's')
		  ->fields('s', array( 'KID' ))
		  ->condition('kategorie', $this->explodedsparten[$i], '=')
		  ->execute();

		if ($resultsparte->rowCount() == 0) {
          //nein: Tag in $tbl_sparte einfügen
		  $this->sparte_id = db_insert($tbl_sparte)
		    ->fields(array( 'kategorie' => $this->explodedsparten[$i] ))
			->execute();
		} else {
          //ja: KID des Tags holen
		  foreach ($resultsparte as $row) {
		    $this->sparte_id = $row->KID;
		  }
		}

		//2. Akteur+Tag in Tabelle $tbl_hat_sparte einfügen
		$insertakteursparte = db_insert($this->tbl_hat_sparte)
		  ->fields(array(
		    'hat_AID' => $this->akteur_id,
		    'hat_KID' => $this->sparte_id,
		  ))
		  ->execute();
	    $i++;
	  }
	}

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    session_start();
    $_SESSION['sysmsg'][] = 'Ihr Akteurprofil wurde erfolgreich erstellt!';

	header("Location: Akteurprofil/" . $this->akteur_id);
    // Beamen wir dich mal auf die neue Seite...
  } // END function akteurSpeichern()

  /**
   * Akteurinformationen aktualisieren in DB
   */
  private function akteurUpdaten() {

    //Wenn Bilddatei ausgewählt wurde...
    if ($_FILES) {
      $bildname = $_FILES['bild']['name'];
      if ($bildname != "") {
        if (!move_uploaded_file($_FILES['bild']['tmp_name'], $this->bildpfad.$bildname)) {
          echo 'Error: Konnte Bild nicht hochladen. Bitte informieren Sie den Administrator. Bildname: <br />' . $bildname;
          exit();
        }
        $this->bild = base_path() . $this->short_bildpfad . $bildname;
      }
    }

	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$resultadresse = db_select($this->tbl_adresse, 'a')
    ->fields('a', array(  'ADID' ))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

	//wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
	if ($resultadresse->rowCount() == 0) {
      //Adresse nicht vorhanden
	  $this->adresse = db_insert($this->tbl_adresse)
		->fields(array(
		  'strasse' => $this->strasse,
		  'nr' => $this->nr,
		  'adresszusatz' => $this->adresszusatz,
		  'plz' => $this->plz,
		  'bezirk' => $this->ort,
		  'gps' => $this->gps,
		))
		->execute();
	} else {
	  foreach ($resultadresse as $row) {
	    $this->adresse = $row->ADID;
	  }
	}

	$akteur_updated = db_update($this->tbl_akteur)
      ->fields(array(
		'name' => $this->name,
		'adresse' => $this->adresse,
		'email' => $this->email,
		'telefon' => $this->telefon,
		'url' => $this->url,
		'ansprechpartner' => $this->ansprechpartner,
		'funktion' => $this->funktion,
		'bild' => $this->bild,
		'beschreibung' => $this->beschreibung,
		'oeffnungszeiten' => $this->oeffnungszeiten,
	  ))
	  ->condition('AID', $this->akteur_id, '=')
	  ->execute();

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    session_start();
    $_SESSION['sysmsg'][] = 'Ihr Akteurprofil wurde erfolgreich bearbeitet!';

	header("Location: Akteurprofil/" . $this->akteur_id);

  } // END function akteurUpdaten()

  /**
   * Holen der Akteurattribute aus DB
   */
  private function akteurGetFields() {

    //Auswahl der Daten des eingeloggten Akteurs:
    $resultakteur = db_select($this->tbl_akteur, 'c')
      ->fields('c', array(
	    'name',
	    'adresse',
	    'email',
	    'telefon',
	    'url',
	    'ansprechpartner',
	    'funktion',
	    'bild',
	    'beschreibung',
	    'oeffnungszeiten',
	  ))
	  ->condition('AID', $this->akteur_id, '=')
      ->execute();

    //Speichern der Daten in den Arbeitsvariablen
    foreach ($resultakteur as $row) {
	  $this->name = $row->name;
      $this->adresse = $row->adresse;
	  $this->email = $row->email;
	  $this->telefon = $row->telefon;
	  $this->url = $row->url;
	  $this->ansprechpartner = $row->ansprechpartner;
	  $this->funktion = $row->funktion;
	  $this->bild = $row->bild;
	  $this->beschreibung = $row->beschreibung;
	  $this->oeffnungszeiten = $row->oeffnungszeiten;
    }

    //Adressdaten aus DB holen:
    $resultadresse = db_select($this->tbl_adresse, 'd')
      ->fields('d', array(
	    'strasse',
	    'nr',
	    'adresszusatz',
	    'plz',
	    'bezirk',
	    'gps',
	  ))
	  ->condition('ADID', $this->adresse, '=')
      ->execute();

    //Speichern der Adressdaten in den Arbeitsvariablen
    foreach ($resultadresse as $row) {
	  $this->strasse = $row->strasse;
	  $this->nr = $row->nr;
	  $this->adresszusatz = $row->adresszusatz;
	  $this->plz = $row->plz;
	  $this->ort = $row->bezirk;
	  $this->gps = $row->gps;
    }
  } // END function akteurGetFields()


  /**
   * Darstellung des Formulars
   */
  private function akteurDisplay() {

    $this->resultbezirke = db_select($this->tbl_bezirke, 'b')
     ->fields('b', array( 'BID', 'bezirksname' ))
     ->execute();

    $this->all_sparten = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->execute()
     ->fetchAll();

    $pathThisFile = $_SERVER['REQUEST_URI'];

    //Darstellung
    ob_start(); // Aktiviert "Render"-modus

    include_once path_to_theme() . '/templates/akteurformular.tpl.php';

    return ob_get_clean(); // Übergabe des gerenderten "akteurformular.tpl"

  } // END function akteurDisplay()

} // END class akteurformular
