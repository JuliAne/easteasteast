<?php

// All possible blur types
define('NORMAL_BLUR', 0);
define('ADAPTIVE_BLUR', 1);
define('GAUSSIAN_BLUR', 2);
define('MOTION_BLUR', 3);
define('RADIAL_BLUR', 4);

/**
 * Blurs an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $type
 *   The type of blur.
 * @param $radius
 *   The radius of the blur.
 * @param $sigma
 *   The sigma of the blur.
 * @param $angle
 *   The angle of the blur.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_blur(stdClass $image, $type, $radius, $sigma, $angle) {
  switch ($type) {
    case NORMAL_BLUR:
      return $image->resource->blurImage($radius, $sigma);
      break;
    case ADAPTIVE_BLUR:
      return $image->resource->adaptiveBlurImage($radius, $sigma);
      break;
    case GAUSSIAN_BLUR:
      return $image->resource->gaussianBlurImage($radius, $sigma);
      break;
    case MOTION_BLUR:
      return $image->resource->motionBlurImage($radius, $sigma, $angle);
      break;
    case RADIAL_BLUR:
      return $image->resource->radialBlurImage($angle);
      break;
  }
}

/**
 * Implements the imagick bur effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_blur($image, $data = array()) {
  image_toolkit_invoke('blur', $image, $data);
}

/**
 * Settings form for the imagick blur effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_blur_form($data) {
  $data = array_merge(imagick_blur_defaults(), (array) $data);

  $form['type'] = array(
    '#type' => 'select',
    '#title' => t('Blur type'),
    '#options' => array(
      NORMAL_BLUR => t('Normal'),
      ADAPTIVE_BLUR => t('Adaptive'),
      GAUSSIAN_BLUR => t('Gaussian'),
      MOTION_BLUR => t('Motion'),
      RADIAL_BLUR => t('Radial'),
    ),
    '#default_value' => $data['type'],
  );
  $form['radius'] = array(
    '#type' => 'textfield',
    '#title' => t('Radius'),
    '#description' => t('The radius of the Gaussian, in pixels, not counting the center pixel.'),
    '#default_value' => $data['radius'],
    '#size' => 3,
    '#states' => array(
      'invisible' => array(
        ':input[name="data[type]"]' => array(
          'value' => RADIAL_BLUR,
        ),
      ),
    ),
  );
  $form['sigma'] = array(
    '#type' => 'textfield',
    '#title' => t('Sigma'),
    '#description' => t('The standard deviation of the Gaussian, in pixels'),
    '#default_value' => $data['sigma'],
    '#size' => 3,
    '#states' => array(
      'invisible' => array(
        ':input[name="data[type]"]' => array(
          'value' => RADIAL_BLUR,
        ),
      ),
    ),
  );
  $form['angle'] = array(
    '#type' => 'textfield',
    '#title' => t('Angle'),
    '#description' => t('The angle of the blur'),
    '#default_value' => $data['angle'],
    '#size' => 3,
    '#states' => array(
      'visible' => array(
        ':input[name="data[type]"]' => array(
          array('value' => MOTION_BLUR),
          array('value' => RADIAL_BLUR),
        ),
      ),
    ),
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_blur_defaults() {
  return array(
    'type' => NORMAL_BLUR,
    'radius' => '16',
    'sigma' => '16',
    'angle' => '0',
  );
}
