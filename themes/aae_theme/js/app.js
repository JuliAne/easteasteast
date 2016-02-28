$(document).ready(function() {

 activePopupId = false;
 activePopupCaller = false;

 setInterval(function(){$('#alert').slideUp('slow')}, 10000);

 $(document).scroll(function(){
  if($(window).scrollTop()>0){
   $('#mainnav').addClass('scrolled');
   $('.pace-progress').addClass('pace-scrolled');
  } else {
   $('#mainnav').removeClass('scrolled');
   $('.pace-progress').removeClass('pace-scrolled');
  }
 });

 $('#timespace .slider').on('moved.zf.slider', function() {
  var leftS = $('#timespace .sh-1').css('left');
  var rightS = $('#timespace .sh-2').css('left');

  //$('#timespace ul')

  //14.25
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
  $('#alert-box').fadeOut('slow');
 });

 $('#presentationFilter li a').click(function(){
  var that = $(this);
  $('#presentationFilter').find('.active').removeClass('active').addClass('secondary');
  that.removeClass('secondary').addClass('active');
  //return false;
 });

 $('#filterForm').submit(function(){
  if ($('#akteure #filter').length){
   // Add selected presentation mode to URL
   var presentationValue = $('#presentationFilter').find('.active').attr('name');
   $('<input />').attr('type', 'hidden').attr('name', 'presentation').attr('value', presentationValue).appendTo('#filterForm');
  }
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
