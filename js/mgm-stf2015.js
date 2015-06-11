(function($) {
  var menu_links = {

    position_interval : false,

    init : function() {
      var self = this;
      $('.mgm_wrapper').each(function() {
        var map = mgm.getMap($(this).attr('data-map-idx'));
        self.initOptions(map);
        self.initLocations(map);
        self.initPosition(map);
      });
    },

    initLocations : function(_map) {
      var self = this;
      var map = _map;
      $('.stf2015-location-link a').each(function() {
        var config = JSON.parse(decodeURIComponent($(this).closest('.stf2015-location-link').attr('data-config')));

        $(this).click(function(e) {
          if(get_params()['makae-map'].indexOf(config.mapid) != 0 ) {
            return;
          } else {
            e.preventDefault();
            e.stopImmediatePropagation();
          }

          map.scrollPan(config.coords.latitude, config.coords.longitude);
        });
      });

    },

    initOptions : function(_map) {
      var self = this;
      var map = _map;
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
    },

    initPosition : function(_map) {
      var self = this;
      var map = _map;
      var params = get_params();
      if(params['lat'] && params['lng'])
        map.scrollPan(params['lat'], params['lng'])

      if(!navigator.geolocation) {
        $('.mgm-stf-position-wrapper').remove();
        return;
      }

      $('.mgm-stf-menu-position .stf2015-target').click($.proxy(this.toggleLocation, this, map));

    },

    isCurrentSite : function(map_id) {
      return window.location.href.indexOf("makae-map=" + map_id)
    },

    toggleLocation : function(map) {
      var self = this;

      if(self.position_interval) {
        self.cleanPosition(map);
        return;
      }

      var enable = function(position) {
        self.updatePosition(map, position);
        $('.mgm-stf-menu-position .stf2015-target').addClass('active');
        self.position_interval = window.setInterval(function() {
          navigator.geolocation.getCurrentPosition($.proxy(self.updatePosition, self, map), $.proxy(self.handleError, self, map));
        }, 500);
      };

      var error = function(e, map) {
        self.showError(e, map);
        self.cleanPosition(map);
      };

      navigator.geolocation.getCurrentPosition(enable, error);
    },

    showError : function(e, map) {
      var $msg = $("<div class='mgm-wrapper-message error'><div class='msg-wrapper stdanimation'><span class='message-icon'></span><span>GPS nicht aktiviert</span></div></div>");
      $('.mgm_wrapper').prepend($msg);

      setTimeout(function() {
        $('.mgm_wrapper .mgm-wrapper-message').addClass('active');
        setTimeout(function() {
          $('.mgm_wrapper .mgm-wrapper-message').removeClass('active');
        },2500);
      }, 250);
    },

    cleanPosition : function(map) {
      var self = this;
      window.clearInterval(self.position_interval);
      self.position_interval = false;
      $('.mgm-stf-menu-position .stf2015-target').removeClass('active');
      if(map.__location_marker) {
        map.removeGizmo(map.__location_marker);
        delete map.__location_marker;
      }
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
  };

  var bad_weather = {
    init : function() {
      var self = this;
      mgm.registerListener('mgm.content_manager.call.handler', function(e, data) {
        var gizmo = data.gizmo;
        var config = gizmo.mgm_map.instance_config;

        if(data.gizmo.content_data.bad_weather_active && config._bad_weather_program)
          return $.proxy(self.loadBadWeather, self);

        return false;
      });
    },

    loadBadWeather : function(gizmo, callback) {
      var title = gizmo.name != '' ? gizmo.name : 'Schlechtwetterprogramm!';
      var text = gizmo.content_data.bad_weather_text ;
      if(text == mgm_stf_config.default_key)
        text = mgm_stf_config.bad_weather_text;

      var html = '<h5>' + title + '</h5>' +
                 '<p>' + text + '</p>';
      callback(html);
    }

  }

  var get_params = function () {
    // source http://stackoverflow.com/questions/979975/how-to-get-the-value-from-the-url-parameter
    // This function is anonymous, is executed immediately and
    // the return value is assigned to QueryString!
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
      var pair = vars[i].split("=");
          // If first entry with this name
      if (typeof query_string[pair[0]] === "undefined") {
        query_string[pair[0]] = pair[1];
          // If second entry with this name
      } else if (typeof query_string[pair[0]] === "string") {
        var arr = [ query_string[pair[0]], pair[1] ];
        query_string[pair[0]] = arr;
          // If third or later entry with this name
      } else {
        query_string[pair[0]].push(pair[1]);
      }
    }
      return query_string;
  };

  var init = function() {
    bad_weather.init();
    menu_links.init();
  }
  if(mgm)
    init();
  else
    $(window).on('mgm.admin.loaded', init);
})(jQuery);