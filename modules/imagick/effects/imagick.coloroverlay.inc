<?php

/**
 * Adds an overlay to a image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $layer
 *   The image that needs to be placed on top of the source image.
 * @param $x
 *   The horizontal offset of the overlay.
 * @param $y
 *   The vertical offset of the overlay.
 * @param $alpha
 *   The transparency of the overlay layer.
 */
function image_imagick_overlay(stdClass $image, stdClass $layer, $x, $y, $alpha = 100, $reverse = FALSE) {
  $path = drupal_realpath($layer->source);
  $overlay = new Imagick($path);
  if ($alpha != 100) {
    $overlay->setImageFormat('png');
    $overlay->setImageOpacity($alpha / 100);
  }
  return $image->resource->compositeImage($overlay, Imagick::COMPOSITE_DEFAULT, $x, $y);
}

/**
 * Implements hook_{toolkit}_{effect}()
 */
function image_imagick_coloroverlay(stdClass $image, $data = array()) {
  $color = $data['RGB']['HEX'];
  $color = empty($color) ? 'none' : $color;

  $overlay = new Imagick();
  $overlay->newImage($image->info['width'], $image->info['height'], new ImagickPixel($color));

  return $image->resource->compositeImage($overlay, Imagick::COMPOSITE_DEFAULT, 0, 0);
}

/**
 * Adds an overlay to an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $hex
 *   The color used to create the overlay.
 * @param $alpha
 *   The transparency of the overlay layer.
 */
function image_imagick_coloroverlay_alpha(stdClass $image, $color, $alpha) {
  $color = empty($color) ? 'none' : $color;

  $overlay = new Imagick();
  $overlay->newImage($image->info['width'], $image->info['height'], new ImagickPixel($color));
  $overlay->setImageFormat('png');
  $overlay->setImageOpacity($alpha / 100);

  return $image->resource->compositeImage($overlay, Imagick::COMPOSITE_DEFAULT, 0, 0);
}

/**
 * Implements the imagick color overlay with alpha effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_coloroverlay_alpha($image, $data = array()) {
  image_toolkit_invoke('coloroverlay_alpha', $image, $data);
}

/**
 * Settings form for the imagick color overlay with alpha effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_coloroverlay_alpha_form($data) {
  $data = array_merge(imagick_coloroverlay_defaults(), (array) $data);

  $form['HEX'] = array(
    '#type' => 'textfield',
    '#title' => t('HEX'),
    '#default_value' => $data['HEX'],
    '#size' => 7,
    '#colorpicker' => TRUE,
  );
  $form['alpha'] = array(
    '#type' => 'textfield',
    '#title' => t('Opacity'),
    '#description' => t('Opacity of the color overlay in percents.'),
    '#default_value' => $data['alpha'],
    '#size' => 3,
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_coloroverlay_defaults() {
  return array(
    'HEX' => '#FFFFFF',
    'alpha' => 50,
  );
}
