<?php

/**
 * Autorotates an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_autorotate(stdClass $image, $data = array()) {
  $orientation = $image->resource->getImageOrientation();
  switch($orientation) {
    case imagick::ORIENTATION_BOTTOMRIGHT:
      $image->resource->rotateimage(new ImagickPixel(), 180); // rotate 180 degrees
      break;
    case imagick::ORIENTATION_RIGHTTOP:
      $image->resource->rotateimage(new ImagickPixel(), 90); // rotate 90 degrees CW
      break;
    case imagick::ORIENTATION_LEFTBOTTOM:
      $image->resource->rotateimage(new ImagickPixel(), -90); // rotate 90 degrees CCW
      break;
  }

  // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
  return $image->resource->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
}

/**
 * Imagick autorotate effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_autorotate($image, $data = array()) {
  image_toolkit_invoke('autorotate', $image, $data);
}
