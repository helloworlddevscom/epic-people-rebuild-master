(function ($) {
  'use strict';

  /**
   * All of the code for admin-facing JavaScript source
   * should reside in this file.
   */

  $(document).ready(function () {

    // -------------------------------------------------------------
    //   Auto term use Dandelion check
    // -------------------------------------------------------------
    $(document).on('click', '.autoterm_use_dandelion', function (e) {
      autoterm_use_dandelion_action();
    });
    autoterm_use_dandelion_action();
    function autoterm_use_dandelion_action() {
      if ($('.autoterm_use_dandelion').length > 0) {
        if ($('.autoterm_use_dandelion').prop("checked")) {
          $('.terms_datatxt_access_token').closest('tr').removeClass('st-hide-content');
          $('.terms_datatxt_min_confidence').closest('tr').removeClass('st-hide-content');
        } else {
          $('.terms_datatxt_access_token').closest('tr').addClass('st-hide-content');
          $('.terms_datatxt_min_confidence').closest('tr').addClass('st-hide-content');
        }
      }
    }

    // -------------------------------------------------------------
    //   Auto term use OpenCalais check
    // -------------------------------------------------------------
    $(document).on('click', '.autoterm_use_opencalais', function (e) {
      autoterm_use_opencalais_action();
    });
    autoterm_use_opencalais_action();
    function autoterm_use_opencalais_action() {
      if ($('.autoterm_use_opencalais').length > 0) {
        if ($('.autoterm_use_opencalais').prop("checked")) {
          $('.terms_opencalais_key').closest('tr').removeClass('st-hide-content');
        } else {
          $('.terms_opencalais_key').closest('tr').addClass('st-hide-content');
        }
      }
    }

    // -------------------------------------------------------------
    //   Auto term Regex check
    // -------------------------------------------------------------
    $(document).on('click', '.autoterm_use_regex', function (e) {
      autoterm_use_regex_action();
    });
    autoterm_use_regex_action();
    function autoterm_use_regex_action() {
      if ($('.autoterm_use_regex').length > 0) {
        if ($('.autoterm_use_regex').prop("checked")) {
          $('.terms_regex_code').closest('tr').removeClass('st-hide-content');
        } else {
          $('.terms_regex_code').closest('tr').addClass('st-hide-content');
        }
      }
    }
  
  });

})(jQuery);
