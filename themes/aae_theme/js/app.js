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
 if ($(window).width() > 640){
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

 var filterMenuToggled = false;

 $('#filter #toggleFilterForm').click(function(){
  if (filterMenuToggled) {
   $('#filter #filterForm').slideUp(400);
   $('#aaeModal').removeClass('onlyBgActive');
   $(this).html('&#x25B4;');
   filterMenuToggled = false;
  } else {
   $('#filter #filterForm').slideDown(400);
   $('#aaeModal').addClass('onlyBgActive');
   $(this).html('&#x25BE;');
   filterMenuToggled = true;
  }
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
     $('#aaeCalendar').html(data);
     setHandlers();
   });

  return false;

  });

  // Closes lightbox when hitting "ESC".
  window.addEventListener("keydown",function(event) {
     
   if (event.defaultPrevented) {
     return;
   }

   if (event.key == "Escape") {
    $('.aaeModal .button.closeBtn').click();
    $('#alert').slideUp('slow');
   }

  }, true);
   
   // TODO: Allow calendar to be swiped on mobile devices
   //$("#aae_calendar").dragend(); 

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

 $('.aaeActionBar .toggleActionBar').click(function(){
 if ($('.aaeActionBar').hasClass('toggled')){
  $('.aaeActionBar').removeClass('toggled');
 } else {
  $('.aaeActionBar').addClass('toggled');
 }
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

/*!
 * --------------------------- DRAGEND JS 0.2.0 --------------------------------
 * Version: 0.2.0
 * https://github.com/Stereobit/dragend
 * Copyright (c) 2014 Tobias Otte, t@stereob.it
 * Licensed under MIT-style license
 * Used in aae-data to allow swiping of kalender
 */
!function(a){"use strict";function b(a){function b(){}function e(){return!1}function f(a,b){var c,d;for(c in b)if(b.hasOwnProperty(c)){switch(d=b[c],c){case"height":case"width":case"marginLeft":case"marginTop":d+="px"}a.style[c]=d}return a}function g(a,b){var c;for(c in b)a[c]=b[c];return a}function h(a,b){return function(){return a.apply(b,Array.prototype.slice.call(arguments))}}function i(b,c){var d;return d=a?a(c).find("."+b):Array.prototype.slice.call(c.getElementsByClassName(b))}function j(b,c,d,e,g){var h={};h[c]=d,a?a(b).animate(h,e,g):f(b,h)}function k(a){var b=a.touches,c=b&&b.length?b:a.changedTouches;return{x:p?c[0].pageX:a.pageX,y:p?c[0].pageY:a.pageY}}function l(a,b){var d=g({},o);this.settings=g(d,b),this.container=a,this.pageContainer=c.createElement("div"),this.scrollBorder={x:0,y:0},this.page=0,this.preventScroll=!1,this.pageCssProperties={margin:0},this._onStart=h(this._onStart,this),this._onMove=h(this._onMove,this),this._onEnd=h(this._onEnd,this),this._onKeydown=h(this._onKeydown,this),this._sizePages=h(this._sizePages,this),this._afterScrollTransform=h(this._afterScrollTransform,this),this.pageContainer.innerHTML=a.cloneNode(!0).innerHTML,a.innerHTML="",a.appendChild(this.pageContainer),this._scroll=x?this._scrollWithTransform:this._scrollWithoutTransform,this._animateScroll=x?this._animateScrollWithTransform:this._animateScrollWithoutTransform,f(a,v),setTimeout(h(function(){this.updateInstance(b),this.settings.preventDrag||this._observe(),this.settings.afterInitialize.call(this)},this),10)}function m(b,c,d){a?a(b).on(c,d):b.addEventListener(c,d,!1)}function n(b,c,d){a?a(b).off(c,d):b.removeEventListener(c,d,!1)}var o={pageClass:"dragend-page",direction:"horizontal",minDragDistance:"40",onSwipeStart:b,onSwipeEnd:b,onDragStart:b,onDrag:b,onDragEnd:b,afterInitialize:b,keyboardNavigation:!1,stopPropagation:!1,itemsInPage:1,scribe:0,borderBetweenPages:0,duration:300,preventDrag:!1},p="ontouchstart"in d,q=p?"touchstart":"mousedown",r=p?"touchmove":"mousemove",s=p?"touchend":"mouseup",t={37:"left",38:"up",39:"right",40:"down"},u={pages:"No pages found"},v={overflow:"hidden",padding:0},w=function(){var a=c.createElement("div"),b="Khtml Ms O Moz Webkit".split(" "),d=b.length;return function(c){if(c in a.style)return!0;for(c=c.replace(/^[a-z]/,function(a){return a.toUpperCase()});d--;)if(b[d]+c in a.style)return!0;return!1}}(),x=w("transform");return g(l.prototype,{_checkOverscroll:function(a,b,c){var d={x:b,y:c,overscroll:!0};switch(a){case"right":if(!this.scrollBorder.x)return d.x=Math.round((b-this.scrollBorder.x)/5),d;break;case"left":if((this.pagesCount-1)*this.pageDimentions.width<=this.scrollBorder.x)return d.x=Math.round(-((Math.ceil(this.pagesCount)-1)*(this.pageDimentions.width+this.settings.borderBetweenPages))+b/5),d;break;case"down":if(!this.scrollBorder.y)return d.y=Math.round((c-this.scrollBorder.y)/5),d;break;case"up":if((this.pagesCount-1)*this.pageDimentions.height<=this.scrollBorder.y)return d.y=Math.round(-((Math.ceil(this.pagesCount)-1)*(this.pageDimentions.height+this.settings.borderBetweenPages))+c/5),d}return{x:b-this.scrollBorder.x,y:c-this.scrollBorder.y,overscroll:!1}},_observe:function(){m(this.container,q,this._onStart),this.container.onselectstart=e,this.container.ondragstart=e,this.settings.keyboardNavigation&&m(c.body,"keydown",this._onKeydown),m(d,"resize",this._sizePages)},_onStart:function(a){a=a.originalEvent||a,this.settings.stopPropagation&&a.stopPropagation(),m(c.body,r,this._onMove),m(c.body,s,this._onEnd),this.startCoords=k(a),this.settings.onDragStart.call(this,a)},_onMove:function(a){if(a=a.originalEvent||a,!(a.touches&&a.touches.length>1||a.scale&&1!==a.scale)){a.preventDefault(),this.settings.stopPropagation&&a.stopPropagation();var b=this._parseEvent(a),c=this._checkOverscroll(b.direction,-b.distanceX,-b.distanceY);this.settings.onDrag.call(this,this.activeElement,b,c.overscroll,a),this.preventScroll||this._scroll(c)}},_onEnd:function(a){a=a.originalEvent||a,this.settings.stopPropagation&&a.stopPropagation();var b=this._parseEvent(a);this.startCoords={x:0,y:0},Math.abs(b.distanceX)>this.settings.minDragDistance||Math.abs(b.distanceY)>this.settings.minDragDistance?this.swipe(b.direction):(b.distanceX>0||b.distanceX>0)&&this._scrollToPage(),this.settings.onDragEnd.call(this,this.container,this.activeElement,this.page,a),n(c.body,r,this._onMove),n(c.body,s,this._onEnd)},_parseEvent:function(a){var b=k(a),c=this.startCoords.x-b.x,d=this.startCoords.y-b.y;return this._addDistanceValues(c,d)},_addDistanceValues:function(a,b){var c={distanceX:0,distanceY:0};return"horizontal"===this.settings.direction?(c.distanceX=a,c.direction=a>0?"left":"right"):(c.distanceY=b,c.direction=b>0?"up":"down"),c},_onKeydown:function(a){var b=t[a.keyCode];b&&this._scrollToPage(b)},_setHorizontalContainerCssValues:function(){g(this.pageCssProperties,{cssFloat:"left",overflowY:"auto",overflowX:"hidden",padding:0,display:"block"}),f(this.pageContainer,{overflow:"hidden",width:(this.pageDimentions.width+this.settings.borderBetweenPages)*this.pagesCount,boxSizing:"content-box","-webkit-backface-visibility":"hidden","-webkit-perspective":1e3,margin:0,padding:0})},_setVerticalContainerCssValues:function(){g(this.pageCssProperties,{overflow:"hidden",padding:0,display:"block"}),f(this.pageContainer,{"padding-bottom":this.settings.scribe,boxSizing:"content-box","-webkit-backface-visibility":"hidden","-webkit-perspective":1e3,margin:0})},setContainerCssValues:function(){"horizontal"===this.settings.direction?this._setHorizontalContainerCssValues():this._setVerticalContainerCssValues()},_setPageDimentions:function(){var a=this.container.offsetWidth,b=this.container.offsetHeight;"horizontal"===this.settings.direction?a-=parseInt(this.settings.scribe,10):b-=parseInt(this.settings.scribe,10),this.pageDimentions={width:a,height:b}},_sizePages:function(){var a=this.pages.length;this._setPageDimentions(),this.setContainerCssValues(),"horizontal"===this.settings.direction?g(this.pageCssProperties,{height:this.pageDimentions.height,width:this.pageDimentions.width/this.settings.itemsInPage}):g(this.pageCssProperties,{height:this.pageDimentions.height/this.settings.itemsInPage,width:this.pageDimentions.width});for(var b=0;a>b;b++)f(this.pages[b],this.pageCssProperties);this._jumpToPage("page",this.page)},_calcNewPage:function(a,b){var c=this.settings.borderBetweenPages,d=this.pageDimentions.height,e=this.pageDimentions.width,f=this.page;switch(a){case"up":f<this.pagesCount-1&&(this.scrollBorder.y=this.scrollBorder.y+d+c,this.page++);break;case"down":f>0&&(this.scrollBorder.y=this.scrollBorder.y-d-c,this.page--);break;case"left":f<this.pagesCount-1&&(this.scrollBorder.x=this.scrollBorder.x+e+c,this.page++);break;case"right":f>0&&(this.scrollBorder.x=this.scrollBorder.x-e-c,this.page--);break;case"page":switch(this.settings.direction){case"horizontal":this.scrollBorder.x=(e+c)*b;break;case"vertical":this.scrollBorder.y=(d+c)*b}this.page=b;break;default:this.scrollBorder.y=0,this.scrollBorder.x=0,this.page=0}},_onSwipeEnd:function(){this.preventScroll=!1,this.activeElement=this.pages[this.page*this.settings.itemsInPage],this.settings.onSwipeEnd.call(this,this.container,this.activeElement,this.page)},_jumpToPage:function(a,b){a&&this._calcNewPage(a,b),this._scroll({x:-this.scrollBorder.x,y:-this.scrollBorder.y})},_scrollToPage:function(a,b){this.preventScroll=!0,a&&this._calcNewPage(a,b),this._animateScroll()},_scrollWithTransform:function(a){var b="horizontal"===this.settings.direction?"translateX("+a.x+"px)":"translateY("+a.y+"px)";f(this.pageContainer,{"-webkit-transform":b,"-moz-transform":b,"-ms-transform":b,"-o-transform":b,transform:b})},_animateScrollWithTransform:function(){var a="transform "+this.settings.duration+"ms ease-out",b=(this.container,this._afterScrollTransform);f(this.pageContainer,{"-webkit-transition":"-webkit-"+a,"-moz-transition":"-moz-"+a,"-ms-transition":"-ms-"+a,"-o-transition":"-o-"+a,transition:a}),this._scroll({x:-this.scrollBorder.x,y:-this.scrollBorder.y}),m(this.container,"webkitTransitionEnd",b),m(this.container,"oTransitionEnd",b),m(this.container,"transitionend",b),m(this.container,"transitionEnd",b)},_afterScrollTransform:function(){var a=this.container,b=this._afterScrollTransform;this._onSwipeEnd(),n(a,"webkitTransitionEnd",b),n(a,"oTransitionEnd",b),n(a,"transitionend",b),n(a,"transitionEnd",b),f(this.pageContainer,{"-webkit-transition":"","-moz-transition":"","-ms-transition":"","-o-transition":"",transition:""})},_scrollWithoutTransform:function(a){var b="horizontal"===this.settings.direction?{marginLeft:a.x}:{marginTop:a.y};f(this.pageContainer,b)},_animateScrollWithoutTransform:function(){var a="horizontal"===this.settings.direction?"marginLeft":"marginTop",b="horizontal"===this.settings.direction?-this.scrollBorder.x:-this.scrollBorder.y;j(this.pageContainer,a,b,this.settings.duration,h(this._onSwipeEnd,this))},swipe:function(a){this.settings.onSwipeStart.call(this,this.container,this.activeElement,this.page),this._scrollToPage(a)},updateInstance:function(a){if(a=a||{},"object"==typeof a&&g(this.settings,a),this.pages=i(this.settings.pageClass,this.pageContainer),!this.pages.length)throw new Error(u.pages);this.pagesCount=this.pages.length/this.settings.itemsInPage,this.activeElement=this.pages[this.page*this.settings.itemsInPage],this._sizePages(),this.settings.jumpToPage&&(this.jumpToPage(a.jumpToPage),delete this.settings.jumpToPage),this.settings.scrollToPage&&(this.scrollToPage(this.settings.scrollToPage),delete this.settings.scrollToPage),this.settings.destroy&&(this.destroy(),delete this.settings.destroy)},destroy:function(){var a=this.container;n(a,q),n(a,r),n(a,s),n(c.body,"keydown",this._onKeydown),n(d,"resize",this._sizePages),a.removeAttribute("style");for(var b=0;b<this.pages.length;b++)this.pages[b].removeAttribute("style");a.innerHTML=this.pageContainer.innerHTML},scrollToPage:function(a){this._scrollToPage("page",a-1)},jumpToPage:function(a){this._jumpToPage("page",a-1)}}),a&&(a.fn.dragend=function(b){return b=b||{},this.each(function(){var c=a(this).data("dragend");c?c.updateInstance(b):(c=new l(this,b),a(this).data("dragend",c)),"string"==typeof b&&c.swipe(b)}),this}),l}var c=document,d=a;"function"==typeof define&&"object"==typeof define.amd&&define.amd?define(function(){return b(d.jQuery||d.Zepto)}):d.Dragend=b(d.jQuery||d.Zepto)}(window);