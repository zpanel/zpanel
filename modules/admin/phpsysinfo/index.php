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

if (isset($_POST['inSubmit'])) {
    addhtaccess($_POST['inPassA'], $_POST['inPassB'], $_POST['inAuthName'], $_POST['inUserName']);
}
if (isset($_POST['inDelete'])) {
    deletehtaccess();
}

echo $lang['344'];
echo "<br><br>";
echo "&raquo; <a href=\"apps/phpsysinfo/\" target=\"_blank\" title=\"" . $lang['345'] . "\">" . $lang['345'] . "</a>";
?>


<FORM id="frmSysInfo" name="frmSysInfo" method="post" action="<?php echo GetFullURL(); ?>">
    <br><h2><?php echo $lang['346']; ?></h2>
    <table class="zgrid">
        <tr><th><?php echo $lang['340']; ?>:</th><td><input type="text" name="inAuthName" value="<?php echo $lang['326']; ?>"></td></tr>
        <tr><th><?php echo $lang['109']; ?>:</th><td><input type="text" name="inUserName"></td></tr>
        <tr><th><?php echo $lang['116']; ?>:</th><td><input type="password" name="inPassA"></td></tr>
        <tr><th><?php echo $lang['339']; ?>:</th><td><input type="password" name="inPassB"></td></tr>
        <tr><th></th><td align="right"><input type="submit" name="inSubmit" value="<?php echo $lang['101']; ?>"><input type="submit" name="inDelete" value="<?php echo $lang['84']; ?>"></td></tr>
    </table>
</form>



<?php

//ADD .HTACCESS
function addhtaccess($inPassA, $inPassB, $inAuthName, $inUserName) {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    if (!empty($inPassA) && !empty($inPassB) && ($inPassA == $inPassB)) {
        system(GetSystemOption('htpasswd_exe') . " -b -m -c " . GetSystemOption('zpanel_root') . "apps/phpsysinfo/phpsysinfo.htpasswd " . $inUserName . " " . $inPassA . "");
        $htaccessfile = GetSystemOption('zpanel_root') . "/apps/phpsysinfo/.htaccess";
        $fh = fopen($htaccessfile, 'w') or die('<div class="zannouce">' . $lang['331'] . '</div><br>');
        $stringData = "AuthUserFile " . GetSystemOption('zpanel_root') . "apps/phpsysinfo/phpsysinfo.htpasswd\r\nAuthType Basic\r\nAuthName \"" . $inAuthName . "\"\r\nRequire valid-user";
        fwrite($fh, $stringData);
        fclose($fh);
        echo '<div class="zannouce">' . $lang['347'] . '</div><br>';
    } else {
        echo '<div class="zannouce">' . $lang['333'] . '</div><br>';
    }
}

//DELETE .HTACCESS
function deletehtaccess() {
    include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
    if (file_exists(GetSystemOption('zpanel_root') . "apps/phpsysinfo/.htaccess")) {
        unlink(GetSystemOption('zpanel_root') . "apps/phpsysinfo/.htaccess");
    }
    if (file_exists(GetSystemOption('zpanel_root') . "apps/phpsysinfo/phpsysinfo.htpasswd")) {
        unlink(GetSystemOption('zpanel_root') . "apps/phpsysinfo/phpsysinfo.htpasswd");
    }
    echo '<div class="zannouce">' . $lang['348'] . '</div><br>';
}
?>
