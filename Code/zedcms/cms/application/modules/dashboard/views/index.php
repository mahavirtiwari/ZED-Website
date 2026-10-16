<div class="container zed-dashboard">
	<div class="row">
		<div class="col-sm-24">
			<div class="zedd-welcome">
				<h1>Welcome, <?php echo ucfirst($this->ion_auth->user()->row()->first_name);?></h1>
				<?php
				date_default_timezone_set('Asia/Calcutta');
					$hour      = date('H');

					if ($hour >= 20) {
					    $greetings = "Good Night";
					} elseif ($hour > 17) {
					   $greetings = "Good Evening";
					} elseif ($hour > 11) {
					    $greetings = "Good Afternoon";
					} elseif ($hour < 12) {
					   $greetings = "Good Morning";
					}
				?>
				<p><?php echo $greetings;?></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="zedd-brick">
				<div class="zedd-brick-label">Total Pages</div>
				<div class="zedd-brick-content">
					<div class="text-huge"><?php echo (isset($pages) ? $pages : 0 );?></div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="zedd-brick">
				<div class="zedd-brick-label">Total News</div>
				<div class="zedd-brick-content">
					<div class="text-huge"><?php echo (isset($news) ? $news : 0 );?></div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="zedd-brick">
				<div class="zedd-brick-label">Total Stakeholders</div>
				<div class="zedd-brick-content">
					<div class="text-huge"><?php echo (isset($stakeholders) ? $stakeholders : 0 );?></div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="zedd-brick">
				<div class="zedd-brick-label">Total Contact Requests</div>
				<div class="zedd-brick-content">
					<div class="text-huge"><?php echo (isset($contacts) ? $contacts : 0 );?></div>
				</div>
			</div>
		</div>
	</div>
</div>