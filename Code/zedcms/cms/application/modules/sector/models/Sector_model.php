<?php 
Class Sector_model extends CI_Model
{
	public function insertsector($data)
	{
		$this->db->insert(TBL_SECTOR, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatesector($id,$data)
	{
		$this->db->where('sectorid',$id);
		$this->db->update(TBL_SECTOR, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectsector($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_SECTOR);
		$this->db->where('sectorid', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function dropdownsector()
	{
		$this->db->select("sectorid,code,name");
		$this->db->from(TBL_SECTOR);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allsector()
	{
		$this->db->select("*");
		$this->db->from(TBL_SECTOR);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}