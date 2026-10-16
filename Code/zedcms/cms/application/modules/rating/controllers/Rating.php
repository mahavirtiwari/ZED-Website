<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Rating extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('rating/rating_model');
		$this->load->model('sector/sector_model');
	}
	public function index()
	{
		$this->data['details']=$this->rating_model->allrating();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','rating'=>'Rating'));
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
			$this->db->where_in('rating_id', $ids);
			$this->db->update('zed_ratings',$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Ratings are deleted !');
					redirect(base_url().'rating');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Rating is deleted !');
					redirect(base_url().'rating');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'rating');
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		/*$this->form_validation->set_rules('s_code', 'Sector Code', 'callback_checkduplicatesector');*/
		$this->form_validation->set_rules("ep_name" ,"Post Description",'required');
		$sector=$this->sector_model->dropdownsector();
		$parent_sector=array();
		foreach ($sector as $key => $value) {
			 $parent_sector[$value['code']."-".$value['name']]=$value['code']."-".$value['name'];
		}
		if($this->form_validation->run()==TRUE)
		{
			$data['ep_name']=$this->input->post('ep_name');
			$data['ep_url']=$this->input->post('ep_url');
			$data['sector']=$this->input->post('sector');
			$data['unit_address']=$this->input->post('unit_address');
			$data['state']=$this->input->post('state');
			$data['size']=$this->input->post('size');
			$data['rating']=$this->input->post('rating');
			$data['status'] =$this->input->post('r_status')=='on' ? 1 : 0;
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
			$data = $this->security->xss_clean($data);
			if($new)
			{
				$result=$this->rating_model->insertsector($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Rating added successfully !');
					redirect(base_url().'rating');
				}
			}
			else
			{
				$result=$this->rating_model->updaterating($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Rating updated successfully !');
					redirect(base_url().'rating');
				}
			}
		}
		else
		{
			$this->data['form_errors']=$this->form_validation->error_array();
		}
		if(!$new)
		{
			$this->data['details'] = $this->rating_model->selectrating($id);
		}
		$this->data['parent']=$parent_sector;
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','rating'=>'Rating','addedit'=>($new ? 'New Rating' : 'Edit Rating')));
		$this->data['page']='addedit';

		$this->_render_page(get_template(),$this->data);
	}
}