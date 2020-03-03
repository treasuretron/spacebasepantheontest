/**
 * @file
 * jQuery to let the Industry chart and map interact with each other,
 * and now also related map changes.
 */

(function ( $ ) {
  'use strict';

  /** chartClickPrep = prep the chart for clicks that change the map **/
  $.fn.chartClickPrep = function() {

    // on click of pie chart label, fire the exposed filter of the map:
    $('g.c3-arcs').on( "click", function() {

      var classes = $(this).attr("class");
      var regex = /c3-arcs-{3,}((\w+)(-\w+)*)-{3,}/;
      var indus = $(this).attr("class").match(regex); // failure is null
      if (indus.length) {
        var $nzmap = $('.view-organizations-nz-overview-map');
        var $select = $nzmap.find('select[name="field_industry_segment_target_id"]');
        var words = indus[1].split('-');
        var contains = 'option';
        for (var i=0; i<words.length; i++) {
          contains += ':contains(' + words[i] + ')';
        }
        var $option = $select.find(contains);
        // Set the (probably hidden) form SELECT
        $option.prop("selected",true).trigger('change');
        // Click submit.
        $nzmap.find('button').click();
      }
    });

    // on click a taxonomy label below the map
    $('g.c3-legend-item').on( "click", function() {
      // Set the (probably hidden) form SELECT
      var regex = /((\w+)([- ]\w+)*)/;
      var indus = $(this).text().match(regex);
      if (indus.length) {
        var $nzmap = $('.view-organizations-nz-overview-map');
        var $select = $nzmap.find('select[name="field_industry_segment_target_id"]');
        var $option = $select.find('option:contains("' + indus[1] + '")');
        // Set the (probably hidden) form SELECT
        $option.prop("selected",true).trigger('change');
        // Click submit.
        $nzmap.find('button').click();
      }
    });

    return this;
  };

  /** Bind events on map features to a function that greps out the city and
   * alters the chart exposed filter, when leaflet.feature's are triggered
   * (with the first map, and new maps created by the chart)
   * @ToDo: isn't bind deprecated; use on?
   * @ToDo: I think there is a more efficient way to set an event listener
   * on a layer, not each marker.
   */

  /** Add a click function to features added to the map.
   * Note: This fires the first time the map appears and when ajax generates
   * a new view/map. It was previously encased in:
   *
   *   Drupal.behaviors.spacebase_core = {
   *     attach: function (context, settings) {
   *
   * but that didn't run the first time.
   */
  jQuery(document).bind('leaflet.feature', function(e, lFeature, feature) {
    lFeature.on('click', function(e) {
      // Get the city
      var regex = /locality">([\w\d\s]*)<\/span/;
      var city = lFeature._popup._content.match(regex); // null or Array, need [1]
      if (city !== null) {
        // exposed filter id's change after click, thus twisty effort to id:
        jQuery('input[name="city"]').val(city[1]);
        jQuery('#views-exposed-form-organizations-by-field-and-city-views-block-filter-bpdb-1 button').click();
      }
    })
  });

  // Map: enable the gestureHandling library by disabling map interactions:
  jQuery(document).bind( 'leaflet.map', function( e, map, lMap ) {
    lMap.gestureHandling.addHooks();
  });


  /** When ajax reloads the charts from clicking cities on the map,
   * make the chart clickable again: */
  $( document ).ajaxComplete(function( event, xhr, settings ) {
    if (settings.url == '/views/ajax?_wrapper_format=drupal_ajax') {
      $().chartClickPrep();
    }
  });

  /** On initial pageload, make the chart clickable **/
  $(function () {
    $().chartClickPrep();
  });

})(jQuery);

