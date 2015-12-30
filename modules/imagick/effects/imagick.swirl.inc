<?php

/**
 * Swirls an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $degrees
 *   The amplitude of the wave effect.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_swirl(stdClass $image, $degrees) {
  return $image->resource->swirlImage($degrees);
}

/**
 * Implements the imagick swirl effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_swirl($image, $data = array()) {
  image_toolkit_invoke('swirl', $image, $data);
}

/**
 * Settings form for the imagick swirl effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_swirl_form($data) {
  $data = array_merge(imagick_swirl_defaults(), (array) $data);

  $form['degrees'] = array(
    '#type' => 'textfield',
    '#title' => t('Degrees'),
    '#description' => t('The number of degrees of the swirl effect.'),
    '#default_value' => $data['degrees'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_swirl_defaults() {
  return array(
    'degrees' => '90',
  );
}
