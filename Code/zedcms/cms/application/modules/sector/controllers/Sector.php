<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sector extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('sector/sector_model');
	}
	public function index()
	{
		$this->data['details']=$this->sector_model->allsector();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','sector'=>'Sector'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	/**delte Sectors
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('sectorid', $ids);
			$this->db->update(TBL_SECTOR,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Sectors are deleted !');
					redirect(base_url().'sector');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Sector is deleted !');
					redirect(base_url().'sector');
				}
			}

		}

	}
	/**
	 * Duplicate check for Sector Code
	 *
	 */	
	function checkduplicatesector()
	{
		$q = $this->db->query('SELECT sectorid FROM '.TBL_SECTOR.' WHERE LOWER(code) = ? AND status != ? AND sectorid != ?',array(strtolower($this->input->post('s_code',TRUE)),2,(int)$this->input->post('id')));
		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatesector', 'Sector Code already exists !');
			return false;
		}
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		$this->form_validation->set_rules('s_code', 'Sector Code', 'callback_checkduplicatesector');
		$sector=$this->sector_model->dropdownsector();
		$parent_sector=array();
		foreach ($sector as $key => $value) {
			 $parent_sector[$value['sectorid']]=$value['code']."-".$value['name'];
		}
		if($this->form_validation->run()==TRUE)
		{
			$data['code']=$this->input->post('s_code');
			$data['name']=$this->input->post('s_name');
			$data['parentcode']=$this->input->post('s_parent');
			$data['status'] =$this->input->post('s_status')=='on' ? 1 : 0;
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
				$result=$this->sector_model->insertsector($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Sector added successfully !');
					redirect(base_url().'sector');
				}
			}
			else
			{
				$result=$this->sector_model->updatesector($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Sector updated successfully !');
					redirect(base_url().'sector');
				}
			}
		}
		else
		{
			$this->data['form_errors']=$this->form_validation->error_array();
		}
		if(!$new)
		{
			$this->data['details'] = $this->sector_model->selectsector($id);
		}
		$this->data['parent']=$parent_sector;
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','sector'=>'Sector','addedit'=>($new ? 'New Sector' : 'Edit Sector')));
		$this->data['page']='addedit';

		$this->_render_page(get_template(),$this->data);
	}
}