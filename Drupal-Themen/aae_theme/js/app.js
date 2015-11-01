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
  $('#alert').fadeUp('fast');
 });

 $('#presentationFilter li a').click(function(){
  var that = $(this);
  $('#presentationFilter').find('.active').removeClass('active').addClass('secondary');
  that.removeClass('secondary').addClass('active');
  return false;
 });

 $('#filterForm').submit(function(){
  var presentationValue = $('#presentationFilter').find('.active').attr('name');
   $('<input />').attr('type', 'hidden').attr('name', 'presentation').attr('value', presentationValue).appendTo('#filterForm');
 });
});
