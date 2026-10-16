<?php 
Class Notification_model extends CI_Model
{
	public function insertnotification($data)
	{
		$this->db->insert(TBL_NOTIFICATIONS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatenotification($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_NOTIFICATIONS, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectnotification($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_NOTIFICATIONS);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function dropdownsector()
	{
		$this->db->select("sectorid,code,name");
		$this->db->from(TBL_NOTIFICATIONS);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allnotifications()
	{
		$this->db->select("*");
		$this->db->from(TBL_NOTIFICATIONS);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
}