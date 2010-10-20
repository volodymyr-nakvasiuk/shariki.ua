js.module("inc.services.detail");
js.include("jquery.min");
js.include("jquery.scrollable");
js.include("jquery.lightbox");
$(function() {
	$(".scrollable").scrollable({
		keyboard: false
	});
	$(window).resize(function (){
		$(".scrollable").hide();
		var w = $(".site_content_center").width()-320;
		$(".scrollable").width(w);
		var wi = 140;
		var mi = 20;
		var c = Math.floor(w/(wi+mi));
		var m = (w-(wi+mi)*c)/c;
		$(".scrollable .items div.item_box").css('margin-left', mi+m+'px');
		var api = $(".scrollable").data("scrollable");
		api.getConf().moveCount = c;
		api.seekTo(0,0);
		$(".scrollable").show();
	}).resize();
	$("a.lightbox_img").lightBox({
		txtImage      : "Изображение",
		txtOf         : "из",
		imageLoading  : "/img/lightbox-ico-loading.gif",
		imageBtnClose : "/img/lightbox-btn-close.gif",
		imageBtnPrev  : "/img/lightbox-btn-prev.gif",
		imageBtnNext  : "/img/lightbox-btn-next.gif"
	});
});