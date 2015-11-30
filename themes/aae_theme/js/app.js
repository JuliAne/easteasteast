$(document).ready(function() {

 activePopupId = false;
 activePopupCaller = false;

 $(document).scroll(function(){
  if($(window).scrollTop()>0){
   $('#mainnav').addClass('scrolled');
  } else {
   $('#mainnav').removeClass('scrolled');
  }
 });

 $('.login_first').click(function(){
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
  $('#alert-box').fadeOut('fast');
 });

 $('#presentationFilter li a').click(function(){
  var that = $(this);
  $('#presentationFilter').find('.active').removeClass('active').addClass('secondary');
  that.removeClass('secondary').addClass('active');
  return false;
 });

 $('#filterForm').submit(function(){
  var presentationValue = $('#presentationFilter').find('.active').attr('name');
  //var displayNumber
   $('<input />').attr('type', 'hidden').attr('name', 'presentation').attr('value', presentationValue).appendTo('#filterForm');
 });

 $('#project-contact a').click(function(){
  $('.aaeModal .content').html('<p style="padding:15px 0;text-align:center;">Lade Kontaktinformationen...</p>')
  $('.aaeModal').fadeIn('slow');

  var segments = $(location).attr('href').split('/')
  var actionUrl = segments[4];

  $.get("../ajax/getAkteurKontakt/" + actionUrl, function(data) {
   $('.aaeModal .content').html(data);
   $('.aaeModal .button').click(function(){
    $('.aaeModal').fadeOut('slow');
   });
  });
 });

 setHandlers();

});

setHandlers = function(){

$('#calendar .next').click(function(){

 $('#calendar').fadeOut('slow');

 $.get($(this).attr('href'), function(data){
   $('#aae_calendar').html(data);
   setHandlers();
 })

 return false;
});

$('#calendar .prev').click(function(){

 $('#calendar').fadeOut('slow');

 $.get($(this).attr('href'), function(data){
   $('#aae_calendar').html(data);
   setHandlers();
 })

 return false;
});

}
