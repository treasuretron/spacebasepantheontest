(function($, Drupal) {
  Drupal.behaviors.sbSearch = {
    attach:function() {
      
      $('.form-item-sf-group-description input').click(function() {
        $( "#views-exposed-form-sitewide-search-search" ).submit();
      });

    }
  };
}(jQuery, Drupal));