;
(function ($) {
    $(function () {
        //城市选择
        $(".ct_choose").on('click', function () {
            var ciPanel = $("#ui_city_plugs");
            if (!ciPanel.data("show")) {
                $(this).addClass("hasShowPanel");
                ciPanel.slideDown().data("show", true);
            } else {
                $(this).removeClass("hasShowPanel");
                ciPanel.slideUp().data("show", false);
            }

        });
        $(".ui_close").on("click", function () {
            $(".ct_choose").removeClass("hasShowPanel");
            $("#ui_city_plugs").slideUp();
        });
        var cBtns = $(".ui_city_cType>ul>li"), cCitis = $(".ui_city_List");
        cBtns.on("click", function () {
            var idx = $.inArray(this, cBtns.get());
            $(this).find("a").addClass("select");
            $(this).siblings().find("a").removeClass("select");
            cCitis.eq(idx).siblings(':not(.ui_city_cType)').addClass('none').end().removeClass('none');

        });
        //我的订单
        $("ul.yhxxx>li.help").on("mouseenter", function (ev) {
            var $b = $(this);
            $b.addClass('bc_white');
            $("ul.help_list").show().on("mouseleave", function () {
                $(this).hide();
                $b.removeClass('bc_white');
            });
        });
        /*==========================未注册页面======================*/
        //轮播
        $("#lbbox_movies_index").lunbo({
            begin: 1,
            speed: 2000,
            selectedClass: "active",
            btns: ".title ul.point li",
            pics: ".pic>li",
            lbtn: "#movies_l_btn",
            rbtn: "#movies_r_btn",
            txts: ".title p"
        });
        $('.left_clum1 .pic').hover(function () {
            $('.text', this).stop().animate({
                height: '240px'
            });
        }, function () {
            $('.text', this).stop().animate({
                height: '0'
            });
        });
        //本周热映
        $(".right_clum1 ul#chart2").hoverShowSiblingsHide({
            initShow: 1,
            showEl: "li",
            elTitle: '.fig_tt',
            elContent: ".fig_wd2"
        });
        $(".right_clum1 ul#chart1").hoverShowSiblingsHide({
            initShow: 1,
            showEl: "li",
            elTitle: '.fig_tt',
            elContent: ".fig_wd",
            afterShow: function () {
                $(this).find('.fig_tt em').hide();
            },
            afterHide: function () {
                $(this).find('.fig_tt em').show();
            }
        });
        //电影资讯
        $(".clum4_rt ul#moves_infos").hoverShowSiblingsHide({
            initShow: 1,
            showEl: "li",
            elTitle: '.rt_title',
            elContent: ".rt_word",
            showElSelectClass: null
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

        $('#moves_zzry').longlunbo({
            box: "#left_movies_1",
            tagName: '#moves_zzry li',
            auto: false,
            duration: 500,
            timeInterval: 2000, //
            direction: 'left', // 滚动方向
            visual: 4, //可视数
            prev: '#left_movies_1 .l_btn',
            next: '#left_movies_1 .r_btn',
            amount: 1, // 滚动数  默认是1
            loop: false
        });
        $('#moves_zzry2').longlunbo({
            box: "#left_movies_2",
            tagName: '#moves_zzry2 li',
            auto: true,
            duration: 500,
            timeInterval: 2000, //
            direction: 'right', // 滚动方向
            visual: 4, //可视数
            prev: '#left_movies_2 .l_btn',
            next: '#left_movies_2 .r_btn',
            amount: 1, // 滚动数  默认是1
            loop: true
        });
        $("#_choose_area1").longlunbo({
            box: "#_choose_area1box",
            tagName: '#_choose_area1 a',
            auto: false,
            duration: 500,
            timeInterval: 2000, //
            direction: 'left', // 滚动方向
            visual: 11, //可视数
            alwaysShowButton: true,
            prev: '#_choose_area1_rbtn',
            next: '#_choose_area1_lbtn',
            amount: 1, // 滚动数  默认是1
            loop: false,
            onLeftEnd: function () {
                $('#_choose_area1_lbtn').removeClass('op06');
                $('#_choose_area1_rbtn').addClass('op06');
            },
            onRightEnd: function () {
                $('#_choose_area1_lbtn').addClass('op06');
                $('#_choose_area1_rbtn').removeClass('op06');
            }
        });


    });
})(jQuery);
