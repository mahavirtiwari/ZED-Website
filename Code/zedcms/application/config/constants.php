<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
define('EXT', '.php');// this for routing constant
define('DS', DIRECTORY_SEPARATOR);
define('FOLDER_PATH', '');
// Site URLs: APP_BASE_URL (public site root) can be set in the environment for
// production; otherwise it is detected from the request.
if ( ! function_exists('zed_detect_root_url'))
{
	function zed_detect_root_url($strip_dir = '')
	{
		if (getenv('APP_BASE_URL'))
		{
			return rtrim(getenv('APP_BASE_URL'), '/');
		}

		$scheme = ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
		$dir = rtrim(str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/')), '/');
		if ($strip_dir !== '' && substr($dir, -strlen($strip_dir)) === $strip_dir)
		{
			$dir = substr($dir, 0, -strlen($strip_dir));
		}

		return $scheme.'://'.$host.$dir;
	}
}
define('SITEURL', zed_detect_root_url());
// define assests folder url (URLs always use '/', never DIRECTORY_SEPARATOR)
define('URL_ASSETS',SITEURL.'/assets');
define('URL_ASSETS_CSS',URL_ASSETS.'/css');
define('URL_ASSETS_JS',URL_ASSETS.'/js');
define('URL_ASSETS_IMG',URL_ASSETS.'/images');
define('URL_ASSETS_FONTS',URL_ASSETS.'/fonts');
// define table prefix
define('TBL_PREFIX', 'zed_');

// define constant for tables
define('TBL_CATEGORIES',TBL_PREFIX.'category');
define('TBL_POSTS',TBL_PREFIX.'posts');
define('TBL_PAGES',TBL_PREFIX.'pages');
define('TBL_FAQ',TBL_PREFIX.'faq');
define('TBL_MENU',TBL_PREFIX.'menu');
define('TBL_MEDIA',TBL_PREFIX.'media');
define('TBL_SLIDER',TBL_PREFIX.'home_slider');
define('TBL_CONTACT',TBL_PREFIX.'contact');
define('TBL_GLOBAL',TBL_PREFIX.'global_config');
define('TBL_NEWS_BLOCK',TBL_PREFIX.'news_block');
define('TBL_SECTOR',TBL_PREFIX.'sector');
define('TBL_DISCIPLINE',TBL_PREFIX.'discipline');
define('TBL_PARAMETER',TBL_PREFIX.'parameter');
define('TBL_STAKEHOLDERS',TBL_PREFIX.'stakeholders');
define('TBL_VIDEOS',TBL_PREFIX.'homevideos');
define('TBL_TESTIMONIALS',TBL_PREFIX.'testimonials');

