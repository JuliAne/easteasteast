<?php
global $user;
/*
function aae_data_form(){
	return drupal_get_form('aae_data_kontaktform');
}
*/
  /**
 * This function gets called in the browser address bar for: 
 * "http://yourhost/my_module/form" or 
 * "http://yourhost/?q=my_module/form". It will generate
 * a page with this form on it.
 * This function is called the "form builder". It builds the form.
 */
function aae_data_kontaktform($form_state) {
    
 $form['firstname'] = array(
    '#type' => 'textfield',
    '#title' => t('Vorname'),
    //'#required' => TRUE,
    '#description' => "Bitte geben Sie Ihren Vornamen ein.", // added
    '#size' => 20, // added
    '#maxlength' => 20, // added
  );
  $form['lastname'] = array(
    '#type' => 'textfield',
    '#title' => t('Nachname'),
    //'#required' => TRUE,
    '#description' => "Bitte geben Sie Ihren Nachnamen ein.", // added
    '#size' => 20, // added
    '#maxlength' => 20, // added
  );
  $form['email'] = array(
    '#type' => 'textfield',
    '#title' => t('Emailadresse'),
   // '#required' => TRUE,
    '#description' => "Bitte geben Sie Ihre Emailadresse an.",
    '#size' => 20,
    '#maxlength' => 20,
  );
  $form['subject'] = array(
    '#type' => 'textfield',
    '#title' => t('Betreff'),
   // '#required' => TRUE,
    '#default_value' => "Sonstiges",
    '#description' => "Bitte geben Sie einen Betreff an.",
    '#size' => 20,
    '#maxlength' => 20,
  );
  $form['text'] = array(
    '#type' => 'textarea',
    '#title' => t('Nachricht'),
 //   '#required' => TRUE,
    '#description' => "Bitte geben Sie Ihre Nachricht ein.",
  );

  // Adds a simple submit button that refreshes the form and clears its contents -- this is the default behavior for forms.
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
   // '#submit' => array('kontakt_form_submit'),
  );
  //$form['#submit'][] = 'aae_data_kontakt_form_submit';

  return $form;
}

/*
function aae_data_kontakt_form_alter(&$form, &$form_state, $form_id){
	$form['#submit'][] = 'aae_data_kontakt_form_submit';
}
*/
function aae_data_kontaktform_validate($form, &$form_state){
	
}
/**
 * Add a submit handler/function to the form.
 *
 * This will add a completion message to the screen when the
 * form successfully processes
 */
function aae_data_kontaktform_submit($form, &$form_state) {
	/*---
	// Send email with $body and $subject constructed here:
	  $body = $form_state['values']['text'];
	  $to = 'ruthvonborell@posteo.de';
	  $from = $form_state['values']['email'];
	  // If you are using suhosin (the hardened PHP extension) and want to use
	  // Cc or Bcc, then make sure that suhosin.mail.protect is not set to 2, 
	  // or the mail will be rejected as an attack.
	  $header_bcc = 'ruthvonborell@posteo.de';
	  $header_cc = 'ruthvonborell@posteo.de';
	  $subject = $form_state['values']['subject'];
	  // you can set 'message_key' to any key you want, and process 
	  // that in the hook_mail as shown in the commented out code.
	  // Or, just set it to some string and ignore it as we are here.
	  // You can add extra headers into the headers array here.  If you
	  // are trying to send an HTML formatted email, please use either
	  // the MIME Mail or HTMLMail modules, rather than doing it
	  // by just setting the Content-Type.
	  $message = drupal_mail('aae_data', 'message_key', $to, user_preferred_language($user), array('body' => $body, 'subject' => $subject, 'headers' => array('Cc' => $header_cc, 'Bcc' => $header_bcc)), $from, TRUE);
	  // You can check $message['result'] to see if the message sent successfully.
	----*/
	    $to = "ruthvonborell@posteo.de";
	    $subject = "Hi!";
	    $body = "Hi,\n\nHow are you?";
	    //smail($to, $subject, $body);
	    if (mail($to, $subject, $body)) {
	            drupal_goto("Akteure");
	    } else {
	            //echo("<p>Message delivery failed...</p>");
				drupal_goto("Events");
	    }
    //drupal_goto("Akteure");
  //drupal_set_message(t('Das Formular wurde abgeschickt.'));
}