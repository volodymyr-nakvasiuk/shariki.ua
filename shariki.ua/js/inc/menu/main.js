js.module("inc.menu.main");
js.include("jquery.min");
$(function(){
	$("#menu_main").hover(function(){
		var colorsTmp = {
			'2d002d':'rgb(45, 0, 45)',
			'960018':'rgb(150, 0, 24)',
			'1a4780':'rgb(26, 71, 128)',
			'013220':'rgb(1, 50, 32)',
			'806b2a':'rgb(128, 107, 42)'
		}
		var bg = $(this).css("background-color");
		var colors = [];
		for (var i in colorsTmp) if (colorsTmp[i]!=bg) colors.push(i);
		var h = colors[Math.floor(Math.random()*(colors.length-1))];
		$(this).css("background-color","#"+h);
	},function(){}).find("tr td").hover(function(){
		$(this).parent().children('td').removeClass('hover');
		$(this).addClass('hover');
	},
	function(){
		$(this).removeClass('hover');
	}).click(function(){
		window.location.href = $(this).children('a').attr('href');
	});
	
});