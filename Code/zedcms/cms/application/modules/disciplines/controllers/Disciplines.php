<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Disciplines  extends MY_Controller
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
		$this->load->model('disciplines/discipline_model');
	}
	public function index()
	{
		$this->data['details']=$this->discipline_model->joindiscipline();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','disciplines'=>'Disciplines'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	/**delte Discipline
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('disciplineid', $ids);
			$this->db->update(TBL_DISCIPLINE,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Disciplines are deleted !');
					redirect(base_url().'disciplines');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Discipline is deleted !');
					redirect(base_url().'disciplines');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'disciplines');
	}
	/**
	 * Duplicate check for Menu
	 *
	 */	
	function checkduplicatediscipline()
	{
		$q = $this->db->query('SELECT disciplineid FROM '.TBL_DISCIPLINE.' WHERE LOWER(code) = ? AND status != ? AND disciplineid != ?',array(strtolower($this->input->post('dis_code',TRUE)),2,(int)$this->input->post('id')));

		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatediscipline', 'Discipline Code already exists !');
			return false;
		}
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		$this->form_validation->set_rules('dis_code', 'Discipline Code', 'callback_checkduplicatediscipline');
		$sector=$this->sector_model->dropdownsector();
		$parent_sector=array();
		foreach ($sector as $key => $value) {
			$parent_sector[$value['sectorid']]=$value['code']."-".$value['name'];
		}
		if($this->form_validation->run()==TRUE)
		{
			$data['code']=$this->input->post('dis_code');
			$data['name']=$this->input->post('dis_name');
			if($this->input->post('dis_sector_ids'))
			{
				$data['sectorids']=implode(',', $this->input->post('dis_sector_ids'));
			}
			
			$data['status'] =$this->input->post('dis_status')=='on' ? 1 : 0;
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
				$result=$this->discipline_model->insertdiscipline($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Discipline added successfully !');
					redirect(base_url().'disciplines');
				}
			}
			else
			{
				$result=$this->discipline_model->updatediscipline($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Discipline updated successfully !');
					redirect(base_url().'disciplines');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->discipline_model->selectdiscipline($id);
		}
		$this->data['parent']=$parent_sector;
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','disciplines'=>'Disciplines','addedit'=>($new ? 'New Disciplines' : 'Edit Disciplines')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}