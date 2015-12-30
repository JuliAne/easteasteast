<?php

/**
 * Image effect form callback for the convert image format effect.
 *
 * @param array $data
 *   The current configuration for this image effect.
 *
 * @return array
 *   The form definition for this effect.
 */
function image_imagick_convert(stdClass $image, $format, $quality) {
  $formats = imagick_file_formats();
  $image->info['mime_type'] = $format;
  $image->info['extension'] = $formats[$format];

  // Set a white background color when converting to JPG because this file
  // format does not support transparency
  if ($format == 'image/jpeg') {
    $background = new Imagick();
    $background->newImage($image->info['width'], $image->info['height'], 'white');
    $background->compositeImage($image->resource, Imagick::COMPOSITE_OVER, 0, 0);
    $image->resource = $background;
  }

  $image->resource->setImageCompressionQuality((int) $quality);
  $image->resource->setImageFormat($formats[$format]);

  return $image;
}

/**
 * Implements the imagick convert effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_convert($image, $data = array()) {
  image_toolkit_invoke('convert', $image, $data);
}

/**
 * Settings form for the imagick convert effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_convert_form($data) {
  $data = array_merge(imagick_convert_defaults(), (array) $data);

  $form['format'] = array(
    '#title' => t("File format"),
    '#type' => 'select',
    '#default_value' => $data['format'],
    '#options' => imagick_file_formats(),
  );
  $form['quality'] = array(
    '#type' => 'textfield',
    '#title' => t('Quality'),
    '#description' => t('Override the default image quality. Works for Imagemagick only. Ranges from 0 to 100. For jpg, higher values mean better image quality but bigger files. For png it is a combination of compression and filter'),
    '#size' => 3,
    '#maxlength' => 3,
    '#default_value' => $data['quality'],
    '#field_suffix' => '%',
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_convert_defaults() {
  return array(
    'format' => 'image/jpeg',
    'quality' => variable_get('image_jpeg_quality', 75),
  );
}
