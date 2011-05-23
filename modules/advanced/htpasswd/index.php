<?php
/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
include('conf/zcnf.php');
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
include('inc/zAccountDetails.php');

$sql = "SELECT * FROM z_mysql WHERE my_acc_fk=" . $useraccount['ac_id_pk'] . " AND my_deleted_ts IS NULL";
$listmysql = DataExchange("r", $z_db_name, $sql);
$rowmysql = mysql_fetch_assoc($listmysql);
$path = GetSystemOption('hosted_dir') . $_SESSION['zUsername'] . "/";
$userreturnpath = trim(substr($_POST['returnpath'], strlen(GetSystemOption('hosted_dir')), strlen($_POST['returnpath'])));

if (isset($_POST['deletehtaccess'])) {
    deletehtaccess($useraccount['ac_id_pk'], $_POST['deletehtaccess'], $_POST['upatereturnpath']);
}
if (isset($_POST['deleteuser'])) {
    deleteuser($_POST['ht_id_pk'], $_POST['deleteuser']);
}
if (isset($_POST['addhtaccess'])) {
    addhtaccess($useraccount['ac_id_pk'], $_POST['ht_user_vc'], $_POST['ht_dir_vc'], $_POST['htusername'], $_POST['htpassword1'], $_POST['htpassword2'], $_POST['AuthName']);
}
if (isset($_POST['adduser'])) {
    adduser($useraccount['ac_id_pk'], $_POST['ht_user_vc'], $_POST['ht_dir_vc'], $_POST['htusername'], $_POST['htpassword1'], $_POST['htpassword2']);
}

//HEADER AND SELECTED FOLDER DIV
echo $lang['314'] . "<br><br>";
if (isset($_POST['returnpath'])) {
    if ($_POST['returnpath'] != "") {
        echo '<div class="zannouce" style="margin-right:10px;"><b>' . $lang['317'] . ': </b>' . $userreturnpath . '</div><br>';
    } else {
        echo '<div class="zannouce" style="margin-right:10px;">' . $lang['343'] . '</div><br>';
    }
}
?>
<FORM id="getpath" action="<?php echo GetFullURL(); ?>" method="POST" name="getpath">
    <table class="none" id="none"><tr><td>
                <INPUT type="hidden" id="returnpath" name="returnpath"> 
                <input type="submit" value="<?php echo $lang['338']; ?>" onclick="appendText()" />
            </td><td><div id="filetreeinner" style="font-weight:bold"></div></td></tr></table>
</form>
<br>
<table style="padding-right:10px;" align="left" width="100%" cellpadding="0" cellspacing="0">
    <tr valign="top">
        <td align="left" height="100%">
            <table height="100%" class="statsdata"><tr valign="top"><td>
                        <div id="htfileTree" class="fileTree"></div>
                    </td></tr></table>
        </td>
        <td style="padding-left:10px;" width="100%" height="500px">

            <?php
//UPDATE
            if (isset($_POST['upatehtaccess'])) {
                $result = mysql_query("SELECT * FROM `z_htaccess` WHERE `ht_id_pk`='" . $_POST['upatehtaccess'] . "'");
                $row = mysql_fetch_assoc($result);
                if (!empty($row['ht_dir_vc'])) {
                    echo'<h2>' . $lang['315'] . '</h2>';
                    if (file_exists(GetSystemOption('zpanel_root') . "modules/advanced/htpasswd/files/" . $_POST['upatehtaccess'] . ".htpasswd")) {
                        echo'
			<table class="zgrid" width="100%" cellpadding="5"><tr><th>' . $lang['318'] . '</th><th>' . $lang['319'] . '</th><th></th></tr>';

                        $file = GetSystemOption('zpanel_root') . "modules/advanced/htpasswd/files/" . $_POST['upatehtaccess'] . ".htpasswd";
                        $lines = file($file);
                        foreach ($lines as $line_num => $line) {
                            $data = explode(":", $line);
                            echo'<tr><td width="10%">' . $data[0] . '</td><td>' . $data[1] . '</td><td align="right"><input type="submit" name="inSubmit" id="inSubmit" value="' . $lang['320'] . '" onClick="document.deleteuser.deleteuser.value=\'' . $data[0] . '\'; document.deleteuser.submit();"/>
				</td></tr>';
                        }
                    } else {
                        echo'<table class="zannouce" width="100%" cellpadding="5"><tr><td>' . $lang['316'] . '</td></tr>';
                    }

                    echo'</table>';
                    if (file_exists(GetSystemOption('zpanel_root') . "modules/advanced/htpasswd/files/" . $_POST['upatehtaccess'] . ".htpasswd")) {
                        echo'
			 <FORM id="adduser" action="' . GetFullURL() . '" method="POST" name="adduser">
			 <INPUT type="hidden" name="adduser">
			 <INPUT type="hidden" name="ht_acc_fk" value="' . $useraccount['ac_id_pk'] . '">
			 <INPUT type="hidden" name="ht_dir_vc" value="' . $_POST['returnpath'] . '">
			 <INPUT type="hidden" name="ht_user_vc" value="' . $_SESSION['zUsername'] . '">
			 <br><h2>' . $lang['342'] . '</h2>
			 <table class="zgrid">
			 <tr><th>' . $lang['109'] . ':</th><td><input type="text" name="htusername"></td></tr>
			 <tr><th>' . $lang['116'] . ':</th><td><input type="password" name="htpassword1"></td></tr>
			 <tr><th>' . $lang['339'] . ':</th><td><input type="password" name="htpassword2"></td></tr>
			 <tr><th></th><td align="right"><input type="submit" value="' . $lang['322'] . '"></td></tr>
			 </table>
			 </form>';
                    }
                }
            }

//MAIN DISPLAY 
            if (!isset($_POST['returnpath']) && !isset($_POST['upatehtaccess'])) {
                echo $lang['321'];
            }

//EDIT DIRECTORY
            if (isset($_POST['returnpath']) && !isset($_POST['upatehtaccess']) && $_POST['returnpath'] != "") {
                $result = mysql_query("SELECT * FROM `z_htaccess` WHERE `ht_user_vc`='" . $_SESSION['zUsername'] . "' AND `ht_dir_vc`='" . $_POST['returnpath'] . "'");
                $row = mysql_fetch_assoc($result);
                if (!empty($row['ht_dir_vc'])) {
                    echo'<h2>' . $lang['323'] . '</h2>
			 <table class="zgrid" width="100%" cellpadding="5"><tr><th colspan="3">' . $lang['324'] . '</th></tr>
			 <tr><td width="100%">' . trim(substr($row['ht_dir_vc'], strlen(GetSystemOption('hosted_dir')), strlen($row['ht_dir_vc']))) . '</td><td align="right">
			 <input type="submit" name="inSubmit" id="inSubmit" value="' . $lang['325'] . '" onClick="document.upatehtaccess.upatehtaccess.value=\'' . $row['ht_id_pk'] . '\'; document.upatehtaccess.submit();"/>
			 </td><td align="right"><input type="submit" name="inSubmit" id="inSubmit" value="' . $lang['84'] . '" onClick="document.deletehtaccess.deletehtaccess.value=\'' . $row['ht_id_pk'] . '\'; document.deletehtaccess.submit();"/>
			 </td></tr>
			 </table>';
                } else {
//ADD USERS
                    echo'
			 <FORM id="addhtaccess" action="' . GetFullURL() . '" method="POST" name="addhtaccess">
			 <INPUT type="hidden" name="addhtaccess">
			 <INPUT type="hidden" name="ht_acc_fk">
			 <INPUT type="hidden" name="ht_dir_vc" value="' . $_POST['returnpath'] . '">
			 <INPUT type="hidden" name="ht_user_vc" value="' . $_SESSION['zUsername'] . '">
			 <h2>' . $lang['341'] . '</h2>
			 <table class="zgrid">
			 <tr><th>' . $lang['340'] . ':</th><td><input type="text" name="AuthName" value="' . $lang['326'] . '"></td></tr>
			 <tr><th>' . $lang['109'] . ':</th><td><input type="text" name="htusername"></td></tr>
			 <tr><th>' . $lang['116'] . ':</th><td><input type="password" name="htpassword1"></td></tr>
			 <tr><th>' . $lang['339'] . ':</th><td><input type="password" name="htpassword2"></td></tr>
			 <tr><th></th><td align="right"><input type="submit" value="' . $lang['327'] . '"></td></tr>
			 </table>
			 </form>';
                }
            }

//GET ALL .HTACCESS DIRECTORIES
            getallhtaccess($_SESSION['zUsername']);
            ?>
        </td>
    </tr>
</table>

<FORM id="deletehtaccess" action="<?php echo GetFullURL(); ?>" method="POST" name="deletehtaccess">
    <INPUT type="hidden" name="upatereturnpath" value="<?php echo $row['ht_dir_vc']; ?>">
    <INPUT type="hidden" name="deletehtaccess">
</form>

<FORM id="upatehtaccess" action="<?php echo GetFullURL(); ?>" method="POST" name="upatehtaccess">
    <INPUT type="hidden" name="upatereturnpath" value="<?php echo $row['ht_dir_vc']; ?>">
    <INPUT type="hidden" name="returnpath" value="<?php echo $_POST['returnpath']; ?>">
    <INPUT type="hidden" name="upatehtaccess">
</form>

<FORM id="deleteuser" action="<?php echo GetFullURL(); ?>" method="POST" name="deleteuser">
    <INPUT type="hidden" name="ht_id_pk" value="<?php echo $_POST['upatehtaccess']; ?>">
    <INPUT type="hidden" name="deleteuser">
</form>

<?php

//****************SCRIPT FUNCTIONS****************//
//GET ALL DIRECTORIES WITH .HTACCESS
function getallhtaccess($zUsername) {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    $result = mysql_query("SELECT * FROM `z_htaccess` WHERE `ht_user_vc`='" . $zUsername . "'");
    $count = mysql_num_rows($result);
    if ($count > 0) {
        echo'<br><h2>' . $lang['328'] . '</h2>
		<table class="zgrid" width="100%" cellpadding="5"><tr><th colspan="2">' . $lang['329'] . '</th></tr>';
        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr><td width=\"100%\">" . trim(substr($row['ht_dir_vc'], strlen(GetSystemOption('hosted_dir')), strlen($row['ht_dir_vc']))) . "</td><td align=\"right\">
			<input type=\"submit\" name=\"inSubmit\" id=\"inSubmit\" value=\"" . $lang['330'] . "\" onClick=\"document.getpath.returnpath.value='" . $row['ht_dir_vc'] . "'; document.getpath.submit();\"/>
			</td></tr>";
        }
        echo'</table>';
    }
}

//ADD .HTACCESS
function addhtaccess($ht_acc_fk, $ht_user_vc, $ht_dir_vc, $htusername, $htpassword1, $htpassword2, $AuthName) {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    if (!empty($htpassword1) && !empty($htpassword2) && ($htpassword1 == $htpassword2)) {
        $result = mysql_query("INSERT INTO `z_htaccess` (`ht_acc_fk`, `ht_user_vc`, `ht_dir_vc`) VALUES ('$ht_acc_fk', '$ht_user_vc', '$ht_dir_vc')");
        $result = mysql_query("SELECT * FROM `z_htaccess` WHERE `ht_acc_fk` ='" . $ht_acc_fk . "' ORDER BY `ht_id_pk` DESC LIMIT 1");
        $row = mysql_fetch_assoc($result);
        system(GetSystemOption('htpasswd_exe') . " -b -m -c " . GetSystemOption('zpanel_root') . "/modules/advanced/htpasswd/files/" . $row['ht_id_pk'] . ".htpasswd " . $htusername . " " . $htpassword1 . "");
        $htaccessfile = $ht_dir_vc . "/.htaccess";
        $fh = fopen($htaccessfile, 'w') or die('<div class="zannouce">' . $lang['331'] . '</div><br>');
        $stringData = "AuthUserFile " . GetSystemOption('zpanel_root') . "modules/advanced/htpasswd/files/" . $row['ht_id_pk'] . ".htpasswd\r\nAuthType Basic\r\nAuthName \"" . $AuthName . "\"\r\nRequire valid-user";
        fwrite($fh, $stringData);
        fclose($fh);
        echo '<div class="zannouce">' . $lang['332'] . trim(substr($ht_dir_vc, strlen(GetSystemOption('hosted_dir')), strlen($ht_dir_vc))) . '</div><br>';
    } else {
        echo '<div class="zannouce">' . $lang['333'] . '</div><br>';
    }
}

//DELETE .HTACCESS
function deletehtaccess($useraccount, $ht_id_pk, $upatereturnpath) {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    $result = mysql_query("DELETE FROM `z_htaccess` WHERE `ht_acc_fk`='" . $useraccount . "' AND `ht_id_pk`='" . $ht_id_pk . "'");
    if (file_exists(GetSystemOption('zpanel_root') . "modules/advanced/htpasswd/files/" . $ht_id_pk . ".htpasswd")) {
        unlink(GetSystemOption('zpanel_root') . "modules/advanced/htpasswd/files/" . $ht_id_pk . ".htpasswd");
    }
    if (file_exists($upatereturnpath . "/.htaccess")) {
        unlink($upatereturnpath . "/.htaccess");
    }
    echo '<div class="zannouce">' . $lang['334'] . trim(substr($upatereturnpath, strlen(GetSystemOption('hosted_dir')), strlen($upatereturnpath))) . '</div><br>';
}

//DELETE USER
function deleteuser($ht_id_pk, $user) {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    system(GetSystemOption('htpasswd_exe') . " -D " . GetSystemOption('zpanel_root') . "/modules/advanced/htpasswd/files/" . $ht_id_pk . ".htpasswd " . $user . "");
    echo '<div class="zannouce">' . $lang['337'] . '</div><br>';
}

//ADD USER
function adduser($ht_acc_fk, $ht_user_vc, $ht_dir_vc, $htusername, $htpassword1, $htpassword2) {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    if (!empty($htpassword1) && !empty($htpassword2) && ($htpassword1 == $htpassword2)) {
        $result = mysql_query("SELECT * FROM `z_htaccess` WHERE `ht_acc_fk` ='" . $ht_acc_fk . "' AND `ht_dir_vc` = '" . $ht_dir_vc . "'");
        $row = mysql_fetch_assoc($result);
        system(GetSystemOption('htpasswd_exe') . " -b -m " . GetSystemOption('zpanel_root') . "/modules/advanced/htpasswd/files/" . $row['ht_id_pk'] . ".htpasswd " . $htusername . " " . $htpassword1 . "");
        echo '<div class="zannouce">' . $lang['335'] . trim(substr($ht_dir_vc, strlen(GetSystemOption('hosted_dir')), strlen($ht_dir_vc))) . '</div><br>';
    } else {
        echo '<div class="zannouce">' . $lang['336'] . '</div><br>';
    }
}
?>

<link rel="stylesheet" type="text/css" href="modules/advanced/htpasswd/js/jqueryFileTree.css" media="screen">
<!--This function SHOULD check if jquery is loaded, and if not then load as needed-->
<script type="text/javascript">
    if (typeof jQuery == 'undefined') { 
        var head = document.getElementsByTagName("head")[0];
        script = document.createElement('script');
        script.id = 'jQuery';
        script.type = 'text/javascript';
        script.src = 'modules/advanced/htpasswd/js/jquery.js';
        head.appendChild(script);
    }
</script>
<?php /* <script type="text/javascript" src="modules/advanced/htpasswd/js/jquery.min.js"></SCRIPT> */ ?>
<script type="text/javascript" src="modules/advanced/htpasswd/js/jquery.easing.js"></SCRIPT>
<script type="text/javascript" src="modules/advanced/htpasswd/js/jqueryFileTree.js"></SCRIPT>
<?php /* <script type="text/javascript" src="templates/zpanel6/inc/jqf1.english.js"></script> */ ?>
<script type="text/javascript">
    $(document).ready( function() {	
        $('#htfileTree').fileTree({ root: '<?php echo $path; ?>', script: 'modules/advanced/htpasswd/js/connectors/jqueryFileTree.php' });				
    });
</script>
<script type="text/javascript" language="javascript">
    function appendText($folderlink, fieldID) {
        w=document.getElementById('filetreeinner');
        x=document.getElementById('returnpath');
        y=document.getElementById(fieldID);
        x.value=y.name;
        w.innerHTML=$folderlink;
    }
</script>
<style type="text/css">
    .fileTree {
        padding:0;
        margin:0;
        width: 100%;
        min-width:175px;
        max-width:250px;
        height: 500px;
        overflow-y: none;
        overflow-x: auto;
    }
</style>