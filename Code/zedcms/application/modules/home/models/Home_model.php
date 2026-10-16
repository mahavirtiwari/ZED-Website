<?php
class Home_model extends CI_Model
{
	public function get_menus($page_menu_id='')
	{
		// return $this->db->query('SELECT A.menu_name,A.status,A.id,A.created_by,A.modified_by,B.id,B.page_menu,B.page_name,B.page_slug,B.page_description FROM   '.TBL_MENU.' A left join '.TBL_PAGES.' B on FIND_IN_SET(B.id,A.menu_related_pages) > 0 WHERE A.status =1 and B.status=1 and B.page_menu='.$page_menu_id.' and B.page_type!=1 and A.menu_type=1' )->result();
		return $this->db->query('SELECT A.menu_name,A.id,A.menu_type,A.menu_external_link,A.menu_order,group_concat(B.page_slug ORDER BY FIND_IN_SET(B.id,A.menu_related_pages)) as page_slug, group_concat(B.page_name ORDER BY FIND_IN_SET(B.id,A.menu_related_pages)) as page_name  FROM   '.TBL_MENU.' A left join '.TBL_PAGES.' B on FIND_IN_SET(B.id,A.menu_related_pages) WHERE B.page_menu = ? AND A.status=1 AND (( A.menu_type=3 ) OR ( A.menu_type!=3 AND B.status=1 ) ) group by A.id,A.menu_name,A.menu_type,A.menu_external_link,A.menu_order ORDER BY A.menu_order ASC', array((int) $page_menu_id))->result();
	}

	public function get_news_blocks()
	{
		return $this->db->select('*')->where('news_block_status','active')->get(TBL_NEWS_BLOCK)->result();
	}
	
	public function get_posts_blocK3()
	{
		return $this->db->select('*')->where(array('post_category'=>3,'status'=>1))->get(TBL_POSTS)->result();
	}
	public function get_home_pages()
	{
		return $this->db->query('SELECT A.page_name ,A.page_slug,A.page_short_description,A.page_home_order,MIN(B.id) AS id FROM   '.TBL_PAGES.' A left join '.TBL_MENU.' B on FIND_IN_SET(A.id,B.menu_related_pages)>0 WHERE A.status =1 and page_type=3 group by A.id,A.page_name,A.page_slug,A.page_short_description,A.page_home_order ORDER BY A.page_home_order' )->result();
	}
	public function slider()
	{
		return $this->db->select('*')->where('status',1)->order_by('slider_sorting','ASC')->get(TBL_SLIDER)->result();
	}
	// get news 
	public function news()
	{
		return $this->db->select('*')->where('status',1)->order_by('id','DESC')->get(TBL_POSTS)->result();
	}
	// get news two 
	public function home_news(){
		return $this->db->select('*')->where('status',1)->where('post_category',1)->where('post_latest_news',1)->order_by('id','DESC')->limit(2)->get(TBL_POSTS)->result();
	}
	// get breaking news
	public function home_breaking_news(){
		return $this->db->select('*')->where('post_breaking_news',1)->where('status',1)->order_by('id','DESC')->get(TBL_POSTS)->result();
	}
	// get footer news
	public function footer_news(){
		return $this->db->select('*')->where('status',1)->order_by('id','DESC')->limit(3)->get(TBL_POSTS)->result();
	}
	// get news depends on block
	public function get_news_post()
	{

		return $this->db->query('SELECT B.news_block_heading,GROUP_CONCAT(A.post_slug ORDER BY A.created_on DESC, A.id DESC) as post_slug, GROUP_CONCAT(A.post_name ORDER BY A.created_on DESC, A.id DESC) as post_name FROM   '.TBL_POSTS.' A inner join '.TBL_NEWS_BLOCK.' B ON A.post_category = B.news_block_id  WHERE A.status =1 and B.news_block_status="active" group by B.news_block_heading ORDER BY MAX(A.created_on) DESC' )->result_array();
	}
	public function sector()
	{
		$this->db->select("sectorid,code,name");
		$this->db->where('status',1);
		$this->db->order_by('code','ASC');
		$this->db->from(TBL_SECTOR);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function allparameter()
	{
		$this->db->select(''.TBL_PARAMETER.'.parameterid,concat_ws("-",'.TBL_PARAMETER.'.code, '.TBL_PARAMETER.'.name) as para_code_name ,concat_ws("-",'.TBL_DISCIPLINE.'.code ,'.TBL_DISCIPLINE.'.name ) as para_discipline,concat_ws("-",'.TBL_SECTOR.'.code ,'.TBL_SECTOR.'.name ) as para_sector, '.TBL_SECTOR.'.code as para_sector_id' );
		$this->db->from(TBL_PARAMETER);
		$this->db->join(TBL_SECTOR, ''.TBL_PARAMETER.'.sectorid ='.TBL_SECTOR.'.sectorid');
		$this->db->join(TBL_DISCIPLINE,''.TBL_PARAMETER.'.disciplineid='.TBL_DISCIPLINE.'.disciplineid');
		$this->db->where(''.TBL_PARAMETER.'.status',1);
		$this->db->order_by(''.TBL_PARAMETER.'.parameterid','DESC');
		return $this->db->get()->result();
	}
	// footer Gallery
	public function footer_gallery()
	{
		return $this->db->select('media_slug,media_name,media_title,media_caption,media_seo_alt_text,id')->where('status!=',2)->order_by('id','DESC')->limit(9)->get(TBL_MEDIA)->result();
	}
	// gallery
	public function gallery()
	{
		//return $this->db->select('media_slug,media_name,media_title,media_caption,media_seo_alt_text,id,Year(created_on) as year, MONTHNAME(created_on) as month')->where('status!=',2)->order_by('month','DESC')->group_by(array("year","month"))->get(TBL_MEDIA)->result();
		return $this->db->select('MAX(media_slug) AS media_slug, MAX(media_name) AS media_name, MAX(media_title) AS media_title, MAX(media_caption) AS media_caption, MAX(media_seo_alt_text) AS media_seo_alt_text, MAX(id) AS id, YEAR(created_on) AS year, MONTHNAME(created_on) AS month', FALSE)
			->where('status!=',2)
			->group_by(array('YEAR(created_on)', 'MONTH(created_on)', 'MONTHNAME(created_on)'))
			->order_by('YEAR(created_on) DESC, MONTH(created_on) DESC', '', FALSE)
			->get(TBL_MEDIA)->result();
		
	}
	public function gallery_month($year,$month)
	{
		return $this->db->select('media_name, media_title,media_caption,media_seo_alt_text,media_slug,id,Year(created_on) as year, MONTHNAME(created_on) as month')->where('status!=',2)->order_by('id','DESC')->where(array('Year(created_on)'=>(int) $year,'MONTHNAME(created_on)'=>$month))->get(TBL_MEDIA)->result();
	}
	public function search($search)
	{
		$result=$this->db->select('id,page_name,page_description,page_slug')->like('page_name',$search,'both')->where('status',1)->get(TBL_PAGES);
		if($result->num_rows()>0)
		{
			return $result->result();
		}
		else
		{
			return false;
		}
	}
	// get stakeholder
	public function stakeholders()
	{
		return $this->db->select('*')->where('status!=',2)->order_by('id','DESC')->get(TBL_STAKEHOLDERS)->result();
	}
	// get stakeholder
	public function homevideos()
	{
		return $this->db->select('*')->where('status',1)->order_by('video_sorting','ASC')->get(TBL_VIDEOS)->result();
	}
	public function disciplineresults($sector_id)
	{
		
		
		$this->db->select(TBL_DISCIPLINE.'.code,MIN(concat_ws("-",'.TBL_DISCIPLINE.'.code ,'.TBL_DISCIPLINE.'.name )) as para_discipline,MIN(concat_ws("-",'.TBL_PARAMETER.'.code, '.TBL_PARAMETER.'.name)) as para_code_name ,MIN('.TBL_PARAMETER.'.description) as description', FALSE);
		$this->db->from(TBL_PARAMETER);
		$this->db->join(TBL_SECTOR, ''.TBL_PARAMETER.'.sectorid ='.TBL_SECTOR.'.sectorid');
		$this->db->join(TBL_DISCIPLINE,''.TBL_PARAMETER.'.disciplineid='.TBL_DISCIPLINE.'.disciplineid');
		$this->db->where(''.TBL_PARAMETER.'.status',1);
		$this->db->where(''.TBL_PARAMETER.'.sectorid',$sector_id);
		$this->db->order_by(''.TBL_DISCIPLINE.'.code','ASC');
		$this->db->order_by(''.TBL_PARAMETER.'.code','ASC');
		$this->db->group_by(''.TBL_DISCIPLINE.'.code');
		$this->db->group_by(''.TBL_PARAMETER.'.code');
		return $this->db->get()->result_array();
	}
	public function zedmaturity($sector_id)
	{
		$this->db->select(''.TBL_PARAMETER.'.parameterid,'.TBL_PARAMETER.'.code, '.TBL_PARAMETER.'.name ,concat_ws("-",'.TBL_DISCIPLINE.'.code ,'.TBL_DISCIPLINE.'.name ) as para_discipline,concat_ws("-",'.TBL_SECTOR.'.code ,'.TBL_SECTOR.'.name ) as para_sector,'.TBL_PARAMETER.'.level1_comment,'.TBL_PARAMETER.'.level2_comment,'.TBL_PARAMETER.'.level3_comment,'.TBL_PARAMETER.'.level4_comment,'.TBL_PARAMETER.'.level5_comment' );
		$this->db->from(TBL_PARAMETER);
		$this->db->join(TBL_SECTOR, ''.TBL_PARAMETER.'.sectorid ='.TBL_SECTOR.'.sectorid');
		$this->db->join(TBL_DISCIPLINE,''.TBL_PARAMETER.'.disciplineid='.TBL_DISCIPLINE.'.disciplineid');
		$this->db->where(''.TBL_PARAMETER.'.status',1);
		$this->db->where(''.TBL_PARAMETER.'.sectorid',$sector_id);
		$this->db->order_by(''.TBL_PARAMETER.'.code','ASC');
		return $this->db->get()->result_array();
	}
	public function zedguidance($sector_id)
	{
		$this->db->select('*');
		$this->db->from('zed_guidance_document');
		$this->db->where('sector',$sector_id);
		$this->db->order_by('id','ASC');
		return $this->db->get()->result_array();
	}
	public function allrating()
	{
		$this->db->select("*");
		$this->db->from('zed_ratings');
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function ratingImage()
	{
		$this->db->select("*");
		$this->db->from('rating_images');
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->row();
	}
	public function ratingImageProcess()
	{
		$this->db->select("*");
		$this->db->from('zed_rating_process');
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->row();
	}
	public function allprograms()
	{
		$this->db->select("*");
		$this->db->from('zed_programs');
		$this->db->where('status',1);
		$query = $this->db->order_by('sorting','ASC')->get();
		return $query->result();
	}
	public function incentives()
	{
		$this->db->select("*");
		$this->db->from("zed_incentives");
		$this->db->where('status',1);
		$query = $this->db->order_by('sorting','ASC')->get();
		return $query->result();
	}
	public function allnotifications()
	{
		$this->db->select("*");
		$this->db->from('zed_notifications');
		$this->db->where('status',1);
		$query = $this->db->order_by('sorting','ASC')->get();
		return $query->result();
	}
	public function faqs()
	{
		$this->db->select("*");
		$this->db->from('zed_faq');
		$this->db->where('status',1);
		$query = $this->db->order_by('faq_order','ASC')->get();
		return $query->result();
	}
	public function testimonials()
	{
		return $this->db->select('*')->where('status',1)->order_by('sorting','ASC')->get(TBL_TESTIMONIALS)->result();
	}
}