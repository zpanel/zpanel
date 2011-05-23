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

# Grab a list of the system settings...
$sql = "SELECT * FROM z_settings WHERE st_editable_in=1";
$listoptions = DataExchange("r", $z_db_name, $sql);
$rowoptions = mysql_fetch_assoc($listoptions);

echo $lang['74'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['75'] . "</div>";
}
echo "<br><h2>" . $lang['198'] . "</h2>";
echo "<form action=\"runner.php?load=obj_zpconfig\" method=\"post\" name=\"frmZPConfig\" id=\"frmZPConfig\">";
echo "<table class=\"zgrid\">";
do {
    echo "<tr>
    			<th>" . $lang[$rowoptions['st_label_vc']] . "</th>
    			<td><input type=\"text\" name=\"" . $rowoptions['st_name_vc'] . "\" value=\"" . $rowoptions['st_value_tx'] . "\"></td>
  		</tr>";
} while ($rowoptions = mysql_fetch_assoc($listoptions));
echo "<tr><td ><input type=\"hidden\" name=\"inReturn\" value=\"" . GetFullURL() . "\"><input type=\"submit\" name=\"inSaveSystem\"value=\"" . $lang['18'] . "\"></td><td></td></tr>";
echo "</table></form>";
echo "<br><h2>" . $lang['199'] . "</h2>";
echo "<form action=\"runner.php?load=obj_zpconfig\" method=\"post\" name=\"frmZPConfig\" id=\"frmZPConfig\">";
echo "<table class=\"zgrid\">";
echo "<tr>
<th>" . $lang['200'] . "</th>
<td><select name=\"inTemplate\" id=\"inTemplate\">";
$handle = @opendir(GetSystemOption('zpanel_root') . "templates");
$chkdir = GetSystemOption('zpanel_root') . "templates/";
if (!$handle) {
    # Log an error as the folder cannot be opened...
    TriggerLog($useraccount['ac_id_pk'], $b = "Was unable to read the templates in (" . $chkdir . "), please ensure this folder exists.");
} else {
    while ($file = readdir($handle)) {
        if ($file != "." && $file != "..") {
            if (is_dir($chkdir . $file)) {
                if ($file == GetSystemOption('zpanel_template')) {
                    echo "<option value=" . $file . " selected=selected>" . $file . "</option>\n";
                } else {
                    echo "<option value=" . $file . ">" . $file . "</option>\n";
                }
            }
        }
    }
    closedir($handle);
}
echo "</select></td></tr>";
echo "<tr>
<th>" . $lang['226'] . "</th>
<td><select name=\"inTranslation\" id=\"inTranslation\">";
$handle = @opendir(GetSystemOption('zpanel_root') . "lang");
$chkdir = GetSystemOption('zpanel_root') . "lang/";
if (!$handle) {
    # Log an error as the folder cannot be opened...
    TriggerLog($useraccount['ac_id_pk'], $b = "Was unable to read the Language packs in (" . $chkdir . "), please ensure this folder exists.");
} else {
    while ($file = readdir($handle)) {
        if ($file != "." && $file != ".." && strstr($file, '.php') && !strstr($file, '_override')) {
            if (str_replace(".php", "", $file) == GetSystemOption('zpanel_lang')) {
                echo "<option value=" . str_replace(".php", "", $file) . " selected=selected>" . str_replace(".php", "", $file) . "</option>\n";
            } else {
                echo "<option value=" . str_replace(".php", "", $file) . ">" . str_replace(".php", "", $file) . "</option>\n";
            }
        }
    }
    closedir($handle);
}
echo "</select></td></tr>";
echo "<tr><td ><input type=\"hidden\" name=\"inReturn\" value=\"" . GetFullURL() . "\"><input type=\"submit\" name=\"inSaveTemplate\"value=\"" . $lang['18'] . "\"></td><td></td></tr>";
echo "</table></form>";
?>