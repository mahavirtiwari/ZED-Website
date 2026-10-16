<script src="<?php echo base_url();?>assets/select2/js/select2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/select2/css/select2.css">
<br>
<div class="zed-parameters-screen">
  <div class="row">
    <div class="col-sm-8">
      <div class="form-group">
        <label>View Model as per your NIC Sector (2 digit)</label>
        <?php       
        $categories=$parent;
        $js = 'id="paramSector" class="form-control" style="width: 100% !important; max-width: 500px !important;"';
//$value=7;
        echo form_dropdown('paramSector',$categories,'',$js);?>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12" id="displine_div"></div>
  </div>
</div>
<script type="text/javascript">
  $('#paramSector').select2();
  $(document).on("change",'#paramSector',function(e){
    var data=return_ajax_response('<?php echo base_url('home/zedmatrixparameter');?>','&id='+$(this).val());
    var disdiv = "";
    if(jQuery.isEmptyObject(data.discipline))
    {
      disdiv+= '<div class="zed-discipline-box">';
      disdiv += "<h4 id='para'>No Results Found</h4>";
      disdiv+="</div>";
      $("#displine_div").html(disdiv);
    }
    else
    {
      disdiv+= '<div class="zed-discipline-box zed-matrix-table table-responsive">';
      disdiv += "<table class='table table-bordered' style='table-layout: fixed;'><thead><th style='width: 150px;'>Parameter</th> <th>Level 1</th> <th>Level 2</th> <th>Level 3</th> <th>Level 4</th> <th>Level 5</th></thead><tbody>";
      $.each(data.discipline, function(index, element) {
        disdiv += "<tr>"+"<td>"+element.code+" - "+element.name+ "</td>"+"<td>"+element.level1_comment+ "</td>"+"<td>"+element.level2_comment+ "</td>"+"<td>"+element.level3_comment+ "</td>"+"<td>"+element.level4_comment+ "</td>"+"<td>"+element.level5_comment+ "</td>"+"</tr>";
        ;
        disdiv+="";
      })
      disdiv+=" </tbody></table></div>";
      $("#displine_div").html(disdiv);
    }
  });
</script>

