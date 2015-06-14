// JavaScript Document
$(function() {
    //城市选择
    $(".ct_choose").on('click', function() {
        var ciPanel = $("#ui_city_plugs");
        if (!ciPanel.data("show")) {
            $(this).addClass("hasShowPanel");
            ciPanel.slideDown().data("show", true);
        } else {
            $(this).removeClass("hasShowPanel");
            ciPanel.slideUp().data("show", false);
        }

    });
    $(".ui_close").on("click", function() {
        $(".ct_choose").removeClass("hasShowPanel");
        $("#ui_city_plugs").slideUp();
    });
    var cBtns = $(".ui_city_cType>ul>li"), cCitis = $(".ui_city_List");
    cBtns.on("click", function() {
        var idx = $.inArray(this, cBtns.get());
        $(this).find("a").addClass("select");
        $(this).siblings().find("a").removeClass("select");
        cCitis.eq(idx).siblings(':not(.ui_city_cType)').addClass('none').end().removeClass('none');

    });
    //我的订单
    $("ul.yhxxx>li.help").on("mouseenter", function(ev) {
        var $b = $(this);
        $b.addClass('bc_white');
        $("ul.help_list").show().on("mouseleave", function() {
            $(this).hide();
            $b.removeClass('bc_white');
        });
    });
    //首页目录
    var menus = $("ul.nav_meun_ul>li");
    if (window.sessionStorage) {
        var idx = window.sessionStorage["_tmp_menu_idx"],posStr= window.sessionStorage["_tmp_menu_idx_pos"];
        if (idx) {
            //menus.eq(parseInt(idx))
            $("ul.nav_meun_ul .slider").css(JSON.parse(posStr));
        }
    }
    $.each(menus, function (i, v) {
        $(v).on({
            "mouseenter.menu": function () {
                var w = $(this).width(), l = $(this).position().left;
                $("ul.nav_meun_ul .slider").stop(true, false).animate({
                    left: (l + 16) + "px",
                    width: (w - 32) + "px"
                }, 300);
            },
            "click.menu": function () {
                window.sessionStorage["_tmp_menu_idx"] = i;
                var w = $(this).width(), l = $(this).position().left;
                var pos = {
                    left: (l + 16) + "px",
                    width: (w - 32) + "px"
                };
                window.sessionStorage["_tmp_menu_idx_pos"] = JSON.stringify(pos);
            }
        });
    });
    //表单相关
    $("label.inpt").on({
        "focusin" : function(ev) {
            $(this).find(".overTxtLabel").hide();
        },
        "focusout" : function(ev) {
            if (!$.trim($(this).find(".G_input").val())) {
                $(this).find(".overTxtLabel").show();
            }

        }
    });
   

});
