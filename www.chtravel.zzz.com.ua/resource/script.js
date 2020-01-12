
// for make padding for content
$('document').ready(function() {
	$('.content').css("padding-top",$('.header').css('height'));
	//for map
	if ($('.svg').length>0) {
		$.post('/ajax_functions',{func: "get_active_countries"}, function(data) {
			data = 'data = '+ data +';';
			eval(data);
			data.forEach(function(el) {
				$('#'+el).attr('class', 'land active-country');
			});

			$('.active-country').click(function() {
				if (active)
				window.location.href = "/country/" + this.getAttribute('id'); 
			} );
		});
	}
});

window.onresize = function(event) {
	$('.content').css("padding-top",$('.header').css('height'));
	$('.slider').css({height: $('.slider img').css('height')});
};

// navigation line of menu in header

$(".menu li").mouseover(function () {
	$(".nav-line").stop();
	$(".nav-line").animate({left: ($(this).position().left), width: ($(this).outerWidth())}, 150, "linear");
});
$(".menu li").mouseout(function () {
	$(".nav-line").stop();
	$(".nav-line").animate({left: ($(this).position().left), width: 0}, 150, "linear");
});

// modal box for login/register menu in header

$('.login').click(function() {
	$.post('/ajax_functions',{func:"load_login_block"},function(data) {
		$('body').prepend(data);
	//for captcha — max 5 'Тільки цифри!'

		$('.register').click(function() {
			$('.login-box').remove();
			$.post('/ajax_functions',{func:"load_register_block"}, function(data) {
				$('.modal-bg').after(data);

				//for login — 3-10 'Не менше 3 i не більше 10 латинських символів або цифр';
				//for phone —  'Тільки цифри і знак +';
				//for password — 6-15 'Не менше 6 i не більше 15 латинських символів або цифр';
				//for captcha — max 5 'Тільки цифри!';

				var err = false;

				$('input[name="login"]').focusout(function() {
					if (this.value.length < 3 || this.value.length > 10) 
						$('input[name="login"] + .red').text('Довжина логіну має бути більше 3 та менше 10 символів');
					else $.post('/ajax_functions',{func: "check_login", param: this.value}, function(data) {
						if(data) $('input[name="login"] + .red').text('Такий логін вже використовується');
							else $('input[name="login"] + .red').text('');
					});
				});

				$('input[name="phone"]').focusout(function() {
					if (/\+?\d/.test(this.value)) $('input[name="phone"] + .red').text('');
					else $('input[name="phone"] + .red').text('Номер телефону має містити цифри та може містити знак "+"');
				});

				$('.modal-close').click(function() {
					$('.modal-bg').remove();
					$('.modal-box').remove();
				});
			});
		});

		$('.modal-close').click(function() {
			$('.modal-bg').remove();
			$('.modal-box').remove();
		});
	});
});


//search for menu in header

$('.search').click(function() {
	if ($('.search-field-box').length == 0) {
		var b;
		if ($('.currency-box').length > 0) {
			$('.currency-box').stop();
			$('.currency-box').animate({height: "0em"},250);
			setTimeout(function() {$('.currency-box').remove();}, 250);
			b = true;
		}
		$('.search-field-box').stop();
		$('.header').append('<div class="search-field-box"><div class="box"><input class="search-field" type="text" name="search-ch"><div class="search-result"></div></div></div>');
		$('.search-field-box').delay((b)?250:0).animate({height: "5em"},250);
		$('.search-field').keyup(function() {
			$.post("/ajax_functions",{func:"search_field", param:this.value}, function(data) {
				$('.search-result').css('display',(($('.search-field').val())?'block':'none'));
				$('.search-result').html((!$('.search-field').val())?'':((data)?data:'Пошук не дав результатів'));
			});
		});
	}
	else {
		$('.search-field-box').stop();
		$('.search-field-box').animate({height: "0em"},250);
		setTimeout(function() {$('.search-field-box').remove();}, 250);
	}
});

$('.currency').click(function() {
	if ($('.currency-box').length == 0) {
		var b;
		if (($('.search-field-box').length > 0)) {
			$('.search-field-box').stop();
			$('.search-field-box').animate({height: "0em"},250);
			setTimeout(function() {$('.search-field-box').remove();}, 250);
			b = true;
		}
		$('.currency-box').stop();
		$.post('/ajax_functions',{func: "add_currency_box"}, function(data) {
				$('.header .box').append(data);
				$('.currency-box').delay((b)?250:0).animate({height: "3em"},250);
				$('select').change(function() {
					$.post('/ajax_functions',{func: this.name, param: this.value});
					location.reload();
				});
		});
	}
	else {
		$('.currency-box').stop();
		$('.currency-box').animate({height: "0em"},250);
		setTimeout(function() {$('.currency-box').remove();}, 250);
	}
});

//Slider
var slideNum = 1;
var slideLength = $('.slider li').length;
var autoSlide = true;
var slideInterval = setInterval(nextSlide,7000);

$('document').ready(function() {
	$('.slider').append("<div class='slider-nav'></div>");
	for (i = 1; i <= slideLength; i++) {$('.slider-nav').append("<div class='slider-item"+((i==1)?" active":'')+"' rel='"+i+"'></div> ");}
	$('.slider .slider-item').click(function() {
		$('.slider li:nth-child('+ (slideNum) +')').removeClass('active');
		$('.slider .slider-item:nth-child('+ (slideNum) +')').removeClass('active');
		slideNum = $(this).attr('rel');
		$('.slider li:nth-child('+ (slideNum) +')').addClass('active');
		$('.slider .slider-item:nth-child('+ (slideNum) +')').addClass('active');
	});
});

function nextSlide() {
	$('.slider li:nth-child('+ (slideNum) +')').removeClass('active');
	$('.slider .slider-item:nth-child('+ (slideNum++) +')').removeClass('active');
	if (slideNum > slideLength) slideNum = 1;
	$('.slider li:nth-child('+ (slideNum) +')').addClass('active');
	$('.slider .slider-item:nth-child('+ (slideNum) +')').addClass('active');
}

$('.next').click(nextSlide);

$('.prev').click(function() {
	$('.slider li:nth-child('+ (slideNum) +')').removeClass('active');
	$('.slider .slider-item:nth-child('+ (slideNum--) +')').removeClass('active');
	if (!slideNum) slideNum = slideLength;
	$('.slider li:nth-child('+ (slideNum) +')').addClass('active');
	$('.slider .slider-item:nth-child('+ (slideNum) +')').addClass('active');
});

$('.pause').click(function() {
	autoSlide = !autoSlide;
	if (autoSlide) {
		slideInterval = setInterval(nextSlide,7000);
		$('.pause').text("pause");
	}
	else {
		clearInterval(slideInterval);
		$('.pause').text("play");
	};
});

//Save start_city and currency

$('select').change(function() {
	$.post('/ajax_functions',{func: this.name, param: this.value});
});

//message-block

$('.close-message').click(function() {
	$.post('/ajax_functions',{func: "close_message"});
	$(this).parent('.message-box').remove();
});

//Admin part

// script for order-page

// $('.order-page tr').click(function() {
// 	if ($(this).hasClass('selected'))
// 		$(this).removeClass('selected');
// 	else $(this).addClass('selected');
// });

$('.order-state').change(function(){
	$(this).attr('class','order-state');
	$(this).addClass($(this).find(':selected').attr('class'));
});

$('.field-list').on("click", '.btn-block .icon:first-child', function() {
	$(this).closest('.field-list').append('<div class="field-block">' + $(this).closest('.field-block').html() + '</div>');
});

$('.field-list').on("click", '.btn-block .icon:last-child', function() {
	if ($(this).closest('.field-list').find('.field-block').length > 1) $(this).closest('.field-block').remove();
});

var posScroll;

$(document).scroll(function() {
	if ($(document).scrollTop() <= $('.header').height()) {
		$('.header').stop();
		$('.header').animate({top: 0}, 1);
	}
	else {
		if (posScroll - $(document).scrollTop() < 0) {
			$('.header').stop();
			$('.header').animate({top: - $('.header').height()}, 100);
		}
		else {
			$('.header').stop();
			$('.header').animate({top: 0}, 100);
		}
		posScroll = $(document).scrollTop();
	}
});

$('.add-tours').click(function() {
	$.post('/ajax_functions',{func: "add_tours", param: $('.preview-tour-box').length}, function(data) {
		if (data) $('.tour-list').append(data);
		else $('.add-tours').css('display', 'none');
	});
});

// Map

var active = true;

var z = 1;
var mouseX, mouseY, posX, posY;

if ($('#svg').length > 0) {
document.getElementById('svg').addEventListener ("wheel", onWheel, false);
};

function onWheel(e) {
	z -= e.deltaY / 500;
	if (z<1) { z = 1; e.deltaY = 0;}
	$('.svg').css('transform' , 'scale('+z+') translate('+(posX)+'px ,'+(posY)+'px )' );
	e.preventDefault ? e.preventDefault() : (e.returnValue = false);
};

var isMouseDown = false;
$('.map').mousedown(function(){
	isMouseDown = true;
});

$('.map').mouseup(function(){
	isMouseDown = false;
});

$('.map').on("mousemove", function( event ) {
	if (isMouseDown) {
		$('.svg').css('transform' , 'scale('+z+') translate('+(posX + event.pageX - mouseX)+'px ,'+(posY + event.pageY - mouseY)+'px )' );
		active = false;
	}
	else {mouseX = event.pageX; mouseY = event.pageY; posX = parseInt($('.svg').css('transform').split(',')[4]) / z; posY = parseInt($('.svg').css('transform').split(',')[5]) / z; active = true;}
});

$('.search-button').click(function() {
	$.post('/ajax_functions',{
		func: 'search_tours', 
		type_of_tour: $('select[name=type_of_tour]').val(),
		country: $('select[name=country]').val(),
		min_price: $('input[name=min_price]').val(),
		max_price: $('input[name=max_price]').val(),
		min_date: $('input[name=min_date]').val(),
		max_date: $('input[name=max_date]').val(),
	},function(data) {
		$('.tour-list').html(data);
		$('.add-tours').css("display","inline-block");
	})
});

$('.order-tour').click(function(){
	$.post('/ajax_functions',{func: "user_order_tour", param: $(this).attr('rel')},function(data) {
		$('body').prepend(data);

		$('.close-message').click(function() {
			$.post('/ajax_functions',{func: "close_message"});
			$(this).parent('.message-box').remove();
		});

		$('.confirm').click(function() {
			$.post('/ajax_functions',{
				func: "confirm_order_tour",
				param: $(this).attr('rel'),
				pib: $('.modal-box input[name=pib]').val(),
				phone:$('.modal-box input[name=phone]').val(),
				seats:$('.modal-box select[name=seats_number]').val()},
				function(data){
					$('body').prepend(data);

					$('.close-message').click(function() {
						$.post('/ajax_functions',{func: "close_message"});
						$(this).parent('.message-box').remove();
						});
				});
		});

		$('.modal-close').click(function() {
			$('.modal-bg').remove();
			$('.modal-box').remove();
		});
	});
});

$('.order-state').change(function() {
	$.post('/ajax_functions',{func:"change_order_state", id:$(this).attr('rel') , param:this.value});
});

$('.add-comment').click(function() {
	$.post('/ajax_functions', {func: "add_comment", param: $('.comment-field').val(), location: window.location.pathname}, function(data) {
		$('body').prepend(data);

		$('.close-message').click(function() {
			$.post('/ajax_functions',{func: "close_message"});
			$(this).parent('.message-box').remove();
		});

	});
	$('.comment-field').val(null);
	$.post('/ajax_functions', {func: "update_comments", param: window.location.pathname}, function(data) {
		$('.comment-list').html(data);
	});
});

$('.del-comment').click(function() {
	$.post('/ajax_functions', {func: "del_comment", param: $(this).closest('.comment-box').attr('rel')}, function(data) {
		$('body').prepend(data);

		$('.close-message').click(function() {
			$.post('/ajax_functions',{func: "close_message"});
			$(this).parent('.message-box').remove();
		});

	});
	$.post('/ajax_functions', {func: "update_comments", param: window.location.pathname}, function(data) {
		$('.comment-list').html(data);
	});
});

$('.search-country').keyup(function() {
	$('.countries').css('display',(($('.search-country').val())?'block':'none'));
	$.post("/ajax_functions",{func:"get_countries", param:this.value}, function(data) {
		$('.countries').html((!$('.search-country').val())?'':((data)?data:'Пошук не дав результатів'));
	});
});