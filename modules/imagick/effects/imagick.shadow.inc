<?php

/**
 * Genrates a shadow around an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $color
 *   The color of the shadow.
 * @param $opacity
 *   The opacity of the shadow.
 * @param $sigma
 *   The sigma of the shadow.
 * @param $x
 *   The angle of the shadow.
 * @param $y
 *   The angle of the shadow.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_shadow(stdClass $image, $color, $opacity, $sigma, $x, $y) {
  $color = empty($color) ? 'none' : $color;

  $shadow = clone $image->resource;
  $shadow->setImageBackgroundColor(new ImagickPixel($color));
  $shadow->shadowImage($opacity, $sigma, $x, $y);
  $shadow->compositeImage($image->resource, Imagick::COMPOSITE_OVER, $x + ($sigma * 2), $y + ($sigma * 2));

  $image->resource = $shadow;
  return $image->resource;
}

/**
 * Implements the imagick shadow effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_shadow($image, $data = array()) {
  image_toolkit_invoke('shadow', $image, $data);
}

/**
 * Settings form for the imagick shadow effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_shadow_form($data) {
  $data = array_merge(imagick_shadow_defaults(), (array) $data);

  $form['color'] = array(
    '#type' => 'textfield',
    '#title' => t('Color of the shadow'),
    '#default_value' => $data['color'],
    '#size' => 7,
    '#colorpicker' => TRUE,
  );
  $form['opacity'] = array(
    '#type' => 'textfield',
    '#title' => t('Opacity'),
    '#description' => t('The opacity of the shadow'),
    '#default_value' => $data['opacity'],
    '#size' => 3,
  );
  $form['sigma'] = array(
    '#type' => 'textfield',
    '#title' => t('Sigma'),
    '#description' => t('The sigma of the shadow'),
    '#default_value' => $data['sigma'],
    '#size' => 3,
  );
  $form['x'] = array(
    '#type' => 'textfield',
    '#title' => t('X'),
    '#description' => t('The X value of the shadow'),
    '#default_value' => $data['x'],
    '#size' => 3,
  );
  $form['y'] = array(
    '#type' => 'textfield',
    '#title' => t('Y'),
    '#description' => t('The Y value of the shadow'),
    '#default_value' => $data['y'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_shadow_defaults() {
  return array(
    'color' => '#454545',
    'opacity' => '100',
    'sigma' => '25',
    'x' => '0',
    'y' => '0',
  );
}
