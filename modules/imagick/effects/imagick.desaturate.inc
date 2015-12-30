<?php

/**
 * Convert an image resource to grayscale.
 *
 * Note that transparent GIFs loose transparency when desaturated.
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @return
 *   TRUE or FALSE, based on success.
 *
 * @see image_desaturate()
 */
function image_imagick_desaturate(stdClass $image) {
  $res = $image->resource;

  if (!$res->setImageType(imagick::IMGTYPE_GRAYSCALEMATTE)) {
    return FALSE;
  }
  return TRUE;
}
