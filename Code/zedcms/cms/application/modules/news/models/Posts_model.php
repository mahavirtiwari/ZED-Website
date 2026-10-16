<?php
Class Posts_model extends CI_Model
{
	public function categories()
	{
		return $this->db->select('id,category_name')->where('status',1)->get(TBL_CATEGORIES)->result_array();
	}
	public function insertpost($data)
	{
		$this->db->insert(TBL_POSTS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatepost($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_POSTS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectpost($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_POSTS);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function check_slug($slug)
	{
		return $this->db->select("page_slug")->where(array('status'=>1,'page_slug'=>$slug))->limit(1)->get(TBL_PAGES)->result_array();
		
	}
}