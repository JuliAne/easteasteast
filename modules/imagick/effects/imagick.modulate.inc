<?php

/**
 * Modulates the image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $type
 *   The type of noise.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_modulate(stdClass $image, $brightness, $saturation, $hue) {
  return $image->resource->modulateImage($brightness, $saturation, $hue);
}

/**
 * Implements the imagick modulate effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_modulate($image, $data = array()) {
  image_toolkit_invoke('modulate', $image, $data);
}

/**
 * Settings form for the imagick modulate effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_modulate_form($data) {
  $data = array_merge(imagick_modulate_defaults(), (array) $data);

  $form['brightness'] = array(
    '#type' => 'textfield',
    '#title' => t('Brightness in percentage'),
    '#default_value' => $data['brightness'],
    '#size' => 3,
  );
  $form['saturation'] = array(
    '#type' => 'textfield',
    '#title' => t('Saturation in percentage'),
    '#default_value' => $data['saturation'],
    '#size' => 3,
  );
  $form['hue'] = array(
    '#type' => 'textfield',
    '#title' => t('Hue in percentage'),
    '#default_value' => $data['hue'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_modulate_defaults() {
  return array(
    'brightness' => 150,
    'saturation' => 150,
    'hue' => 150,
  );
}
