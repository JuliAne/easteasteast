<?php
/**
 * @file festivalformular.php
 * 
 * Stellt ein Hilfs-Formular dar, in welches grundlegende
 * Informationen über ein Festival eingetragen & bearbeitet werden können.
 */
 
namespace Drupal\AaeData;

Class festivalformular extends festivals {

  // $tbl_festival

  
  // misc
  var $fehler = array();
  var $freigabe = true;

  var $resultBezirke = '';
  var $allAkteure; // akteure-object
  var $authorizedAkteure; // akteure-object
  var $target = '';

  function __construct($action) {
    
   parent::__construct();

   global $user;

   if (!array_intersect(array('administrator', 'festival'), $user->roles)) {
    drupal_access_denied();
    drupal_exit();
   } else {
     # if update -> needs to be admin or global admin
   }

   if ($action == 'update') {
    $this->target = 'update';
   }
   
  }

  /**
   *  Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
   *  @returns $output;
   */
  public function run() {

    $explodedpath = explode('/', current_path());
    $this->akteur_id = $this->clearContent($explodedpath[1]);

    $output = '';

    if (isset($_POST['submit'])) {
      if ($this->festivalCheckPost()) {
	     if ($this->target == 'update') {
	      $this->festivalUpdaten();
	     } else {
		    $this->festivalSpeichern();
	     }
       $output = $this->festivalDisplay();
      } else {
	    $output = $this->festivalDisplay();
      }
    } else {
      if ($this->target == 'update') {
	     $this->festivalGetFields();
      }
      $output = $this->festivalDisplay();
    }

    return $output;
  }

  /**
   * Wird ausgeführt, wenn auf "Speichern" geklickt wird
   * @return $this->freigabe : boolean
   */

  private function festivalCheckPost() {
    
    // For Festival:
    $this->name = $this->clearContent($_POST['name']);
    $this->email = $this->clearContent($_POST['email']);
    $this->festivalAkteur = $this->clearContent($_POST['festivalAkteur']);
    $this->alias = $this->clearContent($_POST['alias']);
    $this->beschreibung = $this->clearContent($_POST['beschreibung']);
    if (isset($_POST['bild'])) $this->bild = $_POST['bild'];
    $this->sponsoren = $_POST['sponsoren'];
    $this->authorizedAkteure = $_POST['authorizedAkteure'];

    // For Akteur, if "+ Neuer Akteur" selected
    if ($this->festivalAkteur == 'newAkteur'){
     $this->akName = $this->clearContent($_POST['akName']);
     $this->akStrasse = $this->clearContent($_POST['akStrasse']);
     $this->akNr = $this->clearContent($_POST['akNr']);
     $this->akAdresszusatz = $this->clearContent($_POST['akAdresszusatz']);
     $this->akPlz = $this->clearContent($_POST['akPlz']);
     $this->akOrt = $this->clearContent($_POST['akOrt']);
     $this->akGps = $this->clearContent($_POST['akGps']);
    }

    //-------------------------------------

    if (empty($this->name)) {
     $this->fehler['name'] = "Bitte einen Organisations- bzw. Festivalnamen eingeben!";
     $this->freigabe = false;
    }

    if (empty($this->email) || !valid_email_adress($this->email)) {
     $this->fehler['email'] = "Bitte eine (gültige) Emailadresse eingeben!";
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

    return $this->freigabe;

  } // END akteurCheckPost


  /**
   * Schreibt Daten in DB
   */
  private function festivalSpeichern() {

   $gps = explode(',', $this->gps, 2);

   $this->adresse = db_insert($this->tbl_adresse)
	  ->fields(array(
	   'strasse' => $this->strasse,
	   'nr' => $this->nr,
	   'adresszusatz' => $this->adresszusatz,
	   'plz' => $this->plz,
	   'bezirk' => $this->ort,
	   'gps_lat' => $gps[0],
     'gps_long' => $gps[1]
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
    // FUNCTION -> Festivalpage?
   );

   menu_link_save($item);

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Das Festival wurde erfolgreich erstellt!'));
   header('Location: '. $base_url . '/akteurprofil/' . $this->akteur_id);

  } // END function festivalSpeichern()

  private function festivalUpdaten() {

    //Wenn Bilddatei ausgewählt wurde...
    if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
     $this->bild = $this->upload_image($_FILES['bild']);
    } else if (isset($_POST['oldPic'])) {
     $this->bild = $this->clearContent($_POST['oldPic']);
    }

    $akteurAdresse = db_select($this->tbl_akteur, 'a')
     ->fields('a', array('adresse'))
     ->condition('AID', $this->akteur_id, '=')
     ->execute()
     ->fetchObject();

    $gps = explode(',', $this->gps, 2);

	  $updateAdresse = db_update($this->tbl_adresse)
	  	->fields(array(
		  'strasse' => $this->strasse,
		  'nr' => $this->nr,
		  'adresszusatz' => $this->adresszusatz,
		  'plz' => $this->plz,
		  'bezirk' => $this->ort,
		  'gps_lat' => $gps[0],
      'gps_long' => $gps[1]
		 ))
     ->condition('ADID', $akteurAdresse->adresse, '=')
		 ->execute();

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

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Das Festival wurde erfolgreich bearbeitet!'));
   	header('Location: '. $base_url .'/' . $this->alias);

  } // END function festivalUpdaten()

  private function festivalGetFields() {

    // Auswahl der Daten des ausgewählten Akteurs:
    $resultakteur = db_select($this->tbl_akteur, 'c')
     ->fields('c')
	   ->condition('AID', $this->akteur_id, '=')
     ->execute();

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
     $this->created = new \DateTime($row->created);
     $this->modified = new \DateTime($row->modified);
    }

    //Adressdaten aus DB holen:
    $resultAdresse = db_select($this->tbl_adresse, 'd')
     ->fields('d')
	   ->condition('ADID', $this->adresse, '=')
     ->execute();

    // Speichern der Adressdaten in den Arbeitsvariablen
    foreach ($resultAdresse as $row) {
     $gps = explode(',', $row->gps, 2);

	   $this->strasse = $row->strasse;
	   $this->nr = $row->nr;
	   $this->adresszusatz = $row->adresszusatz;
	   $this->plz = $row->plz;
	   $this->ort = $row->bezirk;
	   $this->gps = $row->gps_lat.','.$row->gps_long;
    }

  } // END function akteurGetFields()


  /**
   * Darstellung des Formulars
   */
  private function festivalDisplay() {

    $this->resultBezirke = db_select($this->tbl_bezirke, 'b')
     ->fields('b', array('BID', 'bezirksname'))
     ->execute()
     ->fetchAll();
    
    $this->ownedAkteure = $this->akteure->getAkteure(array(
      'filter' => array(
       'uid' => $this->user_id
      )
     ), 'ultraminimal'
    );
     
    $this->allAkteure = $this->akteure->getAkteure(NULL, 'ultraminimal');
     
    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme() . '/templates/festivalformular.tpl.php';
    return ob_get_clean();

  } // END function festivalDisplay()

} // END class festivalformular
