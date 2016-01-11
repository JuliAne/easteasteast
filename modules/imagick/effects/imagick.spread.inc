<?php

/**
 * Adds spread to an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $radius
 *   The radius of spread effect.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_spread(stdClass $image, $radius) {
  return $image->resource->spreadImage($radius);
}

/**
 * Implements the imagick spread effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_spread($image, $data = array()) {
  image_toolkit_invoke('spread', $image, $data);
}

/**
 * Settings form for the imagick spread effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_spread_form($data) {
  $data = array_merge(imagick_spread_defaults(), (array) $data);

  $form['radius'] = array(
    '#type' => 'textfield',
    '#title' => t('Radius'),
    '#description' => t('The spread radius, in pixels.'),
    '#default_value' => $data['radius'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_spread_defaults() {
  return array(
    'radius' => 10,
  );
}
