<?php
class Menu_model extends CI_Model
{
	public function pages()
	{
		return $this->db->select('id,page_name')->where('status',1)->get(TBL_PAGES)->result_array();
		
	}
	public function insertmenu($data)
	{
		$this->db->insert(TBL_MENU, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatemenu($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update(TBL_MENU, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectmenu($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_MENU);
		$this->db->where('id', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
}