(function()
{
	$.toasty = function(message, type, options)
	{
		options = $.extend(true, {}, $.toasty.defaults, options);

		var toastyBody = '<div class="rtd-toast alert alert-' + (type ? type : options.type) + ' ' + (options.cssClass ? options.cssClass : '') +'">';
		if(options.allowDismiss)
			toastyBody += '<span class="close" data-dismiss="alert">&times;</span>';
		toastyBody += message;
		toastyBody += '</div>';

		var offsetSum = options.offset.amount;
		if(!options.stack)
			{   $('.rtd-toast').each(function()
			{
				return offsetSum = Math.max(offsetSum, parseInt($(this).css(options.offset.from)) + this.offsetHeight + options.spacing);
			});
	}
	else
	{
		$(options.appendTo).find('.rtd-toast').each(function()
		{
			return offsetSum = Math.max(offsetSum, parseInt($(this).css(options.offset.from)) + this.offsetHeight + options.spacing);
		});
	}

	var css =
	{
		'position': (options.appendTo === 'body' ? 'fixed' : 'absolute'),
		'margin': 0,
		'z-index': '9999',
		'display': 'none',
		'min-width': options.minWidth,
		'max-width': options.maxWidth
	};

	css[options.offset.from] = offsetSum + 'px';

	var $alert = $(toastyBody).css(css)
	.appendTo(options.appendTo);

	switch (options.position)
	{
		case "center":
		$alert.css(
		{
			"left": "50%",
			"margin-left": "-" + ($alert.outerWidth() / 2) + "px"
		});
		break;
		case "left":
		$alert.css("left", "20px");
		break;
		default:
		$alert.css("right", "20px");
	}
	
	if($alert.fadeIn) $alert.fadeIn();
	else $alert.css({display: 'block', opacity: 1});
	
	function removeAlert()
	{
		$.toasty.remove($alert);
	}

	if(options.timeout > 0)
	{
		setTimeout(removeAlert, options.timeout);
	}

	$alert.find("[data-dismiss=\"alert\"]").removeAttr('data-dismiss').click(removeAlert);

	return $alert;
};

$.toasty.remove = function($alert)
{
	if($alert.fadeOut)
	{
		return $alert.fadeOut(function()
		{
			return $alert.remove();
		});
	}
	else
	{
		return $alert.remove();
	}
};

$.toasty.defaults = {
	appendTo: "body",
	stack: false,
	cssClass: false,
	type: "info",
	offset:
	{
		from: "top",
		amount: 20
	},
	position: "center",
	minWidth: 250,
	maxWidth: 450,
	timeout: 4000,
	allowDismiss: true,
	spacing: 10
};
})();