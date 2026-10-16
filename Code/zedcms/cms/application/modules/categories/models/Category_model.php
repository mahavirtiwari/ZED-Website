<?php 
Class Category_model extends CI_Model
{
	public function insertcategory($data)
	{
		$this->db->insert(TBL_CATEGORIES, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatecategory($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_CATEGORIES, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectcategory($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_CATEGORIES);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}