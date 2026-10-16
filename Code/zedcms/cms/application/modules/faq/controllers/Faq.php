<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Faq extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$groups = array('superadmin','admin');
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($groups))
		{
			redirect('auth/login', 'refresh');
		}
		$this->load->model('faq/faq_model');
	}
	public function index()
	{
		$this->data['details']=$this->faq_model->faq();
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','faq'=>'FAQ'));
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);
	}

	/**
	* Delete 
	* @var
	* @var 
	*
	**/
	public function bulk_actions()
	{
		if($this->input->post("ids"))
		{	
			$ids=(array)$this->input->post("ids");
			$data=array('status'=>2);
			$this->db->where_in('id', $ids);
			$this->db->update(TBL_FAQ,$data);
			if($this->db->affected_rows())
			{
				if(count($ids)>1)
				{
					$this->session->set_flashdata('message','FAQS are deleted !');
					redirect(base_url().'faq');
				}
				else if(count($ids)==1)
				{
					$this->session->set_flashdata('message','FAQ is deleted !');
					redirect(base_url().'faq');
				}
			}

		}

		// nothing selected or nothing changed: back to the list, not a blank page
		redirect(base_url().'faq');
	}
	/**
	 * Duplicate check for faq
	 *
	 * @param	
	 * @return	Boolean
	 */	
	function checkduplicatefaq()
	{
		$q = $this->db->query('SELECT id FROM '.TBL_FAQ.' WHERE LOWER(faq_question) = ? AND status != ? AND id != ?',array(strtolower($this->input->post('f_question',TRUE)),2,(int)$this->input->post('id')));
		$data = $q->result_array();
		// the record being edited is excluded above, so any row left is a duplicate
		if (count($data) == 0) {
			return true;
		}else {
			$this->form_validation->set_message('checkduplicatefaq', 'FAQ Question already exists !');
			return false;
		}
	}
	public function addedit($id='')
	{
		$id=(isset($_POST['id']) ? $_POST['id'] : $id);
		$new=($id=='' ? TRUE : FALSE);
		$this->form_validation->set_rules('f_question', 'FAQ Question', 'trim|required|callback_checkduplicatefaq');
		if($this->form_validation->run()==TRUE)
		{
			$data['faq_question']=$this->input->post('f_question');
			$data['faq_answer']=$this->input->post('f_answer');
			$data['faq_order'] =$this->input->post('f_order');
			$data['status']=$this->input->post('f_status')=='on' ? 1 : 0;
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
			$data=xss_clean($data);
			if($new)
			{
				$result=$this->faq_model->insertFaq($data);
				if($result)
				{
					$this->session->set_flashdata('message','FAQ Added Successfully');
					if($this->input->post('zed-save')=='Save')
					{
						redirect(base_url().'faq');
					}
					else
					{
						redirect(base_url().'faq/addedit');
					}
					
				}
			}
			else
			{
				$result=$this->faq_model->updateFaq($id,$data);
				if($result)
				{
					$this->session->set_flashdata('message','FAQ Updated Successfully');
					if($this->input->post('zed-save')=='Save')
					{
						redirect(base_url().'faq');
					}
					else
					{
						redirect(base_url().'faq/addedit');
					}
				}

			}
		}
		if(!$new)
		{
			$this->data['details'] = $this->faq_model->selectFaq($id);
		}
		$this->data['bread_crumb']=render_bread_crumb(array('dashboard'=>'Dashboard','faq'=>'FAQ','addedit'=>$new ? 'Add FAQ' : 'Edit FAQ'));
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