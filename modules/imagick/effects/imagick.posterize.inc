<?php

/**
 * Posterizes an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $level
 *   The strenght of the effect
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_posterize(stdClass $image, $levels) {
  return $image->resource->posterizeImage($levels, TRUE);
}


/**
 * Implements the imagick posterize effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_posterize($image, $data = array()) {
  image_toolkit_invoke('posterize', $image, $data);
}

/**
 * Image effect form callback for the posterize effect.
 *
 * @param array $data
 *   The current configuration for this image effect.
 *
 * @return array
 *   The form definition for this effect.
 */
function imagick_posterize_form($data) {
  $data = array_merge(imagick_posterize_defaults(), (array) $data);

  $form['colors'] = array(
    '#type' => 'textfield',
    '#title' => t('Color levels per channel'),
    '#default_value' => isset($data['colors']) ? $data['colors'] : '',
    '#required' => TRUE,
    '#size' => 10,
    '#element_validate' => array('image_effect_integer_validate'),
    '#allow_negative' => FALSE,
    '#description' => t('Number of unique values per color channel to reduce this image to. The transparency channel is left unchanged. This effect can be used to reduce file size on png images.'),
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_posterize_defaults() {
  return array(
    'colors' => '5',
  );
}
