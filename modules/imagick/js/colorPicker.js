(function ($) {
  Drupal.behaviors.colorPicker = {
    attach: function(context) {
      $('.colorentry').once('colorpicker').each(function () {
        var $container = $(this).closest('.form-item'),
            $entry = $(this),
            $colorpicker = $('<div></div>').addClass('colorpicker').mouseup(function() {
              $entry.blur()
            });

        $container.append($colorpicker);

        farb = jQuery.farbtastic($colorpicker, function(color){
          $entry.val(color.toUpperCase()).change();
        }).setColor($entry.val());
      });
    }
  }
})(jQuery);
