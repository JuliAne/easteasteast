$(document).ready(function(){

  $("#eventSpartenInput").tokenInput("?q=ajax");

  $('#eventStrasseInput').click(function(){

    $.ajax({
      url: "https://api.mapbox.com/v4/geocode/mapbox.places/1600+pennsylvania+ave+nw.json?access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
    })
      .done(function( data ) {
        if ( console && console.log ) {
          console.log( "Sample of data:", data.slice( 0, 100 ) );
        }
      });
  });

});
