<?php
	/**---------------------------------------------------------------------------------------------------
	 * Global Helper
	 *
	 * 
	 * All the set of Global Helper Functions
	 * 1.global_datetime
	 * ---------------------------------------------------------------------------------------------------
	 */
/**
	 * Global DateTime Function
	 * @return  timestamp Y-m-d H:i:s [<description>]
	 */
if(!function_exists('global_datetime')){
	function global_datetime(){
		date_default_timezone_set('Asia/Calcutta');
		return date('Y-m-d H:i:s');
	}
}


if(! function_exists('sendEmail')){

        /**
         * Send an HTML email through SMTP (PHPMailer 7).
         * SMTP settings come from the environment: SMTP_HOST, SMTP_PORT,
         * SMTP_USER, SMTP_PASS, SMTP_SECURE (tls|ssl), MAIL_FROM, MAIL_FROM_NAME.
         * @return bool TRUE when the message was handed to the SMTP server
         */
        function sendEmail($subject,$message,$to,$cc='')
        {
        	require_once FCPATH.'phpmailer/src/Exception.php';
        	require_once FCPATH.'phpmailer/src/PHPMailer.php';
        	require_once FCPATH.'phpmailer/src/SMTP.php';

        	if ( ! getenv('SMTP_HOST'))
        	{
        		log_message('error', 'sendEmail: SMTP_HOST is not configured, email "'.$subject.'" not sent');
        		return FALSE;
        	}

        	$mail = new PHPMailer\PHPMailer\PHPMailer(TRUE);
        	try
        	{
		        $mail->isSMTP();
		        $mail->Host       = getenv('SMTP_HOST');
		        $mail->Port       = (int) (getenv('SMTP_PORT') ?: 587);
		        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: 'tls';
		        $mail->SMTPAuth   = (bool) getenv('SMTP_USER');
		        $mail->Username   = (string) getenv('SMTP_USER');
		        $mail->Password   = (string) getenv('SMTP_PASS');
		        $mail->CharSet    = 'UTF-8';
		        $mail->setFrom(getenv('MAIL_FROM') ?: 'info@zedcs.com', getenv('MAIL_FROM_NAME') ?: 'Team ZED');
		        $mail->addAddress($to, 'ZED');
		        if ( ! empty($cc))
		        {
		        	$mail->addCC($cc);
		        }
		        $mail->isHTML(TRUE);
		        $mail->Subject = $subject;
		        $mail->Body    = $message;
		        $mail->AltBody = trim(html_entity_decode(strip_tags(str_replace('</p>', "</p>\n", $message)), ENT_QUOTES, 'UTF-8'));
		        return $mail->send();
        	}
        	catch (PHPMailer\PHPMailer\Exception $e)
        	{
        		log_message('error', 'sendEmail failed: '.$mail->ErrorInfo);
        		return FALSE;
        	}
        }
    }
	/**
	 * Get Template
	 * @return String [Path to a specific template.php]
	 */
	if(!function_exists('get_template')){
		function get_template(){
			$CI =& get_instance();
			$folder='web';
			return 'templates'.DS.$folder.DS.'template';
		}
	}
	if(! function_exists('getCITokenJsVars')){
		function getCITokenJsVars(){
			$CI = & get_instance();

			echo "<script>
			var ci_csrf_enabled  = ".($CI->config->item("csrf_protection") ? '1':'0').";
			var ci_token_name    = '".$CI->config->item("csrf_token_name")."';
			var ci_token_val	 = '".$CI->security->get_csrf_hash()."';
			var error_redirect	 = '';
			var debug_enable	 = false;					        
		</script>
		";
	}
}
if(!function_exists('htmlSubstr')){
	function htmlSubstr($text, $max_length)
	{
		$tags   = array();
		$result = "";

		$is_open   = false;
		$grab_open = false;
		$is_close  = false;
		$in_double_quotes = false;
		$in_single_quotes = false;
		$tag = "";

		$i = 0;
		$stripped = 0;

		$stripped_text = strip_tags($text);

		while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
		{
			$symbol  = $text[$i];
			$result .= $symbol;

			switch ($symbol)
			{
				case '<':
				$is_open   = true;
				$grab_open = true;
				break;

				case '"':
				if ($in_double_quotes)
					$in_double_quotes = false;
				else
					$in_double_quotes = true;

				break;

				case "'":
				if ($in_single_quotes)
					$in_single_quotes = false;
				else
					$in_single_quotes = true;

				break;

				case '/':
				if ($is_open && !$in_double_quotes && !$in_single_quotes)
				{
					$is_close  = true;
					$is_open   = false;
					$grab_open = false;
				}

				break;

				case ' ':
				if ($is_open)
					$grab_open = false;
				else
					$stripped++;

				break;

				case '>':
				if ($is_open)
				{
					$is_open   = false;
					$grab_open = false;
					array_push($tags, $tag);
					$tag = "";
				}
				else if ($is_close)
				{
					$is_close = false;
					array_pop($tags);
					$tag = "";
				}

				break;

				default:
				if ($grab_open || $is_close)
					$tag .= $symbol;

				if (!$is_open && !$is_close)
					$stripped++;
			}

			$i++;
		}

		while ($tags)
			$result .= "</".array_pop($tags).">";

		return $result;
	}
}
if(!function_exists('convertCurrency')){
	function convertCurrency($number)
	{
		// Convert Price to Crores or Lakhs or Thousands
		$length = strlen($number);
		$currency = '';

		if($length == 4 || $length == 5)
		{
			// Thousand
			$number = $number / 1000;
			$number = round($number,2);
			$ext = "Thousand";
			$currency = $number." ".$ext;
		}
		elseif($length == 6 || $length == 7)
		{
			// Lakhs
			$number = $number / 100000;
			$number = round($number,2);
			if($number ==1){
				$ext = "Lakh";
			}else{
				$ext = "Lakhs";
			}
			
			$currency = $number." ".$ext;

		}
		elseif($length == 8 || $length == 9)
		{
			// Crores
			$number = $number / 10000000;
			$number = round($number,2);
			$ext = "Cr";
			$currency = $number.' '.$ext;
		}

		return $currency;
	}
}

/**
 * A value from zed_global_config (CMS Settings), cached for the request.
 * @return string|NULL  $default when the label does not exist
 */
if ( ! function_exists('zed_setting'))
{
	function zed_setting($label, $default = NULL)
	{
		static $cache = array();
		if ( ! array_key_exists($label, $cache))
		{
			$CI =& get_instance();
			$row = $CI->db->select('value')->where('label', $label)->limit(1)->get(TBL_GLOBAL)->row();
			$cache[$label] = $row ? $row->value : $default;
		}
		return $cache[$label];
	}
}

/**
 * Is the captcha switched on for this form? (CMS Settings > Security)
 * On unless the setting says '0', so a missing setting fails safe.
 */
if ( ! function_exists('captcha_enabled'))
{
	function captcha_enabled($label = 'captcha_contact_us')
	{
		return (string) zed_setting($label, '1') !== '0';
	}
}
?>