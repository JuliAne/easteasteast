<?php

/**
 * Adds edge to an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $radius
 *   The radius of edge effect.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_edge(stdClass $image, $radius) {
  return $image->resource->edgeImage($radius);
}

/**
 * Implements the imagick edge effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_edge($image, $data = array()) {
  image_toolkit_invoke('edge', $image, $data);
}

/**
 * Settings form for the imagick edge effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_edge_form($data) {
  $data = array_merge(imagick_edge_defaults(), (array) $data);

  $form['radius'] = array(
    '#type' => 'textfield',
    '#title' => t('Radius'),
    '#description' => t('The edge radius, in pixels.'),
    '#default_value' => $data['radius'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_edge_defaults() {
  return array(
    'radius' => 10,
  );
}
