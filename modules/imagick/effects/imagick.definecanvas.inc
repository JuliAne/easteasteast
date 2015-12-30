<?php

/**
 * Define the canvas of an image
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $data
 *   Array of data passed through by the form.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_definecanvas(stdClass $image, $color, $under, $exact_size, $exact, $relative) {
  $canvas = new Imagick();

  $color = empty($color) ? 'none' : $color;
  if ($exact_size) {
    $width = imagick_percent_filter($exact['width'], $image->info['width']);
    $height = imagick_percent_filter($exact['height'], $image->info['height']);

    list($x, $y) = explode('-', $exact['anchor']);
    $x = image_filter_keyword($x, $width, $image->info['width']);
    $y = image_filter_keyword($y, $height, $image->info['height']);
  }
  else {
    $width = $image->info['width'] + $relative['leftdiff'] + $relative['rightdiff'];
    $height = $image->info['height'] + $relative['topdiff'] + $relative['bottomdiff'];

    $x = $relative['leftdiff'];
    $y = $relative['topdiff'];
  }

  $canvas->newImage($width, $height, new ImagickPixel($color));
  if ($under) {
    $canvas->compositeImage($image->resource, imagick::COMPOSITE_DEFAULT, $x, $y);
  }

  $image->resource = $canvas;
  return $image;
}

/**
 * Implements the imagick definecanvas effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_definecanvas($image, $data = array()) {
  image_toolkit_invoke('definecanvas', $image, $data);
}

/**
 * Settings form for the imagick definecanvas effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_definecanvas_form($data) {
  $data = array_merge(imagick_definecanvas_defaults(), (array) $data);

  $form['HEX'] = array(
    '#type' => 'textfield',
    '#title' => t('HEX'),
    '#default_value' => $data['HEX'],
    '#size' => 7,
    '#colorpicker' => TRUE,
  );
  $form['under'] = array(
    '#type' => 'checkbox',
    '#title' => t('Resize canvas <em>under</em> image (possibly cropping)'),
    '#default_value' => $data['under'],
    '#description' => t('If <em>not</em> set, this will create a solid flat layer, probably totally obscuring the source image'),
  );

  $form['info'] = array('#value' => t('Enter values in ONLY ONE of the below options. Either exact or relative. Most values are optional - you can adjust only one dimension as needed. If no useful values are set, the current base image size will be used.'));

  $form['exact_measurements'] = array(
    '#type' => 'checkbox',
    '#title' => t('Exact measurements'),
    '#default_value' => $data['exact_measurements'],
  );

  $form['exact'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#title' => 'Exact size',
    'help' => array(
      '#markup' => t('Set the canvas to a precise size, possibly cropping the image. Use to start with a known size.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ),
    'width' => array(
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#default_value' => $data['exact']['width'],
      '#description' => t('Enter a value in pixels or percent'),
      '#size' => 5,
    ),
    'height' => array(
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#default_value' => $data['exact']['height'],
      '#description' => t('Enter a value in pixels or percent'),
      '#size' => 5,
    ),
    'anchor' => array(
      '#type' => 'radios',
      '#title' => t('Anchor'),
      '#options' => array(
        'left-top'      => t('Top left'),
        'center-top'    => t('Top center'),
        'right-top'     => t('Top right'),
        'left-center'   => t('Center left'),
        'center-center' => t('Center'),
        'right-center'  => t('Center right'),
        'left-bottom'   => t('Bottom left'),
        'center-bottom' => t('Bottom center'),
        'right-bottom'  => t('Bottom right'),
      ),
      '#theme' => 'image_anchor',
      '#default_value' => $data['exact']['anchor'],
    ),
    '#states' => array(
      'visible' => array(
        ':input[name="data[exact_measurements]"]' => array('checked' => TRUE),
      ),
    ),
  );
  $form['relative'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#title' => t('Relative size'),
    'help' => array(
      '#markup' => t('Set the canvas to a relative size, based on the current image dimensions. Use to add simple borders or expand by a fixed amount. Negative values may crop the image.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ),
    'leftdiff' => array(
      '#type' => 'textfield',
      '#title' => t('left difference'),
      '#default_value' => $data['relative']['leftdiff'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels.'),
    ),
    'rightdiff' => array(
      '#type' => 'textfield',
      '#title' => t('right difference'),
      '#default_value' => $data['relative']['rightdiff'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels.'),
    ),
    'topdiff' => array(
      '#type' => 'textfield',
      '#title' => t('top difference'),
      '#default_value' => $data['relative']['topdiff'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels.'),
    ),
    'bottomdiff' => array(
      '#type' => 'textfield',
      '#title' => t('bottom difference'),
      '#default_value' => $data['relative']['bottomdiff'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels.'),
    ),
    '#states' => array(
      'visible' => array(
        ':input[name="data[exact_measurements]"]' => array('checked' => FALSE),
      ),
    ),
  );

  return $form;
}

function imagick_percent_filter($length_specification, $current_length) {
  if (strpos($length_specification, '%') !== FALSE) {
    $length_specification =  $current_length !== NULL ? str_replace('%', '', $length_specification) * 0.01 * $current_length : NULL;
  }
  return $length_specification;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_definecanvas_defaults() {
  return array(
    'HEX' => '#FFFFFF',
    'under' => TRUE,
    'exact_measurements' => TRUE,
    'exact' => array(
      'width' => '100',
      'height' => '100',
      'anchor' => 'center-center',
    ),
    'relative' => array(
      'leftdiff' => '20',
      'rightdiff' => '20',
      'topdiff' => '20',
      'bottomdiff' => '20',
    ),
  );
}
