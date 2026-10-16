<?php 
Class Rating_model extends CI_Model
{
	public function insertsector($data)
	{
		$this->db->insert('zed_ratings', $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updaterating($id,$data)
	{
		$this->db->where('rating_id',$id);
		$this->db->update('zed_ratings', $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectrating($id)
	{
		$this->db->select("*");
		$this->db->from('zed_ratings');
		$this->db->where('rating_id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function dropdownsector()
	{
		$this->db->select("sectorid,code,name");
		$this->db->from('zed_ratings');
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allrating()
	{
		$this->db->select("*");
		$this->db->from('zed_ratings');
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}