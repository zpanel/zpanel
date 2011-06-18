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
# Now we need to declare and cleanup some variables
$acc_fk = $useraccount['ac_id_pk'];
$returnurl = Cleaner('o', $_POST['inReturn']);

# Lets get database ID's for all packages.
$sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL";
$listdomains = DataExchange("r", $z_db_name, $sql);
$rowdomains = mysql_fetch_assoc($listdomains);
$totaldomains = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_vhosts WHERE vh_name_vc='" . $_POST['inDomain'] . "' AND vh_deleted_ts IS NULL";
$activedomains = DataExchange("r", $z_db_name, $sql);
$rowactivedomains = mysql_fetch_assoc($activedomains);
$totalactivedomains = DataExchange("t", $z_db_name, $sql);

#get shared domain list
$SharedDomains = array();
$a = GetSystemOption('shared_domains');
$a = explode(',', $a);
foreach ($a as $b) {
    $SharedDomains[] = $b;
}

if ($_POST['inAction'] == 'NewDomain') {
    # Declare the domain name as a string...
    $domain = $_POST['inDomain'];
    $returnurl = $_POST['inReturn'];
    $destination = $_POST['inDestination'];
    # Check for spaces and remove if found...
    $domain = str_replace(' ', '', $domain);
    # Check to make sure the domain is not blank before we go any further...
    if ($domain == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
    # Check for invalid characters in the domain...
    if (!IsValidDomainName($domain)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=badname");
        exit;
    }
    # Check to make sure the domain is in the correct format before we go any further...
    $wwwclean = stristr($domain, 'www.');
    if ($wwwclean == true) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=error");
        exit;
    }
    # Check to see if the domain already exists in ZPanel somewhere and redirect if it does....
    if ($totalactivedomains > 0) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=alreadyexists");
        exit;
    }
    # Check to make sure user not adding a subdomain and blocks stealing of subdomains....
    if (substr_count($domain, ".") > 1) {
        $part = explode('.', $domain);
        foreach ($part as $check) {
            if (!in_array($check, $SharedDomains)) {
                if (strlen($check) > 3) {
                    $sql = "SELECT * FROM z_vhosts WHERE vh_name_vc LIKE '%" . $check . "%' AND vh_type_in !='2' AND vh_deleted_ts IS NULL";
                    $checkdomains = DataExchange("r", $z_db_name, $sql);
                    while ($rowcheckdomains = mysql_fetch_assoc($checkdomains)) {
                        $subpart = explode('.', $rowcheckdomains['vh_name_vc']);
                        foreach ($subpart as $subcheck) {
                            if (strlen($subcheck) > 3) {
                                if ($subcheck == $check) {
                                    if (substr($domain, -7) == substr($rowcheckdomains['vh_name_vc'], -7)) {
                                        header("location: " . GetNormalModuleURL($returnurl) . "&r=nosub");
                                        exit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    # Check to see if its a new home directory or use a current one...
    if ($_POST['inAutoHome'] == 1) {
        $homedirectoy_to_use = "/" . str_replace(".", "_", Cleaner('i', $domain));
        # Create the new home directory... (If it doesnt already exist.)
        zapi_filesystem_add(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/");
    } else {
        $homedirectoy_to_use = "/" . $destination;
    }
    # Now we write to the VHOST file.....
    $alias = "ServerAlias " . $domain . " www." . $domain . "";

    # Only run if the Server platform is Windows.
    if (ShowServerPlatform() == 'Windows') {
        if (GetSystemOption('disable_hostsen') == 'false') {
            # Lets add the hostname to the HOSTS file so that the server can view the domain immediately...
            @exec("C:/ZPanel/bin/zpanel/tools/setroute.exe " . $domain . "");
            @exec("C:/ZPanel/bin/zpanel/tools/setroute.exe www." . $domain . "");
        }
    }

    # Work out what handlers to add and then lets do it...
    $handlers = "";
    if ($packageinfo['pk_enablephp_in'] == 1) {
        $handlers = GetSystemOption('php_handler');
    }
    if ($packageinfo['pk_enablecgi_in'] == 1) {
        $handlers = $handlers . "ScriptAlias /cgi-bin/ \"$homedir/_cgi-bin/\"
<location /cgi-bin>
" . GetSystemOption('cgi_handler') . "
Options ExecCGI -Indexes
</location>";
        zapi_filesystem_add(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_cgi-bin/");
    }

    # Now we get all error pages and prepare them for the vhost container...
    $errorpages = "ErrorDocument 403 /_errorpages/403.html
ErrorDocument 404 /_errorpages/404.html
ErrorDocument 500 /_errorpages/500.html
ErrorDocument 510 /_errorpages/510.html";

    if (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . '/_errorpages/')) {
        mkdir(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages/", 0777);
        # Now lets copy the standard error pages across from the static directory (Added in ZPanel 4.0.3)
        @copy(GetSystemOption('static_dir') . "errorpages/403.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/403.html");
        @copy(GetSystemOption('static_dir') . "errorpages/404.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/404.html");
        @copy(GetSystemOption('static_dir') . "errorpages/500.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/500.html");
        @copy(GetSystemOption('static_dir') . "errorpages/510.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/510.html");

        # If the OS is Linux lets chmod them so they have full access
        if (ShowServerPlatform() <> "Windows") {
            # Lets set some more permissions on it so it can be accessed correctly! (eg. 0777 permissions)
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages/", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/403.html", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/404.html", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/500.html", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/510.html", 0777);
        }
    }

$flags = "php_admin_value open_basedir \"" . GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . ":" . GetSystemOption('temp_dir') . "\"
php_admin_value upload_tmp_dir \"" .GetSystemOption('temp_dir'). "\"";

    $alogs = "ErrorLog \"" . GetSystemOption('logfile_dir') . $useraccount['ac_user_vc'] . "/" . $domain . "-error.log\"
CustomLog \"" . GetSystemOption('logfile_dir') . $useraccount['ac_user_vc'] . "/" . $domain . "-access.log\" common
CustomLog \"" . GetSystemOption('logfile_dir') . $useraccount['ac_user_vc'] . "/" . $domain . "-bandwidth.log\" common";

    if ($personalinfo['ap_email_vc'] != '') {
        $serveradmin = $personalinfo['ap_email_vc'];
    } else {
        $serveradmin = "webmaster@" . $domain;
    }

    # Call the API!
    zapi_vhdomain_add(GetSystemOption('apache_vhost'), $domain, $alias, $serveradmin, GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use, $flags, $alogs, $handlers, $errorpages, $extra, GetSystemOption('directory_index'));


    # Check to see if version IS Windows (If so use the default hMailServer and create domain) - Otherwise we skip it!
    if (ShowServerPlatform() == 'Windows') {
        # Now we also create the database in hMailServer (If the hMailServer system config is set to BLANK)
        $hmaildatabase = GetSystemOption('hmailserver_db');
        $sql = "INSERT INTO hm_domains(domainname,
									domainactive,
									domainpostmaster,
									domainmaxsize,
									domainaddomain,
									domainmaxmessagesize,
									domainuseplusaddressing,
									domainplusaddressingchar,
									domainantispamoptions,
									domainenablesignature,
									domainsignaturemethod,
									domainsignatureplaintext,
									domainsignaturehtml,
									domainaddsignaturestoreplies,
									domainaddsignaturestolocalemail,
									domainmaxnoofaccounts,
									domainmaxnoofaliases,
									domainmaxnoofdistributionlists,
									domainlimitationsenabled,
									domainmaxaccountsize,
									domaindkimselector,
									domaindkimprivatekeyfile) VALUES (
									'" . $domain . "',
									 1,
									 '',
									 0,
									 '',
									 0,
									 0,
									 '',
									 0,
									 0,
									 1,
									 '',
									 '',
									 0,
									 0,
									 0,
									 0,
									 0,
									 0,
									 0,
									 '',
									 '')";
        DataExchange("w", $hmaildatabase, $sql);
    } else {
        # Now we add the domain to the Postfix database.
        $postfixdatabase = GetSystemOption('hmailserver_db');
        $sql = "INSERT INTO domain (domain) VALUES ('" . $domain . "')";
        DataExchange("w", $postfixdatabase, $sql);
    }

    # Just to fix an issue with selecting the database after selecting the hMailServer.
    mysql_select_db($z_db_name, $zdb);

    # If all has gone well we need to now create the domain in the database...
    $sql = "INSERT INTO z_vhosts (vh_acc_fk,
									vh_name_vc,
									vh_directory_vc,
									vh_type_in,
									vh_created_ts) VALUES (
									" . $acc_fk . ",
									'" . Cleaner('i', $domain) . "',
									'" . Cleaner('i', $homedirectoy_to_use) . "',
									1,
									" . time() . ")";
    DataExchange("w", $z_db_name, $sql);

    # Lets copy the default welcome page across...
    if ((!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.html")) && (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.php")) && (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.htm"))) {
        @copy(GetSystemOption('static_dir') . "pages/welcome.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.html");

        # If the OS is Linux lets chmod them so they have full access
        if (ShowServerPlatform() <> "Windows") {
            # Lets set some more permissions on it so it can be accessed correctly! (eg. 0777 permissions)
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.html", 0777);
        }
    }

    # Log the package as modified so the daemon will make changes to vhosts.
    $sql = "UPDATE z_quotas SET qt_modified_in = 1 WHERE qt_id_pk = " . $quotainfo['qt_id_pk'] . "";
    DataExchange("w", $z_db_name, $sql);

    # Now we add some infomation to the system log.
    TriggerLog($useraccount['ac_id_pk'], $b = "New domain (vhost) has been added by the user (" . Cleaner('i', $_POST['inDomain']) . ").");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'NewSubDomain') {
    # Declare the domain name as a string...
    $subdomain = $_POST['inSub'];
    $domain = ($_POST['inSub'] . "." . $_POST['inAlt']);
    $returnurl = $_POST['inReturn'];
    $destination = $_POST['inDestination'];
    # Check for spaces and remove if found...
    $domain = str_replace(' ', '', $domain);
    # Check to make sure the domain is not blank before we go any further...
    if ($subdomain == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
    # Check for invalid characters in the domain...
    if (!IsValidDomainName($domain)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=badname");
        exit;
    }
    # Check to make sure the domain is in the correct format before we go any further...
    $wwwclean = stristr($domain, 'www.');
    if ($wwwclean == true) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=error");
        exit;
    }

    $sql = "SELECT * FROM z_vhosts WHERE vh_name_vc='" . $domain . "' AND vh_deleted_ts IS NULL";
    $activedomains = DataExchange("r", $z_db_name, $sql);
    $rowactivedomains = mysql_fetch_assoc($activedomains);
    $totalactivedomains = DataExchange("t", $z_db_name, $sql);
    if ($totalactivedomains > 0) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=alreadyexists");
        exit;
    }
    # Check to see if its a new home directory or use a current one...
    if ($_POST['inAutoHome'] == 1) {
        $homedirectoy_to_use = "/" . str_replace(".", "_", Cleaner('i', $domain));
        # Create the new home directory... (If it doesnt already exist.)
        zapi_filesystem_add(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/");
    } else {
        $homedirectoy_to_use = "/" . $destination;
    }

    # Now we write to the VHOST file.....
    $alias = "ServerAlias " . $domain . " www." . $domain . "";

    # Check to make sure the Server is Windows before doing this..
    if (ShowServerPlatform() == 'Windows') {
        if (GetSystemOption('disable_hostsen') == 'false') {
            # Lets add the hostname to the HOSTS file so that the server can view the domain immediately...
            @exec("C:/ZPanel/bin/zpanel/tools/setroute.exe " . $domain . "");
            @exec("C:/ZPanel/bin/zpanel/tools/setroute.exe www." . $domain . "");
        }
    }

    # Work out what handlers to add and then lets do it...
    $handlers = "";
    if ($packageinfo['pk_enablephp_in'] == 1) {
        $handlers = GetSystemOption('php_handler');
    }
    if ($packageinfo['pk_enablecgi_in'] == 1) {
        $handlers = $handlers . "ScriptAlias /cgi-bin/ \"$homedir/_cgi-bin/\"
<location /cgi-bin>
" . GetSystemOption('cgi_handler') . "
Options ExecCGI -Indexes
</location>";
        zapi_filesystem_add(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_cgi-bin/");
    }

    # Now we get all error pages and prepare them for the vhost container...
    $errorpages = "ErrorDocument 403 /_errorpages/403.html
ErrorDocument 404 /_errorpages/404.html
ErrorDocument 500 /_errorpages/500.html
ErrorDocument 510 /_errorpages/510.html";

    if (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . '/_errorpages/')) {
        mkdir(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages/", 0777);
        # Now lets copy the standard error pages across from the static directory (Added in ZPanel 4.0.3)
        @copy(GetSystemOption('static_dir') . "errorpages/403.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/403.html");
        @copy(GetSystemOption('static_dir') . "errorpages/404.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/404.html");
        @copy(GetSystemOption('static_dir') . "errorpages/500.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/500.html");
        @copy(GetSystemOption('static_dir') . "errorpages/510.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/510.html");

        # If the OS is Linux lets chmod them so they have full access
        if (ShowServerPlatform() <> "Windows") {
            # Lets set some more permissions on it so it can be accessed correctly! (eg. 0777 permissions)
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages/", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/403.html", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/404.html", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/500.html", 0777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/_errorpages" . "/510.html", 0777);
        }
    }
$flags = "php_admin_value open_basedir \"" . GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . ":" . GetSystemOption('temp_dir') . "\"
php_admin_value upload_tmp_dir \"" .GetSystemOption('temp_dir'). "\"";


    $alogs = "ErrorLog \"" . GetSystemOption('logfile_dir') . $useraccount['ac_user_vc'] . "/" . $domain . "-error.log\"
CustomLog \"" . GetSystemOption('logfile_dir') . $useraccount['ac_user_vc'] . "/" . $domain . "-access.log\" common
CustomLog \"" . GetSystemOption('logfile_dir') . $useraccount['ac_user_vc'] . "/" . $domain . "-bandwidth.log\" common";

    if ($personalinfo['ap_email_vc'] != '') {
        $serveradmin = $personalinfo['ap_email_vc'];
    } else {
        $serveradmin = "webmaster@" . $_POST['inAlt'];
    }

    # Call the API!
    zapi_vhsub_add(GetSystemOption('apache_vhost'), $domain, $alias, $serveradmin, GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use, $flags, $alogs, $handlers, $errorpages, $extra, GetSystemOption('directory_index'));

    # If all has gone well we need to now create the domain in the database...
    $sql = "INSERT INTO z_vhosts (vh_acc_fk,
									vh_name_vc,
									vh_directory_vc,
									vh_type_in,
									vh_created_ts) VALUES (
									" . $acc_fk . ",
									'" . Cleaner('i', $domain) . "',
									'" . Cleaner('i', $homedirectoy_to_use) . "',
									2,
									" . time() . ")";
    DataExchange("w", $z_db_name, $sql);

    # Copy the default welcome page over...
    if ((!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.html")) && (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.php")) && (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.htm"))) {
        @copy(GetSystemOption('static_dir') . "pages/welcome.html", GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.html");

        # If the OS is Linux lets chmod them so they have full access
        if (ShowServerPlatform() <> "Windows") {
            # Lets set some more permissions on it so it can be accessed correctly! (eg. 0777 permissions)
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/index.html", 0777);
        }
    }

    # Log the package as modified so the daemon will make changes to vhosts.
    $sql = "UPDATE z_quotas SET qt_modified_in = 1 WHERE qt_id_pk = " . $quotainfo['qt_id_pk'] . "";
    DataExchange("w", $z_db_name, $sql);

    # Now we add some infomation to the system log.
    TriggerLog($useraccount['ac_id_pk'], $b = "New sub-domain (vhost) has been added by the user (" . Cleaner('i', $domain) . ").");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'NewParkedDomain') {
    # Declare the domain name as a string...
    $domain = $_POST['inDomain'];
    $returnurl = $_POST['inReturn'];
    # Check for spaces and remove if found...
    $domain = str_replace(' ', '', $domain);
    # Check to make sure the domain is not blank before we go any further...
    if ($domain == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
    # Check for invalid characters in the domain...
    if (!IsValidDomainName($domain)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=badname");
        exit;
    }
    # Check to make sure the domain is in the correct format before we go any further...
    $wwwclean = stristr($domain, 'www.');
    if ($wwwclean == true) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=error");
        exit;
    }
    # Check to make sure user not adding a subdomain and blocks stealing of subdomains....
    if (substr_count($domain, ".") > 1) {
        $part = explode('.', $domain);
        foreach ($part as $check) {
            if (!in_array($check, $SharedDomains)) {
                if (strlen($check) > 3) {
                    $sql = "SELECT * FROM z_vhosts WHERE vh_name_vc LIKE '%" . $check . "%' AND vh_type_in !='2' AND vh_deleted_ts IS NULL";
                    $checkdomains = DataExchange("r", $z_db_name, $sql);
                    while ($rowcheckdomains = mysql_fetch_assoc($checkdomains)) {
                        $subpart = explode('.', $rowcheckdomains['vh_name_vc']);
                        foreach ($subpart as $subcheck) {
                            if (strlen($subcheck) > 3) {
                                if ($subcheck == $check) {
                                    if (substr($domain, -7) == substr($rowcheckdomains['vh_name_vc'], -7)) {
                                        header("location: " . GetNormalModuleURL($returnurl) . "&r=nosub");
                                        exit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $sql = "SELECT * FROM z_vhosts WHERE vh_name_vc='" . $domain . "' AND vh_deleted_ts IS NULL";
    $activedomains = DataExchange("r", $z_db_name, $sql);
    $rowactivedomains = mysql_fetch_assoc($activedomains);
    $totalactivedomains = DataExchange("t", $z_db_name, $sql);
    if ($totalactivedomains > 0) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=alreadyexists");
        exit;
    }

    # Now we write to the VHOST file.....
    $alias = "ServerAlias " . $domain . " www." . $domain . "";

    # Check to see if version IS Windows (If so use the default hMailServer and create domain) - Otherwise we skip it!
    if (ShowServerPlatform() == 'Windows') {
        if (GetSystemOption('disable_hostsen') == 'false') {
            # Lets add the hostname to the HOSTS file so that the server can view the domain immediately...
            @exec("C:/ZPanel/bin/zpanel/tools/setroute.exe " . $domain . "");
            @exec("C:/ZPanel/bin/zpanel/tools/setroute.exe www." . $domain . "");
        }
    }

    # Call the API!
    zapi_vhparked_add(GetSystemOption('apache_vhost'), $domain, GetSystemOption('parking_path'));

    # If all has gone well we need to now create the domain in the database...
    $sql = "INSERT INTO z_vhosts (vh_acc_fk,
									vh_name_vc,
									vh_directory_vc,
									vh_type_in,
									vh_created_ts) VALUES (
									" . $acc_fk . ",
									'" . Cleaner('i', $domain) . "',
									'" . Cleaner('i', $homedirectoy_to_use) . "',
									3,
									" . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    # Now we add some infomation to the system log.
    TriggerLog($useraccount['ac_id_pk'], $b = "New parked domain has been added by the user (" . Cleaner('i', $_POST['inDomain']) . ").");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}











if ($_POST['inAction'] == 'delete') {
# User has choosen to delete the task...
    do {
        if (isset($_POST['inDelete_' . $rowdomains['vh_id_pk']])) {
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User domain vhost ID: " . $rowdomains['ct_id_pk'] . " was deleted.");

            # Call the API
            zapi_vhost_remove(GetSystemOption('apache_vhost'), $rowdomains['vh_name_vc']);

            # Check to see if version IS Windows (If so use the default hMailServer and create domain) - Otherwise we skip it!
            if (ShowServerPlatform() == 'Windows') {
                # Lets now go and try removing the domain from hMailServer (if configured in the ZPanel system settings:-
                $hmaildatabase = GetSystemOption('hmailserver_db');
                if (GetSystemOption('hmailserver_db') <> "") {
                    # Lets delete all hMailServer accounts...
                    $sql = "SELECT domainid FROM hm_domains WHERE domainname='" . $rowdomains['vh_name_vc'] . "'";
                    $hmdomainid = DataExchange("l", $hmaildatabase, $sql);
                    $hmisdomain = DataExchange("t", $hmaildatabase, $sql);
                    $domain_id = $hmdomainid['domainid'];
                    # Lets delete the domain (if it exists in the hMailServer database....
                    if ($hmisdomain > 0) {
                        # Delete the domain now...
                        $sql = "DELETE FROM hm_domains WHERE domainid=" . $domain_id . "";
                        DataExchange("w", $hmaildatabase, $sql);

                        zapi_filesystem_remove("C:/Zpanel/bin/hmailserver/Data/" . $rowdomains['vh_name_vc'] . "/");
                        zapi_filesystem_remove("C:/Program Files/hMailServer/Data/" . $rowdomains['vh_name_vc'] . "/");
                    }
                }
            } else {
                # Now we delete the domain from the Postfix database.
                $postfixdatabase = GetSystemOption('hmailserver_db');
                $sql = "DELETE FROM domain WHERE domain = '" . $rowdomains['vh_name_vc'] . "'";
                DataExchange("w", $postfixdatabase, $sql);
            }

            # Remove the domain from the MySQL database now..
            $sql = "UPDATE z_vhosts SET vh_deleted_ts=" . time() . " WHERE vh_id_pk=" . $rowdomains['vh_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
        }
    } while ($rowdomains = mysql_fetch_assoc($listdomains));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}
?>
