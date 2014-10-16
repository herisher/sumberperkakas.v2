var responsiveflagMenu = false;
var categoryMenu = $('ul.sf-menu');
var mCategoryGrover = $('.sf-contener .cat-title');

$(document).ready(function(){
	responsiveMenu();
	$(window).resize(responsiveMenu);
});

// check resolution
function responsiveMenu()
{
   if ($(document).width() <= 767 && responsiveflagMenu == false)
	{
		menuChange('enable');
		responsiveflagMenu = true;
	}
	else if ($(document).width() >= 768)
	{
		menuChange('disable');
		responsiveflagMenu = false;
	}
}

// init Super Fish Menu for 767px+ resolution
function desktopInit()
{
	mCategoryGrover.off();
	mCategoryGrover.removeClass('active');
	$('.sf-menu > li > ul').removeClass('menu-mobile').parent().find('.menu-mobile-grover').remove();
	$('.sf-menu').removeAttr('style');
	categoryMenu.superfish('init');
	//add class for width define
	$('.sf-menu > li > ul').addClass('submenu-container clearfix'); 
	 // loop through each sublist under each top list item
    $('.sf-menu > li > ul').each(function(){
        i = 0;
        //add classes for clearing
        $(this).each(function(){ 
                if ($(this).attr('id') != "category-thumbnail") 
                {
                    i++;
                    if(i % 2 == 1)
                        $(this).addClass('first-in-line-xs');
                    else if (i % 5 == 1)
                        $(this).addClass('first-in-line-lg');
                }
        });
    });
}

function mobileInit()
{
	categoryMenu.superfish('destroy');
	$('.sf-menu').removeAttr('style');

	mCategoryGrover.on('click', function(){
		$(this).toggleClass('active').parent().find('ul.menu-content').stop().slideToggle('medium');
	});

	$('.sf-menu > li > ul').addClass('menu-mobile clearfix').parent().prepend('<span class="menu-mobile-grover"></span>');

	$(".sf-menu .menu-mobile-grover").on('click touchstart', function(){
		var catSubUl = $(this).next().next('.menu-mobile');
		if (catSubUl.is(':hidden'))
		{
			catSubUl.slideDown();
			$(this).addClass('active');
		}
		else
		{
			catSubUl.slideUp();
			$(this).removeClass('active');
		}
		return false;
	});

	if ('ontouchstart' in document.documentElement)
	{
		$('#block_top_menu > ul:first > li > a').on('click', function(e){
			if ($(this).parent('li').find('ul').length)
				e.preventDefault();
		});
	}
}

// change the menu display at different resolutions
function menuChange(status)
{
	status == 'enable'? mobileInit(): desktopInit();
}
