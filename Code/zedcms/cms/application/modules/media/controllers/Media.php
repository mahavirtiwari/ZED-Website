<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Media extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
	}
	public function index()
	{
		$this->data['details']=$this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_MEDIA)->result();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','media'=>'Gallery'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	public function do_upload()
	{
		$upload_path_url = base_url().'uploads/';
		$config['upload_path'] = FCPATH.'uploads/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size'] = '30000';
		$config['overwrite'] = FALSE;
		$config['file_ext_tolower'] = TRUE;
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if(isset($_FILES['userfile']))
		{
			if ( ! $this->upload->do_upload())
			{
				echo strip_tags($this->upload->display_errors());
			}
			else 
			{
				$file = $this->upload->data();

				$data['media_name']=trim($file['file_name']);
				$data['media_slug']=base_url().'uploads/'.trim($file['file_name']);
				$data['media_type']=substr($file['file_type'],0,strpos($file['file_type'],'/'));
				$data['created_on']=global_datetime();
				$data['created_by']=$this->ion_auth->user()->row()->id;
				$this->db->insert(TBL_MEDIA, $data);
				if($this->db->affected_rows())
				{
					echo "file iploaded successfully";
				}
				else
				{
					echo "file not uploaded successfully";
				}


			}
		}

	}
	
	public function addedit($id='')
	{
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','media'=>'Add Gallery'));
		$this->data['page']='addedit';
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
					redirect(base_url().'media');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Image is deleted !');
					redirect(base_url().'media');
				}
			}

		}

	}
}