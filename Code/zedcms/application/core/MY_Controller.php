<?php 

class MY_Controller extends CI_Controller
{
	public $data = array();
	public $viewdata;

	function __construct()
	{
		parent::__construct();
	}
	/**
	 * Displays the specified view
	 * @param array $data
	 */
	function _render_page($view, $data=null, $returnhtml=false)
	{
		
		$this->viewdata = (empty($data)) ? $this->data: $data;

		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);

		if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
	}
}