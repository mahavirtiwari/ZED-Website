<?php 
Class Ratingprocess_model extends CI_Model
{
	public function insertresources($data)
	{
		$this->db->insert(TBL_RATINGIMAGES, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateresources($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_RATINGIMAGES, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectresources($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_RATINGIMAGES);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allresources()
	{
		return $this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_RATINGIMAGES)->result();
	}
	public function deactivateImages()
	{
		$this->db->update(TBL_RATINGIMAGES, ['status'=>0]);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}