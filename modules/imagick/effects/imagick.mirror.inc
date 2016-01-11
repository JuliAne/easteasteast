<?php

/**
 * Mirrors the image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $type
 *   The type of noise.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_mirror(stdClass $image, $flip, $flop) {
  if ($flip) {
    $image->resource->flipImage();
  }
  if ($flop) {
    $image->resource->flopImage();
  }
  return $image;
}

/**
 * Implements the imagick flip/flop effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_mirror($image, $data = array()) {
  image_toolkit_invoke('mirror', $image, $data);
}

/**
 * Settings form for the imagick flip/flop effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_mirror_form($data) {
  $data = array_merge(imagick_mirror_defaults(), (array) $data);

  $form['flip'] = array(
    '#type' => 'checkbox',
    '#title' => t('Mirror image verticaly'),
    '#default_value' => $data['flip'],
  );
  $form['flop'] = array(
    '#type' => 'checkbox',
    '#title' => t('Mirror image horizontaly'),
    '#default_value' => $data['flop'],
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_mirror_defaults() {
  return array(
    'flip' => 0,
    'flop' => 0,
  );
}
