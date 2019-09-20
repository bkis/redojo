<?php

	////////////////////
	// redojo       //
	// server script  //
	////////////////////

	// FOR DEVELOPMENT: UN-COMMENT TO PRINT ERRORS AND WARNINGS
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	
	// load config data
	$jobs_dir = 'jobs';
	$jobs_list = 'redojo.jobs';

	// check if jobs directory exists
	if(!is_dir($jobs_dir) && !mkdir($jobs_dir)){
		echo 'ERROR: Could not create missing jobs directory: ' . $jobs_dir;
		die();
	}
	
	// load all existing jobs
	$job_files = array_diff(scandir($jobs_dir), array($jobs_list, '..', '.'));
	$jobs = array();
	foreach ($job_files as $job_file){
		$jobs[$job_file] = parse_ini_file($jobs_dir . '/' . $job_file);
	}

	// save new job
	if (isset($_POST['action'])
			&& strcmp($_POST['action'], 'save') === 0
			&& isset($_POST['title'])
			&& isset($_POST['url'])){
		$url = htmlspecialchars_decode($_POST['url']);
		$title = preg_replace('/[^A-Za-z0-9.\-_() ]/', '', $_POST['title']) . '.' . pathinfo($url, PATHINFO_EXTENSION);
		$filename = hash('md2', $url);
		//write job file
		$status = file_put_contents(
			$jobs_dir . '/' . $filename,
			('dl_name="' . $title . '"' . "\n" . 'dl_url="' . $url . '"')
		) !== false;
		//add to jobs
		if ($status){
			$jobs[$filename]['dl_name'] = $title;
			$jobs[$filename]['dl_url'] = $url;
		}
	}

	// delete job
	if (isset($_POST['action'])
			&& strcmp($_POST['action'], 'delete') === 0
			&& isset($_POST['job'])){
		$status = false;
		//delete job file
		$status = unlink($jobs_dir . '/' . $_POST['job']);
		//remove from jobs list
		unset($jobs[$_POST['job']]);
	}

	// update jobs list file
	$toWrite = '';
	foreach ($jobs as $id => $job) {
		$toWrite = $toWrite . $id . "\n";
	}
	file_put_contents($jobs_dir . '/' . $jobs_list, $toWrite);

?>


<!DOCTYPE html>
<html>

	<head>
		<title>redojo</title>
		<meta charset="utf-8">
		<meta name="robots" content="noindex,nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>

		<div id="container">

			<!-- OPERATION STATUS MESSAGE -->
			<?php if (isset($status)){ ?>
			<div id="status" class="<?php echo $status ? 'status-successful' : 'status-error' ?>">
				<?php
					echo $status ? (json_decode('"\u263A"') . " Operation successful") : (json_decode('"\u2639"') . "Error");
				?>
			</div>
			<?php } ?>

			<h1>
				<svg class="svg-icon" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M17.012 18.708l-4.318-1.07-3.981 3.871c-.326.317-.755.491-1.209.491-.85 0-1.504-.691-1.504-1.502v-4.519l-4.13-8.979h-1.37c-.311 0-.5-.26-.5-.5 0-.239.189-.5.5-.5h2.025l4.194 9.132 10.38 2.569c.363-1.544 1.75-2.695 3.404-2.695 1.93 0 3.497 1.567 3.497 3.497s-1.567 3.497-3.497 3.497c-1.861 0-3.385-1.457-3.491-3.292zm-10.012-2.481v4.271c0 .48.612.688 1.017.294l3.534-3.437-4.551-1.128zm13.503 1.026c.69 0 1.25.56 1.25 1.25s-.56 1.25-1.25 1.25-1.25-.56-1.25-1.25.56-1.25 1.25-1.25zm1.497-9.25l-17.483-.003 2.454 5.367c.256.561.756.972 1.356 1.114l6.593 1.571c.805.192 1.644-.132 2.112-.814l4.968-7.235zm-10-1.003h-5v-2h5v2zm6 0h-5v-2h5v2zm-3-3h-5v-2h5v2z"/></svg>
				redojo
			</h1>

			<!-- SECTION: NEW DOWNLOAD JOB -->
            <div class="form-container">
                <div class="section-heading">
					<svg class="svg-icon svg-icon-light" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M4.999 19v2h-4.999v-2h4.999zm2 2l5.827-1.319-4.403-4.4-1.424 5.719zm17.001-12.466l-9.689 9.804-4.537-4.536 9.69-9.802 4.536 4.534zm-4.48-1.65l-6.916 6.917.707.707 6.916-6.917-.707-.707z"/></svg>
                    New download job
                </div>
                <form action="index.php" method="post">
                    <label>Title</label>
                    <input
                        type="text"
                        name="title"
                        placeholder="This will be used as file name (extension will be set automatically)"
                        title="All characters but A-Z, a-z, 0-9, .-_() will be stripped! 64 characters max!"
                        maxlength="64" />
                    <br>
                    <label>Download URL</label>
                    <input
                        type="text"
                        name="url"
                        placeholder="URL"
                        title="Fill in the URL to the file that should be downloaded!"
                        maxlength="512" />
                    <div class="form-right">
                        <button type="submit" name="action" value="save" title="Save this download job to the list!">
							save
                        </button>
                    </div>
                </form>
            </div>

			<!-- SECTION: EXISTING DOWNLOAD JOBS -->
            <div class="form-container">
                    <div class="section-heading">
						<svg class="svg-icon svg-icon-light" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M13.403 24h-13.403v-22h3c1.231 0 2.181-1.084 3-2h8c.821.916 1.772 2 3 2h3v9.15c-.485-.098-.987-.15-1.5-.15l-.5.016v-7.016h-4l-2 2h-3.897l-2.103-2h-4v18h9.866c.397.751.919 1.427 1.537 2zm5.097-11c3.035 0 5.5 2.464 5.5 5.5s-2.465 5.5-5.5 5.5c-3.036 0-5.5-2.464-5.5-5.5s2.464-5.5 5.5-5.5zm0 2c1.931 0 3.5 1.568 3.5 3.5s-1.569 3.5-3.5 3.5c-1.932 0-3.5-1.568-3.5-3.5s1.568-3.5 3.5-3.5zm2.5 4h-3v-3h1v2h2v1zm-15.151-4.052l-1.049-.984-.8.823 1.864 1.776 3.136-3.192-.815-.808-2.336 2.385zm6.151 1.052h-2v-1h2v1zm2-2h-4v-1h4v1zm-8.151-4.025l-1.049-.983-.8.823 1.864 1.776 3.136-3.192-.815-.808-2.336 2.384zm8.151 1.025h-4v-1h4v1zm0-2h-4v-1h4v1zm-5-6c0 .552.449 1 1 1 .553 0 1-.448 1-1s-.447-1-1-1c-.551 0-1 .448-1 1z"/></svg>
                        Existing download jobs (<?php echo count($jobs) ?>)
                    </div>
                <form action="index.php" method="post">
                    <select name="job" size="5" required>
						<?php 
							foreach ($jobs as $id => $job) {
								echo '<option value="' . $id . '" title="' . $job['dl_url'] . '">' . $job['dl_name'] . ' (' . $job['dl_url'] . ')</option>';
							}
						?>
                    </select>
                    <div class="form-right">
                        <button type="submit" name="action" value="delete" title="Delete the selected download job!">
							delete
                        </button>
                    </div>
                </form>
			</div>
			
		</div>
	</body>

</html>