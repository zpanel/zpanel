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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>


        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <title>ZPanel &gt; Control your hosting!</title>
        <link href="{{zp:templatepath}}/inc/4.css" rel="stylesheet" type="text/css">
    </head><body>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                    <td class="topbar"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                                    <td rowspan="2" align="left" valign="top"><a href="./" title="Return to Control Panel homepage." alt="Return to Control Panel homepage."><img src="{{zp:templatepath}}/inc/zp_logo4.jpg" width="282" border="0" height="75"></a></td>
                                    <td align="right" valign="top"><table class="topbarlinks" border="0" cellpadding="2" cellspacing="2">
                                            <tbody><tr>
                                                    <td width="16"><img src="{{zp:templatepath}}/inc/logout.png" width="16" height="16"></td>
                                                    <td><p><a href="http://control.fusionised.com/logout.php"></a>{{link:logout}}</p>
                                                    </td>
                                                </tr>
                                            </tbody></table></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody></table>
                    </td>
                </tr>
                <tr>
                    <td class="navbar">{{link:home}}</td>
                </tr>
                <tr>
                    <td class="main"><table class="mainlayout" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                                    <td valign="top">{{zp:content}}
                                    </td>
                                    <td class="statsdata" width="200" align="right" valign="top">

                                        <h2 align="left">Account Information</h2>
                                        <div align="left">
                                            <table width="200" border="0" cellspacing="5">
                                                <tbody><tr>
                                                        <td nowrap="nowrap"><strong>Username</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{account:username}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Full name</strong></td>
                                                        <td nowrap="nowrap">{{account:fullname}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Email address</strong></td>
                                                        <td nowrap="nowrap"><a href="./?c=account&p=my_account" title="Update your contact email address">{{account:email}}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Package</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{package:name}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Account type</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{account:type}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap">&nbsp;</td>
                                                        <td nowrap="nowrap">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Disk Quota:</strong></td>
                                                        <td nowrap="nowrap">{{progbar:diskspace}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap">&nbsp;</td>
                                                        <td nowrap="nowrap" valign="top"><img src="{{zp:templatepath}}/inc/drop.gif" width="10" height="8"> <span class="Side_Info">{{usage:diskspace}}</span> / <span class="Side_Info">{{quota:diskspace}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Bandwidth Quota:</strong></td>
                                                        <td nowrap="nowrap">{{progbar:bandwidth}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap">&nbsp;</td>
                                                        <td nowrap="nowrap" valign="top"><img src="{{zp:templatepath}}/inc/drop.gif" width="10" height="8"><span class="Side_Info"> {{usage:bandwidth}}</span> / <span class="Side_Info">{{quota:bandwidth}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap">&nbsp;</td>
                                                        <td nowrap="nowrap">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Domains</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:domains}}</span> / <span class="Side_Info">{{quota:domains}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Sub-domains</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:subdomains}}</span> / <span class="Side_Info">{{quota:subdomains}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Parked domains:</strong></td>
                                                        <td nowrap="nowrap"> <span class="Side_Info">{{usage:parkeddomains}}</span> / <span class="Side_Info">{{quota:parkeddomains}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>FTP Accounts</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:ftpaccounts}}</span> / <span class="Side_Info">{{quota:ftpaccounts}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>MySQL Databases</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:mysql}}</span> / <span class="Side_Info">{{quota:mysql}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Email Accounts</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:mailboxes}}</span> / <span class="Side_Info">{{quota:mailboxes}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Email Forwarders</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:forwarders}}</span> / <span class="Side_Info">{{quota:forwarders}}</span>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Distrubution Lists</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{usage:distlists}} / {{quota:distlists}}</span></td>
                                                    </tr>
                                                </tbody></table>
                                        </div>
                                        <h2 align="left">Server Information</h2>
                                        <div align="left">
                                            <table width="200" border="0" cellspacing="5">
                                                <tbody><tr>
                                                        <td nowrap="nowrap"><strong>Your IP</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{server:userip}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Server IP</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{server:serverip}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Apache Version</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{server:verapache}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>PHP Version</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{server:verphp}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>MySQL Version</strong></td>
                                                        <td nowrap="nowrap"><span class="Side_Info">{{server:vermysql}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>Perl Version</strong></td>
                                                        <td nowrap="nowrap">5.10</td>
                                                    </tr>
                                                    <tr>
                                                        <td nowrap="nowrap"><strong>ZPanel Version</strong></td>
                                                        <td nowrap="nowrap">{{server:verzpanel}}</td>
                                                    </tr>
                                                </tbody></table>
                                            <p>&nbsp;</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody></table></td>
                </tr>
                <tr class="copypower">
                    <td class="copypower">Copyright &copy;
                        2004-2010 <a href="http://www.zpanelcp.com/" target="_blank">ZPanel Project</a>.
                        <br></td>
                </tr>
            </tbody></table>
    </body></html>
