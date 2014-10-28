$(document).ready(function(){
	if($(".arriveblock .itemname")[0]){
		$(".arriveblock .catalogset").each(function(){
			selectorHeitChange($(this).find(".itemname"));
			selectorHeitChange($(this).find(".itemshop"));
		});
	}
	if($(".pickupblock .itemname")[0]){
		$(".pickupblock .catalogset").each(function(){
			selectorHeitChange($(this).find(".itemname"));
			selectorHeitChange($(this).find(".itemshop"));
		});
	}
	if($(".catalogset")[0]){
		$(".catalogset").each(function(){
			selectorHeitChange($(this).find(".itemname"));
			selectorHeitChange($(this).find(".itemshop"));
		});
	}
	function selectorHeitChange(selector){
		var framehight = 0;
		selector.each(function(){
			if(framehight < Number($(this).css("height").split("px")[0])){
				framehight = Number($(this).css("height").split("px")[0]);
			}
		});
		selector.css({"height" : framehight+"px"});
	}
});

