$(document).ready(function(){

 /**
  * ACTIVATE SOME PLUGINS ONLY IF A MOBILE DEVICE
  * IS USED (640px)


 if($(window).width() < 640) {*/
  $('#fullpage').fullpage({
	 /*anchors: ['images','home', 'blog', 'events', 'akteure', 'ueberuns'],
	 sectionsColor: ['#FFF', '#F7F7F7', '#F7F7F7', '#F7F7F7', '#FFF'],*/
	 scrollBar: true,
   navigation: true,
	 navigationPosition: 'right',
	 navigationTooltips: ['we','Start', 'Journal', 'Veranstaltungen', 'Akteure', 'Footer'],
   fixedElements: '#mainnav',
   fitToSection : false,

   afterLoad: function(anchor, index){
      if (index == 2) {
       $('#fp-nav').fadeOut('fast');
       $('#imgSlideSection').removeClass('blurme');
      } else {
       $('#fp-nav').fadeIn('slow');
       $('#imgSlideSection').addClass('blurme');
      }
    },

    afterRender: function () {

     $('#imgSlideSection .fp-controlArrow').hide();

     timer = setInterval(function () {
      $.fn.fullpage.moveSlideRight();
     }, 7000);

     $('#fullpage #slideSection').hover(function(ev){
      clearInterval(timer);
     }, function(ev){
      timer = setInterval(function () {
      $.fn.fullpage.moveSlideRight();
     }, 7000);
     });
    }

  });

});
