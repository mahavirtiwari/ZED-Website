<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ZED CMS ADMIN</title>
  <link href="<?php echo URL_ASSETS_IMG; ?>/zed-fav.png" rel="shortcut icon" />

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

  <link href="<?php echo URL_ASSETS_CSS; ?>/animate.css" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_CSS; ?>/bootstrap.css" rel="stylesheet">
  <link href="<?php echo URL_ASSETS_CSS; ?>/zed-login.css" rel="stylesheet">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- The Rockstar jQuery -->
  <script src="<?php echo URL_ASSETS_JS; ?>/jquery-3.7.1.min.js"></script>
  <script src="<?php echo URL_ASSETS_JS; ?>/jquery-migrate-3.5.2.min.js"></script>
</head>
<body>
  <div class="zed-login-container">
    <div class="zed-login-box">
      <div class="zed-login-content">
        <div class="zed-login-left">
          <img src="<?php echo URL_FOLDER;?>assets/images/zed-logo.png"> <!-- class="zed-login-logo" -->
          <div class="zed-login-quote">Zero Defect. Zero Effect.</div>
        </div>
        <div class="zed-login-right">
          <?php if(isset($message)){ ?>
          <div id="infoMessage" class="alert alert-danger"><?php echo $message;?></div>
          <?php } ?>
          <?php echo form_open("auth/login", 'class="login-form"');?>
          <div class="form-group">
            <?php echo form_input($identity);?>
          </div>
          <div class="form-group">
            <?php echo form_input($password);?>
          </div>
          <?php echo form_submit('submit', lang('login_submit_btn'),'class="btn btn-primary btn-block"');?>
          <?php echo form_close();?>
        </div>
      </div>
    </div>
  </div>
  





  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="<?php echo URL_ASSETS_JS; ?>/Chart.min.js"></script>
  <script src="<?php echo URL_ASSETS_PLUGINS; ?>/datatables-1.13.11/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo URL_ASSETS_PLUGINS; ?>/datatables-1.13.11/js/dataTables.bootstrap.min.js"></script>
  <script src="<?php echo URL_ASSETS_PLUGINS; ?>/select2/select2.full.min.js"></script>
  <script src="<?php echo URL_ASSETS_JS; ?>/rtd-toasty.js"></script>
  <script src="<?php echo URL_ASSETS_JS; ?>/rtd-asky.js"></script>
  <script src="<?php echo URL_ASSETS_JS; ?>/bootstrap.min.js"></script>
  <script src="<?php echo URL_ASSETS_JS; ?>/zed-app.js"></script>
</body>
</html>

