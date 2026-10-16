<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php echo pwt_meta(); ?>
	<title>ZED CMS Admin</title>
	<link href="<?php echo URL_ASSETS_IMG; ?>/zed-fav.png" rel="shortcut icon" />
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_CSS; ?>/animate.css" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_CSS; ?>/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URL_ASSETS_PLUGINS; ?>/trumbowyg/dist/ui/trumbowyg.min.css">
  <link href="<?php echo URL_ASSETS_CSS; ?>/zed-icons.css" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_CSS; ?>/rtd-asky.css" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_CSS; ?>/zed-admin.css" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_PLUGINS; ?>/select2/select2.css" rel="stylesheet">
  <!-- The Rockstar jQuery -->
  <script src="<?php echo URL_ASSETS_JS; ?>/jquery-3.7.1.min.js"></script>
  <script src="<?php echo URL_ASSETS_JS; ?>/jquery-migrate-3.5.2.min.js"></script>
  <!-- jquery trunbowyg scripts -->
  <script src="<?php echo URL_ASSETS_PLUGINS; ?>/trumbowyg/dist/trumbowyg.min.js"></script>
  <script src="<?php echo URL_ASSETS_PLUGINS; ?>/trumbowyg/dist/plugins/upload/trumbowyg.upload.min.js"></script>
  <!-- jquery cropit plugin -->
  <script src="<?php echo URL_ASSETS_PLUGINS; ?>/cropit/jquery.cropit.js"></script>
  </head>
<body>
	<?php include('top.php');?>
	<?php include('left.php');?>
	<div id="content" class="zed-content">
   <?php $this->load->view($page);?>
 </div>
 <?php include('right.php');?>
  <!-- conact Modal -->
 <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Contact Details</h4>
      </div>
      <div class="modal-body" id="view-message-modal">
         
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Contact Modal -->
 <!-- CONFIRM ALERT MODAL WINDOW -->
 <style type="text/css">
  #confirm-modal-window div.modal-body{background: #fff;text-align: center !important;}
  #confirm-modal-window div.modal-footer{background: #f6f6f6;text-align:center!important;}
</style>
<div id="confirm-modal-window" class="modal fade">
 <div class="modal-dialog modal-sm">
  <div class="modal-body">
    Are you sure?
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-primary" id="confirm-delete">Delete</button>
    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
  </div>
</div>
</div>
<!-- ./CONFIRM ALERT MODAL WINDOW -->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo URL_ASSETS_JS; ?>/Chart.min.js"></script>
<script src="<?php echo URL_ASSETS_PLUGINS; ?>/datatables-1.13.11/js/jquery.dataTables.min.js"></script>
<script src="<?php echo URL_ASSETS_PLUGINS; ?>/datatables-1.13.11/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo URL_ASSETS_PLUGINS; ?>/select2/select2.full.min.js"></script>
<script src="<?php echo URL_ASSETS_JS; ?>/rtd-toasty.js"></script>
<script src="<?php echo URL_ASSETS_JS; ?>/rtd-asky.js"></script>
<script src="<?php echo URL_ASSETS_JS; ?>/bootstrap.min.js"></script>
<script src="<?php echo URL_ASSETS_JS; ?>/zed-app.js"></script>
<script src="<?php echo URL_ASSETS_JS; ?>/zed-pwt.js"></script>
<script src="<?php echo URL_ASSETS_JS; ?>/zed-editor-styles.js"></script>

<!-- jquery file upload js -->
<script type="text/javascript">
  setTimeout(function() {
    $('.alert').fadeOut('slow');
  },3000);
</script>
<!-- Delete Function  -->
<script type="text/javascript">
  var i=0;
  $("#delete").click(function(){
   var arr = [];
   $('input[type=checkbox]:checked').each(function(){        
    arr[i++] = $(this).val();
  });
   if(arr.length==0) 
   {
     $.toasty('Must choose at least one! ','danger');
   }
   else
   {
    $('#confirm-modal-window').modal({ backdrop: 'static', keyboard: false })
    .one('click', '#confirm-delete', function() {
     $('#bulk_actions_form').submit();
   });
  }
});
</script>
<!-- Google Search results preview -->
<script>
  // Reflect text as they type
  $(document).ready(function() {
    $('[data-reflecton]').each(function(){
      $(this).on('input propertychange paste', function(){
        var $tgt = $($(this).attr('data-reflecton'));
        if($tgt.length){
          $tgt.text($(this).val());
        }
      }).trigger('input');
    });
  });
</script>

</body>
</html>