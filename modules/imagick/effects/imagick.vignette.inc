<?php

/**
 * Adds vignette to an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $blackpoint
 *   The blackpoint of the shadow.
 * @param $whitepoint
 *   The whitepoint of the shadow.
 * @param $x
 *   The angle of the shadow.
 * @param $y
 *   The angle of the shadow.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_vignette(stdClass $image, $blackpoint, $whitepoint, $x, $y) {
  return $image->resource->vignetteImage($blackpoint, $whitepoint, $x, $y);
}

/**
 * Implements the imagick vignette effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_vignette($image, $data = array()) {
  image_toolkit_invoke('vignette', $image, $data);
}

/**
 * Settings form for the imagick vignette effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_vignette_form($data) {
  $data = array_merge(imagick_vignette_defaults(), (array) $data);

  $form['blackpoint'] = array(
    '#type' => 'textfield',
    '#title' => t('Blackpoint'),
    '#description' => t('The blackpoint of the vignette'),
    '#default_value' => $data['blackpoint'],
    '#size' => 3,
  );
  $form['whitepoint'] = array(
    '#type' => 'textfield',
    '#title' => t('Whitepoint'),
    '#description' => t('The whitepoint of the vignette'),
    '#default_value' => $data['whitepoint'],
    '#size' => 3,
  );
  $form['x'] = array(
    '#type' => 'textfield',
    '#title' => t('X'),
    '#description' => t('The X value of the vignette'),
    '#default_value' => $data['x'],
    '#size' => 3,
  );
  $form['y'] = array(
    '#type' => 'textfield',
    '#title' => t('Y'),
    '#description' => t('The Y value of the vignette'),
    '#default_value' => $data['y'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_vignette_defaults() {
  return array(
    'blackpoint' => '0.5',
    'whitepoint' => '0.5',
    'x' => '5',
    'y' => '5',
  );
}
