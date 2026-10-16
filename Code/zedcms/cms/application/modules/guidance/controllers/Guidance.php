<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Guidance extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('guidance/guidance_model');
		$this->load->model('sector/sector_model');
	}
	public function index()
	{
		$this->data['details']=$this->guidance_model->alllist();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','guidancedocument'=>'Guidance Document'));
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
			$this->db->where_in('id', $ids);
			$this->db->update('zed_guidance_document',$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Guidance Documents are deleted !');
					redirect(base_url().'guidance');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Guidance Document is deleted !');
					redirect(base_url().'guidance');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'guidance');
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		/*$this->form_validation->set_rules('s_code', 'Sector Code', 'callback_checkduplicatesector');*/
		$this->form_validation->set_rules("d_name" ,"Name ",'required');
		$sector=$this->sector_model->dropdownsector();
		$parent_sector=array();
		foreach ($sector as $key => $value) {
			 $parent_sector[$value['sectorid']]=$value['code']."-".$value['name'];
		}
		if($this->form_validation->run()==TRUE)
		{
			$data['d_name']=$this->input->post('d_name');
			$data['d_url']=$this->input->post('d_url');
			$data['sector']=$this->input->post('sector');
			$data['status'] =$this->input->post('status')=='on' ? 1 : 0;
			if($new)
			{
				$data['created_at']=global_datetime();
				$data['created_by']=$this->ion_auth->user()->row()->id;
			}
			else
			{
				$data['modified_at']=global_datetime();
				$data['modified_by']=$this->ion_auth->user()->row()->id;
			}
			/*$data = $this->security->xss_clean($data);*/
			if($new)
			{
				$result=$this->guidance_model->insertElement($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Guidance Document added successfully !');
					redirect(base_url().'guidance');
				}
			}
			else
			{
				$result=$this->guidance_model->updateElement($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Guidance Document updated successfully !');
					redirect(base_url().'guidance');
				}
			}
		}
		else
		{
			$this->data['form_errors']=$this->form_validation->error_array();
		}
		if(!$new)
		{
			$this->data['details'] = $this->guidance_model->selectElement($id);
		}
		$this->data['parent']=$parent_sector;
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','guidancedocument'=>'Guidance Document','addedit'=>($new ? 'New Guidance Document' : 'Edit Guidance Document')));
		$this->data['page']='addedit';

		$this->_render_page(get_template(),$this->data);
	}
}