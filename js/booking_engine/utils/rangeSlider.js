 $(function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 24,
      values: [ 2, 7 ],
      slide: function( event, ui ) {
        $( "#time" ).val( ui.values[ 0 ] + "hr" + " - " + ui.values[ 1 ] + "hr" );
      }
    });
    $( "#time" ).val( $( "#slider-range" ).slider( "values", 0 ) + "hr" + " - " +
      $( "#slider-range" ).slider( "values", 1 ) + "hr" );
  });