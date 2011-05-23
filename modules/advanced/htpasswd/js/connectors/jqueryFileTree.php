<?php
$_POST['dir'] = urldecode($_POST['dir']);

if( file_exists($root . $_POST['dir']) ) {
	$files = scandir($root . $_POST['dir']);
	natcasesort($files);
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		// All dirs
		foreach( $files as $file ) {
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
			$userreturnpath = trim(substr($root . $_POST['dir'] . $file, GetFiller(), strlen($root . $_POST['dir'] . $file)));
				echo "<li class=\"directory collapsed\"><a href=\"#\" 
															name=\"" . htmlentities($_POST['dir'] . $file) . "\"
															id=\"" . htmlentities($_POST['dir'] . $file) . "\"
															rel=\"" . htmlentities($_POST['dir'] . $file) . "/\"
															onClick=\"appendText('".$userreturnpath."', this.id);\"
															>" . htmlentities($file) . "</a></li>";
			}
		}
		// All files
		foreach( $files as $file ) {
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) && strstr($file, '.htaccess')) {
				$ext = preg_replace('/^.*\./', '', $file);
				echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
			}
		}
		echo "</ul>";	
	}
}

function GetFiller(){
	switch (strtoupper(substr(PHP_OS, 0, 3))) {
	case 'WIN':
		return 19;
		break;
	case 'LIN':
		return 21;
		break;
    case 'FRE':
		return 21;
		break;
    }	
}

?>
