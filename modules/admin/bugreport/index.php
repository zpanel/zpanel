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

# Set the default infomation submitted in the bug form
$returnurl = GetFullURL() . "&ok";
$zpanelurl = $_SERVER['SERVER_NAME'];
$serversoft = $_SERVER['SERVER_SOFTWARE'];
$phpversion = ShowPHPVersion();
$mysqlversion = ShowMySQLVersion();
$apacheversion = ShowApacheVersion();
$zpanelversion = GetSystemOption('zpanel_version');
echo $lang['55'];
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo $lang['60'];
}
echo "<br><br>";
?>
<form name="frmReport" id="frmReport" method="post" action="http://bugs.zpanel.co.uk/bugapi.php?secure=<?php echo base64_encode("" . $returnurl . "|||" . $zpanelurl . "|||" . $serversoft . "|||" . $apacheversion . "|||" . $phpversion . "|||" . $mysqlversion . "|||" . $zpanelversion . ""); ?>">
    <table class="zform">
        <tbody><tr>
                <th><?php echo $lang['56']; ?></th>
                <td><input name="bugEmail" id="bugEmail" value="<?php echo $personalinfo['ap_email_vc']; ?>" size="60" type="text"></td>
            </tr>
            <tr>
                <th valign="top"><?php echo $lang['57']; ?></th>
                <td><input name="bugSummary" id="bugSummary" size="60" type="text">
                </td>
            </tr>
            <tr>
                <th valign="top"><?php echo $lang['58']; ?></th>
                <td><textarea name="bugDescription" cols="50" rows="10" id="bugDescription"></textarea></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right"><input name="Submit" value="<?php echo $lang['59']; ?>" type="submit"></td>
            </tr>
        </tbody></table>
</form>

