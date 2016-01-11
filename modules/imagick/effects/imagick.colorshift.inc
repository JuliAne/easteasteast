<?php

/**
 * Colorshifts an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $data
 *   Array of data passed through by the form.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_colorshift(stdClass $image, $color) {
  $color = empty($color) ? 'none' : $color;

  return $image->resource->colorizeImage(new ImagickPixel($color), 1);
}

/**
 * Implements the imagick colorshift effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_colorshift($image, $data = array()) {
  image_toolkit_invoke('colorshift', $image, $data);
}

/**
 * Settings form for the imagick colorshift effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_colorshift_form($data) {
  $data = array_merge(imagick_colorshift_defaults(), (array) $data);

  $form['HEX'] = array(
    '#type' => 'textfield',
    '#title' => t('HEX'),
    '#default_value' => $data['HEX'],
    '#size' => 7,
    '#colorpicker' => TRUE,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_colorshift_defaults() {
  return array(
    'HEX' => '#FF2E2E',
  );
}
