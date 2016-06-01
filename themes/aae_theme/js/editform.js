$(document).ready(function(){

  $('#eventSpartenInput').tokenize({
    displayDropdownOnFocus : true,
    onRemoveToken : function(value, e){

     if((parseFloat(value) == parseInt(value)) && !isNaN(value)) {
      $('form').append('<input type="hidden" name="removedTags[]" value="'+value+'" />');
     }

    }
  });

  if ($('#veranstalter').length && $('#veranstalter').find('option:selected').attr('value')=='0'){
   // manually set veranstalter from "private" to the first Akteur available in list
   $('#veranstalter option:eq(1)').attr('selected','selected');
  }

  $('.page-events-new .switch-paddle,.page-eventprofil-edit .switch-paddle').click(function(){
   if ($('#eventRecurres').prop('checked')){
    $('#eventRecurresData').fadeOut('fast');
   } else {
    $('#eventRecurresData').fadeIn('fast'); 
   }
  });

  $("#eventStartdatumInput,#eventEnddatumInput,.page-events-new #eventRecurresTill,.page-eventprofil-edit #eventRecurresTill").Zebra_DatePicker({
    readonly_element : false,
    format : 'Y-m-d',
    days : ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
    months : ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
    lang_clear_date : 'Löschen',
    show_select_today : 'Heute'
   });
  
  $(document).on('change', '#eventBildInput,#akteurBildInput', function(){
   $(this).parent().find('label').html($(this).val());
  });

  if ($('#currentPic').length){
   $('#currentPic a').click(function(){
     var r = confirm("Bild wirklich löschen?");
     if (r == true) {
      $('#currentPic img').fadeOut('slow');
      $('#currentPic a').fadeOut('slow');
      $('form').append('<input type="hidden" name="removeCurrentPic" value="'+$('input[name="oldPic"]').attr('value')+'" />');
     }
     return false;
   });
  }

  $('form').submit(function(){
   $('#beschreibung').html(CKEDITOR.instances.beschreibung.getData());
   $('#kurzbeschreibung').html(CKEDITOR.instances.kurzbeschreibung.getData());
  });

  $('#veranstalter').change(function(){

    var actionUrl = $(this).find('option:selected').attr('value');

    if (actionUrl !== 0) {

    $.get("../ajax/getAkteurAdresse/" + actionUrl, function(data) {

     if (data.substring(2) !== '') {
      var obj = jQuery.parseJSON(data);
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

    if ($('#NrInput').val() != '' && $('#PLZInput').val() != '' && $('#StrasseInput').val() != '' && $(this).attr('name') != 'gps') {

    var strasse = $('#StrasseInput').val();

    if (strasse.slice(-1) == '.') {
      // Replace characters to optimize query-results
      strasse = strasse.replace('.', 'aße');
      strasse = strasse.replace('ss', 'ß');
      $('#StrasseInput').val(strasse);
    }

    $('#GPSInput').val('Ermittle Geo-Koordinaten...');

    $.ajax({
      //url: "https://api.mapbox.com/v4/geocode/mapbox.places/"+ $('#PLZInput').val() +"Leipzig+" + $('#StrasseInput').val() + "+"+ $('#NrInput').val() +".json?access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
      url: "https://api.mapbox.com/geocoding/v5/mapbox.places/"+ $('#PLZInput').val() +"+Leipzig+" + strasse + "+"+ $('#NrInput').val() +".json?access_token=pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg",
    })
    .done(function( data ) {

      console.log( "Ermittelte Geo-Koordinaten: ", data.features[0].center );

      $('#GPSInput').val(data.features[0].center[1] + ',' + data.features[0].center[0]);

      $('#show_coordinates a').attr('href', 'http://www.openstreetmap.org/#map=13/'+data.features[0].center[1]+'/'+data.features[0].center[0]);
      $('#show_coordinates').fadeIn('fast');

      });
    }
  });
});
