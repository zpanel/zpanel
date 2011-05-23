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
echo $lang['19'];
echo "<br><br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><br><div class=\"zannouce\">" . $lang['21'] . "</div>";
    echo "<br><br>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok-both')) {
    echo "<br><br><div class=\"zannouce\">" . $lang['20'] . "</div>";
    echo "<br><br>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'error')) {
    echo "<br><br><div class=\"zannouce\">" . $lang['22'] . "</div>";
    echo "<br><br>";
}
echo "<form id=\"frmPasswordAssistant\" name=\"frmPasswordAssistant\" method=\"post\" action=\"runner.php?load=obj_resetpassword\">
<table class=\"zform\">
<tr>
<th>" . $lang['23'] . "</th>
<td><input name=\"inCurPass\" type=\"password\" id=\"inCurPass\" /></td>
</tr>
<tr>
<th>" . $lang['24'] . "</th>
<td><input name=\"inNewPass\" type=\"password\" id=\"inNewPass\" /></td>
</tr>
<tr>
<th>" . $lang['25'] . "</th>
<td><input name=\"inConPass\" type=\"password\" id=\"inConPass\" /></td>
</tr>
<tr>
<th>" . $lang['26'] . "</th>
<td><input name=\"inResMySQL\" type=\"checkbox\" id=\"inResMySQL\" value=\"1\" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td align=\"right\"><input type=\"hidden\" name=\"inReturnURL\" id=\"inReturnURL\" value=\"" . GetFullURL() . "\" /><input name=\"Submit\" type=\"submit\" id=\"Submit\" value=\"Change\" /></td>
</tr>
</table>
</form>";
?>