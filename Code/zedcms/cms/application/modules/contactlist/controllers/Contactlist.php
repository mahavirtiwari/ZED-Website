<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contactlist extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
	}
	public function index()
	{
		$this->data['details']=$this->db->select('*')->where('status',1)->order_by('created_on','DESC')->get(TBL_CONTACT)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','contacts'=>'All Registed Users'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	/**delte Pages
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_CONTACT,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Contact details are deleted !');
					redirect(base_url().'contactlist');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Contact is deleted !');
					redirect(base_url().'contactlist');
				}
			}

		}

	}
	
}