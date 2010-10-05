js.module("inc.menu.main");
js.include("jquery.min");
$(function(){
	$("#menu_main").hover(function(){
		var h = (35+Math.floor(Math.random()*85)).toString(16);
		$(this).css("background-color","#"+h+"00"+h);
	}).find("tr td").hover(function(){
		$(this).parent().children('td').removeClass('hover');
		$(this).addClass('hover');
	},
	function(){
		$(this).removeClass('hover');
	}).click(function(){
		window.location.href = $(this).children('a').attr('href');
	});
	
});