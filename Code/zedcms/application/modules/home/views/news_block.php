<!-- confrim new block modal -->
<style type="text/css">
  #news_confirm_modal div.modal-header{background: #fff;}
  #news_confirm_modal div.modal-body{background: #fff;}
  #news_confirm_modal div.modal-footer{background: #f6f6f6;}
</style>
<div id="news_confirm_modal" class="modal fade">
 <div class="modal-dialog">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">News Block</h4>
  </div>
  <div class="modal-body">
   <div class="table-responsive">   
    <table class="table">
      <thead>
        <tr>
          <th>Block</th>
          <th>Show</th>
          <th>Block Heading</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>News Block 1</th>
          <td><input type="radio" name="n_block_show_1" value="1">&nbsp;&nbsp;Yes 
            <input type="radio" name="n_block_show_1" value="5">&nbsp;&nbsp;No</td>
            <td><input type="text" name="n_block_name_1" id="news_block_name"></td>
          </tr>
          <tr>
            <th>News Block 2</th>
            <td><input type="radio" name="n_block_show_2" value="1">&nbsp;&nbsp;Yes 
              <input type="radio" name="n_block_show_2" value="0">&nbsp;&nbsp;No</td>
              <td><input type="text" name="n_block_name_2" id="news_block_name"></td>
            </tr>
            <tr>
              <th>News Block 3</th>
              <td><input type="radio" name="n_block_show_3" value="1">&nbsp;&nbsp;Yes 
                <input type="radio" name="n_block_show_3" value="0">&nbsp;&nbsp;No</td>
                <td><input type="text" name="n_block_name_3" id="news_block_name"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" data-dismiss="modal" class="btn btn-primary" id="confirm-news">Save</button>
        <button type="button" data-dismiss="modal" class="btn">Cancel</button>
      </div>
    </div>
  </div>
  <!-- confirm news block modal -->
  <script type="text/javascript">

  $("#news_block").click(function(){

    $('#news_confirm_modal').show();

  });
</script>