<?php

/**
 * Annotates an image with text
 *
 * @param $image
 *   An image object. The $image->resource value will be modified by this call.
 * @param $text
 *   Text settings of the annotate effect.
 * @param $position
 *   Position settings of the annotate effect.
 * @return
 *   TRUE or FALSE, based on success.
 */
function image_imagick_annotate(stdClass $image, $text, $position) {
  $padding = array(
    'x' => $position['padding_x'],
    'y' => $position['padding_y'],
  );

  // Check if percent is used
  $percent_x = explode('%', $padding['x']);
  if (count($percent_x) == 2) {
    $padding['x'] = $image->info['width'] / 100 * reset($percent_x);
  }
  $percent_y = explode('%', $padding['y']);
  if (count($percent_y) == 2) {
    $padding['y'] = $image->info['height'] / 100 * reset($percent_y);
  }

  // Create new transparent layer
  $text_layer = new Imagick();
  $text_layer->newImage(
    $image->info['width'] - (2 * $padding['x']),
    $image->info['height'] - (2 * $padding['y']),
    new ImagickPixel('transparent')
  );

  // Font properties
  $draw = new ImagickDraw();
  $draw->setFont($text['font']);
  $draw->setFillColor($text['HEX']);
  $draw->setFontSize($text['size']);

  // Calculate text width and height
  $imagick = new Imagick();

  list($lines, $lineHeight) = wordWrapAnnotation($imagick, $draw, $text['text'], $text_layer->getImageWidth());

  // Calcuate position
  $text_dimensions = $imagick->queryFontMetrics($draw, $text['text']);
  $text_height = count($lines) * $lineHeight;
  list($left, $top) = explode('-', $position['anchor']);

  $y = image_filter_keyword($top, $text_layer->getImageHeight(), $text_height);
  $y += ($text_dimensions['textHeight'] + $text_dimensions['descender']);

  foreach ($lines as $line) {
    $line_dimensions = $imagick->queryFontMetrics($draw, $line);

    $x = image_filter_keyword($left, $text_layer->getImageWidth(), $line_dimensions['textWidth']);
    $text_layer->annotateImage($draw, $x, $y, 0, $line);

    // Add lineheight for next line
    $y += $lineHeight;
  }

  return $image->resource->compositeImage($text_layer, Imagick::COMPOSITE_OVER, $padding['x'], $padding['y']);
}

/**
 * Implements the imagick annotate effect.
 *
 * @param $image
 *   An image object
 * @param array $data
 *   The data passed from the form
 */
function imagick_annotate($image, $data = array()) {
  image_toolkit_invoke('annotate', $image, $data);
}

/**
 * Settings form for the imagick annotate effect.
 *
 * @param $action
 *   The saved action form parameters.
 */
function imagick_annotate_form($data) {
  $data = array_merge(imagick_annotate_defaults(), (array) $data);

  // Get fonts
  $imagick = new Imagick();
  $available_fonts = $imagick->queryFonts();

  $form['text_fieldset'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#title' => t('Text'),

    'text' => array(
      '#type' => 'textfield',
      '#title' => t('Text'),
      '#description' => t('Text to annotate the image with.'),
      '#default_value' => $data['text_fieldset']['text'],
    ),
    'font' => array(
      '#type' => 'select',
      '#options' => array_combine($available_fonts, $available_fonts),
      '#title' => t('Font'),
      '#description' => t('Fonts that ImageMagick knows about.'),
      '#default_value' => $data['text_fieldset']['font'],
    ),
    'size' => array(
      '#type' => 'textfield',
      '#title' => t('Font size'),
      '#default_value' => $data['text_fieldset']['size'],
      '#size' => 3,
    ),
    'HEX' => array(
      '#type' => 'textfield',
      '#title' => t('Color'),
      '#default_value' => $data['text_fieldset']['HEX'],
      '#size' => 7,
      '#colorpicker' => TRUE,
    ),

  );
  $form['position_fieldset'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#title' => t('Position'),

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
      '#default_value' => $data['position_fieldset']['anchor'],
    ),
    'padding_x' => array(
      '#type' => 'textfield',
      '#title' => t('Padding X'),
      '#default_value' => $data['position_fieldset']['padding_x'],
      '#description' => t('Enter a value in pixels or percent'),
      '#size' => 3,
    ),
    'padding_y' => array(
      '#type' => 'textfield',
      '#title' => t('Padding Y'),
      '#default_value' => $data['position_fieldset']['padding_y'],
      '#description' => t('Enter a value in pixels or percent'),
      '#size' => 3,
    ),
  );

  return $form;
}

/**
 * Returns the default settings of this effect.
 */
function imagick_annotate_defaults() {
  return array(
    'text_fieldset' => array(
      'text' => 'Annotation',
      'font' => 'Helvetica',
      'size' => 20,
      'HEX' => '#000000',
    ),
    'position_fieldset' => array(
      'anchor' => 'right-bottom',
      'padding_x' => 20,
      'padding_y' => 20,
    ),
  );
}

/**
 * Helper funtion to wrap text when it is to long
 */
function _image_imagick_word_wrap_annotation($image, $draw, $text, $maxWidth) {
  $text = trim($text);

  $words = preg_split('%\s%', $text, -1, PREG_SPLIT_NO_EMPTY);
  $lines = array();
  $i = 0;
  $lineHeight = 0;

  while (count($words) > 0)
  {
    $metrics = $image->queryFontMetrics($draw, implode(' ', array_slice($words, 0, ++$i)));
    $lineHeight = max($metrics['textHeight'], $lineHeight);

    // check if we have found the word that exceeds the line width
    if ($metrics['textWidth'] > $maxWidth or count($words) < $i)
    {
      // handle case where a single word is longer than the allowed line width (just add this as a word on its own line?)
      if ($i == 1)
        $i++;

      $lines[] = implode(' ', array_slice($words, 0, --$i));
      $words = array_slice($words, $i);
      $i = 0;
    }
  }

  return array($lines, $lineHeight);
}
