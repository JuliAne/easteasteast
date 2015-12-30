<?php

/**
 * Adds a frame to an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $matte_color
 *   The string representing the matte color
 * @param $width
 *   The width of the border
 * @param $height
 *   The height of the border
 * @param $inner_bevel
 *   The inner bevel width
 * @param $outer_bevel
 *   The outer bevel width
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_frame(stdClass $image, $matte_color, $width, $height, $inner_bevel, $outer_bevel) {
  $color = empty($matte_color) ? 'none' : $matte_color;

  return $image->resource->frameImage(new ImagickPixel($color), $width, $height, $inner_bevel, $outer_bevel);
}

/**
 * Implements the imagick frame effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_frame($image, $data = array()) {
  image_toolkit_invoke('frame', $image, $data);
}

/**
 * Settings form for the imagick frame effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_frame_form($data) {
  $data = array_merge(imagick_frame_defaults(), (array) $data);

  $form['matte_color'] = array(
    '#type' => 'textfield',
    '#title' => t('Color of the shadow'),
    '#default_value' => $data['matte_color'],
    '#size' => 7,
    '#colorpicker' => TRUE,
  );
  $form['width'] = array(
    '#type' => 'textfield',
    '#title' => t('Width'),
    '#description' => t('The width of the frame'),
    '#default_value' => $data['width'],
    '#size' => 3,
  );
  $form['height'] = array(
    '#type' => 'textfield',
    '#title' => t('Height'),
    '#description' => t('The height of the frame'),
    '#default_value' => $data['height'],
    '#size' => 3,
  );
  $form['inner_bevel'] = array(
    '#type' => 'textfield',
    '#title' => t('Inner bevel'),
    '#description' => t('The inner bevel of the frame'),
    '#default_value' => $data['inner_bevel'],
    '#size' => 3,
  );
  $form['outer_bevel'] = array(
    '#type' => 'textfield',
    '#title' => t('Outer bevel'),
    '#description' => t('The outer bevel of the frame'),
    '#default_value' => $data['outer_bevel'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_frame_defaults() {
  return array(
    'matte_color' => '#707070',
    'width' => '20',
    'height' => '20',
    'inner_bevel' => '5',
    'outer_bevel' => '5',
  );
}
