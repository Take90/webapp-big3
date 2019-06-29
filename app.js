$(function(){
    //ハンバーガーメニュー
    $('.js-toggle-sp-menu').on('click', function () {
        $(this).toggleClass('active');
        $('.js-toggle-sp-menu-target').toggleClass('active');
    });

    $('.menu-link').on('click', function(){
        $('.nav-menu').toggleClass('active');
    });
});