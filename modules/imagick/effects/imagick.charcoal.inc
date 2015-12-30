<?php

/**
 * Charcoals an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $radius
 *   The radius of the charcoal effect.
 * @param $sigma
 *   The sigma of the charcoal effect.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_charcoal(stdClass $image, $radius, $sigma) {
  return $image->resource->charcoalImage($radius, $sigma);
}

/**
 * Implements the imagick charcoal effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_charcoal($image, $data = array()) {
  image_toolkit_invoke('charcoal', $image, $data);
}

/**
 * Settings form for the imagick charcoal effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_charcoal_form($data) {
  $data = array_merge(imagick_charcoal_defaults(), (array) $data);

  $form['radius'] = array(
    '#type' => 'textfield',
    '#title' => t('Radius'),
    '#description' => t('The radius of the charcoal effect.'),
    '#default_value' => $data['radius'],
    '#size' => 3,
  );
  $form['sigma'] = array(
    '#type' => 'textfield',
    '#title' => t('Sigma'),
    '#description' => t('The sigma of the charcoal effect.'),
    '#default_value' => $data['sigma'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_charcoal_defaults() {
  return array(
    'radius' => '16',
    'sigma' => '16',
  );
}
