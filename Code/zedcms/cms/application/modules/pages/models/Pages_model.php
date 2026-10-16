<?php
Class Pages_model extends CI_Model
{
	public function pages()
	{
		return $this->db->select('id,page_name,page_slug,page_type,status,created_by,created_on,modified_by,modified_on')->where('status!=',2)->order_by('created_on','DESC')->get(TBL_PAGES)->result();
	}
	public function insertpage($data)
	{
		$this->db->insert(TBL_PAGES, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatepage($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_PAGES, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectpage($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_PAGES);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function menus()
	{
		return $this->db->select('id,menu_name')->where('status',1)->order_by('menu_order')->get(TBL_MENU)->result();
	}
	/**
	 * Slugs share one URL space (site root): a page slug may not be used by any
	 * news item, or by another page, that is not deleted.
	 * @return array  empty when the slug is free
	 */
	public function check_slug($slug, $id=0)
	{
		$used = $this->db->select("post_slug")->where(array('status!='=>2,'post_slug'=>$slug))->limit(1)->get(TBL_POSTS)->result_array();
		if(empty($used))
		{
			$used = $this->db->select("page_slug")->where(array('status!='=>2,'page_slug'=>$slug,'id!='=>(int)$id))->limit(1)->get(TBL_PAGES)->result_array();
		}
		return $used;
	}
}