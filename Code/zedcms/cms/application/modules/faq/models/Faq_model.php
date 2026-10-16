<?php 
Class Faq_model extends CI_Model
{
	public function insertFaq($data)
	{
		$this->db->insert(TBL_FAQ, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateFaq($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_FAQ, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectFaq($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_FAQ);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function faq()
	{
		return $this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_FAQ)->result();
	}
}