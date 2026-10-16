<?php 
Class Parameter_model extends CI_Model
{
	public function insertparameter($data)
	{
		$this->db->insert(TBL_PARAMETER, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function updateparameter($id,$data)
	{
		$this->db->where('parameterid',$id);
		$this->db->update(TBL_PARAMETER, $data);
		if($this->db->affected_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function selectparameter($id)
	{
		$this->db->select("*");
		$this->db->from(TBL_PARAMETER);
		$this->db->where('parameterid', $id);
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
	public function dropdowndiscipline()
	{
		$this->db->select("disciplineid,code,name");
		$this->db->from(TBL_DISCIPLINE);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allparameter()
	{
			$this->db->select(''.TBL_PARAMETER.'.parameterid,'.TBL_PARAMETER.'.code, '.TBL_PARAMETER.'.name ,'.TBL_PARAMETER.'.status,'.TBL_PARAMETER.'.created_by,'.TBL_PARAMETER.'.modified_by,concat_ws("-",'.TBL_DISCIPLINE.'.code ,'.TBL_DISCIPLINE.'.name ) as para_discipline,concat_ws("-",'.TBL_SECTOR.'.code ,'.TBL_SECTOR.'.name ) as para_sector' );
			$this->db->from(TBL_PARAMETER);
			$this->db->join(TBL_SECTOR, ''.TBL_PARAMETER.'.sectorid ='.TBL_SECTOR.'.sectorid');
			$this->db->join(TBL_DISCIPLINE,''.TBL_PARAMETER.'.disciplineid='.TBL_DISCIPLINE.'.disciplineid');
			$this->db->where(''.TBL_PARAMETER.'.status!=', 2);
			$this->db->order_by(''.TBL_PARAMETER.'.parameterid','DESC');
			return $this->db->get()->result();
	}
}