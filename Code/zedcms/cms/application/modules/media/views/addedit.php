<div class="container form-page">
	<div class="row">
		<div class="col-sm-18">
			<div class="zed-tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="javascript:void(0);" data-toggle="tab" data-target="#tab1">Basic</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active fade in" id="tab1">
						<form id="fileupload" action="<?php echo base_url();?>media/do_upload" method="POST" enctype="multipart/form-data">
							<div class="row fileupload-buttonbar">
							<div class="col-lg-24">
									<!-- The fileinput-button span is used to style the file input field as button -->
									<span class="btn btn-success fileinput-button">
										<i class="glyphicon glyphicon-plus"></i>
										<span>Add Images...</span>
										<input type="file" name="userfile" multiple >
									</span>
									<!-- The global file processing state -->
									<span class="fileupload-process"></span>
								</div>
								<!-- The global progress state -->
								<div class="col-lg-5 fileupload-progress fade">
									<!-- The global progress bar -->
									<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
										<div class="progress-bar progress-bar-success" style="width:0%;"></div>
									</div>
									<!-- The extended global progress state -->
									<div class="progress-extended">&nbsp;</div>
								</div>
							</div>
							<!-- The table listing the files available for upload/download -->
							<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
						</form>
						<br>

					</div>
					<!-- The template to display files available for upload -->
					<script id="template-upload" type="text/x-tmpl">
						{% for (var i=0, file; file=o.files[i]; i++) { %}
						<tr class="template-upload fade">
							<td>
								<span class="preview"></span>
							</td>
							<td>
								<p class="name">{%=file.name%}</p>
								<strong class="error text-danger"></strong>
							</td>
							<td>
								<p class="size">Processing...</p>
								<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
							</td>
							<td>
								{% if (!i && !o.options.autoUpload) { %}
								<button class="btn btn-primary start" disabled>
									<i class="glyphicon glyphicon-upload"></i>
									<span>Start</span>
								</button>
								{% } %}
								
							</td>
						</tr>
						{% } %}
					</script>
					<!-- The template to display files available for download -->
					<script id="template-download" type="text/x-tmpl">
						{% for (var i=0, file; file=o.files[i]; i++) { %}
						<tr class="template-download fade">
							<td>
								<span class="preview">
									{% if (file.thumbnailUrl) { %}
									<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
									{% } %}
								</span>
							</td>
							<td>
								<p class="name">
									{% if (file.url) { %}
									<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
									{% } else { %}
									<span>{%=file.name%}</span>
									{% } %}
								</p>
								{% if (file.error) { %}
								<!-- <div><span class="label label-danger">Error</span> {%=file.error%}</div> -->
								{% } %}
							</td>
							<td>
								<span class="size">{%=o.formatFileSize(file.size)%}</span>
							</td>
							
						</tr>
						{% } %}
					</script>

				</div>
			</div>
		</div>
	</div>
</div>
</div>
<script>
//this is the application.js file from the example code//

$(function () {
  'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload();

});

</script> 