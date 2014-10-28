$(document).ready(function(){
	if(location.href.indexOf("localhost",0)>0){
		$("body").after("<script src=\"http://" + (location.host || "localhost").split(":")[0] + ":35729/livereload.js?snipver=1\"></" + "script>");
	}
	$('.addressselect').customselect();
});