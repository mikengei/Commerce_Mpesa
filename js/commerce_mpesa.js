(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.mpesajs = {
    attach: function (context) {
       $('.btn_prompt').click(function() {
            $(this).hide(1000);
       });
    }
  };

}(jQuery, Drupal, drupalSettings));
