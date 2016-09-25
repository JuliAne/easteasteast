$(document).ready(function() {

/* For akteure- and events-AJAX-loader:
    if ($(document).height() <= $(window).scrollTop() + $(window).height()) {
        alert("End Of The Page");
    } */

 activePopupId = false;
 activePopupCaller = false;

 setInterval(function(){$('#alert').slideUp('slow')}, 10000);

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

 $('#alert .close').click(function(){
  $('#alert').fadeOut('fast');
 });

 $('#alert-box .close').click(function(){
  $('#alert-box').fadeOut('slow');
 });

 $('#presentationFilter li a').click(function(){
  var that = $(this);
  $('#presentationFilter').find('.active').removeClass('active').addClass('secondary');
  that.removeClass('secondary').addClass('active');
  if ($(that).attr('name') == 'map'){
   return false;
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
  } else if ($('#events #filter').length && $('#timespace .activeTime').length && presentationValue != 'calendar'){
   // eventspage.tpl
   // Allow filtering for timespan; but only in timeline-mode!
   $('<input />').attr('type', 'hidden').attr('name', 'timespan').attr('value', $('#timespace .activeTime').first().attr('data-month') + '-' + $('#timespace .activeTime').last().attr('data-month')).appendTo('#filterForm');
  }
  return true;

 });

 $('#inviteBtn').click(function(){
   $('.aaeModal').fadeIn('slow');

   setHandlers();
 })

 $('#project-contact a').click(function(){
  $('.aaeModal .content').html('<p style="padding:15px 0;text-align:center;">Lade Kontaktinformationen...</p>');
  $('.aaeModal').fadeIn('slow');

  var segments = $(location).attr('href').split('/');
  var actionUrl = segments[4];

  $.get("../ajax/getAkteurKontakt/" + actionUrl, function(data) {
   $('.aaeModal .content').html(data);
   $('.aaeModal .button.closeBtn').click(function(){
    $('.aaeModal').fadeOut('slow');
   });
  });
 });

 // Closes lightbox when hitting "ESC".
 window.addEventListener("keydown", function (event) {
     
  if (event.defaultPrevented) {
    return; // Should do nothing if the key event was already consumed.
  }

  if (event.key == "Escape") {
   $('.aaeModal .button.closeBtn').click();
   $('#alert').fadeOut('fast');
  }

  event.preventDefault();
}, true);

 setHandlers();

});

setHandlers = function(){

$('#calendar .next, #calendar .prev').click(function(){

 $('#calendar').fadeOut('slow');

 $.get($(this).attr('href'), function(data){
   $('#aae_calendar').html(data);
   setHandlers();
 });

 return false;
});

 $('.aaeModal .button.closeBtn').click(function(){
  $('.aaeModal').fadeOut('slow');
  if ($('#karibu').length){
   $('#karibu').remove();
  }
 });

}
