(function ($, Drupal) {

    "use strict";

    Drupal.behaviors.place_ads = {
        attach: function (context, settings) {
            setTags(settings);
        }
    };

    function setTags(settings){
        if(typeof TFM == "undefined") return;

        for(var slot_html_id in settings.AdvertisingSlots) {
            var tag = settings.AdvertisingSlots[slot_html_id][adsc_device];
            $('#' + slot_html_id).html('<script>' + TFM.Tag.getAdTag(tag, slot_html_id) + ';</script>');
        }
    }

})(jQuery, Drupal);
