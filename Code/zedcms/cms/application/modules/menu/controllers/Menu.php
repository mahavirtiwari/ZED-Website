<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Menu extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('menu/menu_model');
	}
	public function index()
	{
		
		$this->data['details'] = $this->db->query("SELECT 
        A.menu_name,
        A.menu_external_link,
        A.menu_type,
        A.status,
        A.id,
        A.created_by,
        A.modified_by,
        GROUP_CONCAT(B.page_name) AS page_name
     FROM zed_menu A
     LEFT JOIN zed_pages B ON FIND_IN_SET(B.id, A.menu_related_pages)
     WHERE A.status != 2 AND (A.menu_type = 3 OR A.menu_type = 2)
     GROUP BY 
        A.menu_name,
        A.menu_external_link,
        A.menu_type,
        A.status,
        A.id,
        A.created_by,
        A.modified_by
     ORDER BY A.created_on DESC"
)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','menu'=>'MENU'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	
	/**delte menus
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_MENU,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','MENUS are deleted !');
					redirect(base_url().'menu');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','MENU is deleted !');
					redirect(base_url().'menu');
				}
			}

		}

	}
	/**
	 * Duplicate check for Menu
	 *
	 */	
	function checkduplicatemenu()
	{
		$q = $this->db->query('SELECT id FROM '.TBL_MENU.' WHERE LOWER(menu_name) = ? AND status != ? AND id != ?',array(strtolower($this->input->post('m_name',TRUE)),2,(int)$this->input->post('id')));
		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatemenu', 'MENU already exists !');
			return false;
		}
	}
	public function addedit($id='')
	{
		$id=base64_decode($id);
		$id=(isset($_POST['id']) ? $_POST['id'] : $id);
		$new=($id==''? TRUE: FALSE);
		$pages=$this->menu_model->pages();

		$menu_related_pages=array();
		foreach($pages as $value)
		{
			$menu_related_pages[$value['id']]=$value['page_name'];
		}
		$this->form_validation->set_rules('m_name','Menu Name ','callback_checkduplicatemenu');
		if($this->form_validation->run()==TRUE)
		{
			$data['menu_name']=$this->input->post('m_name');
			$data['menu_related_pages']=implode(",",(array)$this->input->post('m_related_pages'));
			$data['menu_external_link']=$this->input->post('m_link');
			$data['menu_type']= $this->input->post('m_type');
			$data['menu_order']= $this->input->post('m_order');
			$data['status']=$this->input->post('m_status')=='on' ? 1 :0;
			if($new)
			{
				$data['created_on']=global_datetime();
				$data['created_by']=$this->ion_auth->user()->row()->id;
			}
			else
			{
				$data['modified_on']=global_datetime();
				$data['modified_by']=$this->ion_auth->user()->row()->id;
			}
			$data = $this->security->xss_clean($data);
			if($new)
			{
				$result=$this->menu_model->insertmenu($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New MENU added successfully !');
					redirect(base_url().'menu');
				}
			}
			else
			{
				$result=$this->menu_model->updatemenu($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'MENU updated successfully !');
					redirect(base_url().'menu');
				}
			}

		}
		if(!$new)
		{
			$this->data['details'] = $this->menu_model->selectmenu($id);
		}
		$this->data['related_pages']=$menu_related_pages;
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','menu'=>'MENU','addedit'=>$new ? 'Add MENU': 'Edit MENU'));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}

}
