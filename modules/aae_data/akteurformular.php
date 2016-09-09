<?php
/**
 * @file akteurformular.php
 *
 * Stellt ein Formular dar, in welches alle Informationen über
 * einen Akteur eingetragen & bearbeitet werden koennen.
 *
 * Einzige Pflichtfelder sind bisher Name, Email-adresse und Bezirk.
 *
 */
 
namespace Drupal\AaeData;

Class akteurformular extends akteure {

 function __construct($action) {
    
   parent::__construct();

   $explodedpath = explode('/', current_path());
   $this->akteur_id = $this->clearContent($explodedpath[1]);
  # require_once('models/akteure.php');
   $this->akteur = new akteure();
   
   // Check for permissions
   if (!user_is_logged_in() || ($action == 'update' && !$this->akteur->akteurExists($this->akteur_id))) {
    drupal_access_denied();
    drupal_exit();
   }

   // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
   if ($action == 'update') {
    $this->target = 'update';
   }

 }

  /**
   *  Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
   *  @returns $profileHTML;
   */

  public function run() {

    /*if (isset($_POST['submit'])) {
      if ($this->akteurCheckPost()) {
	     if ($this->target == 'update') {
        if (!$this->akteur->isAuthorized($this->akteur_id)){
         drupal_access_denied();
         drupal_exit();
        }
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
      // Lade Feld-Werte via akteurGetFields
      if ($this->target == 'update') {
       if (!$this->akteur->isAuthorized($this->akteur_id)){
        drupal_access_denied();
        drupal_exit();
       } else {
	      $this->akteurGetFields();
       }
      }
      $output = $this->akteurDisplay();
    } */ 

    if (isset($_POST['submit'])) {
    
    } else {

      // Load input-values via akteure-model
      if ($this->target == 'update') {

       if (!$this->akteur->isAuthorized($this->akteur_id)){
        drupal_access_denied();
        drupal_exit();
       } else {
	      $this->akteurGetFields();
       }

      }

    }

    return $this->akteurDisplay();
  }

  /**
   * Wird ausgeführt, wenn auf "Speichern" geklickt wird
   * @return $this->freigabe : boolean
   */

  private function akteurCheckPost() {

    # here: try -> catch
    // Um die bereits gewählten Tags anzuzeigen benötigen wir deren Namen...
    if ($this->freigabe == false) {

     $neueSparten = array();

     foreach ($this->sparten as $sparte) {

      $sparte = strtolower($this->clearContent($sparte));

      if (is_numeric($sparte)) {

      $spartenName = db_select($this->tbl_sparte, 's')
       ->fields('s', array('kategorie'))
       ->condition('KID', $sparte)
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
   * TODO: Vereinheitliche Funktion zum Adressspeichern
   */
  private function akteurSpeichern() {

   // Wenn Bilddatei ausgewählt wurde...

   if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
    $this->bild = $this->upload_image($_FILES['bild']);
   }

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

    $this->sparten = array_unique($this->sparten);

    foreach ($this->sparten as $id => $sparte) {
		// Tag bereits in DB?

    $sparte = strtolower($this->clearContent($sparte));

    $sparte_id = '';

		$resultsparte = db_select($this->tbl_sparte, 's')
		  ->fields('s')
		  ->condition('KID', $sparte)
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

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Ihr Akteurprofil wurde erfolgreich erstellt!'));
   header('Location: '. $base_url . '/akteurprofil/' . $this->akteur_id);

  } // END function akteurSpeichern()

  /**
   * Akteurinformationen aktualisieren in DB
   */
  private function akteurUpdaten() {

    // Wenn Bilddatei ausgewählt wurde...
    if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
     $this->bild = $this->upload_image($_FILES['bild']);
    } else if (isset($_POST['oldPic'])) {
     $this->bild = $this->clearContent($_POST['oldPic']);
    }

    if (!empty($this->removedTags) && is_array($this->removedTags)) {

     foreach($this->removedTags as $tag) {

      $tag = $this->clearContent($tag);

      db_delete($this->tbl_hat_sparte)
       ->condition('hat_KID', $tag)
       ->condition('hat_AID', $this->akteur_id)
       ->execute();

     }
    }

    $akteurAdresse = db_select($this->tbl_akteur, 'a')
     ->fields('a', array('adresse'))
     ->condition('AID', $this->akteur_id)
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
     ->condition('ADID', $akteurAdresse->adresse)
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
	   ->condition('AID', $this->akteur_id)
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

    $this->sparten = array_unique($this->sparten);

    foreach ($this->sparten as $sparte) {
  	// Tag bereits in DB?

    $sparte_id = '';
    $sparte = strtolower($this->clearContent($sparte));

  	$resultsparte = db_select($this->tbl_sparte, 's')
  	 ->fields('s')
  	 ->condition('KID', $sparte)
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
     ->condition('hat_KID', $sparte_id)
     ->condition('hat_AID', $this->akteur_id)
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

   $this->akteur->__setSingleAkteurVars($this->akteur->getAkteure(array('AID' => $this->akteur_id), 'complete')[0]);

   if (module_exists('aggregator')) {
    $this->rssFeed = aggregator_feed_load('aae-feed-'.$this->akteur_id);
   }

  } // END function akteurGetFields()


  /**
   * Darstellung des Formulars
   */
  private function akteurDisplay() {

    $this->allBezirke = db_select($this->tbl_bezirke, 'b')
     ->fields('b')
     ->execute()
     ->fetchAll();

    $this->allTags = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->execute()
     ->fetchAll();

    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme() . '/templates/akteurformular.tpl.php';
    return ob_get_clean(); // Übergabe des gerenderten "akteurformular.tpl"

  } // END function akteurDisplay()

} // END class akteurformular
