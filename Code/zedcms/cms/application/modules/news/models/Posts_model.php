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
	/**
	 * Slugs share one URL space (site root): a news slug may not be used by any
	 * page, or by another news item, that is not deleted.
	 * @return array  empty when the slug is free
	 */
	public function check_slug($slug, $id=0)
	{
		$used = $this->db->select("page_slug")->where(array('status!='=>2,'page_slug'=>$slug))->limit(1)->get(TBL_PAGES)->result_array();
		if(empty($used))
		{
			$used = $this->db->select("post_slug")->where(array('status!='=>2,'post_slug'=>$slug,'id!='=>(int)$id))->limit(1)->get(TBL_POSTS)->result_array();
		}
		return $used;
	}
}