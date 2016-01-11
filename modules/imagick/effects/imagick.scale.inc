<?php

/**
 * Form structure for the image scale form.
 *
 * Note that this is not a complete form, it only contains the portion of the
 * form for configuring the scale options. Therefore it does not not need to
 * include metadata about the effect, nor a submit button.
 *
 * @param $data
 *   The current configuration for this scale effect.
 */
function imagick_scale_form($data) {
  $data = array_merge(imagick_scale_defaults(), (array) $data);
  $form = image_scale_form($data);

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_scale_defaults() {
  return array(
    'width' => 100,
    'height' => 100,
    'upscale' => 0,
  );
}
