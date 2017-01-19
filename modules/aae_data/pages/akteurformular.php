<?php
/**
 * @file akteurformular.php
 *
 * Stellt ein Formular dar, in welches alle Informationen über
 * einen Akteur eingetragen & bearbeitet werden können.
 *
 * Einzige Pflichtfelder sind bisher Name, Email-adresse und Bezirk.
 *
 */
 
namespace Drupal\AaeData;

Class akteurformular extends akteure {

 var $target;

 function __construct($action) {
    
   parent::__construct();

   $explodedpath = explode('/', current_path());
   $this->akteur_id = $this->clearContent($explodedpath[1]);
   
   if (!user_is_logged_in() || ($action == 'update' && !$this->akteurExists($this->akteur_id))) {
    drupal_access_denied();
    drupal_exit();
   }

   // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
   if ($action == 'update') {
    $this->target = 'update';
   }

 }

  /**
   *  Routing behaviour
   *  @returns $profileHTML;
   */

  public function run() {

   if (isset($_POST['submit'])) {

	  if ($this->target == 'update') {
    
     if (!$this->isAuthorized($this->akteur_id)){
      drupal_access_denied();
      drupal_exit();
     }
     
	   $this->akteurUpdaten();
	  
    } else {
		 $this->akteurSpeichern();
	  }
    
   } else {

    // Load input-values via akteure-model
    if ($this->target == 'update') {

     if (!$this->isAuthorized($this->akteur_id)){

      drupal_access_denied();
      drupal_exit();

     } else {

	    # formerly: $this->akteurGetFields();
      $this->__setSingleAkteurVars(reset($this->getAkteure(array('AID' => $this->akteur_id), 'complete')));
      
      if (module_exists('aggregator')) {
       $this->rssFeed = aggregator_feed_load('aae-feed-'.$this->akteur_id);
      }
        
     }
    }

   }

   return $this->akteurDisplay();

  }

  /**
   * Write akteure data
   */
  private function akteurSpeichern() {

   $data = (object)$_POST;
   $data->adresse = (object)$_POST['adresse'];

   $this->akteur_id = $this->setUpdateAkteur($data);
   
   if (!is_array($this->akteur_id)){

    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Ihr Akteurprofil wurde erfolgreich erstellt!'));
    header('Location: '. $base_url . '/akteurprofil/' . $this->akteur_id);

   } else {
    
    # If not extending from akteure-class you would now write $this->fehler = $this->akteur_id;
    $this->tags = $this->tagsHelper->__getKategorieForTags($this->tags);
    $this->rssFeed = $this->clearContent($this->data->rssFeed);
    # $this->akteurDisplay() will be called after that

   }

  } // END function akteurSpeichern()

  /**
   * Update akteure data
   */
  private function akteurUpdaten() {
   
   $data = (object)$_POST;
   $data->adresse = (object)$_POST['adresse'];

   $this->akteur_id = $this->setUpdateAkteur($data, $this->akteur_id);

   if (!is_array($this->akteur_id)){

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Ihr Akteurprofil wurde erfolgreich bearbeitet!'));
   	header('Location: '. $base_url .'/akteurprofil/' . $this->akteur_id);

   } else {

    # If not extending from akteure-class you would now write $this->fehler = $this->akteur_id;
    $this->tags = $this->tagsHelper->__getKategorieForTags($this->tags);
    $this->rssFeed = $this->clearContent($this->data->rssFeed);
    # $this->akteurDisplay() will be called after that

   }

  } // END function akteurUpdaten()

  /**
   * Render akteurformular.tpl.php
   */
  private function akteurDisplay() {

    $this->allBezirke = db_select($this->tbl_bezirke, 'b')
     ->fields('b')
     ->execute()
     ->fetchAll();

    $this->allTags = $this->tagsHelper->getTags();

    return $this->render('/templates/akteurformular.tpl.php');
    
  } // END function akteurDisplay()

} // END class akteurformular
