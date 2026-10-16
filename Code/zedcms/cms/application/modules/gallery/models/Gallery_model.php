<?php 
Class Gallery_model extends CI_Model
{
	public function insertimage($data)
	{
		$this->db->insert(TBL_MEDIA, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateimage($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_MEDIA, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectimage($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_MEDIA);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}