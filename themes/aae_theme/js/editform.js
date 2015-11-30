$(document).ready(function(){

  $('#eventSpartenInput').tokenize({displayDropdownOnFocus:true});

  $("#eventStartdatumInput").Zebra_DatePicker({'readonly_element' : false});
  $("#eventEnddatumInput").Zebra_DatePicker({'readonly_element' : false});

  $('form').submit(function(){
    $('#beschreibung').html(CKEDITOR.instances.beschreibung.getData());
    $('#kurzbeschreibung').html(CKEDITOR.instances.kurzbeschreibung.getData());
  });

  $('#veranstalter').change(function(){

    var actionUrl = $(this).find('option:selected').attr('value');

    if (actionUrl !== 0) {

    $.get("../ajax/getAkteurAdresse/" + actionUrl, function(data) {

     if (data.substring(2) !== '') {
      var obj = jQuery.parseJSON(data.substring(2));
      $('#StrasseInput').attr('value',obj.strasse);
      $('#NrInput').attr('value',obj.nr);
      $('#AdresszusatzInput').attr('value',obj.adresszusatz);
      $('#PLZInput').attr('value',obj.plz);
      $('.Adresse select option[value='+obj.bezirk+']').attr('selected','selected');
      $('#GPSInput').attr('value',obj.gps);
     }

    });

    }

  });

  $('.Adresse input').focusout(function(){

    if ($('#NrInput').val() != '' && $('#PLZInput').val() != '' && $('#StrasseInput').val() != '') {

    $('#GPSInput').val('Ermittle Geo-Koordinaten...');

    $.ajax({

      url: "https://api.mapbox.com/v4/geocode/mapbox.places/"+ $('#PLZInput').val() +"Leipzig+" + $('#StrasseInput').val() + "+"+ $('#NrInput').val() +".json?access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
      //url: "https://api.mapbox.com/geocoding/v5/mapbox.places/Leipzig+" + $('#StrasseInput').val() + "+"+ $('#NrInput').val() +".json?proximity=51.331,12.433&access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
      //url: "https://api.mapbox.com/geocoding/v5/mapbox.places/" + $('#StrasseInput').val() + " "+ $('#NrInput').val() +".json?types=address&access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",

    })
    .done(function( data ) {

      console.log( "Ermittelte Geo-Koordinaten: ", data.features[0].center );

      $('#GPSInput').val(data.features[0].center[1] + ',' + data.features[0].center[0]);

      });
    }
  });
});
