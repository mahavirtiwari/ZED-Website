<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Programs extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('programs/program_model');
	}
	public function index()
	{
		$this->data['details']=$this->program_model->allprogram();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','programs'=>'Programs'));
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
			$this->db->update(TBL_PROGRAM,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Programs are deleted !');
					redirect(base_url().'programs');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Program is deleted !');
					redirect(base_url().'programs');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'programs');
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		if(isset($_POST['zed-save']))
		{
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name']))
			{
				$config['upload_path'] = EDITOR_PATH.'programs/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png';
				$config['overwrite']=FALSE;
				$config['remove_spaces']=TRUE;
				$config['file_ext_tolower']=TRUE;
				$config['encrypt_name']=TRUE;
				$this->load->library('upload', $config);
				// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
				$this->upload->initialize($config);
				if(!$this->upload->do_upload('image'))
				{
	 				$this->session->set_flashdata('file_errors', $this->upload->display_errors());
					 redirect(current_url());
				}
				else
				{

					$image_details=$this->upload->data();
					$file=URL_FOLDER.'uploads/programs/'.trim($image_details['file_name']);
					$data['image']='uploads/programs/'.trim($image_details['file_name']);

				}
			}
		}
			$data['name']=$this->input->post('name');
			$data['caption']=$this->input->post('caption');
			$data['button_name']=$this->input->post('button_name');
			$data['link']=$this->input->post('link');
			$data['status']=$this->input->post('status');
			$data['sorting']=$this->input->post('sorting');
			$data['status']		  =$this->input->post('status')=='on' ? 1 : 0;
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
			if($this->input->post('zed-save')){

				if($new)
				{
					$result=$this->program_model->insertprogram($data);
					if($result)
					{
						$this->session->set_flashdata('message', 'New Program added successfully !');
						redirect(base_url().'programs');
					}
				}
				else
				{
					$result=$this->program_model->updateprogram($id,$data);
					if($result)
					{
						$this->session->set_flashdata('message', 'Program updated successfully !');
						redirect(base_url().'programs');
					}
				}
			}
			//$data = $this->security->xss_clean($data);
		if(!$new)
		{
			$this->data['details'] = $this->program_model->selectprogram($id);
		}
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','programs'=>'Program','addedit'=>($new ? 'New Program' : 'Edit Program')));
		$this->data['page']='addedit';

		$this->_render_page(get_template(),$this->data);
	}
}