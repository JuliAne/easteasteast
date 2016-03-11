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
 */

Class akteurformular extends aae_data_helper {

  // $tbl_akteur
  var $name = "";
  var $adresse = "";
  var $email = "";
  var $telefon = "";
  var $url = "";
  var $ansprechpartner = "";
  var $funktion = "";
  var $bild = "";
  var $beschreibung = "";
  var $oeffnungszeiten = "";
  var $barrierefrei = "";
  var $created = "";
  var $modified = "";

  // $tbl_adresse
  var $strasse = "";
  var $nr = "";
  var $adresszusatz = "";
  var $plz = "";
  var $ort = "";
  var $gps = "";

  // $tbl_tag
  var $sparten = "";
  var $all_sparten = ''; // Ermöglicht Tokenizer-plugin im Frontend

  var $akteur_id = "";
  var $user_id = "";
  var $fehler = array();
  var $freigabe = true; // Variable zur Freigabe: muss true sein

  // $tbl_akteur
  var $ph_name = "Name des Vereins/ der Organisation";
  var $ph_email = "E-mail Addresse";
  var $ph_telefon = "Telefonnummer";
  var $ph_url = "Website";
  var $ph_ansprechpartner = "Kontaktperson";
  var $ph_funktion = "Funktion der Kontaktperson";
  var $ph_bild = "Dateiname mit Endung";
  var $ph_beschreibung = "Beschreibungstext. In der Vorschau werden die ersten 30 Wörter angezeigt.";
  var $ph_oeffnungszeiten = "Öffnungszeiten";

  // $tbl_adresse
  var $ph_strasse = "Strasse";
  var $ph_nr = "Hausnummer";
  var $ph_adresszusatz = "Adresszusatz";
  var $ph_plz = "PLZ";
  var $ph_ort = "Bezirk";
  var $ph_gps = "GPS-Addresskoordinaten";

  // $tbl_akteur_hat_Sparte
  var $countsparten = "";
  var $sparte_id = "";

  var $resultbezirke = "";
  var $target = "";
  var $modulePath;
  var $removedTags;
  var $removedPic;
  var $rssFeed = "";

  //-----------------------------------

  function __construct($action) {

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

  /*  $og_title = array(
  '#tag' => 'meta',
  '#attributes' => array(
    'property' => 'og:title',
    'content' => 'bla',
  ),
);

drupal_add_html_head($og_title, 'og_title'); */

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
   * Wird ausgeführt, wenn auf "Speichern" geklickt wird
   * @return $this->freigabe [boolean]
   */

  private function akteurCheckPost() {

    $this->name = $this->clearContent($_POST['name']);
    $this->email = $this->clearContent($_POST['email']);
    $this->telefon = $this->clearContent($_POST['telefon']);
    $this->url = $this->clearContent($_POST['url']);
    $this->ansprechpartner = $this->clearContent($_POST['ansprechpartner']);
    $this->funktion = $this->clearContent($_POST['funktion']);
    if (isset($_POST['bild'])) $this->bild = $_POST['bild'];
    $this->beschreibung = $this->clearContent($_POST['beschreibung']);
    $this->oeffnungszeiten = $this->clearContent($_POST['oeffnungszeiten']);
    $this->strasse = $this->clearContent($_POST['strasse']);
    $this->nr = $this->clearContent($_POST['nr']);
    $this->adresszusatz = $this->clearContent($_POST['adresszusatz']);
    $this->plz = $this->clearContent($_POST['plz']);
    $this->ort = $this->clearContent($_POST['ort']);
    $this->gps = $this->clearContent($_POST['gps']);
    $this->sparten = $_POST['sparten'];
    $this->removedTags = $_POST['removedTags'];
    $this->removedPic = $_POST['removeCurrentPic'];
    $this->rssFeed = $this->clearContent($_POST['rssFeed']);

    //-------------------------------------

    if (empty($this->name)) {
     $this->fehler['name'] = "Bitte einen Organisationsnamen eingeben!";
     $this->freigabe = false;
    }

    if (empty($this->email)) {
     $this->fehler['email'] = "Bitte eine Emailadresse eingeben!";
	   $this->freigabe = false;
    }

    if (empty($this->ort)) {
     $this->fehler['ort'] = "Bitte einen Bezirk auswählen!";
     $this->freigabe = false;
    }

    // Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.
    if (strlen($this->name) > 64) {
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

    if (!empty($this->url) && preg_match('/\A(http:\/\/|https:\/\/)(\w*[.|-]\w*)*\w+\.[a-z]{2,3}(\/.*)*\z/',$this->url)==0) {
     $this->fehler['url'] = "Bitte eine gültige URL zur Akteurswebseite eingeben! (z.B. <i>http://meinakteur.de</i>)";
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

    if (strlen($this->beschreibung) > 1200) {
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

    if (strlen($this->rssFeed) > 400) {

    }

    if ($this->gps == 'Ermittle Geo-Koordinaten...') $this->gps = '';

    // Um die bereits gewählten Tag's anzuzeigen benötigen wir deren Namen...
    if ($this->freigabe == false) {

     $neueSparten = array();

     foreach($this->sparten as $sparte) {

      $sparte = strtolower($this->clearContent($sparte));

      if (is_numeric($sparte)) {

      $spartenName = db_select($this->tbl_sparte, 's')
       ->fields('s', array('kategorie'))
       ->condition('KID', $sparte, '=')
       ->execute()
       ->fetchAll();

       $neueSparten[$sparte] = $spartenName[0]->kategorie;

     } else {

      $neueSparten[] = $sparte;

     }
    }
     $this->sparten = $neueSparten;
   }

    return $this->freigabe;

  } // END akteurCheckPost


  /**
   * Schreibt Daten in DB
   */
  private function akteurSpeichern() {

	$this->adresse = db_insert($this->tbl_adresse)
	 ->fields(array(
	  'strasse' => $this->strasse,
	  'nr' => $this->nr,
	  'adresszusatz' => $this->adresszusatz,
	  'plz' => $this->plz,
	  'bezirk' => $this->ort,
	  'gps' => $this->gps
   ))
	 ->execute();

   //Wenn Bilddatei ausgewählt wurde...

   if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
    $this->bild = $this->upload_image($_FILES['bild']);
   }

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
		 'beschreibung' => $this->beschreibung,
		 'oeffnungszeiten' => $this->oeffnungszeiten,
     'barrierefrei' => (isset($_POST['barrierefrei']) && !empty($_POST['barrierefrei']) ? '1' : '0'),
		 'ersteller' => $this->user_id,
     'created' => date('Y-m-d H:i:s', time())
	  ))
	  ->execute();

   db_insert($this->tbl_hat_user)
	  ->fields(array(
	    'hat_UID' => $this->user_id,
	    'hat_AID' => $this->akteur_id,
    ))
	 ->execute();

   if (module_exists('aggregator') && !empty($this->rssFeed)) {

    $feed = array(
     'category' => 'aae-feeds',
     'title' => 'aae-feed-'.$this->akteur_id,
     'description' => t('Feed for AAE-User :username', array(':username' => $this->name)),
     'url' => $this->rssFeed,
     'refresh' => '86400',
     'link' => base_path().'akteurprofil/'.$this->akteur_id,
     'block' => 0
    );

    aggregator_save_feed($feed);
    aggregator_refresh($feed);

   }

  // Tell Drupal about new akteurprofil/ID-item

  $parentItem = db_query(
   "SELECT menu_links.mlid
    FROM {menu_links} menu_links
    WHERE menu_name = :menu_name AND link_path = :link_path",
    array(":menu_name" => "navigation", ":link_path" => 'akteure'));

   $plid = $parentItem->fetchObject();

   $item = array(
    'menu_name' => 'navigation',
    'weight' => 1,
    'link_title' => t('Akteurprofil von !username', array('!username' => $this->name)),
    'module' => 'aae_data',
    'link_path' => 'akteurprofil/'.$this->akteur_id,
    'plid' => $plid->mlid
   );

   menu_link_save($item);

   if (is_array($this->sparten) && !empty($this->sparten)) {

    foreach ($this->sparten as $id => $sparte) {
		// Tag bereits in DB?

    $sparte = strtolower($this->clearContent($sparte));

    $sparte_id = '';

		$resultsparte = db_select($this->tbl_sparte, 's')
		  ->fields('s')
		  ->condition('KID', $sparte, '=')
		  ->execute();

		if ($resultsparte->rowCount() == 0) {
     // Tag in DB einfügen
		 $sparte_id = db_insert($this->tbl_sparte)
		  ->fields(array('kategorie' => $sparte))
		  ->execute();

		} else {

		  foreach ($resultsparte as $row) {
		    $sparte_id = $row->KID;
		  }

		}

		// Akteur & Tag in Tabelle $tbl_hat_sparte einfügen

		$insertAkteurSparte = db_insert($this->tbl_hat_sparte)
		  ->fields(array(
		    'hat_AID' => $this->akteur_id,
		    'hat_KID' => $sparte_id,
		  ))
		  ->execute();
	  }
	 }

   // Call hooks
   module_invoke_all('hook_akteur_created');

   // Gebe auf der nächsten Seite eine Erfolgsmeldung aus...

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Ihr Akteurprofil wurde erfolgreich erstellt!'));
   header('Location: '. $base_url . '/akteurprofil/' . $this->akteur_id);

  } // END function akteurSpeichern()

  /**
   * Akteurinformationen aktualisieren in DB
   */
  private function akteurUpdaten() {

    //Wenn Bilddatei ausgewählt wurde...
    if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
     $this->bild = $this->upload_image($_FILES['bild']);
    } else if (isset($_POST['oldPic'])) {
     $this->bild = $this->clearContent($_POST['oldPic']);
    }

    if (!empty($this->removedTags) && is_array($this->removedTags)) {

     foreach($this->removedTags as $tag) {

      $tag = $this->clearContent($tag);

      db_delete($this->tbl_hat_sparte)
       ->condition('hat_KID', $tag, '=')
       ->condition('hat_AID', $this->akteur_id, '=')
       ->execute();

     }
    }

    $akteurAdresse = db_select($this->tbl_akteur, 'a')
     ->fields('a', array('adresse'))
     ->condition('AID', $this->akteur_id, '=')
     ->execute()
     ->fetchObject();

	  $updateAdresse = db_update($this->tbl_adresse)
	  	->fields(array(
		  'strasse' => $this->strasse,
		  'nr' => $this->nr,
		  'adresszusatz' => $this->adresszusatz,
		  'plz' => $this->plz,
		  'bezirk' => $this->ort,
		  'gps' => $this->gps,
		))
    ->condition('ADID', $akteurAdresse->adresse, '=')
		->execute();

    // remove current picture manually

    if (!empty($this->removedPic)) {

     $b = end(explode('/', $this->removedPic));

     if (file_exists($this->short_bildpfad.$b)) {
      unlink($this->short_bildpfad.$b);
     }

     if ($_POST['oldPic'] == $this->removedPic) $this->bild = '';

    }

	  $updateAkteur = db_update($this->tbl_akteur)
     ->fields(array(
	    'name' => $this->name,
		  'email' => $this->email,
		  'telefon' => $this->telefon,
		  'url' => $this->url,
		  'ansprechpartner' => $this->ansprechpartner,
		  'funktion' => $this->funktion,
		  'bild' => $this->bild,
		  'beschreibung' => $this->beschreibung,
      'barrierefrei' => (isset($_POST['barrierefrei']) && !empty($_POST['barrierefrei']) ? '1' : '0'),
		  'oeffnungszeiten' => $this->oeffnungszeiten,
      'modified' => date('Y-m-d H:i:s', time())
	   ))
	   ->condition('AID', $this->akteur_id, '=')
	   ->execute();

     if (module_exists('aggregator')) {

       $akteurFeed = db_select('aggregator_feed', 'af')
        ->fields('af', array('fid','url'))
        ->condition('title', 'aae-feed-'.$this->akteur_id)
        ->execute();

       $hasFeed = $akteurFeed->rowCount();
       $akteurFeed = $akteurFeed->fetchObject();

     if (!empty($this->rssFeed) && $hasFeed){

      // rewrite RSS-path of Feed
      $feedUpdate = db_update('aggregator_feed')
       ->fields(array('url' => $this->rssFeed))
       ->condition('title', 'aae-feed-'.$this->akteur_id)
       ->execute();

      //remove all current feed items
      db_delete('aggregator_item')
       ->condition('fid', $hasFeed);

     } else if (!empty($this->rssFeed) && !$hasFeed) {

     $feed = array(
      'category' => 'aae-feeds',
      'title' => 'aae-feed-'.$this->akteur_id,
      'description' => t('Feed for AAE-User :username', array(':username' => $this->name)),
      'url' => $this->rssFeed,
      'refresh' => '86400', // daily
      'link' => base_path().'akteurprofil/'.$this->akteur_id,
      'block' => 0
     );
     aggregator_save_feed($feed);
     aggregator_refresh($feed);

    } else if (empty($this->rssFeed) && $hasFeed && $akteurFeed->url != $this->rssFeed) {

     // remove akteur-feed and its items

     db_delete('aggregator_feed')
      ->condition('fid', $akteurFeed->fid)
      ->execute();
     db_delete('aggregator_item')
      ->condition('fid', $akteurFeed->fid)
      ->execute();

    }
   }

   // Update Tags

   if (is_array($this->sparten) && !empty($this->sparten)) {

    foreach ($this->sparten as $sparte) {
  	// Tag bereits in DB?

    $sparte_id = '';
    $sparte = strtolower($this->clearContent($sparte));

  	$resultsparte = db_select($this->tbl_sparte, 's')
  	 ->fields('s')
  	 ->condition('KID', $sparte, '=')
  	 ->execute();

    if ($resultsparte->rowCount() == 0) {
     // Tag in DB einfügen
     $sparte_id = db_insert($this->tbl_sparte)
  	   ->fields(array('kategorie' => $sparte))
  	 	 ->execute();

  	} else {

  	  foreach ($resultsparte as $row) {
  	    $sparte_id = $row->KID;
  	  }
  	}

    // Hat der Akteur dieses Tag bereits zugeteilt?

    $hatAkteurSparte = db_select($this->tbl_hat_sparte, 'hs')
     ->fields('hs')
     ->condition('hat_KID', $sparte_id, '=')
     ->condition('hat_AID', $this->akteur_id, '=')
     ->execute();

     if ($hatAkteurSparte->rowCount() == 0) {
      // Nein, daher rein damit

      db_insert($this->tbl_hat_sparte)
      ->fields(array(
       'hat_AID' => $this->akteur_id,
       'hat_KID' => $sparte_id
       ))
      ->execute();

     }
    }
   }

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Ihr Akteurprofil wurde erfolgreich bearbeitet!'));
   	header("Location: ". $base_url ."/akteurprofil/" . $this->akteur_id);

  } // END function akteurUpdaten()

  /**
   * Holen der Akteursattribute aus DB (Aufgerufen bei akteuredit/)
   */
  private function akteurGetFields() {

    // Auswahl der Daten des ausgewählten Akteurs:
    $resultakteur = db_select($this->tbl_akteur, 'c')
     ->fields('c')
	   ->condition('AID', $this->akteur_id, '=')
     ->execute();

    if (module_exists('aggregator')) {
     $this->rssFeed = aggregator_feed_load('aae-feed-'.$this->akteur_id);
    }

    // Speichern der Daten in den Arbeitsvariablen
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
     $this->barrierefrei = $row->barrierefrei;
     $this->created = new DateTime($row->created);
     $this->modified = new DateTime($row->modified);
    }

    //Adressdaten aus DB holen:
    $resultAdresse = db_select($this->tbl_adresse, 'd')
     ->fields('d', array(
	    'strasse',
	    'nr',
	    'adresszusatz',
	    'plz',
	    'bezirk',
	    'gps'
	   ))
	   ->condition('ADID', $this->adresse, '=')
     ->execute();

    // Speichern der Adressdaten in den Arbeitsvariablen
    foreach ($resultAdresse as $row) {
	   $this->strasse = $row->strasse;
	   $this->nr = $row->nr;
	   $this->adresszusatz = $row->adresszusatz;
	   $this->plz = $row->plz;
	   $this->ort = $row->bezirk;
	   $this->gps = $row->gps;
    }

    $resultSparten = db_select($this->tbl_hat_sparte, 'hs')
     ->fields('hs')
     ->condition('hat_AID', $this->akteur_id, '=')
     ->execute()
     ->fetchAll();

    $sparten = array();

    foreach($resultSparten as $sparte) {

     $sparten[] = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->condition('KID', $sparte->hat_KID, '=')
     ->execute()
     ->fetchAll();

    }

    $this->sparten = $sparten;

  } // END function akteurGetFields()


  /**
   * Darstellung des Formulars
   */
  private function akteurDisplay() {

    $this->resultBezirke = db_select($this->tbl_bezirke, 'b')
     ->fields('b', array( 'BID', 'bezirksname' ))
     ->execute();

    $this->all_sparten = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->execute()
     ->fetchAll();

    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme() . '/templates/akteurformular.tpl.php';
    return ob_get_clean(); // Übergabe des gerenderten "akteurformular.tpl"

  } // END function akteurDisplay()

} // END class akteurformular
