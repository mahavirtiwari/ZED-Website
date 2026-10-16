<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Testimonials extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('testimonials/testimonials_model');
	}
	public function index()
	{
		$this->data['details']=$this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_TESTMONILAS)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','testimonials'=>'Testimonials'));
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
			$this->db->update(TBL_TESTMONILAS,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Testimonials are deleted !');
					redirect(base_url().'testimonials');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Testimonial is deleted !');
					redirect(base_url().'testimonials');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'testimonials');
	}

	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		if(isset($_POST['zed-save']))
		{
			if(isset($_FILES['profile']['name']) && !empty($_FILES['profile']['name']))
			{
				$config['upload_path'] = EDITOR_PATH.'testimonials/';
				$config['allowed_types'] = 'gif|jpeg|jpg|png';
				$config['overwrite']=FALSE;
				$config['remove_spaces']=TRUE;
				$config['file_ext_tolower']=TRUE;
				$config['encrypt_name']=TRUE;
				$this->load->library('upload', $config);
				// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
				$this->upload->initialize($config);
				if(!$this->upload->do_upload('profile'))
				{
	 				$this->session->set_flashdata('file_errors', $this->upload->display_errors());
					 redirect(current_url());
				}
				else
				{

					$image_details=$this->upload->data();
					$file=URL_FOLDER.'uploads/testimonials/'.trim($image_details['file_name']);
					$data['profile']='uploads/testimonials/'.trim($image_details['file_name']);

				}
			}
			$data['name']=$this->input->post('name');
			$data['designation']=$this->input->post('designation');
			$data['org_name']=$this->input->post('org_name');
			$data['location']=$this->input->post('location');
			$data['description']=$this->input->post('description');
			$data['sorting']=$this->input->post('sorting');
			$data['status']=$this->input->post('status')=='on' ? 1 : 0;
			if($new)
			{
				$data['created_on']=global_datetime();
				$data['modified_on']=global_datetime();
				$data['created_by']=$this->ion_auth->user()->row()->id;
			}
			else
			{
				$data['modified_on']=global_datetime();
				$data['modified_by']=$this->ion_auth->user()->row()->id;
			}
			if($new)
			{
				$result=$this->testimonials_model->insertstake($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Testimonial added successfully !');
					redirect(base_url().'testimonials');
				}
			}
			else
			{
				$result=$this->testimonials_model->updatestake($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Testimonial Updated successfully!');
					redirect(base_url().'testimonials');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->testimonials_model->selectstake($id);
		}
		
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','testimonials'=>'Testimonials','addtestimonial'=>($new ? 'New Testimonial' : 'Add Testimonials')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}