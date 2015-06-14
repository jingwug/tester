// JavaScript Document
$(function(){
	$(".dropdown01 p").click(function(){
		var ul = $(".dropdown01 ul");
		if(ul.css("display")=="none"){
			ul.slideDown("fast");
		}else{
			ul.slideUp("fast");
		}
	});
	$(".dropdown01 ul li a").click(function(){
		var txt = $(this).text();
		$(".dropdown01 p").html(txt);
		var value = $(this).attr("rel");
		$(".dropdown01 ul").hide();
		$(".result").html("您选择了"+txt+"，值为："+value);
	});
	$(".dropdown02 p").click(function(){
		var ul = $(".dropdown02 ul");
		if(ul.css("display")=="none"){
			ul.slideDown("fast");
		}else{
			ul.slideUp("fast");
		}
	});
	$(".dropdown02 ul li a").click(function(){
		var txt = $(this).text();
		$(".dropdown02 p").html(txt);
		var value = $(this).attr("rel");
		$(".dropdown02 ul").hide();
		$(".result").html("您选择了"+txt+"，值为："+value);
	});
	
});