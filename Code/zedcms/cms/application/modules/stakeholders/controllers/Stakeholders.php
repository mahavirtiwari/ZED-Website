<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stakeholders extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('stakeholders/stakeholders_model');
	}
	public function index()
	{
		$this->data['details']=$this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_STAKEHOLDERS)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','stakeholder'=>'Stakeholders'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);

	}
	/**delte images
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_STAKEHOLDERS,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Stakeholders are deleted !');
					redirect(base_url().'stakeholders');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Stakeholder is deleted !');
					redirect(base_url().'stakeholders');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'stakeholders');
	}

	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		if(isset($_POST['zed-save']))
		{
			$data['stakeholder_title']=$this->input->post('stake_title');
			// upload once and reuse the result for both columns
			$uploaded=(string)gallery_upload('stake_logo');
			$data['stakeholder_logo']=basename($uploaded);
			$data['stakeholder_slug']=$uploaded;
			if($this->input->post('stake_caption'))
			{
				$data['stakeholder_caption']=$this->input->post('stake_caption');
			}
			if($this->input->post('stake_link'))
			{
				$data['stakeholder_link']=$this->input->post('stake_link');
			}
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
			if($new)
			{
				$result=$this->stakeholders_model->insertstake($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Stakeholder added successfully !');
					redirect(base_url().'stakeholders');
				}
			}
			else
			{
				$result=$this->stakeholders_model->updatestake($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Stakeholder Updated successfully!');
					redirect(base_url().'stakeholders');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->stakeholders_model->selectstake($id);
		}
		
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','stake'=>'Stakeholder','addstakeholder'=>($new ? 'New Stakeholder' : 'Add Stakeholder')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}