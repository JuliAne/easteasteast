<?php

/**
 * Scale and crop an image to the specified size using Imagemagick.
 *
 * @param $image_data An image object.
 * @param $requested_x The requested image width.
 * @param $requested_y The requested image height.
 * @param $upscale TRUE if upscaling is allowed.
 * @return TRUE if successful.
 */
function image_imagick_smartcrop_scale(stdClass $image_data, $requested_x, $requested_y, $upscale) {
  $ratio = max($requested_x / $image_data->info['width'], $requested_y / $image_data->info['height']);
  if ($ratio <= 1 || $upscale) {
    if (!image_imagick_resize($image_data, round($ratio * $image_data->info['width']), round($ratio * $image_data->info['height']))) {
      return FALSE;
    }
  }
  return image_imagick_smartcrop_crop($image_data, $requested_x, $requested_y);
}


/**
 * Crop an image, removing the lowest entropy areas.
 *
 * @param array $image_data
 * @param int $requested_x
 * @param int $requested_y
 * @return booleon TRUE if successful
 */
function image_imagick_smartcrop_crop(stdClass $image_data, $requested_x, $requested_y) {
  $sx = $image_data->info['width'];
  $sy = $image_data->info['height'];
  $dx = $sx - min($sx, $requested_x);
  $dy = $sy - min($sy, $requested_y);
  $left = $top = 0;
  $left_entropy = $right_entropy = $top_entropy = $bottom_entropy = 0;
  $right = $sx;
  $bottom = $sy;

  // Slice from left and right edges until the correct width is reached.
  while ($dx) {
    $slice = min($dx, 10);

    // Calculate the entropy of the new slice.
    if (!$left_entropy) {
      $left_entropy = _smartcrop_imagick_entropy_slice($image_data, $left, $top, $slice, $sy);
    }
    if (!$right_entropy) {
      $right_entropy = _smartcrop_imagick_entropy_slice($image_data, $right - $slice, $top, $slice, $sy);
    }

    // Remove the lowest entropy slice.
    if ($left_entropy >= $right_entropy) {
      $right -= $slice;
      $right_entropy = 0;
    }
    else {
      $left += $slice;
      $left_entropy = 0;
    }
    $dx -= $slice;
  }

  // Slice from the top and bottom edges until the correct width is reached.
  while ($dy) {
    $slice = min($dy, 10);

    // Calculate the entropy of the new slice.
    if (!$top_entropy) {
      $top_entropy = _smartcrop_imagick_entropy_slice($image_data, $left, $top, $requested_x, $slice);
    }
    if (!$bottom_entropy) {
      $bottom_entropy = _smartcrop_imagick_entropy_slice($image_data, $left, $bottom - $slice, $requested_x, $slice);
    }

    // Remove the lowest entropy slice.
    if ($top_entropy >= $bottom_entropy) {
      $bottom -= $slice;
      $bottom_entropy = 0;
    }
    else {
      $top += $slice;
      $top_entropy = 0;
    }
    $dy -= $slice;
  }

  // Finally, crop the image using the coordinates found above.
  return image_imagick_crop($image_data, $left, $top, $right - $left, $bottom - $top);
}

/**
 * Compute the entropy of an image slice, defined as -sum(p.*log2(p)).
 *
 * @param $image_data An image object
 * @param $x Starting X coordinate.
 * @param $y Starting Y coordinate.
 * @param $width The width of the slice.
 * @param $height The height of the slice.
 * @return
 */
function _smartcrop_imagick_entropy_slice($image_data, $x, $y, $width, $height) {
  $histogram = _smartcrop_imagick_histogram($image_data->resource, $x, $y, $width, $height);
  $histogram_size = array_sum($histogram);
  $entropy = 0;
  foreach ($histogram as $p) {
    if ($p == 0) {
      continue;
    }
    $p = $p / $histogram_size;
    $entropy += $p * log($p, 2);
  }
  return $entropy * -1;
}

/**
 * Compute a histogram of an image.
 * @param resource $img Imagemagick image resource.
 * @param $x Starting X coordinate.
 * @param $y Starting Y coordinate.
 * @param $width The width of the slice.
 * @param $height The height of the slice.
 * @return array histogram as an array.
 */
function _smartcrop_imagick_histogram($img, $x, $y, $width, $height) {
  $histogram = array_fill(0, 768, 0);
  $it = $img->getPixelRegionIterator((int) $x, (int) $y, (int) $width, (int) $height);
  foreach ($it as $row => $pixels) {
    foreach ($pixels as $column => $pixel) {
      $rgb = $pixel->getColor();
      $r = $rgb['r'];
      $g = $rgb['g'];
      $b = $rgb['b'];
      $histogram[$r]++;
      $histogram[$g + 256]++;
      $histogram[$b + 512]++;
    }
  }
  return $histogram;
}
