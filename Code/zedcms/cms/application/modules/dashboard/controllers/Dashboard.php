<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin','contentmanager');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}

	}
	public function index()
	{
		$this->data['pages']=$this->db->where('status',1)->count_all_results(TBL_PAGES);	
		$this->data['news']=$this->db->where('status',1)->count_all_results(TBL_POSTS);	
		$this->data['stakeholders']=$this->db->where('status',1)->count_all_results(TBL_STAKEHOLDERS);
		$this->data['contacts']=$this->db->where('status!=',2)->count_all_results(TBL_CONTACT);
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard',));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	public function profile()
	{
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','profile'=>'Edit Profile'));
		$this->data['user'] = $this->ion_auth->user()->row();
		$this->form_validation->set_rules('first_name','First Name','trim|required');
		$this->form_validation->set_rules('last_name','Last Name','trim|required');
		if ($this->input->post('password'))
		{
			$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']');
				// $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
		}
		if($this->form_validation->run()==TRUE)
		{
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),

				);
		// update the password if it was posted
			if ($this->input->post('password'))
			{
				$data['password'] = $this->input->post('password');
			}

			if($this->ion_auth->update($this->data['user']->id, $data))
			{
				$this->session->set_flashdata('message','Profile Update Successfully');
				redirect(current_url());	

			}
		}
		$this->data['page']='profile';
		$this->_render_page(get_template(),$this->data);
	}
	public function addedit($id='')
	{

	}
}