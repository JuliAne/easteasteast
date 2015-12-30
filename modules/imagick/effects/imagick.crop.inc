<?php

/**
 * Crop an image using the Imagick toolkit.
 *
 * @param $image
 *   An image object. The $image->resource, $image->info['width'], and
 *   $image->info['height'] values will be modified by this call.
 * @param $x
 *   The starting x offset at which to start the crop, in pixels.
 * @param $y
 *   The starting y offset at which to start the crop, in pixels.
 * @param $width
 *   The width of the cropped area, in pixels.
 * @param $height
 *   The height of the cropped area, in pixels.
 * @return
 *   TRUE or FALSE, based on success.
 *
 * @see image_crop()
 */
function image_imagick_crop(stdClass $image, $x, $y, $width, $height) {
  $res = $image->resource;
  if (!$res->cropImage($width, $height, $x, $y)) {
    return FALSE;
  }
  $image->info['width'] = $width;
  $image->info['height'] = $height;
  return TRUE;
}

/**
 * Implements the imagick crop effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_crop($image, $data = array()) {
  list($x, $y) = explode('-', $data['anchor']);
  $x = image_filter_keyword($x, $image->info['width'], $data['width']);
  $y = image_filter_keyword($y, $image->info['height'], $data['height']);

  $data = array_merge(array(
    'x' => $x,
    'y' => $y,
  ), $data);

  image_toolkit_invoke('crop', $image, $data);
}

/**
 * Form structure for the image crop form.
 *
 * Note that this is not a complete form, it only contains the portion of the
 * form for configuring the crop options. Therefore it does not not need to
 * include metadata about the effect, nor a submit button.
 *
 * @param $data
 *   The current configuration for this crop effect.
 */
function imagick_crop_form($data) {
  $data = array_merge(imagick_crop_defaults(), (array) $data);
  $form = image_crop_form($data);

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_crop_defaults() {
  return array(
    'width' => 100,
    'height' => 100,
    'anchor' => 'center-center',
  );
}
