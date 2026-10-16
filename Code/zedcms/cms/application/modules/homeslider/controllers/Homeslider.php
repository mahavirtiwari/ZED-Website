<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Homeslider extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('homeslider/slider_model');
	}
	public function index()
	{
		$this->data['details']=$this->db->where('status!=',2)->order_by('id','DESC')->get(TBL_SLIDER)->result();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','homeslider'=>'Home Sliders'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_SLIDER,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Sliders are deleted !');
					redirect(base_url().'homeslider');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Slider is deleted !');
					redirect(base_url().'homeslider');
				}
			}

		}
		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'homeslider');
	}
	public function addedit($id='')
	{
		$id=base64_decode($id);
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		if(isset($_POST['zed-save']))
		{
			/*$data['slider_name']=basename(image_upload('s_image'));
			$data['slider_slug']=image_upload('s_image');*/
			if(!empty($_POST['imageData']) && !empty($_FILES['s_image']['name']))
			{
				// only a real PNG/JPEG/GIF is written, under a random name
				$image_name = save_base64_image($_POST['imageData'], UPLOAD_DIR_SLIDER);
				if($image_name === FALSE)
				{
					$this->session->set_flashdata('message', 'Invalid image, please upload a PNG, JPG or GIF file.');
					redirect(current_url());
				}
				$data['slider_name']=$image_name;
				$data['slider_slug']=URL_FOLDER.'slider/'.$image_name;
			}
			else
			{
				$data['slider_slug']=$this->input->post('existed_file');
			}
			$data['slider_description']=$this->input->post('s_description');
			$data['slider_sorting']=$this->input->post('s_sorting');
			$data['status']		  =$this->input->post('s_status')=='on' ? 1 : 0;
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
			//$data = $this->security->xss_clean($data);
			if($new)
			{
				$result=$this->slider_model->insertslider($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Slider added successfully !');
					redirect(base_url().'homeslider');
				}
			}
			else
			{
				$result=$this->slider_model->updateslider($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Slider updated successfully !');
					redirect(base_url().'homeslider');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->slider_model->selectslider($id);
		}
		
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','homeslider'=>'Home Sliders','addedit'=>($new ? 'New Slider' : 'Edit Slider')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
}