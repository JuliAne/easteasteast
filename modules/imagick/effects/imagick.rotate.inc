<?php

/**
 * Rotate an image the given number of degrees.
 *
 * @param $image
 *   An image object. The $image->resource, $image->info['width'], and
 *   $image->info['height'] values will be modified by this call.
 * @param $degrees
 *   The number of (clockwise) degrees to rotate the image.
 * @param $background
 *   An hexadecimal integer specifying the background color to use for the
 *   uncovered area of the image after the rotation. E.g. 0x000000 for black,
 *   0xff00ff for magenta, and 0xffffff for white. For images that support
 *   transparency, this will default to transparent. Otherwise it will
 *   be white.
 * @return
 *   TRUE or FALSE, based on success.
 *
 * @see image_rotate()
 */
function image_imagick_rotate(stdClass $image, $degrees, $background = NULL, $random) {
  $color = empty($background) ? 'none' : $background;

  $res = $image->resource;
  if (!$res->rotateImage(new ImagickPixel($color), $degrees)) {
    return FALSE;
  }
  $image->info['width'] = $res->width;
  $image->info['height'] = $res->height;
  return TRUE;
}


/**
 * Image effect callback; Rotate an image resource.
 *
 * @param $image
 *   An image object returned by image_load().
 * @param $data
 *   An array of attributes to use when performing the rotate effect containing
 *   the following items:
 *   - "degrees": The number of (clockwise) degrees to rotate the image.
 *   - "random": A boolean indicating that a random rotation angle should be
 *     used for this image. The angle specified in "degrees" is used as a
 *     positive and negative maximum.
 *   - "bgcolor": The background color to use for exposed areas of the image.
 *     Use web-style hex colors (#FFFFFF for white, #000000 for black). Leave
 *     blank for transparency on image types that support it.
 * @return
 *   TRUE on success. FALSE on failure to rotate image.
 * @see image_rotate().
 */
function imagick_rotate(&$image, $data) {
  if (!empty($data['random'])) {
    $degrees = $data['degrees'];
    $data['degrees'] = $angle = mt_rand(-$degrees, $degrees);
  }

  image_toolkit_invoke('rotate', $image, $data);
}

/**
 * Form structure for the image rotate form.
 *
 * Note that this is not a complete form, it only contains the portion of the
 * form for configuring the rotate options. Therefore it does not not need to
 * include metadata about the effect, nor a submit button.
 *
 * @param $data
 *   The current configuration for this rotate effect.
 */
function imagick_rotate_form($data) {
  $data = array_merge(imagick_rotate_defaults(), (array) $data);
  $form = image_rotate_form($data);
  $form['bgcolor']['#colorpicker'] = TRUE;

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_rotate_defaults() {
  return array(
    'degrees' => 10,
    'bgcolor' => '#FFFFFF',
    'random' => 0,
  );
}
