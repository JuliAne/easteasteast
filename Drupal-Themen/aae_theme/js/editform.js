$(document).ready(function(){

  $('#eventSpartenInput').tokenize();

  $("#eventStartdatumInput").Zebra_DatePicker({ 'readonly_element' : false });
  $("#eventEnddatumInput").Zebra_DatePicker({ 'readonly_element' : false });

  $('form').submit(function(){
    $('#beschreibung').html(CKEDITOR.instances.beschreibung.getData());
  });

  $('.Adresse input').focusout(function(){

    if ($('#NrInput').val() != '' && $('#PLZInput').val() != '' && $('#StrasseInput').val() != '') {

    $('#GPSInput').val('Ermittle Geo-Koordinaten...');

    $.ajax({
      url: "https://api.mapbox.com/v4/geocode/mapbox.places/"+ $('#PLZInput').val() +"Leipzig+" + $('#StrasseInput').val() + "+"+ $('#NrInput').val() +".json?access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
    })
    .done(function( data ) {
      console.log( "Ermittelte Geo-Koordinaten: ", data.features[0].center );

      $('#GPSInput').val(data.features[0].center[1] + ',' + data.features[0].center[0]);

      });
    }
  });
});
