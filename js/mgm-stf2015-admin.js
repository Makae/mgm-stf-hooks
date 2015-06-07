(function($) {
  var enhancements = {
    html : '<div class="row bad-weather-row">' +
              '<label for="bad_weather_text" class="col col_12_12">Schlechtwetter-Text</label>' +
              '<div class="col col_12_12 no-padding">' +
                '<input name="bad_weather_active" type="checkbox" value="1" />' +
                '<label for="bad_weather_active">Bei schlechten Wetter anzeigen</label>' +
              '</div>' +
              '<textarea name="bad_weather_text" class="col col_12_12">' + mgm_stf_config.default_key + '</textarea>' +
            '</div>',

    init : function() {
      mgm.registerListener('mgm.admin.provider.gizmo.update', $.proxy(this.gizmo_update, this));
      mgm.registerListener('mgm.admin.provider.general.load', $.proxy(this.general_load, this));
    },

    gizmo_update : function(e, data) {
      this.update(data.gizmo, data.form);
      return data;
    },

    general_load : function(e, data) {
      data.data = this.render(data.gizmo, data.data);
      return data;
    },

    render : function(gizmo, $container) {
      var $html = $(this.html);
      var text = gizmo.content_data.bad_weather_text;
      var active = typeof gizmo.content_data.bad_weather_active != 'undefined' ? gizmo.content_data.bad_weather_active :  mgm_stf_config.bad_weather_active_default;

      text = text == mgm_stf_config.default_key || typeof text == 'undefined' ? mgm_stf_config.bad_weather_text : text;

      $html.find('textarea[name="bad_weather_text"]').val(text);
      $html.find('[name="bad_weather_active"]').prop('checked', active ? true : false);

      $container = $container.add($html);

      return $container;
    },

    update : function(gizmo, $form) {
      gizmo.content_data.bad_weather_text = $form.find('textarea[name="bad_weather_text"]').val();
      if(gizmo.content_data.bad_weather_text == mgm_stf_config.bad_weather_text)
        gizmo.content_data.bad_weather_text = '__DEFAULT__';
      gizmo.content_data.bad_weather_active = $form.find('[name="bad_weather_active"]').is(':checked');
    },


  };

  if(mgm && mgm.admin)
    enhancements.init();
  $(window).on('mgm.admin.loaded', enhancements.init);
})(jQuery);