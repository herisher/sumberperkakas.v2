$(document).ready(function(){
	// In the following, please adjust the width and height of the image zoom
	$(".zoomup").fancyZoom({width:500, height:500, scaleImg: true, closeOnClick: true});
	// The following is a thumbnail change logic
	$(".zoomup").html('<img src="'+$(".gallery1").children("img").attr("src")+'" width="270" height="270">');
	$(".zoomphoto").html('<img src="'+$(".gallery1").children("img").attr("src")+'">');
	$(".detailgallery a").on("click", function() {
		$(".zoomup").html('<img src="'+$(this).children("img").attr("src")+'" width="270" height="270">');
		$(".zoomphoto").html('<img src="'+$(this).children("img").attr("src")+'">');
	});
});