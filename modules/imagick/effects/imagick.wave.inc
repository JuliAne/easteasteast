<?php

/**
 * Waves an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $amplitude
 *   The amplitude of the wave effect.
 * @param $length
 *   The length of the wave effect.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_wave(stdClass $image, $amplitude, $length) {
  return $image->resource->waveImage($amplitude, $length);
}

/**
 * Implements the imagick wave effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_wave($image, $data = array()) {
  image_toolkit_invoke('wave', $image, $data);
}

/**
 * Settings form for the imagick wave effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_wave_form($data) {
  $data = array_merge(imagick_wave_defaults(), (array) $data);

  $form['amplitude'] = array(
    '#type' => 'textfield',
    '#title' => t('Amplitude'),
    '#description' => t('The amplitude of the wave effect.'),
    '#default_value' => $data['amplitude'],
    '#size' => 3,
  );
  $form['length'] = array(
    '#type' => 'textfield',
    '#title' => t('Length'),
    '#description' => t('The length of the wave effect.'),
    '#default_value' => $data['length'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_wave_defaults() {
  return array(
    'amplitude' => '8',
    'length' => '64',
  );
}
