<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller{
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('ADMIN_ID')=="") redirect('secure','refresh');
	 	$this->load->model('Admin_model','admin');
	 	$this->load->library('encrypt');
	}
	//---------------------- Pages ----------------------------------
	function index(){		
		$data['act_id']="user";
		$select ="id,username,password,first_name,last_name,email_address,phone,user_type,is_featured";
		$where = "1=1";
		$data['num_rec'] = $num_rec = $this->common->num_users($where);
		if($num_rec){
			$data['dataDs'] = $this->common->getUserList($select,$where);
		}
       
		$this->load->view("webmaster/userlist",$data);
	}
 	function manage_user($userId=0){
 		 
 	 	$data['userData']='';
 	 	$data['act_id']='user';
 	 	$data['userId']=$userId;
 	 	$tbl = 'tbl_user_master';
 	 	$data['country'] = $this->common->get_country();
 	 	$data['style'] = $this->common->get_style();
 	 	$data['galleries'] = $this->common->get_galleries();
		
		if($userId>0)
		{  
			$btnCapt="Update"; 
			$where = array('id' => $userId);
			$data['userdata'] = $this->common->getOneRowArray( '*', $tbl, $where );   
			 
			$this->form_validation->set_rules('email_address', 'Email', 'trim|required|valid_email|callback_unique_email['.$userId.']');
		}else{
			$btnCapt = "Add"; 
			$data['userdata'] = ''; 
			 
			$this->form_validation->set_rules('email_address', 'Email', 'trim|required|valid_email|callback_unique_email');
		}
		$data['btnCapt']=$btnCapt;
 		/*$this->form_validation->set_rules('first_name', 'First name', 'trim|required|alpha');
		$this->form_validation->set_rules('last_name', 'Last name', 'trim|required|alpha'); 
		$this->form_validation->set_rules('galleries_id', 'Gallery Category', 'trim|required'); 
		$this->form_validation->set_rules('style_id', 'Style Category', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim|required'); 
		$this->form_validation->set_rules('country', 'Country', 'trim|required'); 
		$this->form_validation->set_rules('city', 'City', 'trim|required'); 
		$this->form_validation->set_rules('state', 'State', 'trim|required'); 
		$this->form_validation->set_rules('zip', 'Zip Code', 'trim|required|numeric|min_length[4]|max_length[8]');
		$this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|numeric|min_length[9]|max_length[12]'); */
		
		$this->form_validation->set_rules('user_type', 'User Type', 'trim|required');
		if($this->form_validation->run())
		{
			// file upload
			$flag=0;
		  	if($_FILES['profile_pic']['name']!='')
		  	{
				$flag=0;
				$path_img = $_FILES['profile_pic']['name'];
				$ext_img = pathinfo($path_img, PATHINFO_EXTENSION);
				$valid_ext_arr = array('png','jpg','jpeg','gif');
				if(!in_array(strtolower($ext_img),$valid_ext_arr))
				{
					$flag = 1;						
					break;
				}

			} 
			
			$final_img  = '';
			
			if($flag==0)
			{
				if(!is_dir('./uploads/user_profile_pic/'))
				{
					mkdir('./uploads/user_profile_pic/');
				}
		 		if($_FILES['profile_pic']['name']!='')
		 		{
				
					$file=$_FILES;	
					$_FILES['profile_pic']['name'] = $file['profile_pic']['name'];					
					
					$filename = str_replace(' ','_','user-profile-pic')."_".uniqid();
					$path = $_FILES['profile_pic']['name'];
					$ext = pathinfo($path, PATHINFO_EXTENSION);							
					
					$final_img = $filename.".".$ext;					
					
					$this->load->library('upload');
					$config['file_name']     = $filename;
					$config['upload_path']   = './uploads/user_profile_pic';
					$config['allowed_types'] = 'png|jpeg|jpg|gif';
								
					$this->upload->initialize($config);					
					$_FILES['profile_pic']['type']=$file['profile_pic']['type'];
					$_FILES['profile_pic']['tmp_name']=$file['profile_pic']['tmp_name'];
					$_FILES['profile_pic']['error']=$file['profile_pic']['error'];
					$_FILES['profile_pic']['size']=$file['profile_pic']['size'];
					$this->upload->do_upload('profile_pic');
					$this->common->create_thumb_resize($final_img,'./uploads/user_profile_pic/','356','360');
					if($this->input->post('old_profile_pic')!='')
					{
						@unlink('./uploads/user_profile_pic/'.$this->input->post('old_profile_pic'));
					}
				}
				else
				{
					if($this->input->post('old_profile_pic')!='')
					{
						$final_img = $this->input->post('old_profile_pic');
					}
				}
			}
			if($this->input->post('featureRadio')==1)
			{
				$featureArtist = $this->input->post('is_featured');
				//$featureArtist = '1';
			}
			else
			{
				$featureArtist = '0';
			}


			if($userId>0){

				//=====Generate Pwd
				 $strPwd  =  $this->common->genRandomString();
				 $hashPwd = $this->common->generateHashPassword($strPwd);
				//=====
				$style = 0;
				if($this->input->post('style_id')!=''){
					$style = @implode(',',$this->input->post('style_id'));
				}else{
					$style = 0;
				}
				
				$update_array = array(
									'first_name'=>$this->input->post('first_name'),
									'last_name'=>$this->input->post('last_name'),
									'email_address'=>$this->input->post('email_address'),
									'galleries_id'=>$this->input->post('galleries_id'),
									'style_id'=>$style,
									'phone'=>$this->input->post('phone'),
								 	'address'=>$this->input->post('address'),
									'address2'=>$this->input->post('address2'),
									'country'=>$this->input->post('country'),
									'state'=>$this->input->post('state'),
									'city'=>$this->input->post('city'),
									'zip'=>$this->input->post('zip'),
									'user_type'=>$this->input->post('user_type'),
									'is_featured'=>$featureArtist,
									'profile_pic'=>$final_img,
									'notification_status'=>1,
									'notification_des'=> 'has updated own profile.',
									'mod_date'=>date("m/d/Y h:i:s")
									);
				$where_array=array('id'=>$userId);
				$this->common->update_entry($tbl,$update_array,$where_array);
				
				// Added in notification table 
				/*$table_name = 'notifications';
				$insert_notify_array = array(
                    				'notification_from_user_id'=> $this->input->post('first_name').' '.$this->input->post('last_name'),
                    				'notification_type'=>$this->input->post('user_type'),
                    				'notification_text'=>$this->input->post('first_name').' '.$this->input->post('last_name')." has changed own profile.",
                    				'notification_url'=> base_url().'webmaster/user/details/'.$userId.".html",
                    				'notification_datetime'=>date("m/d/Y h:i:s"),
                    				'notification_status'=>'Pending'
                    				);
				$this->common->add_records_notification($table_name,$insert_notify_array);*/
				
				
				$this->session->set_flashdata('Success','User updated successfully.');
				redirect('webmaster/user/index','refresh');

			}
			else
			{
				$this->load->library('email');
	 			//=====Generate Pwd
                $strPwd  =  $this->common->genRandomString();
                $hashPwd = $this->common->generateHashPassword($strPwd);
                $Name = $this->input->post('first_name').' '.$this->input->post('last_name');
                $username = $this->common->random_username($Name);
                $email = $this->input->post('email_address');
				//=====
				$style = 0;
				if($this->input->post('style_id')!=''){
					$style = @implode(',',$this->input->post('style_id'));
				}else{
					$style = 0;
				}
				 
				$insert_array = array(
				                    'username'=>$username,
									'first_name'=>$this->input->post('first_name'),
									'last_name'=>$this->input->post('last_name'),
									'galleries_id'=>$this->input->post('galleries_id'),
									'style_id'=>$style,
									'email_address'=>$this->input->post('email_address'),
									'password'=>$hashPwd,
									'phone'=>$this->input->post('phone'),
								 	'address'=>$this->input->post('address'),
									'address2'=>$this->input->post('address2'),
									'country'=>$this->input->post('country'),
									'state'=>$this->input->post('state'),
									'city'=>$this->input->post('city'),
									'zip'=>$this->input->post('zip'),
									'registration_date' => date('Y-m-d H:i:s'),
									'is_admin_active'=>1,
									'user_type'=>$this->input->post('user_type'),
									'is_featured'=>$featureArtist,
									'profile_pic'=>$final_img,
									'notification_status'=>1,
									'is_activated'=>1,
									'notification_des'=> 'has added new profile.',
									'mod_date'=>date("m/d/Y h:i:s")
									);
        			$this->common->add_records($tbl,$insert_array);
        			$new_registration_id =	$this->db->insert_id();			
        			//=======Add new user to tbl_artist_user
        			$this->common->add_records('tbl_artist_user',array('user_id' => $new_registration_id));
        			//======Send emial to user about his account creation
        			$site_logo = $this->common->getLogo();
        			$email_content = '';
        			$email_content.='<div style="width:99%;margin:0 auto;background:#FFF; height:140px;border:1px solid #666;"><div style="text-align:center; padding:5px 0;"><img src="'.base_url().'uploads/sitelogo/'.$site_logo.'" width="150"></div></div><div style="background-color:#F6F6F6;margin:0 auto; width:99%; border:1px solid #666;"><p style="font-size:14px; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif; color:#313131; padding:5px 0 5px 15px;text-align:left;">Hello '.$Name.',</p><p style="font-size:14px;font-family:Verdana,Arial,Helvetica,sans-serif;padding:5px 0 5px 15px;text-align:left">You have been successfully registered on '.SITENAME.'. You can login to site with following login details.<br/><bold>Username : </bold>'.$email.'<br/><bold>Passowrd : </bold>'.$strPwd.'<br/>You need to active your account before login.To active your account please <a href="'.site_url("#activate_account/".$new_registration_id).'"><u>click here</u></a>.</p><p>&nbsp;</p><p style="padding:5px 0 5px 0;font-size:12px;line-height:22px;text-align:left;padding-left:15px; font-weight:bold;">Thanks & Regards,<br/>'.SITENAME.'</p></div>';
        			$subject1 = SITENAME.": Activate your account";
        			$this->email->from(ADMIN_EMAIL);
        			$this->email->to($email); //
        			$this->email->cc(BCC_EMAIL);
        			$this->email->subject($subject1);
        			$this->email->message($email_content);//$email_content
        			$this->email->send();
			
				
                
			
				$this->session->set_flashdata('Success','User added successfully .');
				redirect('webmaster/user/index','refresh');
			}
		} 
 		$this->load->view('webmaster/manage_user',$data);
	}

	public function details($userId)
	{
		$data['act_id'] = 'user';
		$data['userId'] = $userId;
		$tbl = 'tbl_user_master as u';
		$where =  array('id'=>$userId);
		$isExists = $this->common->getnumRow( $tbl, $where );
	 	if($isExists==1)
	 	{
	 	    //$where2 =  array('id'=>$userId, 'u.galleries_id' => 'g.cat_id');
			//$data['userdata'] = $this->common->getOneRowArray('*',$tbl.',tbl_galleries as g',$where);
		    $this->db->select('tbl_user_master.*, tbl_galleries.cat_name as galName, tbl_style.cat_name as styleName' );
            $this->db->from( 'tbl_user_master');
     		$this->db->where( array('tbl_user_master.id'=>$userId) );
            $this->db->join('tbl_galleries', 'tbl_user_master.galleries_id = tbl_galleries.cat_id');
            $this->db->join('tbl_style', 'tbl_user_master.style_id = tbl_style.cat_id');
            $query = $this->db->get();
            $data['userdata'] =  $query->row_array();
        }
        else
        {
			redirect('webmaster/userlist/index','refresh');
		}
		$this->load->view('webmaster/user_details',$data);
	}
	
	public function delete_user()
	{
		if($this->input->post('action')=="delete")
		{
			for($i=0;$i<count($this->input->post('cb'));$i++)
			{
				$delid=$this->security->xss_clean($this->input->post('cb'));
				$where= array("id " => $delid[$i]);
				$this->common->delete_entry("tbl_artist_user",array('user_id' => $delid[$i]));
				$this->common->delete_entry("tbl_user_master",$where);
			}
			$this->session->set_flashdata('Success','Record deleted successfully!');
			redirect('webmaster/user/index','refresh');
		}
		redirect('webmaster/user/index','refresh');
	}
	
	//=========Form validation
 	public function unique_username($str,$id)//entry already exist or not
	{

		$chk_array=array();

		if($str!="" && $id!="")
		{
			$chk_array=array('username'=>$str,'id !='=>$id);
		}
		else
		{
			$chk_array=array('username'=>$str);
		}
		//print_r($chk_array); 
		$result = $this->db->get_where('tbl_user_master',$chk_array);
		//echo $this->db->last_query();
		//exit();
		if($result->num_rows()>0)
		{   $this->form_validation->set_message('unique_username','Username already exists.');
			return false;
		} 
		else 
		{
			return true;
		}
	}
	public function unique_email($str,$id)
	{
		$chk_array=array();

		if($str!="" && $id!="")
		{
			$chk_array=array('email_address'=>$str,'id !='=>$id);
		}
		else
		{
			$chk_array=array('email_address'=>$str);
		}
		
		$result = $this->db->get_where('tbl_user_master',$chk_array);
		if($result->num_rows()>0)
		{   $this->form_validation->set_message('unique_email','Email Address already exists.');
			return false;
		} 
		else 
		{
			return true;
		}
	}
	//=========Form validation

	function othersections($userId){
		$data['act_id']="user";
		$data['userId'] = $userId;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0){

			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
			if($userDetails['user_type']=='artist'){
				$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
				$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']);
				$this->load->view('webmaster/user_other_sections',$data);
			}else{
				redirect('webmaster/user');
			}
		}else{
				redirect('webmaster/user');
		}

	}

	//======Interview
	function interviews($userId,$id=0){
		$data['act_id']="user";
		$data['userId'] = $userId;
		$data['id'] = $id;
		$tbl = 'tbl_interview';
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($id>0){
			$data['btnCapt'] = 'Update';
			$where = array('id' => $id);
			$data['dataDs'] = $this->common->getOneRowArray( '*', $tbl, $where );
		}else{
			$data['btnCapt'] = 'Add';
		}

		if($isExists>0){
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code
			$data['interviewData'] = $this->common->getInterviewsOfUser($userId);
			$this->load->view('webmaster/interviews',$data);
		}else{
			redirect('webmaster/user','refresh');
		}
	}

	function ck_imageupload(){
		$data['kk']=1;
		$this->load->view("webmaster/imageupload",$data);	
	}


	function manage_interview($userId,$id){
		$data['act_id']='user';
 	 	$data['userId']=$userId;
 	 	$data['id']=$id;
 	 	$tbl = 'tbl_interview';


 	 	//========== ckeditor  starts ============
		//========== ckeditor  starts ============
		$data['ckeditor'] = array(		
			//ID of the page_descarea that will be replaced
			'id' 	=> 	'questions_answer',
			'path'	=>	'js/ckeditor',	
			'filebrowserImageUploadUrl' =>	site_url('webmaster/user/ck_imageupload') //'imageupload.php',	
		);//========== ckeditor  ends ============		

 	  	if($id>0)
		{  
			$btnCapt="Update"; 
			$where = array('id' => $id);
			$data['dataDs'] = $this->common->getOneRowArray( '*', $tbl, $where );
		}else{
			$btnCapt = "Add"; 
			$data['dataDs'] = ''; 
			
		}
		$data['btnCapt']=$btnCapt;
 		$this->form_validation->set_rules('question', 'Question', 'trim|required');
		$this->form_validation->set_rules('questions_answer', 'Answer', 'trim|required'); 

		//====common code for top header of artist
		$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
	 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
		$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
		//====common code

		if($this->form_validation->run())
		{

			$update_array = array(
								'question'=>$this->input->post('question'),
								'questions_answer'=>$this->input->post('questions_answer'),
								'added_by'=>0,
								'user_id'=>$userId,
								'added_date'=>date('Y-m-d h:m:s')
								);

			if($id>0){
				$where_array=array('id'=>$id);
				$this->common->update_entry($tbl,$update_array,$where_array);
				$this->session->set_flashdata('Success','Record updated successfully .');
				redirect('webmaster/user/interviews/'.$userId.'/0','refresh');

			}else{
				$this->common->add_records($tbl,$update_array);
				$new_registration_id =	$this->db->insert_id();			
				$this->session->set_flashdata('Success','Record added successfully .');
				redirect('webmaster/user/interviews/'.$userId.'/0','refresh');
			}
		} 

		$data['interviewData'] = $this->common->getInterviewsOfUser($userId);
		$this->load->view('webmaster/interviews',$data);
 	}

 	function delete_interview($userId){
 		$tbl = 'tbl_interview';
 		if($this->security->xss_clean($this->input->post('action')=="delete")){
			for($i=0;$i<count($this->security->xss_clean($this->input->post('cb')));$i++){
				$delid=$this->security->xss_clean($this->input->post('cb'));
				
				$where = array( "id " => $delid[$i] );
				$select = "*";
				$this->common->delete_entry($tbl, $where);
			}
			$this->session->set_flashdata("Success", "Record deleted successfully!");			
		}
		redirect("webmaster/user/interviews/".$userId,"refresh");
 	  
 	}

 	// descriptions for all featured sections.
 	function manage_desc($userId,$field){
 		$data['act_id']="user";
		$data['userId'] = $userId;
		$data['field'] = $field;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0){

			$data['ckeditor'] = array(		
				//ID of the page_descarea that will be replaced
				'id' 	=> 	'description',
				'path'	=>	'js/ckeditor',	
				'filebrowserImageUploadUrl' =>	site_url('webmaster/user/ck_imageupload') //'imageupload.php',	
			);//========== ckeditor  ends ============		


			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code


			$tbl = 'tbl_artist_user';
			$where_array =  array('user_id' => $userId );
			$fieldName = '';
			if($field=='interviews'){
				$fieldName = "interview_desc";
			}else if($field=='artgallery'){
				$fieldName = "feature_artwork_gallery_desc";}
			else if($field=='shortinsidethestudio'){
				$fieldName = "feature_short_inside_the_studio_desc";
			}else if($field=='insidethestudio'){
				$fieldName = "feature_inside_the_studio_desc";
			}else if($field=='featurevideo'){
				$fieldName = "feature_video_desc";
			}else if($field=='essay'){
				$fieldName = "essay";
			}else if($field=='biography'){
				$fieldName = "biography";
			}else if($field=='statement'){
				$fieldName = "statement";
			}else if($field=='exibition'){
				$fieldName = "exibition";
			}else if($field=='awards'){
				$fieldName = "awards";
			}else if($field=='publication'){
				$fieldName = "publication";
			}else if($field=='featureIntroduction'){
				$fieldName = "featured_desc";
			}
			
			$data['fieldName'] = $fieldName;
			$data['artist_data'] = $this->common->getOneRowArray('*',$tbl,$where_array);
			
			$this->form_validation->set_rules('description','Description','trim');
			if($this->form_validation->run()){

				$update = array( $fieldName => $this->input->post('description') );
				$this->common->update_entry($tbl,$update,$where_array);
				$this->session->set_flashdata('Success','Record updated successfully .');
				redirect('webmaster/user/manage_desc/'.$userId.'/'.$field);
			}
			$this->load->view('webmaster/feature_artist_desc',$data);
		}else 		redirect('webmaster/user');
	}

	//sliders
	function sliders($userId,$type,$id=0){
		$data['act_id']="user";
		$data['userId'] = $userId;
		$data['type'] = $type;
		$data['id'] = $id;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0){
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code

			$tbl = 'tbl_artist_slider';
			$where_array =  array('user_id' => $userId,'type' => $type );
			$data['artistData'] = $this->common->getAllRowArray('*',$tbl,$where_array);

			if($id>0){
				$data['btnCapt'] = 'Update';
				$data['artist_data'] = $this->common->getOneRowArray('*',$tbl,$where_array);
			}else{
				$data['btnCapt'] = 'Add';
			}
			
			 
			if($this->input->post('mode')=='1'){
				if(!is_dir('./uploads/artist_images')){
					mkdir('./uploads/artist_images');
				}

			$flag=0;
		  	if($_FILES['image_name']['name']!=''){
					$flag=0;
					//==validaye image exists 
					$path_img = $_FILES['image_name']['name'];
					$ext_img = pathinfo($path_img, PATHINFO_EXTENSION);
						
					$valid_ext_arr = array('png','jpg','jpeg','gif');
					if(!in_array(strtolower($ext_img),$valid_ext_arr))
					{
						$flag = 1;						
						break;
					}

			} 
			if($flag==0){

					$final_img  = '';
			 		if($_FILES['image_name']['name']!=''){
					
						$file=$_FILES;	
						$_FILES['image_name']['name'] = $file['image_name']['name'];					
						
						$filename = str_replace(' ','_','artist_art')."_".uniqid();
						$path = $_FILES['image_name']['name'];
						$ext = pathinfo($path, PATHINFO_EXTENSION);							
						
						$final_img = $filename.".".$ext;					
						
						$this->load->library('upload');
						$config['file_name']     = $filename;
						$config['upload_path']   = './uploads/artist_images';
						$config['allowed_types'] = 'png|jpeg|jpg|gif';
									
						$this->upload->initialize($config);					
						$_FILES['image_name']['type']=$file['image_name']['type'];
						$_FILES['image_name']['tmp_name']=$file['image_name']['tmp_name'];
						$_FILES['image_name']['error']=$file['image_name']['error'];
						$_FILES['image_name']['size']=$file['image_name']['size'];
						$this->upload->do_upload('image_name');
						if($this->input->post('old_image_name')!=''){
							@unlink('./uploads/artist_images/'.$this->input->post('old_image_name'));
						}

					}else{
						if($this->input->post('old_image_name')!=''){
							$final_img = $this->input->post('old_image_name');
						}
					}

				$valueArray = array( 
									'user_id' => $userId,
									'image_name' => $final_img,
									'type' => $type
								);
				if($id>0){
					$whereSlider =  array('id' => $id, 'user_id' =>$userId );
					$this->common->update_entry($tbl,$valueArray,$whereSlider);
					$this->session->set_flashdata('Success','Record updated successfully .');
					redirect('webmaster/user/sliders/'.$userId.'/'.$type);
				}else{
					$this->common->add_records($tbl,$valueArray);
					$this->session->set_flashdata('Success','Record added successfully .');
					redirect('webmaster/user/sliders/'.$userId.'/'.$type);
				}
				
			}
			}
			$this->load->view('webmaster/feature_artist_slider',$data);
		}else redirect('webmaster/user');
	}


	function delete_slider($userId,$type){
		if($this->input->post('action')=="delete"){
			for($i=0;$i<count($this->input->post('cb'));$i++){
				$delid=$this->security->xss_clean($this->input->post('cb'));
				$where= array("id " => $delid[$i]);
				$dataRs = $this->common->getOneRowArray("*","tbl_artist_slider",$where);
				@unlink('./uploads/artist_images/'.$dataRs['image_name']);
				$this->common->delete_entry("tbl_artist_slider",$where);
			}
			$this->session->set_flashdata('Success','Record deleted successfully!');
			redirect('webmaster/user/sliders/'.$userId.'/'.$type);
		}
		redirect('webmaster/user/sliders/'.$userId.'/'.$type);
	}

	//video
	function feature_videos($userId){	 
		$data['act_id']="user";
		$data['userId'] = $userId;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0){
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code
			$data['artist_data'] = $this->common->getUserArtist($userId);
			if(!is_dir('./uploads/artist_video/')){
				mkdir('./uploads/artist_video/');
			}
			$type= $this->input->post('type');
			//var_dump($type);
			
			if($type=='video'){
				
				if ($_FILES['str_name']['name']!=""){
					$file=$_FILES;	
						$_FILES['str_name']['name'] = $file['str_name']['name'];					
						
						$filename = str_replace(' ','_','image-artist-video')."_".uniqid();
						$path = $_FILES['str_name']['name'];
						$ext = pathinfo($path, PATHINFO_EXTENSION);							
						
						$final_img = $filename.".".$ext;					
						
						$this->load->library('upload');
						$config['file_name']     = $filename;
						$config['upload_path']   = './uploads/artist_video/';
						$config['allowed_types'] = 'mp4|avi|flv|wmv';
									
						$this->upload->initialize($config);					
						$_FILES['str_name']['type']=$file['str_name']['type'];
						$_FILES['str_name']['tmp_name']=$file['str_name']['tmp_name'];
						$_FILES['str_name']['error']=$file['str_name']['error'];
						$_FILES['str_name']['size']=$file['str_name']['size'];
						$this->upload->do_upload('str_name');
					 	if($this->input->post('old_representation_video')!=''){
							@unlink('./uploads/artist_video/'.$this->input->post('old_representation_video'));
						}
				}else if($this->input->post('old_feature_video')!=''){
						$final_img = $this->input->post('old_feature_video');

				}
				

				$where_array = array('user_id' => $userId);
				$update = array( 
				 	'feature_video' => $final_img,
				 	'type' => $type
					);
				

				$this->common->update_entry('tbl_artist_user',$update,$where_array);
				$this->session->set_flashdata('Success','Record updated successfully .');
				redirect('webmaster/user/feature_videos/'.$userId);
			}
			else if($type=="url"){
				$final_img = $this->input->post('str_name');
				
				$where_array = array('user_id' => $userId);
				$update = array( 
				 	'feature_video' => $final_img,
				 	'type' => $type
					);
				

				$this->common->update_entry('tbl_artist_user',$update,$where_array);
				$this->session->set_flashdata('Success','Record updated successfully .');
				redirect('webmaster/user/feature_videos/'.$userId);
			}
			
			$this->load->view('webmaster/featurevideo',$data);
		}else redirect('webmaster/user');

	}

	//manage users videos
	function videos($userId,$id=0)
	{
		$data['act_id']="user";
		$data['userId'] = $userId;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		
		if($isExists>0)
		{
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code
			$data['id'] = $id;
			$tbl = 'tbl_artist_videos';
			$whereLinks =  array('id' => $id, 'user_id' =>$userId );
			
			if($id>0)
			{
				$data['btnCapt'] = 'Update';
				$data['dataDs'] = $this->common->getVideoDetails($whereLinks);
			}
			else
			{ 
			    $data['btnCapt'] = 'Add'; 
			}
 			
 			$this->form_validation->set_rules('videos_link','Link','required|trim|prep_url|callback_valid_url_format|callback_url_exists');
 			
 			if($this->form_validation->run())
 			{
 				$valueArray = array(
 								'user_id' => $userId,
 								'videos_link' => $this->db->escape_str($this->input->post('videos_link')),
 								'added_date' => date('Y-m-d h:m:s'),
 								'added_by' => 0
 					            );


               
 				if($id>0)
 				{
					$this->common->update_entry($tbl,$valueArray,$whereLinks);
					$this->session->set_flashdata('Success','Record updated successfully .');
					redirect('webmaster/user/videos/'.$userId);
				}
				else
				{
                
					$this->common->add_records($tbl,$valueArray);
					$this->session->set_flashdata('Success','Record added successfully .');
					redirect('webmaster/user/videos/'.$userId);
				}
			}
			$where = array('user_id' => $userId);
			$data['artistNumRow'] = $this->common->num_artistVideos($where);
		  //$data['artistData'] = $this->common->getArtistVideoList('id,videos_link,user_id,added_by',$where);
			
			$data['artistData'] = $this->common->getArtistVideoListbyID($userId);
			
			
		    $this->load->view('webmaster/manage_videos',$data);
		}
		else
		{ 
		    redirect('webmaster/user'); 
		}
	}

	function url_exists($url){                                   
        $url_data = parse_url($url); // scheme, host, port, path, query
        if(!@fsockopen($url_data['host'], isset($url_data['port']) ? $url_data['port'] : 80)){
            $this->form_validation->set_message('url_exists', 'The URL you entered is not accessible.');
            return FALSE;
        }               
         
        return TRUE;
    }  

  	function valid_url_format($str){
  		 
        $pattern = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
        if (!preg_match($pattern, $str)){
            $this->form_validation->set_message('valid_url_format', 'The URL you entered is not correctly formatted.');
            return FALSE;
        }
 
        return TRUE;
    } 

    function deleteVideos($userId){
		if($this->input->post('action')=="delete"){
		for($i=0;$i<count($this->input->post('cb'));$i++){
			$delid=$this->security->xss_clean($this->input->post('cb'));
			$where= array("id " => $delid[$i]);
			$this->common->delete_entry("tbl_artist_videos",$where);
		}
		$this->session->set_flashdata('Success','Record deleted successfully!');
		redirect('webmaster/user/videos/'.$userId);
		}
		redirect('webmaster/user/videos/'.$userId);
	}

	//social links
	function socialLinks($userId){
		$data['act_id']="user";
		$data['userId'] = $userId;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0){
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code
			$whereLinks = array('user_id' => $userId);
			$tbl = "tbl_artist_social_links";
			$data['artistNumRow'] =  $artistNumRow = $this->common->num_artistSocialLinks($whereLinks);
			$data['artistData'] = $this->common->getartistSocialLinks($whereLinks);
			$valueArray = array(
 								'user_id' => $userId,
 								'fb' =>'',
 								'pintrest' => "",
 								'twitter' => "",
 								'gplus' => "",
 								'added_by' => 0
 					);
			if($artistNumRow==0){
				$this->common->add_records($tbl,$valueArray);
			}
			$data['btnCapt'] = 'Update';
 			if($this->input->post('mode')==1){
 				
 				$valueArray = array(
 								'fb' => $this->db->escape_str($this->input->post('fb')),
 								'pintrest' => $this->db->escape_str($this->input->post('pintrest')),
 								'twitter' => $this->db->escape_str($this->input->post('twitter')),
 								'gplus' => $this->db->escape_str($this->input->post('gplus')),
 					 );

 			 
					$this->common->update_entry($tbl,$valueArray,$whereLinks);
					$this->session->set_flashdata('Success','Record updated successfully .');
					redirect('webmaster/user/socialLinks/'.$userId);
			 
			}
		$this->load->view('webmaster/manage_socialLinks',$data);
		}else{ redirect('webmaster/user'); }
	}
	
	//Gallery
	function gallery($userId,$id=0)
	{
	    //session_start();
        $_SESSION["morefeuURL"] = $_SERVER["HTTP_REFERER"];
     	$data['act_id']="user";
		$data['userId'] = $userId;
		$data['id'] = $id;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0)
		{
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code 
			$whereLinks = array('user_id' => $userId);
			$data['artistNumRow'] =  $artistNumRow = $this->common->num_ArtistGallery($whereLinks);
			$data['artistData'] = $this->common->getArtistGallery($whereLinks);
			$data['artistDetail']  = '';
			$tbl ="tbl_artist_gallery";
			
		
			if($id>0)
			{
				$btnCapt = 'Update';
				$data['artistDetails']  = $this->common->getArtistGalleryDetails($id);
			}else{
				$btnCapt = "Add";
			}
			$data['btnCapt'] = $btnCapt;
			if($this->input->post('mode')=='1')
			{
    			$flag=0;
    		  	if($_FILES['image_name']['name']!='')
    		  	{
					$flag=0;
					//==validaye image exists 
					$path_img = $_FILES['image_name']['name'];
					$ext_img = pathinfo($path_img, PATHINFO_EXTENSION);
					$valid_ext_arr = array('png','jpg','jpeg','gif');
					if(!in_array(strtolower($ext_img),$valid_ext_arr))
					{
						$flag = 1;						
						break;
					}
			    } 
			    if($flag==0){
			        
			       
					if(!is_dir('./uploads/artist-gallery'))
					{
						mkdir('./uploads/artist-gallery/');
						mkdir('./uploads/artist-gallery/original/');
						mkdir('./uploads/artist-gallery/thumb/');
					    //mkdir('./uploads/artist-gallery/large/');
						mkdir('./uploads/artist-gallery/gallery/');
						//mkdir('./uploads/artist-gallery/style/');
					}

					$final_img  = '';
			 		if($_FILES['image_name']['name']!='')
			 		{
						$file=$_FILES;	
						$_FILES['image_name']['name'] = $file['image_name']['name'];					
						
						$filename = str_replace(' ','_','artist_art')."_".uniqid();
						$path = $_FILES['image_name']['name'];
						$ext = pathinfo($path, PATHINFO_EXTENSION);							
						
					    $final_img = $filename.".".$ext;					
						$WaterMark = './uploads/artist-gallery/original/ArtGalaxie_Watermark.png';
						
						$this->load->library('upload');
						$config['file_name']     = $filename;
						$config['upload_path']   = './uploads/artist-gallery/original';
						$config['allowed_types'] = 'png|jpeg|jpg|gif';
									
						$this->upload->initialize($config);					
						$_FILES['image_name']['type']=$file['image_name']['type'];
						$_FILES['image_name']['tmp_name']=$file['image_name']['tmp_name'];
						$_FILES['image_name']['error']=$file['image_name']['error'];
						$_FILES['image_name']['size']=$file['image_name']['size'];
						//$this->upload->do_upload('image_name');
						///////
						if ( ! $this->upload->do_upload('image_name'))
                        {    
                    
                        $this->form_validation->set_error_delimiters('<p class="error">', '</p>');
                        $error = array('error' => $this->upload->display_errors());
    
                        $this->load->view('image', $error);
                        }
                    else
                    {
                    $data = array('upload_data' => $this->upload->data());
                    
                    $filename = $data['upload_data']['file_name'];
				
                    /* Image resize, crop, rotate, watermark code here */
					$imgConfig = array();
                        
					$imgConfig = array();
                        
					$imgConfig['image_library'] = 'GD2';
											
					$imgConfig['source_image']  = './uploads/artist-gallery/original/'.$filename;

					$imgConfig['wm_type']       = 'overlay';    
											
					$imgConfig['wm_overlay_path'] = './uploads/artist-gallery/original/ArtGalaxie_Watermark.png';
					$imgConfig['wm_opacity'] = '100';
                    $imgConfig['wm_vrt_alignment'] = 'bottom';
                    $imgConfig['wm_hor_alignment'] = 'right';
                    $imgConfig['wm_vrt_offset'] = '10';
					
					$this->load->library('image_lib', $imgConfig);
											
					$this->image_lib->initialize($imgConfig);
					
					$a = $this->input->post('is_feature');
					if($a!='1')
					{
					    $this->image_lib->watermark(); 
					}
				}
						///////
						$this->common->create_thumb_resize($final_img,'./uploads/artist-gallery/original/','356','360','thumb');
					 	//$this->common->create_thumb_resize($final_img,'./uploads/artist-gallery/original/','1029','623','large');
					 	$this->common->create_thumb_resize($final_img,'./uploads/artist-gallery/original/','470','360','gallery');
					 	
						if($this->input->post('old_image_name')!='')
						{
							@unlink('./uploads/artist-gallery/original/'.$this->input->post('old_image_name'));
							@unlink('./uploads/artist-gallery/original/thumb/'.$this->input->post('old_image_name'));
							//@unlink('./uploads/artist-gallery/original/large/'.$this->input->post('old_image_name'));
							@unlink('./uploads/artist-gallery/original/gallery/'.$this->input->post('old_image_name'));
						}
					}
					else
					{
						if($this->input->post('old_image_name')!='')
						{
							$final_img = $this->input->post('old_image_name');
						}
					}
				}

				if($this->input->post('is_feature')==1)
				{
					//$this->admin->setGalleryImageUnFeatured($userId,$id);
				}
				if($id>0)
				{
					$valueArray = array(
					            'image_title' => $this->input->post('image_title'),
								'image_name' => $final_img,
								'is_feature' => $this->input->post('is_feature'),
								'image_color' => $this->input->post('colour_type'),
							//	'water_mark_image'=>$final_imgBlockwater
							);
					$whereGal = array('user_id' => $userId, 'id' => $id);
 					$this->common->update_entry($tbl,$valueArray,$whereGal);
 			
 					// Added in notification table 
                   /* $table_name = 'notifications';
                    $insert_notify_array = array(
                    					'notification_from_user_id'=> $data['userDetails']['first_name'].' '.$data['userDetails']['last_name'],
                    					'notification_type'=>'artist',
                    					'notification_text'=>$data['userDetails']['first_name'].' '.$data['userDetails']['last_name']." has updated Featured image gallery details.",
                    					'notification_url'=>'/webmaster/user/gallery/'.$userId.".html",
                    					'notification_datetime'=>date("m/d/Y h:i:s"),
                    					'notification_status'=>'Pending'
                    					);
                    $this->common->add_records_notification($table_name,$insert_notify_array);*/
                

					$this->session->set_flashdata('Success','Record updated successfully .');
					
					$morefurl =  $_SESSION["morefeuURL"];
					
					if (strpos($morefurl,'morefeaturedgallery') !== false) 
					{
					    redirect('webmaster/categories/more_featured_artists');
					}
					else
					{
            		    redirect('webmaster/user/gallery/'.$userId);    
            		}
            		
				}else{

					$valueArray = array(
									'user_id' => $userId,
									'image_name' => $final_img,
									'image_title' => $this->input->post('image_title'),
									'added_by' => 0,
									'added_date' => date('Y-m-d h:m:s'),
									'is_feature' => $this->input->post('is_feature'),
								//	'water_mark_image'=>$final_imgBlockwater

								);
					$this->common->add_records($tbl,$valueArray);
					$this->session->set_flashdata('Success','Record added successfully .');
					redirect('webmaster/user/gallery/'.$userId);

				}
  			}
 			$this->load->view('webmaster/galleries',$data);
		}else{
			 redirect('webmaster/user'); 
		}
	} 
    
    //Gallery
	function morefeaturedgallery($userId,$id=0){
	    
	    
		$data['act_id']="user";
		$data['userId'] = $userId;
		$data['id'] = $id;
		$where = array('id' => $userId );
		$isExists = $this->common->num_users($where);
		if($isExists>0){
			//====common code for top header of artist
			$data['userDetails'] = $userDetails = $this->common->getUserDetails($userId);
		 	$data['styleName'] = $this->common->getStyleName($userDetails['style_id']);
			$data['galleryName'] = $this->common->getGalleryName($userDetails['galleries_id']); 
			//====common code 
			$whereLinks = array('user_id' => $userId);
			$data['artistNumRow'] =  $artistNumRow = $this->common->num_ArtistGallery($whereLinks);
			$data['artistData'] = $this->common->getArtistGallery($whereLinks);
			$data['artistDetail']  = '';
			$tbl ="tbl_artist_gallery";
			if($id>0){
				$btnCapt = 'Update';
				$data['artistDetails']  = $this->common->getArtistGalleryDetails($id);
			}else{
				$btnCapt = "Add";
			}
			$data['btnCapt'] = $btnCapt;
			if($this->input->post('mode')=='1'){
			$flag=0;
		  	if($_FILES['image_name']['name']!=''){
					$flag=0;
					//==validaye image exists 
					
					$path_img = $_FILES['image_name']['name'];
				
					$ext_img = pathinfo($path_img, PATHINFO_EXTENSION);
						
					$valid_ext_arr = array('png','jpg','jpeg','gif');
					if(!in_array(strtolower($ext_img),$valid_ext_arr))
					{
						$flag = 1;						
						break;
					}

			} 
			if($flag==0){
					if(!is_dir('./uploads/artist-gallery')){
						mkdir('./uploads/artist-gallery/');
						mkdir('./uploads/artist-gallery/original/');
						mkdir('./uploads/artist-gallery/thumb/');
					//	mkdir('./uploads/artist-gallery/large/');
						mkdir('./uploads/artist-gallery/gallery/');
						//mkdir('./uploads/artist-gallery/style/');
					}

					$final_img  = '';
			 		if($_FILES['image_name']['name']!=''){

					
						$file=$_FILES;	
						$_FILES['image_name']['name'] = $file['image_name']['name'];					
						
						$filename = str_replace(' ','_','artist_art')."_".uniqid();
						$path = $_FILES['image_name']['name'];
						$ext = pathinfo($path, PATHINFO_EXTENSION);							
						
					    $final_img = $filename.".".$ext;					
						$WaterMark = './uploads/artist-gallery/original/ArtGalaxie_Watermark.png';
						
						$this->load->library('upload');
						$config['file_name']     = $filename;
						$config['upload_path']   = './uploads/artist-gallery/original';
						$config['allowed_types'] = 'png|jpeg|jpg|gif';
									
						$this->upload->initialize($config);					
						$_FILES['image_name']['type']=$file['image_name']['type'];
						$_FILES['image_name']['tmp_name']=$file['image_name']['tmp_name'];
						$_FILES['image_name']['error']=$file['image_name']['error'];
						$_FILES['image_name']['size']=$file['image_name']['size'];
						$this->upload->do_upload('image_name');
						$this->common->create_thumb_resize($final_img,'./uploads/artist-gallery/original/','356','360','thumb');
					 	//$this->common->create_thumb_resize($final_img,'./uploads/artist-gallery/original/','1029','623','large');
					 	$this->common->create_thumb_resize($final_img,'./uploads/artist-gallery/original/','470','360','gallery');
					    
					    
					    
						if($this->input->post('old_image_name')!=''){
							@unlink('./uploads/artist-gallery/original/'.$this->input->post('old_image_name'));
							@unlink('./uploads/artist-gallery/original/thumb/'.$this->input->post('old_image_name'));
							//@unlink('./uploads/artist-gallery/original/large/'.$this->input->post('old_image_name'));
							@unlink('./uploads/artist-gallery/original/gallery/'.$this->input->post('old_image_name'));
						}

					}else{
						if($this->input->post('old_image_name')!=''){
							$final_img = $this->input->post('old_image_name');
						}
					}
				}

				if($this->input->post('is_feature')==1){
					$this->admin->setGalleryImageUnFeatured($userId,$id);
				}
				if($id>0){
					$valueArray = array(
					            'image_title' => $this->input->post('image_title'),
								'image_name' => $final_img,
								'is_feature' => $this->input->post('is_feature'),
							//	'water_mark_image'=>$final_imgBlockwater
							);
					$whereGal = array('user_id' => $userId, 'id' => $id);
 					$this->common->update_entry($tbl,$valueArray,$whereGal);
					$this->session->set_flashdata('Success','Record updated successfully .');
					
					redirect('webmaster/more_featured_artists');
            	
					
					
				}else{

					$valueArray = array(
									'user_id' => $userId,
									'image_name' => $final_img,
									'added_by' => 0,
									'added_date' => date('Y-m-d h:m:s'),
									'is_feature' => $this->input->post('is_feature'),
								//	'water_mark_image'=>$final_imgBlockwater

								);
					$this->common->add_records($tbl,$valueArray);
					$this->session->set_flashdata('Success','Record added successfully .');
					redirect('webmaster/user/gallery/'.$userId);

				}
  			}
 			$this->load->view('webmaster/galleries',$data);
		}else{
			 redirect('webmaster/user'); 
		}
	} 
	
	
	function delete_gallery($userId){
		if($this->input->post('action')=="delete"){
			for($i=0;$i<count($this->input->post('cb'));$i++){ 
				$delid=$this->security->xss_clean($this->input->post('cb'));
				$where= array("id " => $delid[$i]);
				$dataRs = $this->common->getOneRowArray("*","tbl_artist_gallery",$where);
				@unlink('./uploads/artist-gallery/original/'.$dataRs['image_name']);
				@unlink('./uploads/artist-gallery/original/thumb/'.$dataRs['image_name']);
			//	@unlink('./uploads/artist-gallery/original/large/'.$dataRs['image_name']);
				@unlink('./uploads/artist-gallery/original/gallery/'.$dataRs['image_name']);

				$this->common->delete_entry("tbl_artist_gallery",$where);
			}
			$this->session->set_flashdata('Success','Record deleted successfully!');
			redirect('webmaster/user/gallery/'.$userId);
		}
		redirect('webmaster/user/gallery/'.$userId);
	}

}?>