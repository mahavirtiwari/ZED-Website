<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pages extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin','contentmanager');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('pages/pages_model');

	}
	public function index()
	{
		$this->data['details']=$this->pages_model->pages();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','pages'=>'Pages'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}
	/**delte Pages
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_PAGES,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Pages are deleted !');
					redirect(base_url().'pages');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Page is deleted !');
					redirect(base_url().'pages');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'pages');
	}
	/**
	 * Duplicate check for Page
	 *
	 * @return	Boolean
	 */	
	function checkduplicatepage()
	{
		$q = $this->db->query('SELECT id FROM '.TBL_PAGES.' WHERE LOWER(page_name) = ? AND status != ? AND id != ?',array(strtolower($this->input->post('pg_name',TRUE)),2,(int)$this->input->post('id')));
		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatepage', 'Page already exists !');
			return false;
		}
	}
	/**
	   add and edit page
	*/
	   public function addedit($id='')
	   {
	   	$id=base64_decode($id);
	   	$id=(isset($_POST['id']) ? $_POST['id'] : $id);
	   	$new=($id==''? TRUE: FALSE);
	   	$menu=array();
	   	$menu['']='Select Menu';
	   	foreach ($this->pages_model->menus() as $key => $value) {
	   		$menu[$value->id]=$value->menu_name;
	   	};
	   	$this->data['page_menu']=$menu;

	   	$this->form_validation->set_rules("pg_name",'Page Name','callback_checkduplicatepage');
	   	if($this->form_validation->run()==TRUE)
	   	{
	   		$data['page_name']=$this->input->post('pg_name');
	   		$data['page_slug']=url_title(strtolower($this->input->post('pg_name')));
	   		$data['page_description']=$this->input->post('pg_description');
	   		$data['page_type']=$this->input->post('pg_type');
	   		$data['page_seo_title']=$this->input->post('pg_seo_title');
	   		$data['page_seo_keywords']=$this->input->post('pg_seo_keywords');
	   		$data['page_seo_description']=$this->input->post('pg_seo_description');
	   		$data['status']		  =$this->input->post('pg_status')=='on' ? 1 : 0;
	   		if($this->input->post('pg_menu'))
	   		{
	   			$data['page_menu']=$this->input->post('pg_menu');
	   		}
	   		if($this->input->post('pg_short_description'))
	   		{
	   			$data['page_short_description']=$this->input->post('pg_short_description');
	   		}
	   		if($this->input->post('pg_order'))
	   		{
	   			$data['page_home_order']=$this->input->post('pg_order');
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
	   		//$data=xss_clean($data);
	   		if(!empty($data['page_slug']))
	   		{
	   			// check_slug() returns an empty array when no News or other Page uses this slug
	   			$check_news_pages_slug=$this->pages_model->check_slug($data['page_slug'], $new ? 0 : $id);
	   			if(empty($check_news_pages_slug))
	   			{
	   				if($new)
	   				{
	   					$result=$this->pages_model->insertpage($data);
	   					if($result)
	   					{
	   						$this->session->set_flashdata('message', 'New Page added successfully !');
	   						redirect(base_url().'pages');
	   					}
	   				}
	   				else
	   				{
	   					$result=$this->pages_model->updatepage($id,$data);
	   					if($result)
	   					{
	   						$this->session->set_flashdata('message', 'Page updated successfully !');
	   						redirect(base_url().'pages');
	   					}
	   				}
	   			}
	   			else
	   			{
	   				// the form is shown again in this request, so no flashdata (it would also show on the next page)
	   				$this->data['message1']='This URL is already used by a News item or another Page, please change the Page Name';

	   			}
	   		}
	   		
	   		
	   		
	   	}
	   	if(!$new)
	   	{
	   		$this->data['details'] = $this->pages_model->selectpage($id);
	   	}
	   	$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'dashboard','pages'=>($new ?  'New Page': 'Edit Page')));
	   	$this->data['page']='addedit';
	   	$this->_render_page(get_template(),$this->data);

	   }
	/*
		Image uploading Handle in trumbowyg
	*/
		public function tbwimage()
		{
			editor_image_upload();
		}
	}