<?php 
Class Discipline_model extends CI_Model
{
	public function insertdiscipline($data)
	{
		$this->db->insert(TBL_DISCIPLINE, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updatediscipline($id,$data)
	{
		$this->db->where('disciplineid',$id);
		$this->db->update(TBL_DISCIPLINE, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectdiscipline($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_DISCIPLINE);
		$this->db->where('disciplineid', $id);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function alldiscipline()
	{
		$this->db->select("*");
		$this->db->from(TBL_DISCIPLINE);
		$this->db->where('status!=',2);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function joindiscipline()
	{
		return $this->db->query('SELECT A.disciplineid,A.code,A.name,A.status,A.disciplineid,A.created_by,A.modified_by, group_concat(concat_ws(\'-\',B.code,B.name) SEPARATOR \',\') as sector_name
			FROM   '.TBL_DISCIPLINE.' A LEFT JOIN '.TBL_SECTOR.' B on FIND_IN_SET(B.sectorid,A.sectorids)>0 WHERE A.status !=2
			group by A.disciplineid ORDER BY A.disciplineid DESC ' )->result_array();
	}
	
}