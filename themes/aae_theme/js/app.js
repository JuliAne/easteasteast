$(document).ready(function() {

/* For akteure- and events-AJAX-loader:
    if ($(document).height() <= $(window).scrollTop() + $(window).height()) {
        alert("End Of The Page");
    } */

 activePopupId = false;
 activePopupCaller = false;
 mapModalLoaded = false;
 timespaceMoved = false;

// TODO: Only run once, then also copy to alertMsg
 setInterval(function(){$('#alert').slideUp('slow')}, 12000);

 $(document).scroll(function(){
 if ($(window).width() > '390'){   
  if($(window).scrollTop()>1){
   $('#mainnav').addClass('scrolled');
   $('.pace-progress').addClass('pace-scrolled');
  } else {
   $('#mainnav').removeClass('scrolled');
   $('.pace-progress').removeClass('pace-scrolled');
  } 
 }
 });

 $('#timespace .slider').on('moved.zf.slider', function() {

  timespaceMoved = true;

  $('#timespace ul li').css({'font-weight':'normal'}).removeClass('activeTime');

  var leftS = Math.ceil(parseInt($('#timespace .sh-1').css('left'))/33);
  var rightS = Math.ceil(parseInt($('#timespace .sh-2').css('left'))/33);
  for (i = leftS; i <= rightS; i++){
   $('#timespace ul li').eq(i).css({'font-weight':'bold'}).addClass('activeTime');
  }

 });

 $('.login_first').click(function(){
  $('#login-button').trigger('click');
  $('#user-login-form').attr('action', $('#user-login-form').attr('action')+'/new');
  return false;
 });

 $('#homeLoginBtn').click(function(){
  $('#login-button').trigger('click');
  return false;
 });

 $('.popup-link').click(function(){

   var popupCaller = $(this);
   var target = $(this).attr('href');

   if (target.toString() == activePopupId.toString()) {

     activePopupId = target;
     activePopupCaller = popupCaller;
     $(popupCaller).addClass('active');
     $(target).fadeIn('fast');

   } else {

     $(activePopupCaller).removeClass('active');
     $(activePopupId).fadeOut('fast');
     activePopupId = target;
     activePopupCaller = popupCaller;
     $(popupCaller).addClass('active');
     $(target).fadeIn('fast');

   }

  return false;

 });

 $('html').click(function(el){

  if ($(el.target).closest('.popup').css('display') != 'block') {
   $(activePopupCaller).removeClass('active');
   $(activePopupId).fadeOut('fast');
   activePopupId = false;
   activePopupCaller = false;
  }

 });

 $('#presentationFilter li a').click(function(){
  // right-floated icon-action-bar

  var that = $(this);
  
  $('#presentationFilter').find('.active').removeClass('active').addClass('secondary');
  that.removeClass('secondary').addClass('active');

  if ($(that).attr('name') == 'map'){

   return false;

  } else if ($(that).attr('name') == 'byLocation'){

   $('#eventsByLocation').trigger('click'); // calls getLocation

  } else {

   $('#filterForm').submit();

  }

 });

 $('#filterForm').submit(function(){

  $('#presentationFilter input[type="submit"]').remove();
  // Prepare GET-method & add selected presentation mode
  var presentationValue = $('#presentationFilter').find('.active').attr('name');
  $('<input />').attr('type', 'hidden').attr('name', 'presentation').attr('value', presentationValue).appendTo('#filterForm');

  if ($('#akteure #filter').length){
   // akteurepage.tpl
   // - placeholder -
  } else if ($('#events #filter').length && $('#timespace .activeTime').length && timespaceMoved && presentationValue != 'calendar'){
   // eventspage.tpl
   // Allow filtering for timespan; but only in timeline-mode!
   $('<input />').attr('type', 'hidden').attr('name', 'timespan').attr('value', $('#timespace .activeTime').first().attr('data-month') + '-' + $('#timespace .activeTime').last().attr('data-month')).appendTo('#filterForm');
  }

  return true;

 });

 $('#inviteBtn').click(function(){
   $('#invite-modal').fadeIn('slow');
   setHandlers();
 })

 $('#project-buttons #akteur-contact').click(function(){

  var urlSegments = $(location).attr('href').split('/');

  loadAaeModal('../ajax/getAkteurKontakt/' + urlSegments[4], null, 'Lade Kontaktinformationen...');

 });

 setHandlers();

 function setHandlers(){

  $('#calendar .next, #calendar .prev').click(function(){

   $('#calendar').fadeOut('slow');

   $.get($(this).attr('href'), function(data){
     $('#aae_calendar').html(data);
     setHandlers();
   });

  return false;

  });

  // Closes lightbox when hitting "ESC".
  window.addEventListener("keydown", function (event) {
     
   if (event.defaultPrevented) {
     return;
   }

   if (event.key == "Escape") {
    $('.aaeModal .button.closeBtn').click();
    $('#alert').slideUp('slow');
   }

  }, true);

 }

function getLocation() {
/* Simple function(s) to retrieve user's Geolocation 
   TODO: Translate through placeholder in html-body */
  if (navigator.geolocation) {
    alertMsg('Bestimme Standort, einen Moment bitte...');
    navigator.geolocation.getCurrentPosition(loadMapModal, showMapError);
  } else {
    alertMsg('"Geolocation" wurde leider in Deinem Browser deaktiviert :(');
  }
}


 $('#mainnav #actions #searchByLocation').click(function(){
  getLocation();
  return false;
 });


function loadMapModal(position) {

 loadAaeModal('../ajax/getAllLocations/', function(data){

 var htmlHeaders = data.response.htmlHeaders;

 if (!mapModalLoaded || !isScriptIncluded(htmlHeaders.js)){

  document.head.insertAdjacentHTML('beforeend', '<link rel="stylesheet" href="' + htmlHeaders.css + '" />');

  $.getScript(htmlHeaders.js).done(function(script,textStatus){

   $('#aaeModal .content').html('<div id="map"></div>');
   eval(htmlHeaders.jsInline);
   var map = L.mapbox.map("map", htmlHeaders.mapName).setView([position.coords.latitude,position.coords.longitude], 15);
   L.circle([position.coords.latitude,position.coords.longitude],600).addTo(map);
   var myLayer = L.mapbox.featureLayer().addTo(map);
   var geojson = [];

   $.each(data.response.akteure ,function(key, akteur){

   geojson.push(
    {
    "type": "Feature",
    "geometry": {
      "type": "Point",
      "coordinates": [akteur.adresse.gps_long, akteur.adresse.gps_lat],
    },
    "properties": {
      "title": "<a href=\"https://leipziger-ecken.de/akteurprofil/" + akteur.AID + "\">" + akteur.name + "</a>",
      "description": akteur.kurzbeschreibung.replace(/<[^>]*>?/g, ''),
      "marker-color": "#2199E8",
      "marker-size": "large",
      "marker-symbol": "star"
     }
    }
   );

  });

  $.each(data.response.events ,function(key, event){

   geojson.push(
    {
    "type": "Feature",
    "geometry": {
      "type": "Point",
      "coordinates": [event.adresse.gps_long, event.adresse.gps_lat],
    },
    "properties": {
      "title": "<a href=\"https://leipziger-ecken.de/eventprofil/" + event.EID + "\">" + event.name + "</a>",
      "description": event.kurzbeschreibung.replace(/<[^>]*>?/g, ''),
      "marker-color": "#ff5217",
      "marker-size": "large",
      "marker-symbol": "rocket"
     }
    }
   );

  });

  myLayer.setGeoJSON(geojson);

   mapModalLoaded = true;

  });

 }
  
 }, 'Lade Kartendaten...');

}

function showMapError(error) {
   
  switch(error.code) {
    case error.PERMISSION_DENIED:
      alertMsg('Standort-Zugriff durch Nutzer abgelehnt');
    break;
    case error.POSITION_UNAVAILABLE:
      alertMsg('Standort-Zugriff momentan nicht möglich');
    break;
    case error.TIMEOUT:
      alertMsg('Standort-Zugriff abgebrochen - Internet aktiviert?');
    break;
    case error.UNKNOWN_ERROR:
      alertMsg('Ein unbekannter Fehler ist aufgetreten');
    break;
  }

 }

 function isScriptIncluded(src){
    var scripts = document.getElementsByTagName('script');
    for(var i = 0; i < scripts.length; i++) 
       if(scripts[i].getAttribute('src') == src) return true;
    return false;
 }

 function loadAaeModal(targetUrl, callbackFunc = null, loadText = null){
  // Attention: AS OF NOW THERE ARE .aaeModal & #aaeModal

  if (loadText) 
   $('#aaeModal .content').html('<p style="padding:15px 0;text-align:center;">' + loadText + '</p>');
 
  $('#aaeModal').fadeIn('slow');

  $('#aaeModal .button.closeBtn').click(function(){
   $('#aaeModal').fadeOut('slow');
  });

  $.ajax({
   url: targetUrl,
   data: null,
   cache: true,
   error : function(data) {
    console.log(data);
   },
   success: function(data) {

    if (callbackFunc){
     callbackFunc(data);
    } else {
     $('#aaeModal .content').html(data);
    }

   }
  });

  setHandlers();

 }

 $('.aaeModal .button.closeBtn').click(function(){
  $('.aaeModal').fadeOut('slow');
  if ($('#karibu').length){
   $('#karibu').remove();
  }
 });

 $('#alert .close').click(function(){
  $('#alert').fadeOut('fast');
 });

 $('#alert-box .close').click(function(){
  $('#alert-box').fadeOut('slow');
 });

function alertMsg(msg){

 $('#alert').slideDown('slow');
 $('#alert-content').html(msg);
 setHandlers();

}

});
