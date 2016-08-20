<?php

namespace Drupal\AaeData;

/*
*  Small wannabe-model class that delivers methods
*  for getting and manipulating festival-data.
*
*  To be used for only 1 festival at one time.
*   
*  @use use \Drupal\AaeData\festival()
*       $this->festival = new festival();
*/

Class festival extends aae_data_helper {

 public function __construct() {
  parent::__construct();
 }
 
 /*
  * Checks whether user has permission to add event
  * @return boolean
 */
 public function isAuthorized($aId, $uId){
     
  global $user;
  
  $resultUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u')
   ->condition('hat_AID', $aId)
   ->condition('hat_UID', $this->user_id)
   ->execute();

  if ($resultUser->rowCount() || !array_intersect(array('administrator'), $user->roles)) {
   return true;
  } else {
   return false;
  }
  
 }
 
 /*
  * @return Akteure-object, keyed by AID
  * @param $condition : array
  *
  */

 public function getFestival($conditions = NULL, $fields = 'normal', $orderBy = 'name') {
  # TODO
 }
 
 public function setAkteur($data){
  # TODO
 }
 
}