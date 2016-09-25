<?php

namespace Drupal\AaeData;

/*
*  Small wannabe-model class that delivers methods
*  for getting, setting and manipulating akteure-data.
*   
*  @use use \Drupal\AaeData\akteure()
*       $this->akteure = new akteure();
*
* TODO: Check for unrequired vars
*/

Class akteure extends aae_data_helper {

 // $tbl_akteur
 var $name = '';
 var $adresse = '';
 var $email = '';
 var $telefon = '';
 var $url = '';
 var $ansprechpartner = '';
 var $funktion = '';
 var $bild = '';
 var $beschreibung = '';
 var $oeffnungszeiten = '';
 var $barrierefrei = '';
 var $created = '';
 var $modified = '';

 // $tbl_tag
 var $tags = '';

 var $akteur_id = '';
 var $fehler = array();

 // $tbl_akteur_hat_sparte
 var $countSparten = '';
 var $sparte_id = '';

 var $resultBezirke = '';
 var $target = '';
 var $removedTags;
 var $removedPic;
 var $rssFeed = '';

 public function __construct() {
  parent::__construct();
 }
 
 /*
  * Checks whether user owns akteur
  * @return boolean
 */
 public function isAuthorized($aId, $uId = NULL){

  global $user;

  $uId = (empty($uId) ? $this->user_id : $uId);
  
  $resultUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u')
   ->condition('hat_AID', $aId)
   ->condition('hat_UID', $uId)
   ->execute();
   
  if ($resultUser->rowCount() || in_array('administrator', $user->roles)) {
   return true;
  } else {
   return false;
  }
  
 }

 public function akteurExists($aId){

  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('AID'))
   ->condition('AID', $aId)
   ->execute();

  return $resultAkteur->rowCount();

 }
 
 /*
  * @return Akteure-object, keyed by AID
  * @param $condition : array : see akteurepage.php for examples
  * @param $fields : integer : ULTRAMINIMAL (=EID & name)
                               MINIMAL output (=preview-mode)
  *                            NORMAL output
  *
  */
 public function getAkteure($conditions = NULL, $fields = 'normal', $orderBy = 'name') {
  
  $akteure = db_select($this->tbl_akteur, 'a');
  
  if ($fields == 'ultraminimal'){
   $akteure->fields('a', array('AID', 'name'));
  } elseif ($fields == 'minimal'){
   $akteure->fields('a', array('AID','name','beschreibung','bild','adresse'));
  } else {
   $akteure->fields('a');
  }

  foreach ($conditions as $key => $condition){
   
   switch ($key) {
    
    case ('range') :
     $akteure->range($condition['start'], $condition['end']);
    break;

    case('filter') :
     if (!empty($conditions['filter']))
       $akteure->condition('AID', $this->__filterAkteure($conditions['filter']));
    break;

    default :
     $akteure->condition($key, $condition);
    break;

   }

  }

  $akteure->orderBy('created', DESC)
          ->orderBy('name', ASC); #TODO: make dynamic

  $resultAkteure = $akteure->execute()->fetchAllAssoc('AID');

  foreach ($resultAkteure as $counter => $akteur){

   $numwords = 30;
   preg_match("/(\S+\s*){0,$numwords}/", $akteur->beschreibung, $regs);

   $adresse = db_select($this->tbl_adresse, 'ad');
   
   if ($fields == 'complete'){
     $adresse->fields('ad');
   } else {
     $adresse->fields('ad', array('bezirk','gps_lat','gps_long'));
   }

   $adresse = $adresse->condition('ADID', $akteur->adresse)->execute()->fetchObject();

   $bezirk = db_select($this->tbl_bezirke, 'b')
    ->fields('b')
    ->condition('BID', $adresse->bezirk)
    ->execute()
    ->fetchObject();

   // Hack: add variable to $resultAkteure-object
   # TODO: Put bezirk + gps into adress-object?!
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['adresse'] = $adresse;
   $resultAkteure[$counter]['bezirk'] = $bezirk;
   $resultAkteure[$counter]['gps'] = ($adresse->gps_lat != 'Ermittle Geo-Koordinaten...' && !empty($adresse->gps_lat) ? $adresse->gps_lat.','.$adresse->gps_long : '');
   $resultAkteure[$counter]['kurzbeschreibung'] = trim(strip_tags($regs[0],'<p>'));

   if ($fields == 'complete'){

    // get Tags
    $resultTags = db_select($this->tbl_hat_sparte, 'ht')
     ->fields('ht')
     ->condition('hat_AID', $akteur->AID)
     ->execute()
     ->fetchAll();

    $tags = array();

    foreach($resultTags as $tag) {

     $tags[] = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->condition('KID', $tag->hat_KID)
     ->execute()
     ->fetchAll();

    }

    $resultAkteure[$counter]['tags'] = $tags;
    
   }

   $resultAkteure[$counter] = (object)$resultAkteure[$counter];

  }

  return $resultAkteure;

 }

 protected function __setSingleAkteurVars($data){
   
  # print_r($data);
  # exit();

  $this->name = $this->clearContent($data->name);
  $this->email = $this->clearContent($data->email);
  $this->telefon = $this->clearContent($data->telefon);
  $this->url = $this->clearContent($data->url);
  $this->ansprechpartner = $this->clearContent($data->ansprechpartner);
  $this->funktion = $this->clearContent($data->funktion);
  if (isset($data->bild)) $this->bild = $data->bild;
  $this->beschreibung = $this->clearContent($data->beschreibung);
  $this->oeffnungszeiten = $this->clearContent($data->oeffnungszeiten);
  $this->adresse = $this->clearContent($data->adresse);
  $this->bezirk = $this->clearContent($data->bezirk);
  $this->gps = $this->clearContent($data->gps);
  $this->tags = $data->tags;
  $this->removedTags = $data->removedTags;
  $this->removedPic = $data->removeCurrentPic;
  $this->rssFeed = $this->clearContent($data->rssFeed);
  $this->created = new \DateTime($row->created);
  $this->modified = new \DateTime($row->modified);
  $this->adresse = $data->adresse;

 }
 
 public function setUpdateAkteur($data, $defaultAID = NULL){

 # print_r($data); exit();
  
  $this->__setSingleAkteurVars($data);

  // Validate input's, throw exception if necessary

  if (empty($this->name)) {
   $this->fehler['name'] = t('Bitte einen Organisationsnamen eingeben!');
  }

  if (empty($this->email) || !valid_email_address($this->email)) {
   $this->fehler['email'] = t('Bitte eine (gültige) Emailadresse eingeben!');
  }

  if (empty($this->adresse->bezirk)) {
   $this->fehler['ort'] = t('Bitte einen Bezirk auswählen!');
  }

  if (strlen($this->name) > 64) {
   $this->fehler['name'] = t('Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.');
  }

  if (strlen($this->email) > 100) {
	 $this->fehler['email'] = t('Bitte geben Sie eine kürzere Emailadresse an.');
  }

  if (strlen($this->telefon) > 100) {
 	 $this->fehler['telefon'] = t('Bitte geben Sie eine kürzere Telefonnummer an.');
  }

  if (strlen($this->url) > 100) {
	 $this->fehler['url'] = t('Bitte geben Sie eine kürzere URL an.');
  }

  if (!empty($this->url) && preg_match('/\A(http:\/\/|https:\/\/)(\w*[.|-]\w*)*\w+\.[a-z]{2,3}(\/.*)*\z/',$this->url)==0) {
   $this->fehler['url'] = t('Bitte eine gültige URL zur Akteurswebseite eingeben! (z.B. <i>http://meinakteur.de</i>)');
  }

  if (strlen($this->ansprechpartner) > 100){
	 $this->fehler['ansprechpartner'] = t('Bitte geben Sie einen kürzeren Ansprechpartner an.');
  }

  if (strlen($this->funktion) > 100) {
	 $this->fehler['funktion'] = t('Bitte geben Sie eine kürzere Funktion an.');
  }

  if (strlen($this->beschreibung) > 65000) {
	 $this->fehler['beschreibung'] = t('Bitte geben Sie eine kürzere Beschreibung an.');
  }

  if (strlen($this->oeffnungszeiten) > 200) {
	 $this->fehler['oeffnungszeiten'] = t('Bitte geben Sie kürzere Öffnungszeiten an.');
  }

  if (strlen($this->adresse->strasse) > 100) {
 	 $this->fehler['strasse'] = t('Bitte geben Sie einen kürzeren Strassennamen an.');
  }

  if (strlen($this->adresse->nr) > 100) {
	 $this->fehler['nr'] = t('Bitte geben Sie eine kürzere Hausnummer an.');
  }

  if (strlen($this->adresse->adresszusatz) > 100) {
	 $this->fehler['adresszusatz'] = t('Bitte geben Sie einen kürzeren Adresszusatz an.');
  }

  if (strlen($this->adresse->plz) > 100) {
	 $this->fehler['plz'] = t('Bitte geben Sie eine kürzere PLZ an.');
  }

  if (strlen($this->adresse->gps) > 100) {
   $this->fehler['gps'] = t('Bitte geben Sie kürzere GPS-Daten an.');
  }

  if (strlen($this->rssFeed) > 400 || preg_match('/\A(http:\/\/|https:\/\/)(\w*[.|-]\w*)*\w+\.[a-z]{2,3}(\/.*)*\z/',$this->rssFeed) == 0) {
   # $this->fehler['rssFeed'] = t('Die URL zum RSS-Feed ist zu lang oder ungültig...');
  }

  if ($this->gps == 'Ermittle Geo-Koordinaten...') $this->gps = '';

  if (false) {
    print_r($this->fehler);
   throw new \Exception(/*$this->fehler*/'bla');
  }

  // INSERT- or UPDATE-Action:

  // Wenn Bilddatei ausgewählt wurde...
  if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
   $this->bild = $this->upload_image($_FILES['bild']);
  } else if (isset($_POST['oldPic'])) {
   $this->bild = $this->clearContent($_POST['oldPic']);
  }

  if ($defaultAID){ // = Prepare UPDATE-Action

   $akteurAdress = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('adresse'))
   ->condition('AID', $defaultAID)
   ->execute()
   ->fetchObject();

   $akteurAdress = array('ADID', $akteurAdress->adresse);

   if (!empty($this->removedTags) && is_array($this->removedTags)) {

    foreach ($this->removedTags as $tag) {

      $tag = $this->clearContent($tag);

      // TODO: Put into public function $this->removeTag($tagID)
      //       & remove tag itself if completely unused
      db_delete($this->tbl_hat_sparte)
       ->condition('hat_KID', $tag)
       ->condition('hat_AID', $defaultAID)
       ->execute();

    }
   }

   // remove current picture manually
   if (!empty($this->removedPic)) {

    $b = end(explode('/', $this->removedPic));

    if (file_exists($this->short_bildpfad.$b)) {
     unlink($this->short_bildpfad.$b);
    }

    if ($_POST['oldPic'] == $this->removedPic) $this->bild = '';

   }
  }

  $gps = explode(',', $this->adresse->gps, 2);

  $this->adresse = $this->__db_action($this->tbl_adresse, array(
	 'strasse' => $this->adresse->strasse,
	 'nr' => $this->adresse->nr,
	 'adresszusatz' => $this->adresse->adresszusatz,
	 'plz' => $this->adresse->plz,
	 'bezirk' => $this->adresse->bezirk, # = BID
	 'gps_lat' => $gps[0],
   'gps_long' => $gps[1]
  ), $akteurAdress);

  $this->akteur_id = $this->__db_action($this->tbl_akteur, array(
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
	 ), ($defaultAID ? array('AID', $defaultAID) : NULL), true);
    
   # TODO: if not private...
   $userHasAkteur = $this->__db_action($this->tbl_hat_user, array(
	  'hat_UID' => $this->user_id,
	  'hat_AID' => $this->akteur_id
   ));
   # END TODO

   if (module_exists('aggregator')){
     
    if (empty($defaultAID) && !empty($this->rssFeed)) {
      
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

     } else if (!empty($defaultAID)) {

      $akteurFeed = db_select('aggregator_feed', 'af')
       ->fields('af', array('fid','url'))
       ->condition('title', 'aae-feed-'.$this->akteur_id)
       ->execute();

      $hasFeed = $akteurFeed->rowCount();
      $akteurFeed = $akteurFeed->fetchObject();

      if (!empty($this->rssFeed) && $hasFeed){

       // UPDATE-ACTION: Rewrite RSS-path of Feed
       $feedUpdate = db_update('aggregator_feed')
        ->fields(array('url' => $this->rssFeed))
        ->condition('title', 'aae-feed-'.$this->akteur_id)
        ->execute();

       // Trunk all current feed items
       db_delete('aggregator_item')
        ->condition('fid', $hasFeed);

      } else if (!empty($this->rssFeed) && !$hasFeed) {
      
       // INSERT-ACTION:

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

     // REMOVE-ACTION

     db_delete('aggregator_feed')
      ->condition('fid', $akteurFeed->fid)
      ->execute();
     db_delete('aggregator_item')
      ->condition('fid', $akteurFeed->fid)
      ->execute();

    }

   } // END IF update_mode
  } // END IF module_exists('aggregator')

  // Update or insert Tags

  if (is_array($this->tags) && !empty($this->tags)) {

   $this->tags = array_unique($this->tags);

   foreach ($this->tags as $tag) {

    $tagId = '';
    $tag = strtolower($this->clearContent($tag));

  	$resultTag = db_select($this->tbl_sparte, 's')
  	 ->fields('s')
  	 ->condition('KID', $tag)
  	 ->execute();

    // Tag already existing?...
    if ($resultTag->rowCount() == 0) {

     // ...Nope!
     $tagId = db_insert($this->tbl_sparte)
  	   ->fields(array('kategorie' => $sparte))
  	 	 ->execute();

  	} else {
     
     // ...YIP!
  	 foreach ($resultSparte as $row) {
  	   $tagId = $row->KID;
  	 }

  	}
    
    if (!empty($defaultAID)) {

     // Hat der Akteur dieses Tag bereits zugeteilt?
     $hatAkteurTag = db_select($this->tbl_hat_sparte, 'hs')
      ->fields('hs')
      ->condition('hat_KID', $tagId)
      ->condition('hat_AID', $this->akteur_id)
      ->execute();

     if ($hatAkteurTag->rowCount() == 0) {

      db_insert($this->tbl_hat_sparte)
      ->fields(array(
       'hat_AID' => $this->akteur_id,
       'hat_KID' => $tagId
       ))
      ->execute();

     }
    } // END IF update_mode
   }
  }

  // Finished, getSetAkteur-function, call hooks
  if (empty($defaultAID)) {
   module_invoke_all('hook_akteur_created');
  } else {
   module_invoke_all('hook_akteur_modified');
  }

  return $this->akteur_id;

 }
 
 /* TODO: Use native events-model-functions! */
 public function removeAkteur($aId){
     
  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('name','bild'))
   ->condition('AID', $aId)
   ->execute()
   ->fetchObject();
  
  $resultEvents = db_select($this->tbl_akteur_events, 'ae')
   ->fields('ae')
   ->condition('AID', $aId)
   ->execute()
   ->fetchAll();

  foreach ($resultEvents as $event){
   db_delete($this->tbl_event)
   ->condition('EID', $event->EID)
   ->execute();
  }

  db_delete($this->tbl_akteur_events)
  ->condition('AID', $aId)
  ->execute();

  db_delete($this->tbl_hat_user)
  ->condition('hat_AID', $aId)
  ->execute();

  db_delete($this->tbl_akteur)
  ->condition('AID', $aId)
  ->execute();

  db_delete($this->tbl_hat_sparte)
  ->condition('hat_AID', $aId)
  ->execute();

  // remove profile-image (if possible)
  $bild = end(explode('/', $resultAkteur->bild));

  if (file_exists($this->short_bildpfad.$bild)) {
   unlink($this->short_bildpfad.$bild);
  }

  menu_link_delete(NULL, 'akteurprofil/'.$aId);
  
 }
 
 private function __filterAkteure($filter){
  
  $filteredAkteurIds = array();
  $numFilter = 0;
  $filteredTags = array();
  $filteredBezirke = array();

  if (isset($filter['uid'])){
   // TODO: Make filterable for multiple users & test

   $numFilters++;
   global $user;

   $akteure = db_select($this->tbl_hat_user, 'hu')
    ->fields('hu');

   if (!array_intersect(array('administrator'), $user->roles)) {
    $akteure->condition('hat_UID', $filter['uid']);
   }
   
   foreach ($akteure->execute()->fetchAll() as $akteur){
    $filteredAkteurIds[] = $akteur->hat_AID;
   }

  } // end UserID-Filter
  
  if (isset($filter['tags'])){

   $sparten = db_select($this->tbl_hat_sparte, 'hs')
    ->fields('hs', array('hat_AID'));

   $and = db_and();

   foreach ($filter['tags'] as $tag) {

    $numFilters++;
    $tag = $this->clearContent($tag);
    $filteredTags[$tag] = $tag;
    $and->condition('hat_KID', $tag);

   }

   $filterSparten = $sparten->condition($and)
    ->execute()
    ->fetchAll();

   foreach ($filterSparten as $sparte){
    $filteredAkteurIds[] = $sparte->hat_AID;
   }
   
  } // end Tags-Filter

  if (isset($filter['bezirke'])){

   foreach ($filter['bezirke'] as $bezirk) {

    $numFilters++;
    $bezirkId = $this->clearContent($bezirk);
    $filteredBezirke[$bezirkId] = $bezirkId;

    $adressen = db_select($this->tbl_adresse, 'a')
     ->fields('a', array('ADID'))
     ->condition('bezirk', $bezirkId)
     ->execute()
     ->fetchAll();

    foreach ($adressen as $adresse) {

     $filterBezirke = db_select($this->tbl_akteur, 'a')
      ->fields('a', array('AID'))
      ->condition('adresse', $adresse->ADID)
      ->execute()
      ->fetchAll();

     foreach ($filterBezirke as $bezirk) {
      $filteredAkteurIds[] = $bezirk->AID;
     }
    }
   }
  } // end Bezirke-Filter

  if (isset($filter['keyword'])) {

   $numFilters++;

   $or = db_or()
    ->condition('name', '%'.$filter['keyword'].'%', 'LIKE')
    ->condition('beschreibung', '%'.$filter['keyword'].'%', 'LIKE');

   $filterKeyword = db_select($this->tbl_akteur, 'e')
    ->fields('e', array('AID'))
    ->condition($or)
    ->execute()
    ->fetchAll();

   foreach ($filterKeyword as $keyword){
    $filteredAkteurIds[] = $keyword->AID;
   }
  } // end Keyword-Filter
  
  return $this->getDuplicates($filteredAkteurIds, $numFilters);
   
 }
 
}