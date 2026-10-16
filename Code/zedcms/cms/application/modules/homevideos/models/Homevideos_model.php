<?php 
Class Homevideos_model extends CI_Model
{
	public function insertvideo($data)
	{
		$this->db->insert(TBL_VIDEOS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatevideo($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_VIDEOS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectvideo($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_VIDEOS);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}