<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ratingimages extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('ratingimages/ratingimages_model');
	}
	public function index()
	{
		$this->data['details']=$this->ratingimages_model->allresources();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','ratingimages'=>'Rating Images'));
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
			$this->db->update(TBL_RATINGPROCESS,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Images are deleted !');
					redirect('ratingimages');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Image is deleted !');
					redirect('ratingimages');
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
			$config['upload_path'] = EDITOR_PATH.'rating_images/';
			$config['allowed_types'] = 'gif|jpeg|jpg|png';
			$config['overwrite']=FALSE;
			$config['remove_spaces']=TRUE;
			$config['file_ext_tolower']=TRUE;
			$config['encrypt_name']=TRUE;
			$this->load->library('upload', $config);
			// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
			$this->upload->initialize($config);
			if(!$new && empty($_FILES['ratingprocess_image_upload']['name']))
			{
				// editing without choosing a new image: keep the existing file
			}
			elseif(!$this->upload->do_upload('ratingprocess_image_upload'))
			{
 				$this->session->set_flashdata('file_errors', $this->upload->display_errors());
				 redirect(current_url());
			}
			else
			{

				$image_details=$this->upload->data();
				$file=URL_FOLDER.'uploads/rating_images/'.trim($image_details['file_name']);
				$data['name']=basename($file);
				$data['image_path']='uploads/rating_images/'.trim($image_details['file_name']);

			}
			$data['status']=1;
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
				$this->ratingimages_model->deactivateImages();
				$result=$this->ratingimages_model->insertresources($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Image added successfully !');
					redirect(base_url().'ratingimages');
				}
			}
			else
			{
				$result=$this->ratingimages_model->updateresources($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Image updated successfully !');
					redirect(base_url().'ratingimages');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->ratingimages_model->selectresources($id);
		}
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','assets'=>'ratingimages','addedit'=>'addedit'));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
	
}