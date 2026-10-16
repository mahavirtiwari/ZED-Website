(function()
{
	$.asky = function(question, agreedFunction, options)
	{
		options = $.extend(true, {}, $.asky.defaults, options);

		var askyBody = '<div class="rtd-asky' + (options.cssClass ? options.cssClass : '') +'">';
		askyBody += '<p class="rtd-asky-question">'+question+'</p>';
		askyBody += '<div class="rtd-asky-btns">';
		askyBody += '<a href="javascript:void(0);" class="rtd-asky-no ' + (options.noClass ? options.noClass : '') +'">' + (options.noText ? options.noText : '') +'</a>';
		askyBody += '<a href="javascript:void(0);" class="rtd-asky-yes ' + (options.yesClass ? options.yesClass : '') +'">' + (options.yesText ? options.yesText : '') +'</a>';
		askyBody += '</div>';
		askyBody += '</div>';

		var offsetSum = options.offset.amount;
		if(!options.stack)
			{   $('.rtd-asky').each(function()
			{
				return offsetSum = Math.max(offsetSum, parseInt($(this).css(options.offset.from)) + this.offsetHeight + options.spacing);
			});
	}
	else
	{
		$('body').find('.rtd-asky').each(function()
		{
			return offsetSum = Math.max(offsetSum, parseInt($(this).css(options.offset.from)) + this.offsetHeight + options.spacing);
		});
	}

	var css =
	{
		'position': 'fixed',
		'margin': 0,
		'z-index': '9999',
		'display': 'none',
		'min-width': options.minWidth,
		'max-width': options.maxWidth
	};

	css[options.offset.from] = offsetSum + 'px';

	var bgcss =
	{
		'position': 'fixed',
		'z-index': 9998,
		'display': 'none',
		'background': 'rgba(61, 77, 93, 0.79)',
		'width': '100%',
		'height': '100%',
		'left': 0,
		'top': 0
	};

	var $askybg = $('<div class="rtd-asky-bg"></div>').css(bgcss).prependTo('body');
	var $asky = $(askyBody).css(css).prependTo('body');

	switch (options.position)
	{
		case "center":
		$asky.css(
		{
			"left": "50%",
			"top": "50%",
			"margin-left": "-" + ($asky.outerWidth() / 2) + "px",
			"margin-top": "-" + ($asky.outerHeight() / 2) + "px"
		});
		break;
		case "left":
		$asky.css("left", "20px");
		break;
		default:
		$asky.css("right", "20px");
	}
	$asky.fadeIn();
	$askybg.fadeIn();
	
	function removeAsky()
	{
		$.asky.remove($asky);
		$askybg.fadeOut(function(){
			$askybg.remove();
		});
	}

	function agreedAsky()
	{
		agreedFunction();
		removeAsky();
	}

	$asky.find("[data-dismiss=\"alert\"]").removeAttr('data-dismiss').click(removeAsky);
	$asky.find('.rtd-asky-yes').click(agreedAsky);
	$asky.find('.rtd-asky-no').click(removeAsky);
	if(options.allowBGDismiss){
		$askybg.click(removeAsky);
	}

	return $asky;
};

$.asky.remove = function($asky)
{
	if($asky.fadeOut)
	{
		return $asky.fadeOut(function()
		{
			return $asky.remove();
		});
	}
	else
	{
		return $asky.remove();
	}
};

$.asky.defaults = {
	stack: false,
	cssClass: false,
	yesClass: 'btn btn-success',
	yesText: "Yes",
	noClass: 'btn btn-default',
	noText: "No",
	offset:
	{
		from: "top",
		amount: 20
	},
	position: "center",
	minWidth: 250,
	maxWidth: 450,
	allowBGDismiss: true,
	spacing: 10
};
})();