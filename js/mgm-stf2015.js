(function($) {
  var menu_links = {
    position_interval : false,
    init : function() {
      var self = this;
      $('.mgm_wrapper').each(function() {
        var map = mgm.getMap($(this).attr('data-map-idx'));
        $('.mgm-stf-display-option-list .option').click(function() {
          $this = $(this);
          if($this.hasClass('active'))
            return;

          var key = $this.attr('data-map-type-key');
          var overlay_enabled = $this.attr('data-map-overlay') == 'true';
          var type = google.maps.MapTypeId[key];
          map.toggleOverlay(overlay_enabled);
          map.setMapType(type);
          $this.siblings().filter('.option.active').removeClass('active');
          $this.addClass('active');
        });

        self.initPosition(map);
      });
    },

    initPosition : function(map) {
      if(!navigator.geolocation) {
        $('.mgm-stf-position-wrapper').remove();
        return;
      }

      $('.mgm-stf-menu-position .stf2015-target').click($.proxy(this.toggleLocation, this, map));
    },

    toggleLocation : function(map) {
      var self = this;
      if(self.position_interval) {
        window.clearInterval(self.position_interval);
        self.position_interval = false;
        $('.mgm-stf-menu-position .stf2015-target').removeClass('active');
        if(map.__location_marker) {
          map.removeGizmo(map.__location_marker);
          delete map.__location_marker;
        }

        return;
      }

      var enable = function() {
        $('.mgm-stf-menu-position .stf2015-target').addClass('active');
        self.position_interval = window.setInterval(function() {
          navigator.geolocation.getCurrentPosition($.proxy(self.updatePosition, self, map));
        }, 1000);
      };

      var error = function() {

      };

      navigator.geolocation.getCurrentPosition(enable, error);
    },

    updatePosition : function(map, position) {
      if(map.__location_marker) {
        map.__location_marker.gm_marker.setPosition(mgm.utils.latLngToPos({
          lat : position.coords.latitude,
          lng : position.coords.longitude,
        }));
        return;
      }
      var marker_icon = '/wpdev/wp-content/plugins/stf2015-hooks/images/icons/Target-32-red.png';
      var image = new google.maps.MarkerImage(
          marker_icon,
          null, // size
          null, // origin
          new google.maps.Point(8 , 8), // anchor (move to center of marker)
          new google.maps.Size(16, 16) // scaled size (required for Retina display icon)
      );
      map.__location_marker = map.addGizmo({
        gizmo_type: 'marker',
        title: 'location_marker',
        lat: position.coords.latitude,
        lng: position.coords.longitude,
        icon: marker_icon
      });

      map.scrollPan(position.coords.latitude, position.coords.longitude);

    }
  }

  menu_links.init();
})(jQuery);