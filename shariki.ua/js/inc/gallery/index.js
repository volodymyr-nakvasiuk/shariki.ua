js.module("inc.services.detail");
js.include("jquery.min");
js.include("jquery.scrollable");
js.include("jquery.lightbox");
$(function() {
	$(window).resize(function (){
		var w = $(".site_content_center").width()-120;
		$(".scrollable").width(w);
		$(".scrollable .items div.item_box").width(w);
	}).resize();
	$(".scrollable").scrollable();
	$("a.lightbox_img").lightBox({
		txtImage      : "Изображение",
		txtOf         : "из",
		imageLoading  : "/img/lightbox-ico-loading.gif",
		imageBtnClose : "/img/lightbox-btn-close.gif",
		imageBtnPrev  : "/img/lightbox-btn-prev.gif",
		imageBtnNext  : "/img/lightbox-btn-next.gif"
	});
});