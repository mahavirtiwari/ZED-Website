<?php 
Class Program_model extends CI_Model
{
	public function insertprogram($data)
	{
		$this->db->insert(TBL_PROGRAM, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateprogram($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_PROGRAM, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectprogram($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_PROGRAM);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function dropdownsector()
	{
		$this->db->select("sectorid,code,name");
		$this->db->from(TBL_PROGRAM);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allprogram()
	{
		$this->db->select("*");
		$this->db->from(TBL_PROGRAM);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
}