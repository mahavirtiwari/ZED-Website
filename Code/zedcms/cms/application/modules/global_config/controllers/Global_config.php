<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Global_config extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('ion_auth_model');
		$groups = array('superadmin','admin','contentmanager');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
	}
	public function index()
	{	
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['details'] = $details;

		if(isset($_POST['zed-save']))
		{
			
			unset($_POST['zed-save']);
			foreach ($this->input->post() as $key => $value) {
				$row['label']=$key;
				$row['value']=$value;
				$this->db->where('label',$row['label']);
				$this->db->update(TBL_GLOBAL, $row);
			}
			$this->session->set_flashdata('message', 'Settings updated successfully !');
			redirect(current_url());
		}
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','global'=>'Settings'));
		$this->data['page'] = 'index';
		$this->_render_page(get_template(), $this->data);

	}
	public function gallery()
	{
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['details'] = $details;
		if(isset($_POST['zed-save']))
		{
			unset($_POST['zed-save']);
			foreach ($this->input->post() as $key => $value) {
				$row['label']=$key;
				$row['value']=$value;
				$this->db->where('label',$row['label']);
				$this->db->update(TBL_GLOBAL, $row);
			}
			$this->session->set_flashdata('message', 'Galley Seo Setting updated successfully  !');
			redirect(current_url());
		}
		
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','global'=>'Settings','galley'=>'Gallery Seo Settings'));
		$this->data['page'] = 'gallery';
		$this->_render_page(get_template(), $this->data);
	}
	public function home()
	{
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['details'] = $details;
		if(isset($_POST['zed-save']))
		{
			unset($_POST['zed-save']);
			foreach ($this->input->post() as $key => $value) {
				$row['label']=$key;
				$row['value']=$value;
				$this->db->where('label',$row['label']);
				$this->db->update(TBL_GLOBAL, $row);
			}
			$this->session->set_flashdata('message', 'Home Seo Setting updated successfully  !');
			redirect(current_url());
		}
		
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','global'=>'Settings','home'=>'Home Seo Settings'));
		$this->data['page'] = 'home';
		$this->_render_page(get_template(), $this->data);
	}
	public function counters()
	{
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['details'] = $details;
		if(isset($_POST['zed-save']))
		{
			unset($_POST['zed-save']);
			foreach ($this->input->post() as $key => $value) {
				$row['label']=$key;
				$row['value']=$value;
				$this->db->where('label',$row['label']);
				$this->db->update(TBL_GLOBAL, $row);
			}
			$this->session->set_flashdata('message', 'Counters updated successfully  !');
			redirect(current_url());

		}
		
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','global'=>'Settings','counters'=>'Counters'));
		$this->data['page'] = 'counters';
		$this->_render_page(get_template(), $this->data);
	}
	public function address()
	{
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['details'] = $details;
		if(isset($_POST['zed-save']))
		{
			unset($_POST['zed-save']);
			foreach ($this->input->post() as $key => $value) {
				$row['label']=$key;
				$row['value']=$value;
				$this->db->where('label',$row['label']);
				$this->db->update(TBL_GLOBAL, $row);
			}
			$this->session->set_flashdata('message', 'Adress updated successfully  !');
			redirect(current_url());

		}
		
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','global'=>'Settings','address'=>'Adress'));
		$this->data['page'] = 'address';
		$this->_render_page(get_template(), $this->data);
	}

	/**
	 * Security settings: captcha on/off for the CMS login and Contact Us,
	 * and which Contact Us form the site shows. Super admins and admins only.
	 */
	public function security()
	{
		if ( ! $this->ion_auth->in_group(array('superadmin', 'admin')))
		{
			show_error('You do not have permission to change the security settings.', 403);
		}
		// label => allowed values; anything else posted is ignored
		$fields = array(
			'captcha_cms_login'  => array('1', '0'),
			'captcha_contact_us' => array('1', '0'),
			'contact_form_type'  => array('zoho', 'site'),
		);
		if (isset($_POST['zed-save']))
		{
			foreach ($fields as $label => $allowed)
			{
				$value = (string) $this->input->post($label);
				if ( ! in_array($value, $allowed, TRUE))
				{
					continue;
				}
				if ($this->db->where('label', $label)->count_all_results(TBL_GLOBAL) > 0)
				{
					$this->db->where('label', $label)->update(TBL_GLOBAL, array('value' => $value));
				}
				else
				{
					$this->db->insert(TBL_GLOBAL, array('label' => $label, 'value' => $value));
				}
			}
			$this->session->set_flashdata('message', 'Security settings updated successfully !');
			redirect(current_url());
		}

		$details = array();
		foreach ($this->db->select('label, value')->where_in('label', array_keys($fields))->get(TBL_GLOBAL)->result_array() as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		// same defaults as captcha_enabled() and the Contact Us page
		$this->data['details'] = $details + array('captcha_cms_login' => '1', 'captcha_contact_us' => '1', 'contact_form_type' => 'zoho');
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','global'=>'Settings','security'=>'Security'));
		$this->data['page'] = 'security';
		$this->_render_page(get_template(), $this->data);
	}

}