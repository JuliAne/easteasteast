<?php

/**
 * Adds rounded corners to an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $x_rounding
 *   x rounding
 * @param $y_rounding
 *   y rounding
 * @param $stroke_width
 *   stroke width
 * @param $displace
 *   image displace
 * @param $size_correction
 *   size correction
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_roundedcorners(stdClass $image, $x_rounding, $y_rounding, $stroke_width, $displace, $size_correction) {
  return $image->resource->roundCorners($x_rounding, $y_rounding, $stroke_width, $displace, $size_correction);
}

/**
 * Implements the imagick rounded corners effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_roundedcorners($image, $data = array()) {
  image_toolkit_invoke('roundedcorners', $image, $data);
}

/**
 * Settings form for the imagick rounded corners effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_roundedcorners_form($data) {
  $data = array_merge(imagick_roundedcorners_defaults(), (array) $data);

  $form['x_rounding'] = array(
    '#type' => 'textfield',
    '#title' => t('X rounding'),
    '#description' => t('The x rounding of the rounded corners'),
    '#default_value' => $data['x_rounding'],
    '#size' => 3,
  );
  $form['y_rounding'] = array(
    '#type' => 'textfield',
    '#title' => t('Y rounding'),
    '#description' => t('The y rounding of the rounded corners'),
    '#default_value' => $data['y_rounding'],
    '#size' => 3,
  );
  $form['stroke_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Stroke width'),
    '#description' => t('The stroke width of the rounded corners'),
    '#default_value' => $data['stroke_width'],
    '#size' => 3,
  );
  $form['displace'] = array(
    '#type' => 'textfield',
    '#title' => t('Displace'),
    '#description' => t('The displace of the rounded corners'),
    '#default_value' => $data['displace'],
    '#size' => 3,
  );
  $form['size_correction'] = array(
    '#type' => 'textfield',
    '#title' => t('Size correction'),
    '#description' => t('The size correction of the rounded corners'),
    '#default_value' => $data['size_correction'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_roundedcorners_defaults() {
  return array(
    'x_rounding' => '50',
    'y_rounding' => '50',
    'stroke_width' => '10',
    'displace' => '5',
    'size_correction' => '-6',
  );
}
