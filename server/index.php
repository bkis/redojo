<?php

	//TEMP DEV
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	// load config data
	$jobs_dir = "jobs";
	$jobs_list = "redolito.jobs";

	// check if jobs directory exists
	if(!is_dir($jobs_dir) && !mkdir($jobs_dir)){
		echo "ERROR: Could not create missing jobs directory: " . $jobs_dir;
		die();
	}
	
	// load all existing jobs
	$job_files = array_diff(scandir($jobs_dir), array($jobs_list, '..', '.'));
	$jobs = array();
	foreach ($job_files as $job_file){
		$jobs[$job_file] = parse_ini_file($jobs_dir . "/" . $job_file);
	}

	// save new job
	if (isset($_POST["action"])
			&& strcmp($_POST["action"], "save") === 0
			&& isset($_POST["title"])
			&& isset($_POST["url"])){
		$url = htmlspecialchars_decode($_POST["url"]);
		$title = preg_replace("/[^A-Za-z0-9.\-_() ]/", "", $_POST["title"]) . "." . pathinfo($url, PATHINFO_EXTENSION);
		$filename = hash('md2', $url);
		//add to jobs
		$jobs[$filename]["dl_name"] = $title;
		$jobs[$filename]["dl_url"] = $url;
		//write job file
		file_put_contents($jobs_dir . "/" . $filename, ("dl_name=\"" . $title . "\"\ndl_url=\"" . $url . "\""));
	}

	// delete job
	if (isset($_POST["action"])
			&& strcmp($_POST["action"], "delete") === 0
			&& isset($_POST["job"])){
		//delete job file
		unlink($jobs_dir . "/" . $_POST["job"]);
		//remove from jobs list
		unset($jobs[$_POST["job"]]);
	}

	// update jobs list file
	$toWrite = "";
	foreach ($jobs as $id => $job) {
		$toWrite = $toWrite . $id . "\n";
	}
	file_put_contents($jobs_dir . "/" . $jobs_list, $toWrite);

?>




<!DOCTYPE html>
<html>

	<head>
		<title>redolito</title>
		<meta charset="utf-8">

		<style type="text/css">
			body {
				font-family: sans-serif;
			}
			#container {
				margin: 0 auto;
				width: 512px;
				max-width: 100%;
			}
			form select, form input[type="text"] {
				width: 100%;
			}
			input[type="submit"] {
				margin: 10px;
			}
		</style>
	</head>

	<body>

		<div id="container">

			<h1>redolito</h1>

			<form action="index.php" method="post">
				<select name="job" size="10">
					<?php 
					 foreach ($jobs as $id => $job) {
					 	echo "<option value=\"" . $id . "\">" . $job['dl_name'] . " [" . $job['dl_url'] . "]</option>";
					 }
					?>
				</select><br>
				<input type="submit" name="action" value="delete"/>
			</form>

			<br><br>

			<form action="index.php" method="post">
				<input type="text" name="title" placeholder="Title [A-Za-z0-9.\-_() ]{,20}" pattern="[A-Za-z0-9.\-_() ]{,20}"/><br>
				<input type="text" name="url" placeholder="URL"/><br>
				<input type="submit" name="action" value="save">
			</form>

		</div>
	</body>

</html>