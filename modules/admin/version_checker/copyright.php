<?php
/***********************************************************************
* Module name:            ZPanel Version Checker                       *
* Original coding by:     (author unknown at this time)                *
* Module modified by:     T Gates                                      *
* Module website:         http://www.zpanel.co.uk                      *
* Module author email:    tgates@zpanel.co.uk                          *
* Module first released:  Nov. 13, 2010                                *
* Module version:         v2.4 (Mar. 29, 2011)                         *
* NOTICE: You may edit these files as you wish, but this notice MUST   *
* remain in ALL files associated with this package!                    *
***********************************************************************/

$module_name = "Version Checker";
$author_name = "TGates";
$author_email = "tgates@zpanel.co.uk";
$author_homepage = "http://www.zpanel.co.uk";
$license = "GNU/GPL";
$download_location = "http://www.zpanel.co.uk/modules/";
$module_version = "2.4";
$module_description = "ZPanel Version Checker for ZPanel v6";

// DO NOT TOUCH THE FOLLOWING COPYRIGHT CODE. YOU'RE JUST ALLOWED TO CHANGE YOUR "OWN"
// MODULE'S DATA (SEE ABOVE) SO THE SYSTEM CAN BE ABLE TO SHOW THE COPYRIGHT NOTICE
// FOR YOUR MODULE/ADDON. PLAY FAIR WITH THE PEOPLE THAT WORKED CODING WHAT YOU USE!!
// YOU ARE NOT ALLOWED TO MODIFY ANYTHING ELSE THAN THE ABOVE REQUIRED INFORMATION.
// AND YOU ARE NOT ALLOWED TO DELETE THIS FILE NOR TO CHANGE ANYTHING FROM THIS FILE IF
// YOU'RE NOT THIS MODULE'S AUTHOR.

function show_copyright() {
    global $author_name, $author_email, $author_homepage, $license, $download_location, $module_version, $module_description, $stylesheet;
    if ($author_name == "") { $author_name = "N/A"; }
    if ($author_email == "") { $author_email = "N/A"; }
    if ($author_homepage == "") { $author_homepage = "N/A"; }
    if ($license == "") { $license = "N/A"; }
    if ($download_location == "") { $download_location = "N/A"; }
    if ($module_version == "") { $module_version = "N/A"; }
    if ($module_description == "") { $module_description = "N/A"; }
    $module_name = basename(dirname(__FILE__));
    $module_name = eregi_replace("_", " ", $module_name);
    echo "<html><head>"
	."<style type=\"text/css\">"
	." a, a:visited, a:hover{color:#006699;text-decoration: none;}"
	."</style>"
	."</head>"
	."<body bgcolor=\"#F3F3F3\">"
	."<title>$module_name: Copyright Information</title>"
	."<font size=\"2\" color=\"#000000\" face=\"Arial, Verdana, Helvetica\">"
	."<center><b>Module Copyright &copy; Information</b><br>"
	."$module_name for <a href=\"http://www.zpanel.co.uk\" target=\"_blank\">ZPanel</a> <br><br></center>"
	."<img src=\"arrow.gif\" border=\"0\">&nbsp;<b>Module's Name:</b> $module_name<br>"
	."<img src=\"arrow.gif\" border=\"0\">&nbsp;<b>Module's Version:</b> $module_version<br>"
	."<div align=\"justify\"><img src=\"arrow.gif\" border=\"0\">&nbsp;<b>Module's Description:</b> $module_description</div><br>"
	."<img src=\"arrow.gif\" border=\"0\">&nbsp;<b>License:</b> $license<br>"
	."<img src=\"arrow.gif\" border=\"0\">&nbsp;<b>Author's Name:</b> $author_name<br>"
	."<img src=\"arrow.gif\" border=\"0\">&nbsp;<b>Author's Email:</b> <a href=\"mailto:$author_email\">$author_email</a><br><br><br>"
	."<center>[ <a href=\"$author_homepage\" target=\"_blank\">Author's HomePage</a> ] - [ <a href=\"$download_location\" target=\"_blank\">Module's Download</a> ] - [ <a href=\"javascript:void(0)\" onClick=javascript:self.close()>Close</a> ]</center>"
	."</font>"
	."</body>"
	."</html>";
}

show_copyright();

?>
