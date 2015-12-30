<?php

/**
 * Scale an image to the specified size using Imagick.
 *
 * @param $image
 *   An image object. The $image->resource, $image->info['width'], and
 *   $image->info['height'] values will be modified by this call.
 * @param $width
 *   The new width of the resized image, in pixels.
 * @param $height
 *   The new height of the resized image, in pixels.
 * @return
 *   TRUE or FALSE, based on success.
 *
 * @see image_resize()
 */
function image_imagick_resize(stdClass $image, $width, $height) {
  $res = $image->resource;
  if (!$res->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1)) {
    return FALSE;
  }
  $image->info['width'] = $width;
  $image->info['height'] = $height;
  return TRUE;
}

/**
 * Image effect callback; Resize an image resource.
 *
 * @param $image
 *   An image object returned by image_load().
 * @param $data
 *   An array of attributes to use when performing the resize effect with the
 *   following items:
 *   - "width": An integer representing the desired width in pixels.
 *   - "height": An integer representing the desired height in pixels.
 *
 * @return
 *   TRUE on success. FALSE on failure to resize image.
 *
 * @see image_resize()
 */
function imagick_resize(&$image, $data) {
  image_toolkit_invoke('resize', $image, $data);
}

/**
 * Form structure for the image resize form.
 *
 * Note that this is not a complete form, it only contains the portion of the
 * form for configuring the resize options. Therefore it does not not need to
 * include metadata about the effect, nor a submit button.
 *
 * @param $data
 *   The current configuration for this resize effect.
 */
function imagick_resize_form($data) {
  $data = array_merge(imagick_resize_defaults(), (array) $data);
  $form = image_resize_form($data);

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_resize_defaults() {
  return array(
    'width' => 100,
    'height' => 100,
  );
}

