 $(document).ready(function () {



$('.slot').findHeights();
$('.meeting').findHeights();
$('.marker').findHeights();
$('.schedule').findHeights();
$('.heading').findHeights();

$('.marker').css("overflow", "visible");
$('.title a').css("font-size", "0.8em");
$('.meeting').css("z-index", "500");
$('.title a').css("line-height", "0.7em");

 });
 
 $(function() {
  $("a").attr("target","_blank");
});
 
 $.fn.findHeights = function() {
 
 var multiplierH = 0.75;
 var multiplierW = 0.9;
 
	$(this).each(function(){
	
	var curHeight = parseInt($(this).height());
	var curWidth = parseInt($(this).width());
	var curTop = parseInt($(this).css('top'));
	var curLeft = parseInt($(this).css('left'));

	var newHeight = (curHeight * multiplierH);
	var newWidth = (curWidth * multiplierW);
	var newTop = (curTop * multiplierH);
	var newLeft = (curLeft * multiplierW);
	

	$(this).css({'height': newHeight, 'min-height': newHeight, 'width': newWidth, 'min-width': newWidth, 'top' : newTop, 'left' : newLeft});
	
	});
	return this;
};

