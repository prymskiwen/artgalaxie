<? $act_id='CMS';?>
<!DOCTYPE html>
<html>
<head>
  <? $this->load->view('webmaster/template/head'); ?>
  <link href="<?=base_url()?>webmaster_assets/css/plugins/iCheck/custom.css" rel="stylesheet">
  <script src="<?=base_url()?>webmaster_assets/js/jquery-2.1.1.js"></script>
  <script src="<?=base_url()?>webmaster_assets/ckeditor/ckeditor.js"></script>
</head>
<body >
 <div id="wrapper">
  <!--- Nav start -->
  <? $this->load->view('webmaster/template/left_nav'); ?>
  <!--- Nav end -->
  <div id="page-wrapper" class="gray-bg dashbard-1">
   <? $this->load->view('webmaster/template/top'); ?>
   <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
          <h2>Art Of Giving</h2>
          <ol class="breadcrumb">
            <li><a href="<?=site_url('dashboard')?>">Dashboard</a></li>
            <li><a>Manage Art Of Giving</a></li>
            <li class="active"><strong>Art Of Giving</strong></li>
          </ol>
        </div>
    <div class="col-lg-4"><div class="title-action">
      <a class="btn btn-primary pull-right" href="<?=site_url('webmaster/art_of_giving/just_giving_book')?>">Back to the list</a>
    </div>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row "><div class="col-lg-12"><div class="ibox float-e-margins">
    <div class="ibox-title">
      <h5><?=$btnCapt?> Categories</h5>
      <div class="clearfix">&nbsp;</div>
    </div>
    <div class="ibox-content">
     <? if($this->session->flashdata('Error')){ ?>
     <div class="alert alert-danger alert-dismissable" align="center">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
      <?=$this->session->flashdata('Error')?>
    </div>
    <? } ?>
    <form action="<?=site_url('webmaster/art_of_giving/manage_just_giving_book/'.$id)?>" method="post" enctype="multipart/form-data" name="frm" id="frm" class="form-horizontal">

        <div class="form-group">
         <label class="col-lg-2 control-label">&nbsp;Art Text</label>
         <div class="col-lg-10">
          <textarea name="giving_book_text" id="giving_book_text" class="form-control"  rows="7"><?=@$form_data['giving_book_text']?></textarea>
          <script>
            CKEDITOR.replace('giving_book_text', {
              "filebrowserImageUploadUrl": "<?=site_url('webmaster/manage_just_giving/ck_imageupload')?>"
            });
        </script>
          <span class="help-block text-danger">
                  <?php if(form_error('giving_book_text')!=""){  echo  form_error('giving_book_text'); } ?>
         </span>  
         </div>  
       </div>
          
          
          <?php /*$artist=''; 
          if(@$form_data['artist_id']!=''){
            $artist = @explode(',',$form_data['artist_id']); 
          }*/?>
          
          <?php /* ?>
          <div class="form-group">
             <label class="col-lg-2 control-label">Artist </label>
              <div class="col-lg-10">
                <select name="user_artist[]" id="user_artist" class="form-control"  multiple <?php if($id==0){ ?> required <?php } ?>>
                  <option value=""> Select artists</option>
                  <?php foreach ($user_artist as $user_artistRs) { ?>
                     <option value="<?=stripslashes($user_artistRs['id'])?>" <?php if(@in_array($user_artistRs['id'],$artist,true)){ ?>  selected="selected" <?php } ?>>
                      <?=stripslashes($user_artistRs['first_name']." ".$user_artistRs['last_name'])?>
                    </option>
                  <?php }?>
                </select>
               <span class="help-block text-danger">
                  <?php if(form_error('user_artist')!=""){  echo  form_error('user_artist'); } ?>
                </span> 
              </div>
           </div>
         
          <?php */ ?>
        
            <?php $public=''; 
          if(@$form_data['publication_id']!=''){
            $public = @explode(',',$form_data['publication_id']); 
          }?>
          <div class="form-group">
             <label class="col-lg-2 control-label">Select Publication </label>
              <div class="col-lg-10">
                <select name="publication_id[]" id="publication_id" class="form-control" <?php if($id==0){ ?> required <?php } ?>>
                  <?php 
                  foreach ($Pdata as $user_pdata) { ?>
                     <option value="<?=stripslashes($user_pdata['id'])?>" <?php if(@in_array($user_pdata['id'],$public,true)){ ?>  selected="selected" <?php } ?>><?=$user_pdata['title']?></option>
                  <?php }?>
                </select>
              </div>
           </div>
           
           
        <div class="form-group"><label class="col-lg-2 control-label">Banner Image</label>
          <div class="col-lg-10">
            <div class="radio i-checks">
             <input type="file" name="banner_image" id="banner_image" > 
             <input type="hidden" name="old_banner_image" id="old_banner_image"  value="<?=@$form_data['banner_image']?>" <? if($id==0){ ?> required <?php } ?>> 
             <span style="font-size:10px;color:gray;">Image Size (1920 X 747)<span>
            </div>
          </div>
        </div>

        <?php if(@$form_data['banner_image']!=''){ ?>
          <div class="form-group"><label class="col-lg-2 control-label">Old Banner Image</label>
          <div class="col-lg-10">
            <div class="radio i-checks">
            <input type="hidden" name="old_banner_image" id="old_banner_image"  value="<?=@$form_data['banner_image']?>"> 
            <img src="<?=base_url()?>uploads/art_of_giving/<?=$form_data['banner_image']?>" class="img-responsive">
            </div>
          </div>
        </div>
        <?php } ?>



        

     <div class="hr-line-dashed"></div>
     <div class="form-group">
      <div class="col-sm-4 col-sm-offset-2">
       <input type="submit" class="btn btn-primary" id="btnsave" value="<?=$btnCapt?>" name="btnsave">
     </div>
   </div>
 </form>
</div>
</div>
</div></div></div>
<? $this->load->view("webmaster/template/footer")?>
</div>
</div>
<? $this->load->view('webmaster/template/bot_script'); ?>
   <!-- iCheck -->
        <script src="<?=base_url()?>webmaster_assets/js/plugins/iCheck/icheck.min.js"></script>
        <script>
        $(document).ready(function () {
          $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
          });
        });
       </script>
</body>
</html>
