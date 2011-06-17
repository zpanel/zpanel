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
function BuildApp($a, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo) {
    $body = $a;
    include('conf/zcnf.php');
    # Lets work out what template we are going to need to use..
    $template_path = 'templates/' . GetSystemOption('zpanel_template');
    $templatefile = $template_path . '/template.php';
    $fd = fopen($templatefile, 'r');
    $fileContents = fread($fd, filesize($templatefile));
    fclose($fd);
    $file = $body;
    $fd = fopen($file, 'r');
    $bodycontent = fread($fd, filesize($file));
    fclose($fd);

    function TemplateReplace($content, $bodycontent, $template_path, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo) {
        include('conf/zcnf.php');
        include('lang/' . GetSystemOption('zpanel_lang') . '.php');
        $fileContents = str_replace('{{usage:domains}}', GetQuotaUsages('domains', $useraccount['ac_id_pk']), $content);
        $fileContents = str_replace('{{usage:subdomains}}', GetQuotaUsages('subdomains', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:parkeddomains}}', GetQuotaUsages('parkeddomains', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:mailboxes}}', GetQuotaUsages('mailboxes', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:forwarders}}', GetQuotaUsages('forwarders', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:distlists}}', GetQuotaUsages('distlists', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:ftpaccounts}}', GetQuotaUsages('ftpaccounts', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:mysql}}', GetQuotaUsages('mysql', $useraccount['ac_id_pk']), $fileContents);
        $fileContents = str_replace('{{usage:diskspace}}', FormatFileSize(GetQuotaUsages('diskspace', $useraccount['ac_id_pk'])), $fileContents);
        $fileContents = str_replace('{{usage:bandwidth}}', FormatFileSize(GetQuotaUsages('bandwidth', $useraccount['ac_id_pk'])), $fileContents);
        $fileContents = str_replace('{{quota:domains}}', $quotainfo['qt_domains_in'], $fileContents);
        $fileContents = str_replace('{{quota:subdomains}}', $quotainfo['qt_subdomains_in'], $fileContents);
        $fileContents = str_replace('{{quota:parkeddomains}}', $quotainfo['qt_parkeddomains_in'], $fileContents);
        $fileContents = str_replace('{{quota:mailboxes}}', $quotainfo['qt_mailboxes_in'], $fileContents);
        $fileContents = str_replace('{{quota:forwarders}}', $quotainfo['qt_fowarders_in'], $fileContents);
        $fileContents = str_replace('{{quota:distlists}}', $quotainfo['qt_distlists_in'], $fileContents);
        $fileContents = str_replace('{{quota:ftpaccounts}}', $quotainfo['qt_ftpaccounts_in'], $fileContents);
        $fileContents = str_replace('{{quota:mysql}}', $quotainfo['qt_mysql_in'], $fileContents);
        $fileContents = str_replace('{{quota:diskspace}}', FormatFileSize($quotainfo['qt_diskspace_bi']), $fileContents);
        $fileContents = str_replace('{{quota:bandwidth}}', FormatFileSize($quotainfo['qt_bandwidth_bi']), $fileContents);
        $fileContents = str_replace('{{progbar:diskspace}}', "<img src=\"inc/zProgressBar.php?used=" . GetQuotaUsages('diskspace', $useraccount['ac_id_pk']) . "&total=" . $quotainfo['qt_diskspace_bi'] . "\">", $fileContents);
        $fileContents = str_replace('{{progbar:bandwidth}}', "<img src=\"inc/zProgressBar.php?used=" . GetQuotaUsages('bandwidth', $useraccount['ac_id_pk']) . "&total=" . $quotainfo['qt_bandwidth_bi'] . "\">", $fileContents);
        $fileContents = str_replace('{{server:company}}', GetSystemOption('sever_company'), $fileContents);
        $fileContents = str_replace('{{server:serverip}}', $_SERVER['SERVER_ADDR'], $fileContents);
        $fileContents = str_replace('{{server:userip}}', $_SERVER['REMOTE_ADDR'], $fileContents);
        $fileContents = str_replace('{{server:uptime}}', GetServerUptime(), $fileContents);
        $fileContents = str_replace('{{server:verapache}}', ShowApacheVersion(), $fileContents);
        $fileContents = str_replace('{{server:verphp}}', ShowPHPVersion(), $fileContents);
        $fileContents = str_replace('{{server:verkernal}}', ShowKernelVersion(ShowServerPlatform()), $fileContents);
        $fileContents = str_replace('{{server:vermysql}}', ShowMySQLVersion(), $fileContents);
        $fileContents = str_replace('{{server:veros}}', ShowServerPlatform(), $fileContents);
        $fileContents = str_replace('{{server:osname}}', ShowServerOSName(), $fileContents);
        $fileContents = str_replace('{{server:oslogo}}', "<img src=\"lib/emblems/os_icons/" . ShowServerOSName() . ".png\" title=\"This server is running " . ShowServerOSName() . "\">", $fileContents);
        $fileContents = str_replace('{{server:verzpanel}}', GetSystemOption('zpanel_version'), $fileContents);
        $fileContents = str_replace('{{link:home}}', "<a href=\"./\">" . $lang['215'] . "</a>", $fileContents);
        $fileContents = str_replace('{{link:logout}}', "<a href=\"./login.php?logout\">" . $lang['216'] . "</a>", $fileContents);
        $fileContents = str_replace('{{zp:templatepath}}', $template_path, $fileContents);
        $fileContents = str_replace('{{account:username}}', $useraccount['ac_user_vc'], $fileContents);
        $fileContents = str_replace('{{account:email}}', $personalinfo['ap_email_vc'], $fileContents);
        $fileContents = str_replace('{{account:created}}', date(GetSystemOption('zpanel_df'), $useraccount['ac_created_ts']), $fileContents);
        $fileContents = str_replace('{{account:fullname}}', $personalinfo['ap_fullname_vc'], $fileContents);
        $fileContents = str_replace('{{package:name}}', $packageinfo['pk_name_vc'], $fileContents);
        $fileContents = str_replace('{{account:type}}', ShowAccountType($permissionset), $fileContents);
        $fileContents = str_replace('{{zp:content}}', $bodycontent, $fileContents);
        return $fileContents;
    }

    $templatecontent = TemplateReplace($fileContents, $bodycontent, $template_path, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo);
    return eval('?>' . $templatecontent . '');
}
?>
