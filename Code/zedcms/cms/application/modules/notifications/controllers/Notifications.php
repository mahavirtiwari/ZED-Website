<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Notifications extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('notifications/notification_model');
	}
	public function index()
	{
		$this->data['details']=$this->notification_model->allnotifications();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','notifications'=>'Notifications'));
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
			$this->db->update(TBL_NOTIFICATIONS,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Notifications are deleted !');
					redirect(base_url().'notifications');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Notification is deleted !');
					redirect(base_url().'notifications');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'notifications');
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
			$data['name']=$this->input->post('name');
			$data['status']=$this->input->post('status');
			$data['sorting']=$this->input->post('sorting');
			$data['status']		  =$this->input->post('status')=='on' ? 1 : 0;
			$data['type']=$this->input->post('type');
			$data['link']=$this->input->post('link');
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
				if(isset($_FILES['r_file_upload']['name']) && !empty($_FILES['r_file_upload']['name']))
				{
					$config['upload_path'] = EDITOR_PATH;
					$config['allowed_types'] = ZED_DOC_TYPES;
					$config['max_size']       = 20480;
					$config['overwrite']=FALSE;
					$config['remove_spaces']=TRUE;
					$config['file_ext_tolower']=TRUE;
					$config['encrypt_name']=TRUE;
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					if(!$this->upload->do_upload('r_file_upload'))
					{
						$this->session->set_flashdata('file_errors', $this->upload->display_errors());
						redirect(current_url());
					}
					else
					{
						$image_details=$this->upload->data();
						$file=URL_FOLDER.'uploads/'.trim($image_details['file_name']);
						$data['file_path']='uploads/'.trim($image_details['file_name']);
					}

				}
				if($new)
				{
					$result=$this->notification_model->insertnotification($data);
					if($result)
					{
						$this->session->set_flashdata('message', 'New Notification added successfully !');
						redirect(base_url().'notifications');
					}
				}
				else
				{
					$result=$this->notification_model->updatenotification($id,$data);
					if($result)
					{
						$this->session->set_flashdata('message', 'Notification updated successfully !');
						redirect(base_url().'notifications');
					}
				}
			}
			//$data = $this->security->xss_clean($data);
		if(!$new)
		{
			$this->data['details'] = $this->notification_model->selectnotification($id);
		}
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','notifications'=>'Notifications','addedit'=>($new ? 'New Notification' : 'Edit Notification')));
		$this->data['page']='addedit';

		$this->_render_page(get_template(),$this->data);
	}
}