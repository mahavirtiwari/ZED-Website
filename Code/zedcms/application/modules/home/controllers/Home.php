<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('home/home_model');
		$this->data['menu']=$this->db->query('SELECT A.menu_name,A.id,A.menu_type,A.menu_external_link,A.menu_order,group_concat(B.page_slug ORDER BY FIND_IN_SET(B.id,A.menu_related_pages)) as page_slug, group_concat(B.page_name ORDER BY FIND_IN_SET(B.id,A.menu_related_pages)) as page_name  FROM   '.TBL_MENU.' A left join '.TBL_PAGES.' B on FIND_IN_SET(B.id,A.menu_related_pages) WHERE A.status=1 AND  (( A.menu_type=3 ) OR ( A.menu_type!=3 AND B.status=1 ) ) group by A.id,A.menu_name,A.menu_type,A.menu_external_link,A.menu_order ORDER BY A.menu_order ASC ' )->result();
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		// Disply Counts for dashboard,Gold,silver,bronze,pledge (cached: the
		// remote dashboard is slow and was previously hit on every request)
		$details['counts'] = $this->_dashboard_counts();

		$this->data['social_urls'] = $details;
		// Counters
		$this->data['counters'] = $details;

		// footer news
		$this->data['footer_news']=$this->home_model->footer_news();
		// get footer gallery
		$this->data['footer_gallery']=$this->home_model->footer_gallery();

	}
	private function _dashboard_counts()
	{
		$this->load->driver('cache', array('adapter' => 'file'));
		$counts = $this->cache->get('zed_dashboard_counts');
		if ($counts === FALSE)
		{
			$response = $this->_callAPI('https://loginzed.msme.gov.in/dashboard/getcounts', 1);
			$counts = isset($response['Table'][0]) ? $response['Table'][0] : array();
			// keep good results for 10 minutes, retry failures after 1 minute
			$this->cache->save('zed_dashboard_counts', $counts, empty($counts) ? 60 : 600);
		}
		return $counts;
	}
	// Server-side HTTP helper: private so it cannot be reached as /home/... (SSRF)
	private function _callAPI($url,$method=0){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch,CURLOPT_TIMEOUT, 10);
		if ($method == 1)
		{
			// POST with an empty JSON body; 0 means a plain GET request
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		}

		$response = curl_exec($ch);
		curl_close($ch);
		return is_string($response) ? json_decode($response,true) : NULL;
	}
	public function index($menuid=0)
	{
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['home_seo'] = $details;
		// Get latest news two 
		$this->data['news']=$this->home_model->home_news();
		// Get breaking news
		$this->data['breaking_news']=$this->home_model->home_breaking_news();
		// get stakeholders
		$this->data['stakeholders']=$this->home_model->stakeholders();
		// get youtube videos
		$this->data['videos']=$this->home_model->homevideos();
		$this->data['page_sidemenu']=$this->home_model->get_home_pages();
		$this->data['slider']=$this->home_model->slider();
		$this->data['ratingImage']=$this->home_model->ratingImage();
		$this->data['ratingImageProcess']=$this->home_model->ratingImageProcess();
		$this->data['allprograms']=$this->home_model->allprograms();
		$this->data['incentives']=$this->home_model->incentives();
		$this->data['allnotifications']=$this->home_model->allnotifications();
		$this->data['testimonials']=$this->home_model->testimonials();
		$this->data['gallery']=$this->home_model->gallery();
		$this->data['page']='index';
		$this->_render_page(get_template(),$this->data);

	}
	public function page($page_slug)
	{
		$this->data['details']= $this->db->query('SELECT * FROM '.TBL_PAGES.' WHERE LOWER(page_slug) = ? AND status = ?',array(strtolower($page_slug),1))->row();
		if(empty($this->data['details']))
		{
			//redirect(base_url());
			show_404();
		}
		$page_menu_id= $this->data['details']->page_menu;
		$this->data['page_type']= $this->data['details']->page_type;
		// tabbed Menu
		$this->data['page_sidemenu']=$this->home_model->get_menus($page_menu_id);
		$this->data['parameter']=$this->home_model->allparameter();
		// sector code 
		$sector=$this->home_model->sector();
		$parent_sector=array();
		$parent_sector[]='Select Sector';
		foreach ($sector as $key => $value) {
			$parent_sector[$value['sectorid']]=$value['code']."-".$value['name'];
		}
		// send sector code to parameter page
		// start zed-maturity  matrix code from parameter module
		 //$this->data['zedmaturity']=$this->home_model->zedmaturity();
		// end zed-maturity  matrix code from parameter module
		$this->data['zed_rating']=$this->home_model->allrating();
		$this->data['parent']=$parent_sector;
		if($page_slug=='faq' || $page_slug=='faqs'){
			$this->data['faqs']=$this->home_model->faqs();
			$this->data['page']='faq';
		}
		elseif($page_slug=='benefitsincentives'){
			$this->data['page']='incentives';
		}
		elseif($page_slug=='verify-zed-certificate'){
			$udyamNumber = $this->input->post('udyamNumber');
			$certificationNumber = $this->input->post('certificationNumber');
			$this->data['page']='page';
			$this->data['udyamNumber'] = $udyamNumber;
			$this->data['certificationNumber'] = $certificationNumber;
			if(!empty($udyamNumber) && !empty($certificationNumber)){
				$url = 'https://login.zed.org.in/msme/GetDetailsByUdyam?'.http_build_query(array(
					'token' => getenv('ZED_VERIFY_TOKEN'),
					'udyamNumber' => $udyamNumber,
					'certificationNumber' => $certificationNumber,
				));
				$response = $this->_callAPI($url,0);
				//$result = parse_url(file_get_contents($url));
				$this->data['verifyCertification'] = $response;
			}
		}
		else{
			$this->data['page']='page';
		}
		$this->data['slug']=$page_slug;
		$this->_render_page(get_template(),$this->data);
	}
	public function posts($post_slug)
	{
		$this->data['details']= $this->db->query('SELECT * FROM '.TBL_POSTS.' WHERE LOWER(post_slug) = ? AND status = ?',array(strtolower($post_slug),1))->row();
		if(empty($this->data['details']))
		{
			//redirect(base_url());
			show_404();
		}
		$this->data['popular_news']=$this->db->select('*')->where('id !=',$this->data['details']->id)->where('status',1)->order_by('id','DESC')->limit(3)->get(TBL_POSTS)->result();
		$this->data['page']='post';
		$this->_render_page(get_template(),$this->data);
	}
	public function contactus()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[100]');
		$this->form_validation->set_rules('enterprise_name', 'Enterprise Name', 'trim|max_length[200]');
		$this->form_validation->set_rules('query_category', 'Query Category', 'trim|max_length[200]');
		$this->form_validation->set_rules('message', 'Message', 'trim|required');
		if($this->input->post('submit') && $this->form_validation->run())
		{
			$data['contact_name']=$this->input->post('name');
			$data['contact_email']=$this->input->post('email');
			$data['contact_phone']=(string) $this->input->post('phone');
			$data['enterprise_name']=(string) $this->input->post('enterprise_name');
			$data['query_category']=(string) $this->input->post('query_category');
			$data['contact_query']=$this->input->post('message');
			$data['created_on']=global_datetime();
			$data = $this->security->xss_clean($data);
			if($this->db->insert(TBL_CONTACT,$data))
			{
				$subject="Contact Details";
				$message='<p>Contact Name: '.html_escape($data['contact_name']).'</p>';
				$message.='<p>Contact Email: '.html_escape($data['contact_email']).'</p>';
				$message.='<p>Contact Phone: '.html_escape($data['contact_phone']).'</p>';
				$message.='<p>Enterprise Name: '.html_escape($data['enterprise_name']).'</p>';
				$message.='<p>Query Category: '.html_escape($data['query_category']).'</p>';
				$message.='<p>Contact Query: '.nl2br(html_escape($data['contact_query'])).'</p>';
				if($data['query_category']!='Training Program' && $data['query_category']!='Awareness Program')
				{
					$to='zedtp@qcin.org';
					$cc='zed@qcin.org';
				}
				else{
					$to='zed@qcin.org';
					$cc='';
				}
				
				$success=sendEmail($subject,$message,$to,$cc);
				
				$this->session->set_flashdata('message', 'Your details submit successfully !');
				redirect(base_url().'contact-us');
				
			}


		}
		$this->data['page']='contactus';
		$this->_render_page(get_template(),$this->data);
	}
	public function video()
	{
		$this->data['page']='video';
		$this->_render_page(get_template(),$this->data);
	}
	public function gallery()
	{
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['gallery_seo'] = $details;
		$this->data['details']=$this->home_model->gallery();
		/*echo "<pre>";
		print_r($this->data['details']);
		exit();*/
		$this->data['page']='gallery';
		$this->_render_page(get_template(),$this->data);

	}
	public function gallery_month()
	{
		$year=(int) $this->uri->segment(2);
		$month=ucfirst((string) $this->uri->segment(3));
		$this->data['details']=$this->home_model->gallery_month($year,$month);
		$details = array();
		$query = $this->db->select('label, value')->get(TBL_GLOBAL)->result_array();
		foreach ($query as $row)
		{
			$details[$row['label']] = $row['value'];
		}
		$this->data['gallery_seo'] = $details;
		$this->data['year']=$year;
		$this->data['month']=$month;
		$this->data['page']='individual-gallery';
		$this->_render_page(get_template(),$this->data);
	}
	public function search()
	{
		if($this->input->post('search_query')) {
			$this->data['search']= strip_tags($this->input->post('search_query'));
			$search_query=$this->input->post('search_query');
			$result_array=$this->home_model->search($search_query);
			if ($result_array) {
				$this->data['details']=$result_array;
			} else {
				$this->data['error']='No Results found';
			}
			
		}
		$this->data['page']='search';
		$this->_render_page(get_template(),$this->data);
		

	}
	public function disciplineparameter()
	{
		if(isset($_POST['id']))
		{
			$sector_id=$_POST['id'];
			$discipline_parmeter=$this->home_model->disciplineresults($sector_id);
			$discipline=array();
			foreach ($discipline_parmeter as $key => $value) {
				if(!empty($discipline[$value['code']]))
					array_push($discipline[$value['code']]["data"],$value);
				else{

					$discipline[$value['code']]["para_discipline"] = $value['para_discipline'];
					$discipline[$value['code']]["data"] = array();
					array_push($discipline[$value['code']]["data"],$value);
				}
				
			}
			echo json_encode(array('success'=>TRUE,'discipline'=>$discipline,'token'=>$this->security->get_csrf_hash()));
		}
		else	
		echo json_encode(array('success'=>FALSE,'token'=>$this->security->get_csrf_hash()));
		
	}
	public function zedmatrixparameter()
	{
		if(isset($_POST['id']))
		{
			$sector_id=$_POST['id'];
			$discipline_parmeter=$this->home_model->zedmaturity($sector_id);
			echo json_encode(array('success'=>TRUE,'discipline'=>$discipline_parmeter,'token'=>$this->security->get_csrf_hash()));
		}
		else	
		echo json_encode(array('success'=>FALSE,'token'=>$this->security->get_csrf_hash()));
		
	}
	public function zedguidance()
	{
		//echo print_r($_GET['variable'])
		if(isset($_GET['id']))
		{
			$sector_id=$_GET['id'];
			$discipline_parmeter=$this->home_model->zedguidance($sector_id);
			if(count($discipline_parmeter) > 0)
			{
				echo json_encode(array('success'=>TRUE,'discipline'=>$discipline_parmeter,'token'=>$this->security->get_csrf_hash()));
			}
			else{
				echo json_encode(array('success'=>FALSE,'discipline'=>$discipline_parmeter,'token'=>$this->security->get_csrf_hash()));
			}
			
		}
		else	
		echo json_encode(array('success'=>FALSE,'token'=>$this->security->get_csrf_hash()));
		
	}
	public function news()
	{
		$this->data['details']=$this->home_model->news();
		$this->data['page']='news';
		$this->_render_page(get_template(),$this->data);
	}
}