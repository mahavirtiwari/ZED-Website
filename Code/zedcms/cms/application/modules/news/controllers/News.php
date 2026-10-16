<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class News extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin','contentmanager');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('posts_model');
	}
	/*
		grid page for posts
	*/
		public function index()
		{
			$this->data['details']=$this->db->select('id,post_name,post_slug,status,created_by,modified_by')->where('status!=',2)->order_by('id','DESC')->get(TBL_POSTS)->result();
			$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','posts'=>'News'));
			$this->data['page']='index';
			$this->_render_page(get_template(),$this->data);
		}
	/**delte Posts
	 * 
	 */
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_POSTS,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','News are deleted !');
					redirect(base_url().'news');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','News is deleted !');
					redirect(base_url().'news');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'news');
	}
	/**
	 * Duplicate check for Post
	 *
	 * 
	 * @return	Boolean
	 */	
	function checkduplicatepost()
	{
		$q = $this->db->query('SELECT id FROM '.TBL_POSTS.' WHERE LOWER(post_name) = ? AND status != ? AND id != ?',array(strtolower($this->input->post('p_name',TRUE)),2,(int)$this->input->post('id')));
		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatepost', 'News already exists !');
			return false;
		}
	}
	/**
	   add and edit page
	*/
	   public function addedit($id='')
	   {
	   	$id=base64_decode($id);
        $id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
        $new = ($id=='' ? TRUE : FALSE );
        $categories=array();
        $result_cat=$this->posts_model->categories();
        foreach ($result_cat as $key => $value) 
        {
        	$categories[$value['id']]=$value['category_name'];
        }
        $this->data['categories']=$categories; 
        //$this->form_validation->set_rules("p_name" ,"News Name",'callback_checkduplicatepost');
        if($this->input->post())
        {
        	if($this->input->post('zed-external'))
        	{
        		$data['post_name']=$this->input->post('p_name');
        		$data['post_slug']=url_title(strtolower($this->input->post('p_name')));
        		$data['post_external_link']=$this->input->post('p_external_link');
        		$data['post_category']=2; 
        		$data['post_image']='';
        		$data['featured_name']='';
        		$data['featured_slug']='';
        		$data['post_description']='';
        		$data['post_seo_title']='';
        		$data['post_seo_keywords']='';
        		$data['post_seo_description']='';
        		// 
        		$data['post_display_date']=$this->input->post('p_display_date') ? $this->input->post('p_display_date') : NULL;
        		$data['post_breaking_news']=$this->input->post('p_breaking_news') ? 1 : 0;
        		$data['post_latest_news']=!empty($this->input->post('p_latest_news')) ? 1 : 0;
        		$data['status']		  =$this->input->post('p_status')=='on' ? 1 : 0;

        	}
        	if($this->input->post('zed-fileupload'))
        	{
        		$data['post_name']=$this->input->post('p_name');
        		$data['post_slug']=url_title(strtolower($this->input->post('p_name')));
        		$data['post_category']=3;
        		$data['post_image']=save_file('p_file_upload');
        		// 
        		$data['post_external_link']='';
        		$data['featured_name']='';
        		$data['featured_slug']='';
        		$data['post_description']='';
        		$data['post_seo_title']='';
        		$data['post_seo_keywords']='';
        		$data['post_seo_description']='';
        		// 
        		$data['post_display_date']=$this->input->post('p_display_date') ? $this->input->post('p_display_date') : NULL;
        		$data['post_breaking_news']=$this->input->post('p_breaking_news') ? 1 : 0;
        		$data['status']		  =$this->input->post('p_status')=='on' ? 1 : 0;
        	}
        	if($this->input->post('zed-save'))
        	{
        		$data['post_name']=$this->input->post('p_name');
        		$data['post_category']=1;
        		$data['post_slug']=url_title(strtolower($this->input->post('p_name')));
        		/*$data['featured_name']=basename(gallery_upload('news_featured_image'));
        		$data['featured_slug']=gallery_upload('news_featured_image');*/
        		if(!empty($_POST['imageData']) && !empty($_FILES['news_featured_image']['name']))
        		{
        			// only a real PNG/JPEG/GIF is written, under a random name
        			$image_name = save_base64_image($_POST['imageData'], UPLOAD_DIR_NEWS);
        			if($image_name === FALSE)
        			{
        				$this->session->set_flashdata('message1', 'Invalid featured image, please upload a PNG, JPG or GIF file.');
        				redirect(current_url());
        			}
        			$data['featured_name']=$image_name;
        			$data['featured_slug']=URL_FOLDER.'photogallery/'.$image_name;
        		}
        		else
        		{
        			$data['featured_slug']=$this->input->post('existed_file');
        		}
        		$data['post_description']=$this->input->post('p_description');
        		$data['post_seo_title']=$this->input->post('p_seo_title');
        		$data['post_seo_keywords']=$this->input->post('p_seo_keywords');
        		$data['post_seo_description']=$this->input->post('p_seo_description');
        		$data['post_breaking_news']=$this->input->post('p_breaking_news') ? 1 : 0;
        		$data['post_latest_news']=!empty($this->input->post('p_latest_news')) ? 1 : 0;
        		$data['status']		  =$this->input->post('p_status')=='on' ? 1 : 0;
        		$data['post_display_date']=$this->input->post('p_display_date') ? $this->input->post('p_display_date') : NULL;	
        		// 
        		$data['post_external_link']='';
        		$data['post_image']='';
        		
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
        	if(!empty($data['post_slug']))
        	{
        		// check_slug() returns an empty array when no other Page or News uses this slug
        		$check_news_pages_slug=$this->posts_model->check_slug($data['post_slug'], $new ? 0 : $id);
        		if(empty($check_news_pages_slug))
        		{
        			if($new)
        			{
        				$result=$this->posts_model->insertpost($data);
        				if($result)
        				{
        					$this->session->set_flashdata('message', 'New News added successfully !');
        					redirect(base_url().'news');
        				}
        			}
        			else
        			{
        				
        				$result=$this->posts_model->updatepost($id,$data);
        				if($result)
        				{
        					$this->session->set_flashdata('message', 'News updated successfully !');
        					redirect(base_url().'news');
        				}
        			}
        		}
        		else
        		{
        			// the form is shown again in this request, so no flashdata (it would also show on the next page)
        			$this->data['message1']='This URL is already used by another Page or News item, please change the News Name';

        		}
        	}
        	
        }
        if(!$new)
        {
        	$this->data['details'] = $this->posts_model->selectpost($id);
        }
        $this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','posts'=>'News','addedit'=>($new ? 'New News' : 'Edit News')));
        $this->data['page']='addedit';
        $this->_render_page(get_template(),$this->data);
    }
	/**
	 * Image upload from the Trumbowyg editor
	 */
	public function tbwimage()
	{
		editor_image_upload();
	}
	/*==========news block================*/
	public function news_block()
	{
		if(isset($_POST['zed-save']))
		{
			unset($_POST['zed-save']);
			//print_r($this->input->post());
			$data_1['news_block_id']=1;
			$data_1['news_block_status']=$this->input->post('block_status_1');
			$data_1['news_block_heading']=$this->input->post('block_heading_1');
			$data_2['news_block_id']=2;
			$data_2['news_block_status']=$this->input->post('block_status_2');
			$data_2['news_block_heading']=$this->input->post('block_heading_2');
			$data_3['news_block_id']=3;
			$data_3['news_block_status']=$this->input->post('block_status_3');
			$data_3['news_block_heading']=$this->input->post('block_heading_3');
			
			$this->db->where('news_block_id', 1);
			$this->db->update(TBL_NEWS_BLOCK, $data_1); 
			$this->db->where('news_block_id', 2);
			$this->db->update(TBL_NEWS_BLOCK, $data_2); 
			$this->db->where('news_block_id', 3);
			$this->db->update(TBL_NEWS_BLOCK, $data_3); 
			
			$this->session->set_flashdata('message', 'News Block Settings saved successfully !');
			redirect(current_url());
			
			
			
		}
		$query= $this->db->select('*')->get(TBL_NEWS_BLOCK)->result_array();
		foreach ($query as $key => $value) {
			$this->data['details'][]=$value['news_block_status'];
			$this->data['details'][]=$value['news_block_heading'];
		}
		$this->data['results']=$this->data['details'];

		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','news'=>'News','news_block'=>'News Block'));
		$this->data['page']='news_block';
		$this->_render_page(get_template(),$this->data);
	}
}