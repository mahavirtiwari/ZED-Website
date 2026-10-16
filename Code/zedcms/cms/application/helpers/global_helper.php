<?php
	/**---------------------------------------------------------------------------------------------------
	 * Global Helper
	 *
	 * 
	 * All the set of Global Helper Functions
	 * 
	 * 	1. global_datetime
	 * 	2.get_template
	 	3.render_bread_crumb
	 	4.image_upload
	 	5.token generator
	 * ---------------------------------------------------------------------------------------------------
	 */
/**
 * File types allowed for document uploads (resources, notifications, news files).
 * Never use '*' : the upload folders are served by the web server.
 */
defined('ZED_DOC_TYPES') OR define('ZED_DOC_TYPES', 'pdf|doc|docx|xls|xlsx|ppt|pptx|csv|txt|zip|jpg|jpeg|png|gif');

/**
 * Save a base64 (data URI) image posted by the cropper.
 * The content must decode to a real PNG/JPEG/GIF; the file name is random.
 * @return String|Boolean  generated file name or FALSE
 */
if ( ! function_exists('save_base64_image'))
{
	function save_base64_image($data_uri, $dir)
	{
		if ( ! is_string($data_uri) OR $data_uri === '')
		{
			return FALSE;
		}
		$data_uri = preg_replace('#^data:image/[a-z0-9.+-]+;base64,#i', '', trim($data_uri));
		$binary = base64_decode(str_replace(' ', '+', $data_uri), TRUE);
		if ($binary === FALSE OR $binary === '')
		{
			return FALSE;
		}
		$info = @getimagesizefromstring($binary);
		$extensions = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif');
		if ($info === FALSE OR ! isset($extensions[$info[2]]))
		{
			return FALSE;
		}
		$name = bin2hex(random_bytes(16)).'.'.$extensions[$info[2]];
		if (file_put_contents(rtrim($dir, '/\\').'/'.$name, $binary) === FALSE)
		{
			return FALSE;
		}
		return $name;
	}
}

/**
	 * Global DateTime Function
	 * @return  timestamp Y-m-d h:i:s [<description>]
	 */
if(!function_exists('global_datetime')){
	function global_datetime(){
		date_default_timezone_set('Asia/Calcutta');
		return date('Y-m-d H:i:s');
	}
}

	/**
	 * Get Template
	 * @return String [Path to a specific template.php]
	 */
	if(!function_exists('get_template')){
		function get_template(){
			$CI =& get_instance();
			$group = $CI->ion_auth->get_users_groups()->row();
			switch ($group ? $group->name : '') {
				case 'superadmin':
				$folder = 'superadmin';
				break;
				case 'admin':
				$folder = 'admin';
				break;
				case 'contentmanager':
				$folder = 'contentmanager';
				break;
				default:
				$folder = 'superadmin';
				break;
			}
			return 'templates/'.$folder.'/template';
		}
	}
	/**
	 * Render BreadCrumb
	 * @return String [Bread Crumb]
	 */
	if(!function_exists('render_bread_crumb')){
		function render_bread_crumb($nodes=array()){
			$i=sizeof($nodes)-1;
			$bread_crumb = '';

			foreach ($nodes as $node => $title) {
				$i--;
				if($i>=0)
					$bread_crumb.='<li><a href="javascript:void(0);">'.$title.'</a></li>';
				else
					$bread_crumb.= '<li class="active">'.$title.'</li>';
				
			}

			return $bread_crumb;
		}
	}
	if(! function_exists('image_upload'))
	{
		function image_upload($image)
		{
			$CI=& get_instance();
			$config['upload_path'] = UPLOAD_DIR_SLIDER;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['remove_spaces']=TRUE;
			$config['file_ext_tolower']=TRUE;
			$config['overwrite']=FALSE;
			$config['encrypt_name']=TRUE;
			$CI->load->library('upload', $config);
			// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
			$CI->upload->initialize($config);
			if($CI->upload->do_upload($image))
			{

				$image_details=$CI->upload->data();
				
				return URL_FOLDER.'slider/'.$CI->security->sanitize_filename(trim($image_details['file_name']));
			}
			else
			{
				return $CI->input->post('existed_file');
			}
		}
	}
	/**
	* Gallery and file uploding
	* @var
	* @var 
	*
	**/
	if(! function_exists('gallery_upload'))
	{
		function gallery_upload($image='')
		{
			$CI=& get_instance();
			$config['upload_path'] = UPLOAD_DIR_NEWS;
			$config['allowed_types'] = 'gif|jpeg|jpg|png';
			$config['overwrite']=FALSE;
			$config['remove_spaces']=TRUE;
			$config['file_ext_tolower']=TRUE;
			$config['encrypt_name']=TRUE;
			$CI->load->library('upload', $config);
			// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
			$CI->upload->initialize($config);
			if($CI->upload->do_upload($image))
			{

				$image_details=$CI->upload->data();
				return URL_FOLDER.'photogallery/'.$CI->security->sanitize_filename(trim($image_details['file_name']));
			}
			else
			{

				return $CI->input->post('existed_file');

			}
				
		}
	}
	
	/**
	* Function name and purpose
	* @var input type file name
	* @var  return file slug
	*
	**/
	if(! function_exists('save_file'))
	{
		function save_file($file='')
		{
			$CI=& get_instance();
			$config['upload_path'] = EDITOR_PATH;
			$config['allowed_types'] = ZED_DOC_TYPES;
			$config['overwrite']=FALSE;
			$config['remove_spaces']=TRUE;
			$config['file_ext_tolower']=TRUE;
			$config['encrypt_name']=TRUE;
			$CI->load->library('upload', $config);
			$CI->upload->initialize($config);
			if($CI->upload->do_upload($file))
			{
				$image_details=$CI->upload->data();
				return URL_FOLDER.'uploads/'.$CI->security->sanitize_filename(trim($image_details['file_name']));
			}
			else
			{

				return $CI->input->post('existed_file');

			}

		}
	}
	/**
	* Trumbowyg image upload (pages, news and faq "tbwimage" endpoints)
	* Saves the posted 'tbwimagefield' into uploads/ and sets the JSON reply the
	* editor expects. These URIs are excluded from the CSRF token check, so only
	* same-site AJAX requests are accepted: a cross-site form cannot send the
	* X-Requested-With header that jQuery adds.
	**/
	if(! function_exists('editor_image_upload'))
	{
		function editor_image_upload()
		{
			$CI=& get_instance();
			$CI->output->set_content_type('application/json');
			if( ! $CI->input->is_ajax_request())
			{
				$CI->output->set_status_header(403)->set_output(json_encode(array('success'=>FALSE,'message'=>'Forbidden')));
				return;
			}
			$config['upload_path'] = EDITOR_PATH;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['overwrite']=FALSE;
			$config['encrypt_name']=TRUE;
			$config['file_ext_tolower']=TRUE;
			$CI->load->library('upload', $config);
			$CI->upload->initialize($config);
			if( ! $CI->upload->do_upload('tbwimagefield'))
			{
				// Trumbowyg reads "success" and shows its upload error message
				$CI->output->set_output(json_encode(array('success'=>FALSE,'message'=>strip_tags($CI->upload->display_errors()))));
				return;
			}
			$image_details=$CI->upload->data();
			$data['success']=true;
			$data['status']=200;
			$data['data']['link']=URL_FOLDER."uploads/".trim(sanitize_filename($image_details['file_name']));
			$data['data']['id']=getToken(6);
			$data['data']['description']='';
			$data['data']['datetime']=global_datetime();
			$CI->output->set_output(json_encode($data));
		}
	}
/**
	 * Secured random number generator
	 */
if( ! function_exists('crypto_rand_secure')){
	function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;
		    if ($range < 1) return $min; // not so random...
		    $log = ceil(log($range, 2));
		    $bytes = (int) ($log / 8) + 1; // length in bytes
		    $bits = (int) $log + 1; // length in bits
		    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		    do {
		    	$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
		        $rnd = $rnd & $filter; // discard irrelevant bits
		    } while ($rnd >= $range);
		    return $min + $rnd;
		}
	}
	 /**
	 * Secured Random Tocken Generator !
	 */
	 if( ! function_exists('getToken')){
	 	function getToken($length)
	 	{
	 		$token = "";
	 		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		    //$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	 		$codeAlphabet.= "0123456789";
	 		$max = strlen($codeAlphabet) - 1;
	 		for ($i=0; $i < $length; $i++) {
	 			$token .= $codeAlphabet[crypto_rand_secure(0, $max)];
	 		}
	 		return $token;
	 	}
	 }

/**
 * A value from zed_global_config (Settings), cached for the request.
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
			$CI->load->database();
			$row = $CI->db->select('value')->where('label', $label)->limit(1)->get(TBL_GLOBAL)->row();
			$cache[$label] = $row ? $row->value : $default;
		}
		return $cache[$label];
	}
}

/**
 * Is the captcha switched on for this form? (Settings > Security)
 * On unless the setting says '0', so a missing setting fails safe.
 */
if ( ! function_exists('captcha_enabled'))
{
	function captcha_enabled($label = 'captcha_cms_login')
	{
		return (string) zed_setting($label, '1') !== '0';
	}
}

/**
 * <meta> tags with this session's password-encryption key and nonce,
 * read by assets/js/zed-pwt.js (see libraries/Password_transport.php).
 */
if ( ! function_exists('pwt_meta'))
{
	function pwt_meta()
	{
		$CI =& get_instance();
		$CI->load->library('password_transport');
		$key = $CI->password_transport->public_key();
		if ($key === '')
		{
			return '';
		}
		return '<meta name="zed-pwt-key" content="'.$key.'">'."\n"
			.'<meta name="zed-pwt-nonce" content="'.$CI->password_transport->nonce().'">'."\n";
	}
}

	?>