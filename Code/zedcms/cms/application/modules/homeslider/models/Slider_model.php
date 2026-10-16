<?php 
Class Slider_model extends CI_Model
{
	public function insertslider($data)
	{
		$this->db->insert(TBL_SLIDER, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateslider($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_SLIDER, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectslider($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_SLIDER);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}