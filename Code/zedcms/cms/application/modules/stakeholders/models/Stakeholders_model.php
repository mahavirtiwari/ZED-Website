<?php 
Class Stakeholders_model extends CI_Model
{
	public function insertstake($data)
	{
		$this->db->insert(TBL_STAKEHOLDERS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatestake($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_STAKEHOLDERS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectstake($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_STAKEHOLDERS);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}