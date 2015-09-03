$(document).ready(function(){

  $("#eventSpartenInput").tokenInput("?q=ajax");

  $('.eventAdresse').click(function(){

    if ($('#eventNrInput').val() != '' && $('#eventPLZInput').val() != '' && $('#eventStrasseInput').val() != '') {

    $.ajax({
      url: "https://api.mapbox.com/v4/geocode/mapbox.places/"+ $('#eventPLZInput').val() +"Leipzig+" + $('#eventStrasseInput').val() + "+"+ $('#eventNrInput').val() +".json?access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
    })
    .done(function( data ) {
      console.log( "Ermittelte Geo-Koordinaten", data.features[0].center );

      $('#eventGPSInput').val(data.features[0].center[1] + ',' + data.features[0].center[0]);

      });
    }
  });
});
