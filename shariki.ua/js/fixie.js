js.module("fixie");
js.include("jquery.min");
function ie6nomore_close(){
	$("#ie6nomore").css("display","none");
	$('div.menu_button_refer').css('top', '');
	return false;
}
if ($.browser.msie){
	jQuery(function($) {
		$("a[target='_blank']").click(function(){
			window.open($(this).attr("href"), '_blank', 'scrollbars=1,fullscreen=1,status=1,resizable=1,location=1,toolbar=1,menubar=1,directories=1');
			return false;
		});
		var verIE = parseInt($.browser.version.substr(0,1));
		/*
		if (verIE < 7){
			$("#ie6nomore").html(
				"<div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;margin-top:10px;'>" +
				"<div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='javascript:void(0);' onclick='javascript:ie6nomore_close();'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>" +
				"<div style='width: 940px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>" +
				"<div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>" +
				"<div style='width: 275px; float: left; font-family: Arial, sans-serif;'>" +
				"<div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>Ваш браузер устарел!</div>" +
				"<div style='font-size: 12px; margin-top: 6px; line-height: 13px;'>Для лучшей и более быстрой работы сайта установите современный браузер:</div>" +
				"</div>" +
				"<div style='width: 75px; float: left;'><a href='http://www.google.com/chrome?hl=uk' target='_blank'><img src='/img/browsers/chrome.jpg' style='border: none;' alt='Скачать Google Chrome'/></a></div>" +
				"<div style='width: 75px; float: left;'><a href='http://www.opera.com/browser/download/' target='_blank'><img src='/img/browsers/opera.jpg' style='border: none;' alt='Скачать Opera 10.50'/></a></div>" +
				"<div style='width: 73px; float: left;'><a href='http://www.apple.com/ru/safari/download/' target='_blank'><img src='/img/browsers/safari.jpg' style='border: none;' alt='Скачать Safari 4'/></a></div>" +
				"<div style='width: 75px; float: left;'><a href='http://www.mozilla-europe.org/uk/firefox/' target='_blank'><img src='/img/browsers/firefox.jpg' style='border: none;' alt='Скачать Firefox 3.5'/></a></div>" +
				"<div style='width: 75px; float: left;'><a href='http://www.microsoft.com/ukraine/windows/default.mspx' target='_blank'><img src='/img/browsers/ie.jpg' style='border: none;' alt='Скачать Internet Explorer 8'/></a></div>" +
				"<div style='width: 75px; float: left;'><a href='http://www.flock.com/versions/' target='_blank'><img src='/img/browsers/flock.jpg' style='border: none;' alt='Скачать Flock 2.5'/></a></div>" +
				"<div style='width: 75px; float: left;'><a href='http://www.maxthon.com/download.htm' target='_blank'><img src='/img/browsers/maxthon.jpg' style='border: none;' alt='Скачать Maxthon Browser 2.5'/></a></div>" +
				"<div style='float: left;'><a href='http://www.seamonkey-project.org/releases/' target='_blank'><img src='/img/browsers/seamonkey.jpg' style='border: none;' alt='Скачать SeaMonkey 2.0.3'/></a></div>" +
				"</div>" +
				"</div>"
			);
			$("div.menu_button_refer").css("top", (verIE==7?220:207)+"px");
		}
		*/
	});
}