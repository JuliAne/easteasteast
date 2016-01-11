<?php

/**
 * Encipher the image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $password
 *   The password to encrypt the image with.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_encipher(stdClass $image, $password) {
  $image->resource->encipherImage($password);

  return $image;
}

/**
 * Implements the imagick encipher effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_encipher($image, $data = array()) {
  image_toolkit_invoke('encipher', $image, $data);
}

/**
 * Settings form for the imagick encipher effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_encipher_form($data) {
  $data = array_merge(imagick_encipher_defaults(), (array) $data);

  $form['password'] = array(
    '#type' => 'textfield',
    '#title' => t('Password'),
    '#description' => t('Password to encrypt the image'),
    '#default_value' => $data['password'],
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_encipher_defaults() {
  return array(
    'password' => '',
  );
}
