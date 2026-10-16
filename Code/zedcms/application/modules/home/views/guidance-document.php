<script src="<?php echo base_url();?>assets/select2/js/select2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/select2/css/select2.css">
<div class="zed-parameters-screen">
  <div class="row">  
    <div class="col-sm-12">
      <div class="form-group zed-formgroup">
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
    var url='<?php echo base_url('home/zedguidance');?>';
    var id=$(this).val();
    var disdiv = "";
    $.ajax({
      url:url,
      data:{
        id:id
      },
      method:'GET',
      success:function(response){
        var response=JSON.parse(response);
        if(response.success==true){
          disdiv+= '<div class="zed-discipline-box zed-matrix-table table-responsive">';
          disdiv += "<table class='table table-bordered zed-table-fixed'><thead><th class='zed-col-half'>Name</th> <th>URL</th></thead><tbody>";
            $.each(response.discipline, function(index, element) {
              disdiv += "<tr>"+"<td>"+element.d_name+ "</td>"+"<td><a  target='_blank' href='"+element.d_url+"'>"+element.d_url+"</a></td>"+"</tr>";
              ;
              //disdiv+="";
            })
            disdiv+=" </tbody></table></div>";
            $("#displine_div").html(disdiv);
        }
        else{
          disdiv+= '<div class="zed-discipline-box">';
          disdiv += "<h4 id='para'>No Results Found</h4>";
          disdiv+="</div>";
         $("#displine_div").html(disdiv);
        }
      },
      error:function(){

      }
    }); 
  });
</script>

