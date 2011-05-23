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
include('inc/zAccountDetails.php');
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
echo $lang['2'];
echo "<br><br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><br><div class=\"zannouce\">" . $lang['61'] . "</div>";
    echo "<br><br>";
}
echo "<form id=\"frmPersonalDetails\" name=\"frmPersonalDetails\" method=\"post\" action=\"runner.php?load=obj_personal\">
  <table class=\"zform\">
    <tr>
      <th>" . $lang['13'] . "</th>
      <td><input name=\"inFullname\" type=\"text\" id=\"inFullname\" size=\"40\" value=\"" . Cleaner("o", $personalinfo['ap_fullname_vc']) . "\" /></td>
    </tr>
    <tr>
      <th>" . $lang['14'] . "</th>
      <td><input name=\"inEmail\" type=\"text\" id=\"inEmail\" size=\"40\" value=\"" . Cleaner("o", $personalinfo['ap_email_vc']) . "\" /></td>
    </tr>
    <tr>
      <th>" . $lang['17'] . "</th>
      <td><input name=\"inPhone\" type=\"text\" id=\"inPhone\" size=\"20\" value=\"" . Cleaner("o", $personalinfo['ap_phone_vc']) . "\" /></td>
    </tr>
    <tr>
      <th>Choose Language</th>
      <td>";

echo "<select name=\"inTranslation\" id=\"inTranslation\">";
$handle = @opendir(GetSystemOption('zpanel_root') . "lang");
$chkdir = GetSystemOption('zpanel_root') . "lang/";
if (!$handle) {
# Log an error as the folder cannot be opened...
    TriggerLog($useraccount['ac_id_pk'], $b = "Was unable to read the Language packs in (" . $chkdir . "), please ensure this folder exists.");
} else {
    while ($file = readdir($handle)) {
        if ($file != "." && $file != ".." && strstr($file, '.php') && !strstr($file, '_override')) {
            if (str_replace(".php", "", $file) == $personalinfo['ap_language_vc']) {
                echo "<option value=" . str_replace(".php", "", $file) . " selected=selected>" . str_replace(".php", "", $file) . "</option>\n";
            } else {
                echo "<option value=" . str_replace(".php", "", $file) . ">" . str_replace(".php", "", $file) . "</option>\n";
            }
        }
    }
    closedir($handle);
}

echo"</select>
	  
	  </td>
    </tr>
    <tr>
      <th>" . $lang['15'] . "</th>
      <td><textarea name=\"inAddress\" id=\"inAddress\" cols=\"45\" rows=\"5\">" . Cleaner("o", $personalinfo['ap_address_tx']) . "</textarea></td>
    </tr>
    <tr>
      <th>" . $lang['16'] . "</th>
      <td><input name=\"inPostalCode\" type=\"text\" id=\"inPostalCode\" size=\"15\" value=\"" . Cleaner("o", $personalinfo['ap_postcode_vc']) . "\" /></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td align=\"right\"><input type=\"hidden\" name=\"inReturnURL\" id=\"inReturnURL\" value=\"" . GetFullURL() . "\" /><input type=\"submit\" name=\"" . $lang['18'] . "\" id=\"" . $lang['18'] . "\" value=\"Submit\" /></td>
    </tr>
  </table>
</form>";
?>
