<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sitemap extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		
	}
	public function index()
	{
		$this->data['page']='index';
		$xmlEcho = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlEcho = '<?xml version="1.0" encoding="UTF-8"?>';
		// $xmlEcho .= '<!-- Created with GoSavin XML Sitemap Generator 1.0 https://gosavin.com -->';
		$xmlEcho .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

		$this->data['all_pages'] = $this->db->where('status',1)->get(TBL_PAGES)->result_array();
		$this->data['all_news'] =$this->db->where('status',1)->get(TBL_POSTS)->result_array();
		// $this->data['all_generals'] =$this->db->where('status',1)->get(TBL_GENERAL)->result_array();
		foreach ($this->data['all_pages'] as $key => $value) {
			$xmlEcho .= '<url><loc>'.htmlspecialchars(base_url().$value["page_slug"], ENT_XML1, 'UTF-8').'</loc><changefreq>always</changefreq><priority>1</priority></url>';
		}
		foreach ($this->data['all_news'] as $key => $value) {
			$xmlEcho .= '<url><loc>'.htmlspecialchars(base_url().$value["post_slug"], ENT_XML1, 'UTF-8').'</loc><changefreq>always</changefreq><priority>0.9</priority></url>';
		}
		/*foreach ($this->data['all_pages'] as $key => $value) {
			$xmlEcho .= '<url><loc>https://prakasamlive.com/'.$value["page_url"].'</loc><changefreq>always</changefreq><priority>0.9</priority></url>';
		}*/
		$xmlEcho .= "</urlset>";
		// printf($xmlEcho);
		$this->data['xmltext']=$xmlEcho;
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($xmlEcho);
		$dom->formatOutput = TRUE;
		// serve a real XML document so search engines can read it
		$this->output->set_content_type('application/xml', 'UTF-8')->set_output($dom->saveXml());
	}
	
}