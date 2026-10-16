<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Resources extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('resources/resources_model');
	}
	public function index()
	{
		$this->data['details']=$this->resources_model->allresources();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','assets'=>'Resources'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
		
	}
	/**delte categories
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_RESOURCES,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Files are deleted !');
					redirect('resources');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','File is deleted !');
					redirect('resources');
				}
			}

		}

	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );

		if($this->input->post('zed-save'))
		{
			//$data['resources_name']=basename(save_file('r_file_upload'));
			//$data['resources_slug']=save_file('r_file_upload');
			$config['upload_path'] = EDITOR_PATH;
			$config['allowed_types'] = ZED_DOC_TYPES;
			$config['max_size']       = 20480;
			$config['overwrite']=FALSE;
			$config['remove_spaces']=TRUE;
			$config['file_ext_tolower']=TRUE;
			$config['encrypt_name']=TRUE;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
				if(!$new && empty($_FILES['r_file_upload']['name']))
				{
					// editing without choosing a new file: keep the existing file
				}
 				elseif(!$this->upload->do_upload('r_file_upload'))
				{
 					$this->session->set_flashdata('file_errors', $this->upload->display_errors());
					 redirect(current_url());
					
				}
				else
				{
					$image_details=$this->upload->data();
					$file=URL_FOLDER.'uploads/'.trim($image_details['file_name']);
					// stored under a random name; keep the original name for display
					$data['resources_name']=$this->security->sanitize_filename($image_details['client_name']);
					$data['resources_slug']='uploads/'.trim($image_details['file_name']);
					 
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
				$result=$this->resources_model->insertresources($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New resources added successfully !');
					redirect(base_url().'resources');
				}
			}
			else
			{
				$result=$this->resources_model->updateresources($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'resources updated successfully !');
					redirect(base_url().'resources');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->resources_model->selectresources($id);
		}
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','assets'=>'Resources','addedit'=>'addedit'));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
	
}