<?php

/*
 * Builds and returns the form-elements for AAE-Admin-interface

 Tab Einstellungen
  - Standardbildpfad

 Tab SEO/Content - Beschreibung, description: Standard, Akteure, Events



 */

function build_admin_form(){

 $form = array();

// This is to specify the vertical tab holder.
// One page can have two vertical tabs, but it's always one main tab with sub tabs.
$form['contactform'] = array(
  '#type' => 'vertical_tabs',
);

// This is the field fieldset.
$form['contactformset'] = array(
  '#type' => 'fieldset',
  '#title' => t('Attention, new comment posted'),
  '#collapsible' => TRUE,
  '#description' => t('Edit the email body for the new comment posted on the site.'),
  '#group' => 'contactform'
);

// Adding a simple textarea.
$form['contactformset']['mailbody'] = array(
  '#type' => 'textarea',
  '#required' => TRUE,
  '#default_value' => 'Default',
  '#title' => t('Contact mail.'),
  '#description' => t('Body text when a new comment is posted by users.'),
  '#group' => 'contactform'
);

// This is second fieldset, this will make the two sections under the vertical tab.
$form['settingformset'] = array(
  '#type' => 'fieldset',
  '#title' => t('Site wide settings'),
  '#collapsible' => TRUE,
  '#description' => t('Set different settings which will effect site wide.'),
  '#group' => 'contactform'
);


// Replicating the above textarea, this is just for demo.
$form['settingformset']['setting'] = array(
  '#type' => 'textarea',
  '#required' => TRUE,
  '#default_value' => 'Default',
  '#title' => t('Contact mail.'),
  '#description' => t('Body text when a new comment is posted by users.'),
  '#group' => 'contactform'
);

 $form['#submit'][] = 'adminform_contact_form_submit'; //Submit button call back.

 return $form;
}
