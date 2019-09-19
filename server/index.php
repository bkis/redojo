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
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>

		<div id="container">

			<h1>redolito</h1>

            <div class="form-container">
                <div class="section-heading">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAA4ElEQVRIx91VOwoCMRScfAoLEewE8QLewPt5CbHaygtYWNh7A4/gBUSwyJtssytLSDDsZll0IEVCMvNmHkmAseGcm5M8knySfJGsRGRRTIBkRdIH41SKX5EUADrU1VqbEgI6Qo7EWm8HvhRZysGosEllrdUQ4jaZ6RyEleQidP5VIDxE0qfiixUzekTT9yA3iqICqZ78Zw+6AheSGxFZAtjnxBObh/g8diTX1tpHuKHvRWvPWQBvADMAzKm4T0TXhujgnFsNIYtGJCJbpdQNQLl/uOvAGHP33u8AnJu4fgs1eRF44L1j0OoAAAAASUVORK5CYII=" alt=""/>
                    New download job
                </div>
                <form action="index.php" method="post">
                    <label>Title (will be used as file name)</label>
                    <input
                        type="text"
                        name="title"
                        placeholder="Give this file a descriptive name!"
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
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABNklEQVRIx+3SIUgEQRTG8WM5jgsXLhjFIAaD4RCD4YJBxGAwGAwGg8FgMBkMNqPRZDSZROQwGExGo8EgBjGIiCyG5Xbe+69lB5ZzZ27PXdt98NrM75thplYbJxNgCeipaus/8C7wDSTAXaUlwCIQpnhSaQmwAHwN4HZuyuId4NOBR8ByGXwO+HDgfWD11yZVnTfGNIbhqjoLvHvwtbwTraTXuhKRpgefAd4cuAHWfbhdeCsirRx8Gnj14Bt5JxrE7dyrajuzbgp4ceAKbLoe69qxKQEeVHVCVSeBZw++5XwwEWkCPU/JI/DkwbeHfrm05NJT4pqdwv9aRBrAxQj4bmE8U1IHzgvgeyPjNsaYOnDmwff/jNvEcRwApzn4QWncJoqiADjJ4IeV4TZhGAbAMXBUOT6OzQ+8k9qof8W2WgAAAABJRU5ErkJggg==" alt="save"/>
                        </button>
                    </div>
                </form>
            </div>

            <div class="form-container">
                    <div class="section-heading">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABp0lEQVRIx9WSsUtbURTGjzcSQhAHKSWIg5N0U5ykg0PHdBcJ/g1SxMl/wKE4ODk4OImDiJRCIZSSIXTs4BSCEAUppYqI7ZR3z/k9lxsIiXkvMQTJgcvjvffd7zvfOZ/IuNdEv0Ag7vzmnEu9717b4dCVaLHXWAYZlwN+AzFQHZkN730WuDGzlZGJAFvAGfAhOBrmnHYJqOo0cG9mC8CvIcj/mNmbFu9Eh4tdEZkRkYqInLxwGB+dc9+e/WNmBeDBzGaBqxd0f9jJ2RWtALoVkb8isj9A59dxHC9mMpl/iSgzewfcqWqhLcJpx4DVQRJ1Dmya2TywC/wEmgkCnwfakpm9B/63CS1FUZQHVoEdoAw8BvLvqpoD1oFacFMDSmkuYqAEHAJ14B74Anwys+Vms5lV1beNRsMF8ueclRIF2t9VdQ64CN3VgAfgK7ANXAbCClAMzxio9yUQRdEkcARUzWw6CBaANeCgreNiuFtsLT9VwHs/CRwDFVWd6oGtdTj40Y8D894XgFOgrKr5BGypxw42kgT2AA+cqGouLXlBpB5SVE8kH6t6AuEu1/s8ZvxZAAAAAElFTkSuQmCC" alt=""/>
                        Existing download jobs
                    </div>
                <form action="index.php" method="post">
                    <select name="job" size="5">
						<?php 
							foreach ($jobs as $id => $job) {
								echo "<option value=\"" . $id . "\">" . $job['dl_name'] . " [" . $job['dl_url'] . "]</option>";
							}
						?>
                    </select>
                    <div class="form-right">
                        <button type="submit" name="action" value="delete" title="Delete the selected download job!">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAkklEQVRIx+2V0Q2AMAhEER1KJ3IaN3UFDn9ao6ilRk2N8ZL+QODRI2mJHAEYAAgANUcADF59lQEQIuKjNDPXqfrGA8TmzLwaBoAmwNsbhILbFAdyJ3i99ny9rOW+HreoPEBVO1XtcuNW7g6inzZ3FF/m3mHRD/gB5QHuhyMi7Zm41fde0/GGnqseFtBfhIyhx6wJbMRXxHGKRJQAAAAASUVORK5CYII=" alt="delete"/>
                        </button>
                    </div>
                </form>
			</div>
			
		</div>
	</body>

</html>