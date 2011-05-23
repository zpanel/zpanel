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
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
echo $lang['1'];
echo "<br><br>";
echo "
<table class=\"zgrid\">
  <tr>
    <td align=\"left\" valign=\"top\"><img src=\"inc/zPieGraph.php?used=" . GetQuotaUsages('diskspace', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_diskspace_bi'] . "\" alt=\"Disk usage pie graph\"></td>
    <td align=\"left\" valign=\"top\"><table class=\"zgrid\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
          <th>" . $lang['3'] . "</th>

          <td>" . FormatFileSize(GetQuotaUsages('diskspace', $useraccount['ac_id_pk'])) . " / " . FormatFileSize($quotainfo['qt_diskspace_bi']) . "</td>
        </tr>
        <tr>
          <th>" . $lang['4'] . "</th>
          <td>" . FormatFileSize(GetQuotaUsages('bandwidth', $useraccount['ac_id_pk'])) . " / " . FormatFileSize($quotainfo['qt_bandwidth_bi']) . "</td>

        </tr>
        <tr>
          <th>" . $lang['5'] . "</th>
          <td>" . GetQuotaUsages('domains', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_domains_in'] . "</td>
        </tr>
		<tr>
          <th>" . $lang['6'] . "</th>

          <td>" . GetQuotaUsages('subdomains', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_subdomains_in'] . "</td>
        </tr>
		<tr>
          <th>" . $lang['7'] . "</th>

          <td>" . GetQuotaUsages('parkeddomains', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_parkeddomains_in'] . "</td>
        </tr>
        <tr>
          <th>" . $lang['8'] . "</th>
          <td>" . GetQuotaUsages('ftpaccounts', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_ftpaccounts_in'] . "</td>
        </tr>
        <tr>

          <th>" . $lang['9'] . "</th>
          <td>" . GetQuotaUsages('mysql', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_mysql_in'] . "</td>
        </tr>
        <tr>
          <th>" . $lang['10'] . "</th>
          <td>" . GetQuotaUsages('mailboxes', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_mailboxes_in'] . "</td>
        </tr>

        <tr>
          <th><strong>" . $lang['11'] . "</th>
          <td>" . GetQuotaUsages('forwarders', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_fowarders_in'] . "</td>
        </tr>
		<tr>
          <th>" . $lang['12'] . "</th>
          <td>" . GetQuotaUsages('distlists', $useraccount['ac_id_pk']) . " / " . $quotainfo['qt_distlists_in'] . "</td>
        </tr>
    </table>";
?>
</td>
</tr>

<tr>
    <td style="background-image:none" colspan="2">

        <table class="none" id="none">
            <tr>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['4'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=FFS" . GetQuotaUsages('bandwidth', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_bandwidth_bi'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['5'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('domains', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_domains_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['6'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('subdomains', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_subdomains_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
            </tr>

            <tr>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['7'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('parkeddomains', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_parkeddomains_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['8'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('ftpaccounts', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_ftpaccounts_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['9'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('mysql', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_mysql_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
            </tr>

            <tr>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['10'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('mailboxes', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_mailboxes_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['11'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('forwarders', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_fowarders_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
                <td style="background-image:none; border:none"><?php echo"<h2>" . $lang['12'] . "</h2><img src=\"inc/zPieGraphSmall.php?used=" . GetQuotaUsages('distlists', $useraccount['ac_id_pk']) . "&quota=" . $quotainfo['qt_distlists_in'] . "\" alt=\"Disk usage pie graph\">"; ?>
                </td>
            </tr>
        </table>

    </td>
</tr>

</table>
