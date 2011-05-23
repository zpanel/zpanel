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

echo "" . $lang['33'] . "<br><br><h2>" . $lang['34'] . "</h2>";
echo "<table>
<tr>
<th>HTTP</th>
<td>";
if (CheckServiceUp(80) == 0) {
    echo "<img src=lib/emblems/down.gif>";
} else {
    echo "<img src=lib/emblems/up.gif>";
}
echo "</td>
</tr>
<tr>
<th>FTP</th>
<td>";
if (CheckServiceUp(21) == 0) {
    echo "<img src=lib/emblems/down.gif>";
} else {
    echo "<img src=lib/emblems/up.gif>";
}
echo "</td>
</tr>
<tr>
<th>SMTP</th>
<td>";
if (CheckServiceUp(25) == 0) {
    echo "<img src=lib/emblems/down.gif>";
} else {
    echo "<img src=lib/emblems/up.gif>";
}
echo "</td>
</tr>
<tr>
<th>POP3</th>
<td>";
if (CheckServiceUp(110) == 0) {
    echo "<img src=lib/emblems/down.gif>";
} else {
    echo "<img src=lib/emblems/up.gif>";
}
echo "</td>
</tr>
<tr>
<th>IMAP</th>
<td>";
if (CheckServiceUp(143) == 0) {
    echo "<img src=lib/emblems/down.gif>";
} else {
    echo "<img src=lib/emblems/up.gif>";
}
echo "</td>
</tr>
<tr>
<th>MySQL</th>
<td>";
if (CheckServiceUp(3306) == 0) {
    echo "<img src=lib/emblems/down.gif>";
} else {
    echo "<img src=lib/emblems/up.gif>";
}
echo "</td>
</table>";
echo "<br><h2>" . $lang['35'] . "</h2>";
echo "" . $lang['36'] . " " . GetServerUptime();
?>