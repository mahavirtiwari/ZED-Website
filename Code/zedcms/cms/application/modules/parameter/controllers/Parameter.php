<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Parameter extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('parameter/parameter_model');
	}
	public function index()
	{
		$this->data['details']=$this->parameter_model->allparameter();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','parameter'=>'Parameter'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	/**delte Parameter
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('parameterid', $ids);
			$this->db->update(TBL_PARAMETER,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Parameters are deleted !');
					redirect(base_url().'parameter');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Parameter is deleted !');
					redirect(base_url().'parameter');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'parameter');
	}
	/**
	 * Duplicate check for Parameter
	 *
	 */	
	function checkduplicateparameter()
	{
		$q = $this->db->query('SELECT parameterid FROM '.TBL_PARAMETER.' WHERE LOWER(code) = ? AND status != ?',array(strtolower($this->input->post('parameter_code',TRUE)),2));
		$data = $q->result_array();
		
		if (count($data) == 0 && $this->input->post('id') == '') {
			return true;
		} elseif((count($data) >= 1 || count($data) == 0)&& $this->input->post('id') != '') {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicateparameter', 'Parameter Code already exists !');
			return false;
		}
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		$sector=$this->parameter_model->dropdownsector();
		$discipline=$this->parameter_model->dropdowndiscipline();
		$parent_sector=array();
		$parent_discipline=array();
		//$parent_sector['']='Choose a Sector';
		foreach ($sector as $key => $value) {
			$parent_sector[$value['sectorid']]=$value['code']."-".$value['name'];
		}
		$parent_discipline['']='Choose a Discipline';
		foreach ($discipline as $key => $value) {
			$parent_discipline[$value['disciplineid']]=$value['code']."-".$value['name'];
		}
		$this->form_validation->set_rules('parameter_code', 'Parameter Code', 'callback_checkduplicateparameter');
		
		if($this->form_validation->run()==TRUE)
		{
			$data['code']=$this->input->post('parameter_code');
			$data['name']=$this->input->post('parameter_name');
			if($this->input->post('parameter_discipline'))
			{
				$data['disciplineid']=$this->input->post('parameter_discipline');
			}
			$data['description']=$this->input->post('parameter_description');
			if($this->input->post('parameter_ro'))
			{
				$data['ro']=$this->input->post('parameter_ro');
			}
			if($this->input->post('parameter_gw'))
			{
				$data['gw']=$this->input->post('parameter_gw');
			}
			
				$data['level1_comment']=$this->input->post('parameter_level1_comment');
				$data['level2_comment']=$this->input->post('parameter_level2_comment');
				$data['level3_comment']=$this->input->post('parameter_level3_comment');
				$data['level4_comment']=$this->input->post('parameter_level4_comment');
				$data['level5_comment']=$this->input->post('parameter_level5_comment');
			
			$data['status'] =$this->input->post('parameter_status')=='on' ? 1 : 0;
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
			$sectors = array_values(array_filter((array)$this->input->post('parameter_sector'), 'strlen'));
			$result = FALSE;
			if($new)
			{
				// one parameter row is created per selected sector
				foreach ($sectors as $key => $value)
				{
					$data['sectorid']=$value;
					$result=$this->parameter_model->insertparameter($data);
				}
				
				if($result)
				{

					$this->session->set_flashdata('message', 'New Parameter added successfully !');
					if($this->input->post('zed-save')=='Save')
					{
						redirect(base_url().'parameter');
					}
					else
					{
						redirect(base_url().'parameter/addedit');
					}
					
				}
			}
			else
			{
				// an existing row belongs to exactly one sector: update it once
				// (looping here used to overwrite the same row once per sector)
				if(!empty($sectors))
				{
					$data['sectorid']=$sectors[0];
				}
				$result=$this->parameter_model->updateparameter($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Parameter updated successfully !');
					if($this->input->post('zed-save')=='Save')
					{
						redirect(base_url().'parameter');
					}
					else
					{
						redirect(base_url().'parameter/addedit');
					}
					
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->parameter_model->selectparameter($id);
		}
		$this->data['sector_parameter']=$parent_sector;
		$this->data['sector_discipline']=$parent_discipline;
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','parameter'=>'Parameter','addedit'=>($new ? 'New Parameter' : 'Edit Parameter')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}