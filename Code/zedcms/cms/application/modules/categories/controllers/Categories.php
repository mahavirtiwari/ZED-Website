<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Categories extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin','contentmanager');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('categories/category_model');
		
	}
	public function index()
	{
		$this->data['details']=$this->db->where('status!=',2)->order_by('created_on','DESC')->get(TBL_CATEGORIES)->result();
		$this->data['bread_crumb'] = render_bread_crumb(array('dashboard'=>'Dashboard','categories'=>'Categories'));
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
			$this->db->update(TBL_CATEGORIES,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','Categories are deleted !');
					redirect(base_url().'categories');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','Category is deleted !');
					redirect(base_url().'categories');
				}
			}

		}

	}
	/**
	 * Duplicate check for category
	 *
	 * @param	String	$category_name
	 * @return	Boolean
	 */	
	function checkduplicatecategory()
	{
		$q = $this->db->query('SELECT id FROM '.TBL_CATEGORIES.' WHERE LOWER(category_name) = ? AND status != ? AND id != ?',array(strtolower($this->input->post('c_name',TRUE)),2,(int)$this->input->post('id')));
		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatecategory', 'Category already exists !');
			return false;
		}
	}
	public function addedit($id='')
	{
		$id = (isset($_POST['id']) ? $_POST['id'] : $id ); // If available from post (edit-case)
		$new = ($id=='' ? TRUE : FALSE );
		//$data=array();
		$this->form_validation->set_rules('c_name', 'Category Name', 'trim|required|callback_checkduplicatecategory');
		if($this->form_validation->run()==TRUE)
		{
			$data['category_name']=$this->input->post('c_name');
			$data['status']		  =$this->input->post('c_status')=='on' ? 1 : 0;
			
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
				$result=$this->category_model->insertcategory($data);
				if($result)
				{
					$this->session->set_flashdata('message', 'New Category added successfully !');
					redirect(base_url().'categories');
				}
			}
			else
			{
				$result=$this->category_model->updatecategory($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message', 'Category updated successfully !');
					redirect(base_url().'categories');
				}
			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->category_model->selectcategory($id);
		}
		
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','categories'=>'Categories','addedit'=>($new ? 'New Category' : 'Edit Category')));
		$this->data['page']='addedit';
		$this->_render_page(get_template(),$this->data);
	}
	
}