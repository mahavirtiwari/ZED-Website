<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Gallery extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('gallery/gallery_model');
	}
	public function index()
	{
		$this->data['details']=$this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_MEDIA)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','media'=>'Gallery'));
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
			$this->db->update(TBL_MEDIA,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Images are deleted !');
					redirect(base_url().'gallery');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Image is deleted !');
					redirect(base_url().'gallery');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'gallery');
	}

	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		if(isset($_POST['zed-save']))
		{
			$data['media_title']=$this->input->post('g_title');
			// upload once and reuse the result for both columns
			$uploaded=(string)gallery_upload('g_image');
			$data['media_name']=basename($uploaded);
			$data['media_slug']=$uploaded;
			$data['media_caption']=$this->input->post('g_caption');
			$data['media_seo_alt_text']=$this->input->post('g_seo_alt_text');
			
			
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
				$result=$this->gallery_model->insertimage($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Image added successfully !');
					redirect(base_url().'gallery');
				}
			}
			else
			{
				$result=$this->gallery_model->updateimage($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Image Updated successfully!');
					redirect(base_url().'gallery');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->gallery_model->selectimage($id);
		}
		
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','gallery'=>'Gallery','addimage'=>'Add Image'));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}