$(document).ready(function(){

  var mobileBreakpoint = 650; // px

  $('#aboutEcken .aaeHeadline h1, #aboutEcken #bottomJournalLink').click(function(){
   if (!$(this).hasClass('active')){
    $('#aboutEcken .aaeHeadline .active').removeClass('active');
    if ($('body').hasClass('fp-viewing-1-0')){
     $('#aboutEcken #journalLink').addClass('active');
    } else {
     $('#aboutEcken #dieEckenLink').addClass('active');
    }
    $.fn.fullpage.moveSlideRight();
   }
   return false;
  });

  triggerFormClick = function(){
   $('#triggerFilterBezirke').submit();
  }

  if ($(document).width() > mobileBreakpoint){

  $('#fullpage').fullpage({
   scrollBar: true,
   navigation: true,
   navigationPosition: 'right',
   navigationTooltips: ['Start', 'Die Ecken', 'Events', 'Akteure', 'Footer'],
   fixedElements: '#mainnav',
   fitToSection : false,

   onLeave: function(anchor, index){
    if (index == 1) {
     $('#fp-nav').fadeOut('fast');
    } else {
     $('#fp-nav').fadeIn('slow');
    }
   },

   afterRender: function () {
    $('#aboutEcken .fp-controlArrow').hide();
    $('#fp-nav').fadeOut('fast');
   }
  });

/* Generated by mapstarter.com: */

var width = 600,
    height = 431;

var projection = d3.geo.mercator()
    .scale(215887.47932558382)
    .center([12.468645000000002,51.338734074877436]) 
    .translate([width/2,height/2])

var path = d3.geo.path()
    .projection(projection);

//Create an SVG
var svg = d3.select("#startSection").append("svg")
    .attr("width", width)
    .attr("height", height);

var features = svg.append("g")
    .attr("class","features");

var tooltip = d3.select("#startSection").append("div").attr("class","tooltip");

d3.json("sites/all/themes/aae_theme/bezirke.geojson",function(error,geodata) {
  if (error) return console.log(error);

  //Create a path for each map feature in the data
  features.selectAll("path")
    .data(geodata.features)
    .enter()
    .append("path")
    .attr("d",path)
    .on("mouseover",showTooltip)
    .on("mousemove",moveTooltip)
    .on("mouseout",hideTooltip)
    .on("click",clicked);

});

// Add optional onClick events for features here
// d.properties contains the attributes (e.g. d.properties.name, d.properties.population)
function clicked(d,i) {

}

//Position of the tooltip relative to the cursor
var tooltipOffset = {x: 5, y: -25};

//Create a tooltip, hidden at the start
function showTooltip(d) {
  moveTooltip();

  tooltip.style("display","block")
      .text(d.properties.title);
}

//Move the tooltip to track the mouse
function moveTooltip() {
  tooltip.style("top",(d3.event.pageY+tooltipOffset.y)+"px")
      .style("left",(d3.event.pageX+tooltipOffset.x)+"px");
}

//Create a tooltip, hidden at the start
function hideTooltip() {
  tooltip.style("display","none");
}

$('.pcard').hover(function(){
 if ($(this).hasClass('hasBg')){
  $('#fullpage #bgImageWrapper').css({'background-image' : $(this).find('header').css('background-image'),'opacity' : 1});
 }
}, function(){
 $('#fullpage #bgImageWrapper').css({'opacity':0});
});


  } // end if-desktop-or-up

});