<?php
/**
 * Router for PHP's built-in web server, mirroring the Apache .htaccess rules:
 * real files are served as-is, /cms/* goes to cms/index.php and everything
 * else goes to the public site's index.php.
 *
 *   php -S 127.0.0.1:8080 -t Code/zedcms .localdev/router.php
 */
$docroot = $_SERVER['DOCUMENT_ROOT'];
$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// What the .htaccess files deny on Apache: framework/library code, and
// scripts inside the upload folders.
if (preg_match('#^/(cms/)?(application|system)(/|$)|^/phpmailer(/|$)|/\.#i', $path)
	OR preg_match('#^/(cms/)?(uploads|photogallery|slider)/.*\.(php\d?|phtml|phar)$#i', $path))
{
	http_response_code(404);
	exit('Not Found');
}

if ($path !== '/' && is_file($docroot.$path))
{
	return FALSE;
}

if ($path === '/cms' || strpos($path, '/cms/') === 0)
{
	$front = $docroot.'/cms/index.php';
	$_SERVER['SCRIPT_NAME'] = '/cms/index.php';
}
else
{
	$front = $docroot.'/index.php';
	$_SERVER['SCRIPT_NAME'] = '/index.php';
}

$_SERVER['SCRIPT_FILENAME'] = $front;
chdir(dirname($front));
require $front;
