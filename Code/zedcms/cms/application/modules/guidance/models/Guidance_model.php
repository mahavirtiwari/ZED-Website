<?php 
Class Guidance_model extends CI_Model
{
	public function insertElement($data)
	{
		$this->db->insert('zed_guidance_document', $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateElement($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update('zed_guidance_document', $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectElement($id)
	{
		$this->db->select("*");
		$this->db->from('zed_guidance_document');
		$this->db->where('id', $id);
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
	public function alllist()
	{
		$this->db->select("*");
		$this->db->from('zed_guidance_document');
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}