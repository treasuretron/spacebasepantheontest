(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.pendingMembers = {
    attach: function (context, settings) {
      var clickTap = $.support.touch ? "tap" : "click";
      $('.reject-accept > i', context).once('pendingMembers').on(clickTap, function() {
        var eid = $(this).data('eid');
        var value = $(this).data('value');
        var active = $(this).hasClass('active');
        var state = value+active;
        switch (state) {
          case 'rejectedfalse':
          case 'approvedfalse':
            $(this).addClass('active').siblings('i').removeClass('active');
            $('input[data-eid="'+eid+'"][value="'+value+'"]').prop('checked', true);
            $('input[data-eid="'+eid+'"][value="admin"]').prop('checked', false);
            if (value == 'rejected') {
              $(this).siblings('.make-admin').addClass('hidden');
            }
            else {
              $(this).siblings('.make-admin').removeClass('hidden');
            }
            break;
          case 'rejectedtrue':
          case 'approvedtrue':
            $(this).removeClass('active');
            $('input[data-eid="'+eid+'"][value="pending"]').prop('checked', true);
            $('input[data-eid="'+eid+'"][value="admin"]').prop('checked', false);
            $(this).siblings('.make-admin').addClass('hidden');
            break;
          case 'adminfalse':
          case 'admintrue':
            if (!active) {
              $(this).addClass('active');
            }
            else {
              $(this).removeClass('active');
            }
            $('input[data-eid="'+eid+'"][value="admin"]').prop('checked', !active);
            break;
        }
      });
    }
  };
})(jQuery, Drupal);