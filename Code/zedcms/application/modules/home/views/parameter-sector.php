<script src="<?php echo base_url();?>assets/select2/js/select2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/select2/css/select2.css">
<br>
<div class="zed-parameters-screen">
  <div class="row">
    <div class="col-sm-8">
      <div class="form-group">
        <label>View Parameters as per your NIC Sector (2 digit)</label>
        <?php       
        $categories=$parent;
        $js = 'id="paramSector" class="form-control" ';
        echo form_dropdown('paramSector',$categories,'',$js);?>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12" id="displine_div">
    </div>
  </div>
  <script type="text/javascript">
    $('#paramSector').select2();
    $(document).on("change",'#paramSector',function(e){
      var data=return_ajax_response('<?php echo base_url('home/disciplineparameter');?>','&id='+$(this).val());
      var disdiv = "";
      if(jQuery.isEmptyObject(data.discipline))
      {
        disdiv+= '<div class="zed-discipline-box">';
        disdiv += "<h4 id='para'>NO Resuits Found</h4>";
        disdiv+="</div>";
        $("#displine_div").html(disdiv);
      }
      else
      {
        $.each(data.discipline, function(index, element) {
          disdiv+= '<div class="zed-discipline-box">';
          disdiv += "<h4 id='para'>" + element.para_discipline + "</h4>";
          ;
          disdiv+="<table class='table table-bordered'><thead><tr><th>Parameter</th> <th>Description</th></tr></thead> <tbody>";
          $.each(element.data, function(index, element) {
            disdiv += "<tr>" +"<td>"+element.para_code_name+ "</td>"+"" +"<td>"+element.description+ "</td>"+"</tr>";
          })
          disdiv+=" </tbody></table></div>";
        })
        $("#displine_div").html(disdiv);
      }
    });
  </script>
