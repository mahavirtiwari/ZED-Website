<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Homevideos extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('homevideos/homevideos_model');
	}
	public function index()
	{
		$this->data['details']=$this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_VIDEOS)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','homevideos'=>'Home Videos'));
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
			$this->db->update(TBL_VIDEOS,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Videos are deleted !');
					redirect(base_url().'homevideos');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Video is deleted !');
					redirect(base_url().'homevideos');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'homevideos');
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		if(isset($_POST['zed-save']))
		{
			$data['video_title']=$this->input->post('v_title');
			$data['video_description']=$this->input->post('v_description');
			$data['video_link']=$this->input->post('v_link');
			$data['video_caption']=$this->input->post('v_caption');
			$data['video_sorting']=$this->input->post('v_sorting');
			$data['status']		  =$this->input->post('v_status')=='on' ? 1 : 0;
			
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
				$result=$this->homevideos_model->insertvideo($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Video added successfully !');
					redirect(base_url().'homevideos');
				}
			}
			else
			{
				$result=$this->homevideos_model->updatevideo($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Video Updated successfully!');
					redirect(base_url().'homevideos');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->homevideos_model->selectvideo($id);
		}
		
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','homevideos'=>'Home Videos','addedit'=>($new ? 'New Home Video' : 'Edit Home Video')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}