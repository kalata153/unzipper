<?php
/**
* The Unzipper extracts .zip archives on webservers. It's handy if you
* do not have shell access. E.g. if you want to upload a lot of files (php framework or image collection) as
* archive to save time.
*
* @author Andreas Tasch, at[tec], attec.at
* @license GNU GPL v3
*/

$timestart = microtime(true);

$arc = new Unzipper;

$timeend = microtime(true);
$time = $timeend - $timestart;

class Unzipper
{
	public $localdir = '.';
	public $zipfiles = array();
	public static $status = '';

	public function __construct()
	{
		//read directory and pick .zip files
		if ($dh = opendir($this->localdir)) {
		    while (($file = readdir($dh)) !== false) {
		        if(pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
		        	$this->zipfiles[] = $file;
		        	self::$status = '.zip files found, ready for extraction';
		        }
		    }
		    closedir($dh);
		}

		//check if an archive was selected for unzipping
		//check if archive has been selected
		$input = '';
		$input = strip_tags($_POST['zipfile']);

		//allow only local existing archives to extract
		if($input !== '') {
			if(in_array($input, $this->zipfiles)) {
				self::extract($input, $this->localdir);
			}
		}
	}

	public static function extract($archive, $destination)
	{
		$zip = new ZipArchive;

		if ($zip->open($archive) === TRUE) {
		    $zip->extractTo($destination);
		    $zip->close();
		    self::$status = 'Files unzipped successfully';
		} else {
		    self::$status = 'Error unzipping files';
		}
	}
}
?>

<!DOCTYPE html>
<head>
    <title>File Unzipper</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
	<!--
		body { font-family: Arial, serif; line-height: 150%; }

		fieldset { border: 0px solid #000; }

		.select {
			padding: 5px;
			font-size: 110%;
		}

		.status {
			margin-top: 20px;
			padding: 5px;
			font-size: 80%;
			background: #EEE;
			border: 1px dotted #DDD;
		}

		.submit {
			-moz-box-shadow:inset 0px 1px 0px 0px #bbdaf7;
			-webkit-box-shadow:inset 0px 1px 0px 0px #bbdaf7;
			box-shadow:inset 0px 1px 0px 0px #bbdaf7;
			background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #79bbff), color-stop(1, #378de5) );
			background:-moz-linear-gradient( center top, #79bbff 5%, #378de5 100% );
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#79bbff', endColorstr='#378de5');
			background-color:#79bbff;
			-moz-border-radius:4px;
			-webkit-border-radius:4px;
			border-radius:4px;
			border:1px solid #84bbf3;
			display:inline-block;
			color:#ffffff;
			font-family:arial;
			font-size:15px;
			font-weight:bold;
			padding:10px 24px;
			text-decoration:none;
			text-shadow:1px 1px 0px #528ecc;
		}.submit:hover {
			background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #378de5), color-stop(1, #79bbff) );
			background:-moz-linear-gradient( center top, #378de5 5%, #79bbff 100% );
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#378de5', endColorstr='#79bbff');
			background-color:#378de5;
		}.submit:active {
			position:relative;
			top:1px;
		}
		/* This imageless css button was generated by CSSButtonGenerator.com */


	-->
	</style>
</head>

<body>
	<h1>Archive Unzipper</h1>
	<p>Select .zip archive you want to extract:</p>

	<form action="unzip.php" method="POST">
		<fieldset>

			<select name="zipfile" size="1" class="select">
				<?php foreach($arc->zipfiles as $zip) {
					echo "<option>$zip</option>";
				}
				?>
			</select>

			<br />

			<input type="submit" name="submit" class="submit" value="Unzip Archive" />

		</fieldset>
	</form>
	<p class="status">
		Status: <?php echo $arc::$status; ?>
		<br />
		Processingtime: <?php echo $time; ?>
	</p>
</body>
</html>