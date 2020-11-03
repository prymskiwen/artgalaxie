<!DOCTYPE html>
<html>
<head>
 <? $this->load->view('webmaster/template/head'); ?>
  <!-- Data Tables -->
 <link href="<?=base_url()?>webmaster_assets/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
 <link href="<?=base_url()?>webmaster_assets/css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">
 <link href="<?=base_url()?>webmaster_assets/css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">
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
          <h2>Newsletters Subscriber List</h2>
          <ol class="breadcrumb">
            <li><a href="<?=site_url('dashboard')?>">Dashboard</a></li>
            <li><a>Manage Newsletters Subscriber</a></li>
            <li class="active"><strong>Newsletters Link</strong></li>
          </ol>
        </div>
        </div>
         <div class="wrapper wrapper-content animated fadeInRight">
        <?php if(isset($id) && $id>0){ ?>
           <div class="row "><div class="col-lg-12"><div class="ibox float-e-margins">
            <div class="ibox-title">
              <h5><?=$btnCapt?> Newsletters</h5>
              <div class="clearfix">&nbsp;</div>
            </div>
            <div class="ibox-content">
             <? if($this->session->flashdata('Error')){?>
             <div class="alert alert-danger alert-dismissable" align="center">
              <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
              <?=$this->session->flashdata('Error')?>
            </div>
            <? } ?>
            <form action="<?=site_url('webmaster/newsletters/subscriber/'.$id)?>" method="post" name="frm" id="frm" class="form-horizontal">

                <div class="form-group">
                    <label class="col-lg-2 control-label">&nbsp;Link For</label>
                    <div class="col-lg-10">
                        <input type="text" name="name" id="name" class="form-control" value="<?php if(@$boxData['name']!=""){ echo $boxData['name'];}else{ echo set_value('name');  } ?>" />
                        <span class="help-block text-danger">
                            <?php if(form_error('name')!=""){ echo form_error('name'); } ?>
                        </span>
                    </div>  
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">&nbsp;Link</label>
                    <div class="col-lg-10">
                        <input type="text" name="newsletter_link" id="newsletter_link" class="form-control" value="<?php if(@$boxData['newsletter_link']!=""){ echo $boxData['newsletter_link'];}else{ echo set_value('newsletter_link');  } ?>" />
                        <span class="help-block text-danger">
                            <?php if(form_error('newsletter_link')!=""){ echo form_error('newsletter_link'); } ?>
                        </span>
                    </div>  
                </div>
                
       <div class="hr-line-dashed"></div>
       <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
         <a href="<?=site_url('webmaster/newsletters/subscriber');?>" class="btn btn-white">Cancel</a>
         <input type="submit" class="btn btn-primary" id="btnsave" value="<?=$btnCapt?> Newsletter Link" name="btnsave">
       </div>
     </div>
   </form>
 </div>
</div>
</div></div> <?php } ?>


<div class="row "><div class="col-lg-12"><div class="ibox float-e-margins">
 <!--  <div class="ibox-title"></div> -->
 <div class="ibox-title ">
  <h5 class="pull-left">Newsletters </h5>
  <div class="clearfix"> </div>
</div>
<div class="ibox-content">
  <? if($this->session->flashdata('Success')){ ?>
  <div class="alert alert-success alert-dismissable" align="center">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <?=$this->session->flashdata('Success')?>
  </div>
  <? } ?>


  <form name="frmcustlist" method="post" class="form-horizontal" action="#"> 
            <input type="hidden" value="delete" name="action" /> 
    <table class="table table-striped table-bordered table-hover dataTables-example" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th width="20%"> Link For </th>      
          <th align="left" > Link </th>      
          <th width="15%">Action</th>
        </tr>
      </thead>
      <tbody>  
        <?php if(!empty($num_rec)){?>
        <?php foreach($dataDs as $dataRs){ ?>
        <tr>
           <td><?php  echo stripslashes($dataRs['name']); ?></td>
           <td><?php  echo stripslashes($dataRs['newsletter_link']); ?></td>
           <td align="center"><a href="<?=site_url('webmaster/newsletters/subscriber/'.$dataRs['id'])?>"  title="Edit" ><i class="fa fa-edit"></i></a></td>
        </tr>   
            <?php } ?>
            <?php  }?>
          </tbody>
        </table>
      </form> 
    </div>
  </div>
</div></div></div>
<? $this->load->view("webmaster/template/footer")?>
</div>
</div>
<!-- Mainly scripts -->
<? $this->load->view('webmaster/template/bot_script'); ?>
<!-- Data Tables -->
<script src="<?=base_url()?>webmaster_assets/js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="<?=base_url()?>webmaster_assets/js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script src="<?=base_url()?>webmaster_assets/js/plugins/dataTables/dataTables.responsive.js"></script>
<script src="<?=base_url()?>webmaster_assets/js/plugins/dataTables/dataTables.tableTools.min.js"></script>
<!-- Page-Level Scripts -->
<script>
$(document).ready(function() {
  $('.dataTables-example').dataTable({
    responsive: true,
    "aaSorting": []
  });
});
</script>
</body>
</html>
