<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// define routing urls
require_once( BASEPATH .'database/DB'. EXT );

$db =& DB();
// route keys are regular expressions, so slugs are quoted
$query = $db->select('page_slug')->get(TBL_PAGES);
$result = $query->result();
foreach( $result as $row )
{
	$slug = strtolower(trim((string) $row->page_slug));
	if ($slug === '') continue;
	$route[preg_quote($slug, '#')] = 'home/page/'.$slug;
}

// routing for latest news
$query = $db->select('post_slug')->get(TBL_POSTS);
$result = $query->result();
foreach( $result as $row )
{
	$slug = strtolower(trim((string) $row->post_slug));
	if ($slug === '') continue;
	$route[preg_quote($slug, '#')] = 'home/posts/'.$slug;
}
// the main CI connection is opened separately; don't keep this one around
$db->close();
unset($db, $query, $result, $row, $slug);

//  contact us page routing
$route['contact-us']='home/contactus';
// video and gallery page routing
$route['video']='home/video';
$route['gallery']='home/gallery';
$route['gallery/(:num)/(:any)']='home/gallery_month/$1/$2';
$route['search']='home/search';
$route['news']='home/news';


