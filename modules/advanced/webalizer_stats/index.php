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

$sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL";
$listdomains = DataExchange("r", $z_db_name, $sql);
$rowdomains = mysql_fetch_assoc($listdomains);
$totaldomains = DataExchange("t", $z_db_name, $sql);
if (isset($_GET['a'])) {
    if ($_GET['a'] == 'show') {
        $report_to_show1 = GetSystemOption('webalizer_sd') . $useraccount['ac_user_vc'] . "/" . $_POST['inDomain'] . "/index.html";
        if (!file_exists($report_to_show1)) {
            $report_to_show = "static/nowebstats/index.html";
        } else {
            $report_to_show = GetSystemOption('webalizer_sd') . $useraccount['ac_user_vc'] . "/" . $_POST['inDomain'] . "/index.html";
        }
    }
}
echo "" . $lang['27'] . "<br><br><h2>" . $lang['29'] . "</h2>";
if ($totaldomains > 0) {
    echo "<form action=\"" . GetNormalModuleURL(GetFullURL()) . "&a=show\" method=\"post\" name=\"frmStats\" id=\"frmStats\">
<table class=\"zform\">
<tr>
<td><strong>" . $lang['28'] . "</strong></td>
<td><select name=\"inDomain\" id=\"inDomain\">
<option value=\"\">-- " . $lang['29'] . " --</option>";
    do {
        echo "<option value=\"" . $rowdomains['vh_name_vc'] . "\">" . $rowdomains['vh_name_vc'] . "</option>";
    } while ($rowdomains = mysql_fetch_assoc($listdomains));
    echo "</select></td>
<td><input type=\"submit\" name=\"Submit\" value=\"" . $lang['30'] . "\"></td>
</tr>
</table>
</form>";
    if ((isset($_GET['a'])) && ($_GET['a'] == "show")) {
        echo "<br><h2>" . $lang['31'] . "</h2><iframe height=\"400\" width=\"100%\" allowtransparency=\"\" src=\"" . $report_to_show . "\" title=\"" . $lang['31'] . "\" frameborder=\"0\" scrolling=\"auto\"></iframe>";
    }
} else {
    echo $lang['32'];
}
?>