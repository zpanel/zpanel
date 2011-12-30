#!/usr/bin/php
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
set_time_limit(0);

# Added below line to fix issue with daemon running out of memory. See Bug #339 - http://www.zpanelcp.com/legacybugs/show_bug.php?id=339
ini_set('memory_limit', '256M');

# Disable error messages
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 'Off');
ini_set('date.timezone', 'Europe/London');
include('conf/zcnf.php');

# Check what OS is running, If Windows we need to set the DB include path different to that of POSIX based OSs.

function IsWindows() {
    # DESCRIPTION: Returns true if the OS is Windows.
    # FUNCTION RELEASE: 6.1.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
        return true;
    } else {
        return false;
    }
}

if (IsWindows() == true) {
    $zpanel_db_conf = "C:/ZPanel/panel/conf/zcnf.php";
    echo "This OS has been detected as: Windows\n";
} else {
    $zpanel_db_conf = "/etc/zpanel/conf/zcnf.php";
    echo "This OS has been detected as: Linux\n";
}
#####################################################################################################################################
# LOAD SOME COMPONENTS REQUIRED BY THE SCRIPT....                                                                                   #
#####################################################################################################################################

function DataExchange($a, $b, $c) {
    # DESCRIPTION: Safely queries the MySQL database as well as reports any issues with the Query into the logs table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    global $zpanel_db_conf;
    $querytype = $a;
    $databaseconn = $b;
    $sqlquery = $c;
    include $zpanel_db_conf;
    @mysql_select_db($z_db_name, $zdb) or die("Unable to select database, database (" . $z_db_name . ") appears to not exsist!\nMySQL Said: " . mysql_error() . "\n");
    $fretval = false;

    if ($querytype == 'r') {
        $sql = mysql_query($sqlquery, $zdb) or die("MySQL database error, The [READ] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . "");
        $fretval = $sql;
    }
    if ($querytype == 'l') {
        $sql = mysql_query($sqlquery, $zdb) or die("MySQL database error, The [LIST] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . "");
        $fretval = mysql_fetch_assoc($sql);
    }
    if ($querytype == 'w') {
        $sql = mysql_query($sqlquery, $zdb) or die("MySQL database error, The [WRITE] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . "");
        $fretval = true;
    }
    if ($querytype == 't') {
        $sql = mysql_query($sqlquery, $zdb) or die("MySQL database error, The [SUM] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . "");
        $fretval = mysql_num_rows($sql);
    }
    return $fretval;
}

function GetSystemOption($a=" ") {
    # DESCRIPTION: Gets and returns a value from the 'z_settings' table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    global $zpanel_db_conf;
    $setting_name = $a;
    include $zpanel_db_conf;
    $sql = "SELECT * FROM z_settings WHERE st_name_vc = '" . $setting_name . "'";
    $row_syssetting = DataExchange("l", $z_db_name, $sql);
    $total_syssetting = DataExchange("t", $z_db_name, $sql);
    if ($total_syssetting < 1) {
        $fretval = "Value not found!";
    } else {
        $fretval = $row_syssetting['st_value_tx'];
    }
    return $fretval;
}

$zpanel_path = GetSystemOption('zpanel_root');
include($zpanel_path . "lang/" . GetSystemOption('zpanel_lang') . ".php");

function TriggerLog($a=0, $b="No details.") {
    # DESCRIPTION: Logs an event, for debugging or audit purposes in the 'z_logs' table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    global $zpanel_db_conf;
    $acc_key = $a;
    $log_details = $b;
    include $zpanel_db_conf;
    $sql = "INSERT INTO z_logs (lg_acc_fk, lg_when_ts, lg_ipaddress_vc, lg_details_tx) VALUES (" . $acc_key . ", '" . time() . "', '" . $_SERVER['HTTP_X_FORWARDED_FOR'] . "', '" . $log_details . "')";
    DataExchange("w", $z_db_name, $sql);
    return;
}

function ChangeSafeSlashesToWin($a=0) {
    # DESCRIPTION: Changes MySQL safe directory slashes '\\' to Windows PHP slashes '/'.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $path = $a;
    if (IsWindows() == true) {
        $fretval = str_replace("/", "\\", $path);
    } else {
        $fretval = $path;
    }
    return $fretval;
}

function GetFullDirectorySize($directory) {
    # DESCRIPTION: Gets the full size in bytes of a folder set.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $size = 0;
    if (substr($directory, -1) == '/') {
        $directory = substr($directory, 0, -1);
    }
    if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory)) {
        return -1;
    }
    if ($handle = opendir($directory)) {
        while (($file = readdir($handle)) !== false) {
            $path = $directory . '/' . $file;
            if ($file != '.' && $file != '..') {
                if (is_file($path)) {
                    $size += filesize($path);
                } elseif (is_dir($path)) {
                    $handlesize = GetFullDirectorySize($path);
                    if ($handlesize >= 0) {
                        $size += $handlesize;
                    } else {
                        return -1;
                    }
                }
            }
        }
        closedir($handle);
    }
    return $size;
}

function GenerateBandwidth($f_logs, $f_username, $f_site) {
    # DESCRIPTION: Generates the Bandwidth used!
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    // Path to Apache's log file
    $ac_arr = file($f_logs . $f_username . '/' . $f_site . '-bandwidth.log');
    // Splitting IP from the rest of the record
    $astring = join("", $ac_arr);
    $astring = preg_replace("/(\n|\r|\t)/", "\n", $astring);
    $records = preg_split("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $astring, -1, PREG_SPLIT_DELIM_CAPTURE);
    $sizerecs = sizeof($records);
    // Now split into records
    $j = 0;
    $i = 1;
    $arb = array();
    $each_rec = 0;
    $cur_month = date("/M/Y");
    while ($i < $sizerecs) {
        $ip = $records[$i];
        $all = @$records[$i + 1];
        // Parse other fields
        preg_match("/\[(.+)\]/", $all, $match);
        $access_time = @$match[1];
        $all = @str_replace($match[1], "", $all);
        preg_match("/\"GET (.[^\"]+)/", $all, $match);
        $http = @$match[1];
        $link = explode(" ", $http);
        $all = @str_replace("\"GET $match[1]\"", "", $all);
        preg_match("/([0-9]{3})/", $all, $match);
        $success_code = @$match[1];
        $all = @str_replace($match[1], "", $all);
        preg_match("/\"(.[^\"]+)/", $all, $match);
        $ref = @$match[1];
        $all = @str_replace("\"$match[1]\"", "", $all);
        preg_match("/\"(.[^\"]+)/", $all, $match);
        $browser = @$match[1];
        $all = @str_replace("\"$match[1]\"", "", $all);
        preg_match("/([0-9]+\b)/", $all, $match);
        $bytes = @$match[1];
        $all = @str_replace($match[1], "", $all);
        // The following code is to collect bandwidth usage per user and assign each usage to the user,
        // Successful match, now we need to check if the access date matches the current month.
        // If yes, we'll sum all the sizes from access pages only from the current month
        // and put in database... 
        $arb[$j] = @$arb[$j] + $bytes;
        // Advance to next record
        $i = $i + 1;
        $each_rec++;
    }
    return @$arb[$j];
}

function SureRemoveDir($dir, $DeleteMe) {
    # DESCRIPTION: Removes all directories under a parent.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if (!$dh = @opendir($dir))
        return;
    while (false !== ($obj = readdir($dh))) {
        if ($obj == '.' || $obj == '..')
            continue;
        if (!@unlink($dir . '/' . $obj))
            SureRemoveDir($dir . '/' . $obj, true);
    }

    closedir($dh);
    if ($DeleteMe) {
        @rmdir($dir);
    }
}

function CheckServiceUp($a, $b=0) {
    # DESCRIPTION: Checks the current status of a port, returns 1 if the service is running and 0 if its not.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $port = $a;
    $isudp = $b;
    $timeout = GetSystemOption('servicechk_to');
    if ($isudp == 1) {
        $ip = 'udp://' . $_SERVER['SERVER_ADDR'];
    } else {
        $ip = $_SERVER['SERVER_ADDR'];
    }
    $fp = @fsockopen($ip, $port, $errno, $errstr, 2);
    if (!$fp) {
        $fretval = 0;
    } else {
        $fretval = 1;
    }
    return $fretval;
}

function ShowPHPVersion() {
    # DESCRIPTION: Returns the PHP version number.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $fretval = phpversion();
    return $fretval;
}

function ShowMySQLVersion() {
    # DESCRIPTION: Returns the MySQL Server version.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $fretval = mysql_get_server_info();
    return $fretval;
}

function CheckForNullValue($value, $true, $false) {
    # DESCRIPTION: Checks and converts a given value.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if ($value == 0) {
        return $false;
    } else {
        return $true;
    }
}

function GetServerUptime() {
    # DESCRIPTION: Checks and converts a given value.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $OS = strtolower(PHP_OS);
    if ($OS == "linux") {
        $data = shell_exec('uptime');
        $uptime = explode(' up ', $data);
        $uptime = explode(',', $uptime[1]);
        $uptime = $uptime[0] . ', ' . $uptime[1];
        $loadavg_array = explode(" ", exec("cat /proc/loadavg"));
        $loadavg = $loadavg_array[2];
        $fretval = $uptime;
    } elseif ($OS == "winnt") {
        $pagefile = "" . GetSystemOption('windows_drive') . ":\pagefile.sys";
        $upsince = filemtime($pagefile);
        $gettime = (time() - filemtime($pagefile));
        $days = floor($gettime / (24 * 3600));
        $gettime = $gettime - ($days * (24 * 3600));
        $hours = floor($gettime / (3600));
        $gettime = $gettime - ($hours * (3600));
        $minutes = floor($gettime / (60));
        $gettime = $gettime - ($minutes * 60);
        $seconds = $gettime;
        $days = CheckForNullValue($days != 1, $days . ' days', $hours . ' day');
        $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
        $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
        $seconds = CheckForNullValue($seconds != 1, $seconds . ' seconds', $seconds . ' second');
        $fretval = $days . ", " . $hours . ", " . $minutes . "";
    } else {
        $fretval = "Unsupported Operating System";
    }
    return $fretval;
}

#####################################################################################################################################
# FINISHED LOADING REQUIRED FUNCTIONS.....                                                                                          #
#####################################################################################################################################

TriggerLog(1, $b = "= ZPanel daemon starting execution....");

# Make sure that the webalizer directory exists and if not, lets create it!!
if (!file_exists(GetSystemOption('webalizer_reps') . "/")) {
    @mkdir(GetSystemOption('webalizer_reps'), 777);
    @chmod(GetSystemOption('webalizer_reps'), 0777);
}

# Make sure that the webalizer directory exists for 'zadmin' and if not, lets create it!!
if (!file_exists(GetSystemOption('webalizer_reps') . "/zadmin/")) {
    @mkdir(GetSystemOption('webalizer_reps') . "/zadmin/", 777);
    @chmod(GetSystemOption('webalizer_reps') . "/zadmin/", 0777);
}

#####################################################################################################################################
# Check domain home directories exist, saves Apache from failing to load!                                                           #
#####################################################################################################################################
$sql = "SELECT * FROM z_vhosts LEFT JOIN z_accounts ON z_vhosts.vh_acc_fk=z_accounts.ac_id_pk WHERE vh_deleted_ts IS NULL";
$domains = DataExchange("r", $z_db_name, $sql);
$row_domains = mysql_fetch_assoc($domains);
$totalRows_domains = DataExchange("t", $z_db_name, $sql);
# Check to ensure that a 'domains' log folder exists for the current user....
if (!file_exists(GetSystemOption('logfile_dir') . $row_domains['ac_user_vc'] . "/")) {
    @mkdir(GetSystemOption('logfile_dir') . $row_domains['ac_user_vc'], 777);
    @chmod(GetSystemOption('logfile_dir') . $row_domains['ac_user_vc'], 0777);
}
if ($totalRows_domains > 0) {
    do {
        if (!file_exists(GetSystemOption('hosted_dir') . $row_domains['ac_user_vc'] . $row_domains['vh_directory_vc'])) {
# Document root of domain does not exsist so lets update the VHOST config and change the document root to point to 'domain suspended page'.
            TriggerLog(1, $b = "> Domain: '" . $row_domains['vh_name_vc'] . "' DocumentRoot does not exsist, removing from VHOST config.");
# Delete domain!!!
            $domain = $row_domains['vh_name_vc'];
            $sql = "UPDATE z_vhosts SET vh_deleted_ts=" . time() . " WHERE vh_id_pk=" . $row_domains['vh_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            $content = implode('', file(GetSystemOption('apache_vhost')));
            $content1 = explode("
	# DOMAIN: $domain", $content);
            $content2 = explode("</virtualhost>", $content1[1], 2);
            $content = $content1[0] . $content2[1];
            $editfile = fopen($apache_conf, "w");
            fwrite($editfile, $content);
            fclose($editfile);

            # Now delete the domain from hMail if it is in use.
            $hmaildatabase = GetSystemOption('hmailserver_db');
            if (GetSystemOption('hmailserver_db') <> "") {
                # Lets delete all hMailServer accounts...
                $sql = "SELECT domainid FROM hm_domains WHERE domainname='" . $domain . "'";
                $hmdomainid = DataExchange("l", $hmaildatabase, $sql);
                $hmisdomain = DataExchange("t", $hmaildatabase, $sql);
                $domain_id = $hmdomainid['domainid'];
                # Lets delete the domain (if it exists in the hMailServer database....
                if ($hmisdomain > 0) {
                    # Delete the domain now...
                    $sql = "DELETE FROM hm_domains WHERE domainid=" . $domain_id . "";
                    DataExchange("w", $hmaildatabase, $sql);
                }
            }

            # Send email saying that domain has been removed due to note exsistent document root.
            # Email user if they are over their storage limit and BCC server admin in!
            $messagesubject = $lang['217'];
            $messagebody = $lang['218'];
            $messagebody = str_replace("{{username}}", $row_domains['ac_user_vc'], $messagebody);
            $messagebody = str_replace("{{domain}}", $row_domains['vh_name_vc'], $messagebody);
            $sent = SendAccountMail($row_domains['ac_email_vc'], $messagesubject, $messagebody);
            if ($sent == 1) {
                TriggerLog(1, $b = "> Domain '" . $row_domains['vh_name_vc'] . "' was removed, invalid home directory, Email sent to user!");
            } else {
                TriggerLog(1, $b = "> Domain '" . $row_domains['vh_name_vc'] . "' was removed, invalid home directory, Unable to send user email!");
            }
        }
    } while ($row_domains = mysql_fetch_assoc($domains));
} else {
    echo "No Apache VHOSTs are currently configured on this server!";
}

#####################################################################################################################################
# New 'reload apache implementation, added in Zpanel 4.0.3)                                                                         #
#####################################################################################################################################
if (IsWindows() == true) {
    system("C:\\ZPanel\\bin\\apache\\bin\\httpd.exe -k restart -n \"Apache\"");
} else {
    system("/etc/zpanel/bin/zsudo service " . GetSystemOption('lsn_apache') . " graceful"); # Need to create a system option so that the command can be customised for different distros etc.
}
TriggerLog(1, $b = "> Apache web server has been rebooted.");

#####################################################################################################################################
# Check, record and refresh (if required) the domain access logs...                                                                 #
#####################################################################################################################################
$system_time = GetSystemOption('current_month');
if (date("Ym", time()) > $system_time) {
    # Update the bandwidth table with the total usage for last month for each user...
    $sql = "SELECT * FROM z_accounts WHERE ac_deleted_ts IS NULL";
    $lastband = DataExchange("r", $z_db_name, $sql);
    $row_lastband = mysql_fetch_assoc($lastband);
    $totalRows_lastband = DataExchange("t", $z_db_name, $sql);
    if ($totalRows_lastband > 0) {
        do {
            $sql = "INSERT INTO z_bandwidth (bd_acc_fk, bd_month_in, bd_transamount_bi, bd_diskamount_bi) VALUES (" . $row_lastband['ac_id_pk'] . "," . date("Ym", time()) . ", 0, 0)";
            DataExchange("w", $z_db_name, $sql);
        } while ($row_lastband = mysql_fetch_assoc($lastband));
        TriggerLog(1, $b = "> Bandwidth counts have been reset..");
    }
    $sql = "UPDATE z_settings SET st_value_tx='" . date("Ym", time()) . "' WHERE st_name_vc='current_month'";
    DataExchange("w", $z_db_name, $sql);
    # Log the action.
    TriggerLog(1, $b = "> Updated and refreshed the domain access log files.");
}

#####################################################################################################################################
# Calculate current disk usage for all accounts and log into the database...                                                        #
#####################################################################################################################################
$sql = sprintf("SELECT * FROM z_accounts WHERE ac_deleted_ts IS NULL");
$userspace = DataExchange("r", $z_db_name, $sql);
$row_userspace = mysql_fetch_assoc($userspace);
$totalRows_userspace = DataExchange("t", $z_db_name, $sql);
do {
    # Generate current disk usage and write out to the database.
    $hdirsize = 0;
    $hdir = GetSystemOption('hosted_dir') . $row_userspace['ac_user_vc'] . "/";
    $hdirsize = GetFullDirectorySize($hdir);

    # Now we get the total MySQL database sizes and add them to the database as well as include them in the disk quota...
    $sql = sprintf("SELECT * FROM z_mysql WHERE my_acc_fk=" . $row_userspace['ac_id_pk'] . " AND my_deleted_ts IS NULL");
    $userdbs = DataExchange("r", $z_db_name, $sql);
    $row_userdbs = mysql_fetch_assoc($userdbs);
    $totalRows_userdbs = DataExchange("t", $z_db_name, $sql);
    $all_dbSize = 0;
    if ($totalRows_userdbs > 0) {
        do {
            mysql_select_db($row_userdbs['my_name_vc']);
            $result = mysql_query("SHOW TABLE STATUS");
            $dbSize = 0;
            while ($row = mysql_fetch_array($result)) {
                $dbSize += $row['Data_length'] + $row['Index_length'];
                $all_dbSize = ($all_dbSize + $dbSize);
            }
            # Now lets update the MySQL table with the size of each database...
            mysql_select_db($z_db_name);
            $sql = "UPDATE z_mysql SET my_usedspace_bi=" . $dbSize . " WHERE my_id_pk=" . $row_userdbs['my_id_pk'] . ";";
            DataExchange("w", $z_db_name, $sql);
        } while ($row_userdbs = mysql_fetch_assoc($userdbs));
    }

    # Now we combine the total of the MySQL databases sizes and add it to the actual disk usage before we add it to the database...
    $hdirsize = $hdirsize + ($all_dbSize / 10.24);

    $sql = "UPDATE z_bandwidth SET bd_diskamount_bi=" . $hdirsize . " WHERE bd_acc_fk=" . $row_userspace['ac_id_pk'] . " AND bd_month_in=" . date("Ym", time()) . ";";
    DataExchange("w", $z_db_name, $sql);
    # Generate bandwidth quota from all domains that the user has hosted on his/her account and write to the database.
    $sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk='" . $row_userspace['ac_id_pk'] . "' AND vh_deleted_ts IS NULL";
    $userdoms = DataExchange("r", $z_db_name, $sql);
    $row_userdoms = mysql_fetch_assoc($userdoms);
    $totalRows_userdoms = DataExchange("t", $z_db_name, $sql);
    if ($totalRows_userdoms > 0) {
        $bandwidth_total = 0;
        do {
            $this_domain = 0;
            $this_domain = GenerateBandwidth(GetSystemOption('logfile_dir'), $row_userspace['ac_user_vc'], $row_userdoms['vh_name_vc']);
            if ($this_domain == "") {
                $this_domain = 0;
            }
            $bandwidth_total = ($this_domain + $bandwidth_total);
            # Now we clear the apache bandwidth log:-
            $myFile = GetSystemOption('logfile_dir') . $row_userspace['ac_user_vc'] . "/" . $row_userdoms['vh_name_vc'] . "-bandwidth.log";
            if (fopen($myFile, 'w')) {
                $fh = fopen($myFile, 'w');
                $stringData = "";
                fwrite($fh, $stringData);
                fclose($fh);
            } else {
                TriggerLog(1, $b = "> Cannot open file: \"" . $myFile . "\"");
            }
        } while ($row_userdoms = mysql_fetch_assoc($userdoms));
        # Calculate the total usage of the log file...
        $sql = "UPDATE z_bandwidth SET bd_transamount_bi=(bd_transamount_bi+" . $bandwidth_total . ") WHERE bd_acc_fk=" . $row_userspace['ac_id_pk'] . " AND bd_month_in='" . date("Ym", time()) . "';";
        DataExchange("w", $z_db_name, $sql);
    }
} while ($row_userspace = mysql_fetch_assoc($userspace));
TriggerLog(1, $b = "> Bandwidth and disk usage details have been calculated.");


#####################################################################################################################################
# Clean up the 'Temp' directory!                                                                                                    #
#####################################################################################################################################
TriggerLog(1, $b = "> Preparing to truncate the Temporary file store.");
SureRemoveDir(GetSystemOption('temp_dir'), FALSE);
TriggerLog(1, $b = "> Temporary file store has been truncated.");


#####################################################################################################################################
# Set all domains as 'Live'                                                                                                         #
#####################################################################################################################################
$sql = "UPDATE z_vhosts SET vh_active_in=1";
DataExchange("w", $z_db_name, $sql);
TriggerLog(1, $b = "> All active domains have been marked as (Live).");

#####################################################################################################################################
# Usage Limiting Section for bandwidth and diskspace                                                                                #
#####################################################################################################################################
    # DESCRIPTION: Activates all domains inside their quotas and limits all those that aren't
    # FUNCTION RELEASE: 5.1.2
    # FUNCTION AUTHOR: Kevin Andrews kandrews@zpanelcp.com
    # FULL RE-WRITE FOR ZPANEL 6.1.2 11-10-11

$vhost = GetSystemOption('apache_vhost');
$hosted = GetSystemOption('hosted_dir');
$static = GetSystemOption('zpanel_root');
$temp = GetSystemOption('temp_dir');
$disk = "diskexceeded";
$bandwidth = "bandwidthexceeded";

//build the directory a domain will be limited to
function buildLimitDir($static, $limit){
    return $LimitedDir = "DocumentRoot \"$static" . "static/".$limit."\"";  
}
//build the directory a domain will be activated to
function buildDomainDir($hosted, $domaindir, $restrict, $temp, $user){
    /*
     * @info restricted pulled from database
     * @info use ; or : seperators dependant on OS
     * @info dont use trailing slashes
     * 
     * @info From PHP.NET
     *       Under Windows, separate the directories with a semicolon. On all other systems, separate the directories with a colon. As an Apache module, open_basedir paths from parent directories are now automatically inherited.
     *       The restriction specified with open_basedir is a directory name since PHP 5.2.16 and 5.3.4. 
     *       Previous versions used it as a prefix. This means that "open_basedir = /dir/incl" 
     *       also allowed access to "/dir/include" and "/dir/incls" if they exist. 
     *       When you want to restrict access to only the specified directory, end with a slash. 
     *       For example: open_basedir = /dir/incl/
     *       The default is to allow all files to be opened. 
    */
    if(IsWindows()){
        $seperator = ";";
    } else {
        $seperator = ":";
    }
    if(empty($restrict)){
        return $DomainDir = "DocumentRoot \"$hosted" . $user . "$domaindir\"" . "\r\n" . "";
    } else {
        return $DomainDir = "DocumentRoot \"$hosted" . $user . "$domaindir\"" . "\r\n" . "php_admin_value open_basedir \"$restrict". "$seperator" . GetSystemOption('temp_dir') . "\"\r\n";
}
}
//override a domain record and return vhost file
function setDomainRecord($filein, $domain, $newdir){
    $startpos = strpos($filein, "# DOMAIN: $domain");
    $endpos = strpos($filein, "# END DOMAIN: $domain");
    $endposlength = "# END DOMAIN: $domain";
    $endposlength = strlen($endposlength);
    $record = substr($filein, $startpos, ($endpos - $startpos) + $endposlength);
    $newrecord = preg_replace('/php_admin_value open_basedir \".*?\"\r\n/', "", $record);
    $newrecord = preg_replace('/DocumentRoot \".*?\"\r\n/', $newdir, $newrecord);
    return $fileout = substr_replace($filein, $newrecord, $startpos, ($endpos - $startpos) + $endposlength);   
        }
//get the vhost file into variable
function getVhost($path){
    $vhostfile = file_get_contents($path) or die(TriggerLog(1, $b = "Usage Limiter - cant open the httpd-vhosts.conf"));
    return $vhostfile;
    }
//write changed vhost file to disk
function setVhost($file, $edited, $path){
    if(is_bool($edited)){
        if(0 == $edited){
            TriggerLog(1, $b = "Usage Limiter - No vhost changes made, file not written to.");
        } elseif (1 == $edited) {
            $fh = fopen($path, 'w') or die(TriggerLog(1, $b = "Usage Limiter - can't open the httpd-vhosts.conf"));
            $write = fwrite($fh, $file);

            if ($write) {
                TriggerLog(1, $b = "Usage Limiter - Vhost file written to successfully");
    } else {
                TriggerLog(1, $b = "Usage Limiter - Failed to write to Vhost file");
        }
            fclose($fh);
            
    }
    } else {
        TriggerLog(1, $b = "Usage Limiter - Wrong value supplied for edited");
}

}
//restart the apache server
function restartApache() {
    if (IsWindows() == true) {
        $last_line = exec("C:\\ZPanel\\bin\\apache\\bin\\httpd.exe -k restart -n \"Apache\"", $return);
        TriggerLog(1, $b = "Usage Limiter - Apache restart returned : ".print_r($return)." : ".$last_line);
    } else {
        $last_line = system("/etc/zpanel/bin/zsudo service " . GetSystemOption('lsn_apache') . " graceful");
        TriggerLog(1, $b = "Usage Limiter - Apache graceful returned : ".$last_line);
        }
    }
//grab vhosts file into memory
$vhostfile = getVhost($vhost);

# Enable Bandwidth
$sqlEnableBandwidth = array(true, "SELECT * FROM zpanel_core.z_accounts JOIN (zpanel_core.z_bandwidth, zpanel_core.z_quotas, zpanel_core.z_vhosts) ON (z_accounts.ac_id_pk=z_bandwidth.bd_acc_fk AND z_accounts.ac_package_fk=z_quotas.qt_package_fk AND z_accounts.ac_id_pk=z_vhosts.vh_acc_fk) WHERE (z_bandwidth.bd_transamount_bi < z_quotas.qt_bandwidth_bi) AND (z_vhosts.vh_deleted_ts IS NULL) AND (z_bandwidth.bd_month_in = " . GetSystemOption('current_month') . ")  AND (z_vhosts.vh_type_in<>3) OR (z_quotas.qt_bandwidth_bi = '0'  AND z_vhosts.vh_deleted_ts IS NULL)  AND (z_bandwidth.bd_month_in = " . GetSystemOption('current_month') . ")  AND (z_vhosts.vh_type_in<>3)");
# Enable Diskspace
$sqlEnableDiskspace = array(true, "SELECT * FROM zpanel_core.z_accounts JOIN (zpanel_core.z_bandwidth, zpanel_core.z_quotas, zpanel_core.z_vhosts) ON (z_accounts.ac_id_pk=z_bandwidth.bd_acc_fk AND z_accounts.ac_package_fk=z_quotas.qt_package_fk AND z_accounts.ac_id_pk=z_vhosts.vh_acc_fk) WHERE (z_bandwidth.bd_diskamount_bi < z_quotas.qt_diskspace_bi) AND (z_vhosts.vh_deleted_ts IS NULL) AND (z_bandwidth.bd_month_in = " . GetSystemOption('current_month') . ")  AND (z_vhosts.vh_type_in<>3) OR (z_quotas.qt_diskspace_bi = '0'  AND z_vhosts.vh_deleted_ts IS NULL)  AND (z_bandwidth.bd_month_in = " . GetSystemOption('current_month') . ")  AND (z_vhosts.vh_type_in<>3)");
# Disable Diskspace
$sqlDisableDiskspace = array("$disk", "SELECT * FROM zpanel_core.z_accounts JOIN (zpanel_core.z_bandwidth, zpanel_core.z_quotas, zpanel_core.z_vhosts) ON (z_accounts.ac_id_pk=z_bandwidth.bd_acc_fk AND z_accounts.ac_package_fk=z_quotas.qt_package_fk AND z_accounts.ac_id_pk=z_vhosts.vh_acc_fk) WHERE (z_bandwidth.bd_diskamount_bi >= z_quotas.qt_diskspace_bi) AND (z_vhosts.vh_deleted_ts IS NULL) AND (z_quotas.qt_diskspace_bi != '0'  AND z_vhosts.vh_deleted_ts IS NULL) AND (z_bandwidth.bd_month_in = " . GetSystemOption('current_month') . ") AND (z_vhosts.vh_type_in<>3)");
# Disable Bandwidth
$sqlDisableBandwidth = array("$bandwidth", "SELECT * FROM zpanel_core.z_accounts JOIN (zpanel_core.z_bandwidth, zpanel_core.z_quotas, zpanel_core.z_vhosts) ON (z_accounts.ac_id_pk=z_bandwidth.bd_acc_fk AND z_accounts.ac_package_fk=z_quotas.qt_package_fk AND z_accounts.ac_id_pk=z_vhosts.vh_acc_fk) WHERE (z_bandwidth.bd_transamount_bi >= z_quotas.qt_bandwidth_bi) AND (z_vhosts.vh_deleted_ts IS NULL) AND (z_quotas.qt_bandwidth_bi != '0'  AND z_vhosts.vh_deleted_ts IS NULL) AND (z_bandwidth.bd_month_in = " . GetSystemOption('current_month') . ") AND (z_vhosts.vh_type_in<>3)");


#enble all bandwidh and diskspace over used
$sqlArrayEnable = array($sqlEnableBandwidth, $sqlEnableDiskspace, $sqlDisableDiskspace, $sqlDisableBandwidth);
foreach($sqlArrayEnable as $sql){
    $sqlResult = mysql_query($sql[1]) or die(TriggerLog(1, $b = "Usage Limiter - query failed - $sql"));
    if($sqlResult){
        if(is_bool($sql[0]) && $sql == true){
            while($rows = mysql_fetch_array($sqlResult)){
                $user = $rows['ac_user_vc'];
                $domaindir = $rows['vh_directory_vc'];
                $domain = $rows['vh_name_vc'];
                $restrict = $rows['vh_restrict_vc'];
                $newdomaindir = buildDomainDir($hosted, $domaindir, $restrict, $temp, $user);
                $vhostfile = setDomainRecord($vhostfile, $domain, $newdomaindir);
            }
        } elseif(is_string($sql[0])){
                while($rows = mysql_fetch_array($sqlResult)){
                    $newdomaindir = buildLimitDir($static, $sql[0]);
                    $domain = $rows['vh_name_vc'];
                    $vhostfile = setDomainRecord($vhostfile, $domain, $newdomaindir);
                }
        } else {
        TriggerLog(1, $b = "Usage Limiter - failed at ".__LINE__."");
            }
        }
}

setVhost($vhostfile, true, $vhost);
//restart apache
restartApache();


# Mod_bw bandwitdh and connection limiting apache module
# Get all vhost records that have the modified flag for mod_bw
$sqlvhosts = "SELECT * FROM zpanel_core.z_accounts JOIN (zpanel_core.z_quotas, zpanel_core.z_vhosts, zpanel_core.z_packages) ON (z_accounts.ac_package_fk=z_quotas.qt_package_fk AND z_accounts.ac_id_pk=z_vhosts.vh_acc_fk AND z_accounts.ac_package_fk=z_packages.pk_id_pk) WHERE (z_vhosts.vh_deleted_ts IS NULL) AND (z_vhosts.vh_type_in<>3) AND (z_quotas.qt_modified_in = 1)";
$resultvhosts = mysql_query($sqlvhosts) or die('SQL error from daemon.php mod_bw section around line 723 - ' . mysql_error());
while ($rowvhosts = mysql_fetch_array($resultvhosts)) {
	$search = $rowvhosts['vh_name_vc'];
    $user = $rowvhosts['ac_user_vc'];
    $startpos = strpos($vhostfile, "# DOMAIN: $search");
    $endpos = strpos($vhostfile, "# END DOMAIN: $search");
    $endposlength = "# END DOMAIN: $search";
    $endposlength = strlen($endposlength);
    $vhostrecord = substr($vhostfile, $startpos, ($endpos - $startpos) + $endposlength);
	$replacementtest = "!(.*)Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_(.*).conf!";
    $matchresult = preg_match($replacementtest, $vhostrecord, $matches);
	# Insert the mod_bw config for the first time if mod_bw is enabled for the package
	if(empty($matchresult)){
		if ($rowvhosts['qt_bwenabled_in'] == 1){
		$find = '/<virtualhost \*\:80>/';
		preg_match($find, $vhostrecord, $matches);
		$replacement = $matches[0] ."
Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_". $rowvhosts['pk_name_vc'] .".conf";

    $newvhostrecord = preg_replace($find, $replacement, $vhostrecord);
    $vhostfile = substr_replace($vhostfile, $newvhostrecord, $startpos, ($endpos - $startpos) + $endposlength);
    $edited = 1;
		}
	} else {
	# Mod_bw config exists and is enabled and has been modified.
		if ($rowvhosts['qt_bwenabled_in'] == 1){
		# If there is a change in the package
		$find = "!(.*)Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_(.*)\.conf!";
		$matchresult = preg_match($find, $vhostrecord, $matches);
			if(!empty($matchresult)){
				$replacement = "Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_". $rowvhosts['pk_name_vc'] .".conf";
			}
		# If mod_bw needs to be re-enabled
		$find = "!(.*)Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_(.*)\.conf!";
		$matchresult = preg_match($find, $vhostrecord, $matches);
			if(!empty($matchresult)){
				$replacement = "Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_". $rowvhosts['pk_name_vc'] .".conf";
			}
		} else {
		# Mod_bw has been disabled for vhost 
		$find = "!(.*)Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_(.*)\.conf!";
		$matchresult = preg_match($find, $vhostrecord, $matches);
			if(!empty($matchresult)){
			$replacement = "#Include ". GetSystemOption('mod_bw') ."mod_bw/mod_bw_". $rowvhosts['pk_name_vc'] .".conf";
			}
		}
    $newvhostrecord = preg_replace($find, $replacement, $vhostrecord);
    $vhostfile = substr_replace($vhostfile, $newvhostrecord, $startpos, ($endpos - $startpos) + $endposlength);
    $edited = 1;
	}
}

# Update the quota table and set mofified flag to 0 for all packages
$sqlqtmodified = "UPDATE zpanel_core.z_quotas SET qt_modified_in = 0";
$resultqtmodified = mysql_query($sqlqtmodified) or die('SQL error from daemon.php mod_bw section around line 743 - ' . mysql_error());


#  Changes and Restart
    if(setVhost($vhostfile, true, $vhost)){
        if(restartApache()){
            TriggerLog(1, $b = "> Apache web server has been restarted by the bw_mod");
            } else {
		TriggerLog(1, $b = "> Apache web server reboot failed due to the bm_mod restart");
            }
    }


#####################################################################################################################################
# Generates Webalizer stats for the domain!                                                                                         #
#####################################################################################################################################
$sql = "SELECT * FROM z_vhosts LEFT JOIN z_accounts ON z_vhosts.vh_acc_fk=z_accounts.ac_id_pk WHERE vh_deleted_ts IS NULL";
$domains = DataExchange("r", $z_db_name, $sql);
$row_domains = mysql_fetch_assoc($domains);
$totalRows_domains = DataExchange("t", $z_db_name, $sql);
if ($totalRows_domains > 0) {
    do {
        # Check to ensure folders exists ready for the webalizer reports etc....
        if (!file_exists(GetSystemOption('webalizer_reps') . $row_domains['ac_user_vc'] . "")) {
            @mkdir(GetSystemOption('webalizer_reps') . $row_domains['ac_user_vc'] . "", 777);
        }
        if (!file_exists(GetSystemOption('webalizer_reps') . $row_domains['ac_user_vc'] . "/" . $row_domains['vh_name_vc'])) {
            @mkdir(GetSystemOption('webalizer_reps') . $row_domains['ac_user_vc'] . "/" . $row_domains['vh_name_vc'], 777);
        }
        $runcommand = ChangeSafeSlashesToWin(GetSystemOption('webalizer_exe') . " -o " . GetSystemOption('webalizer_reps') . $row_domains['ac_user_vc'] . "/" . $row_domains['vh_name_vc'] . " -d -F clf -n " . $row_domains['vh_name_vc'] . "  " . GetSystemOption('logfile_dir') . $row_domains['ac_user_vc'] . "/" . $row_domains['vh_name_vc'] . "-access.log");
        system($runcommand);
        echo "<br>".$runcommand;
    } while ($row_domains = mysql_fetch_assoc($domains));
    TriggerLog(1, $b = "> Webalizer domain statistics have been generated.");
}

#####################################################################################################################################
# Just log to say that the ZPanel daemon has finished doing its tasks...                                                            #
#####################################################################################################################################
TriggerLog(1, $b = "= ZPanel daemon finished execution....");


#####################################################################################################################################
# Send data to a zCommander server if one has been specified! - Added in ZPanel 5.1.0                                               #
#####################################################################################################################################
if (GetSystemOption('zms_host') <> "") {
    # Get hosting resource totals...
    $sql = "SELECT COUNT(*) AS tvalue FROM z_accounts WHERE ac_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_zpaccounts = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_cronjobs WHERE ct_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_crontasks = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_distlists WHERE dl_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_distlists = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_forwarders WHERE fw_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_forwarders = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_ftpaccounts WHERE ft_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_ftpaccs = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_mailboxes WHERE mb_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_mailboxes = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_mysql WHERE my_deleted_ts IS NULL;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_mysqldbs = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_vhosts WHERE vh_deleted_ts IS NULL AND vh_type_in=1;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_domains = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_vhosts WHERE vh_deleted_ts IS NULL AND vh_type_in=2;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_subdoms = $row_res['tvalue'];

    $sql = "SELECT COUNT(*) AS tvalue FROM z_vhosts WHERE vh_deleted_ts IS NULL AND vh_type_in=3;";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_parkeddoms = $row_res['tvalue'];

    $sql = "SELECT * FROM z_bandwidth WHERE bd_month_in = '" . GetSystemOption('current_month') . "';";
    $res = DataExchange("r", $z_db_name, $sql);
    $row_res = mysql_fetch_assoc($res);
    $no_bandwidth = 0;
    $no_diskspace = 0;
    if ($totalRows_res > 0) {
        do {
            # Add up all accounts bandwidth and disk space to calculate the total!
            $no_diskspace = ($no_diskspace + $row_res['bd_diskamount_bi']);
            $no_bandwidth = ($no_bandwidth + $row_res['bd_transamount_bi']);
        } while ($row_res = mysql_fetch_assoc($res));
    } else {
        $no_bandwidth = 0;
        $no_diskspace = 0;
    }

    # Now we contact the zCommander server web communicator system to pass the infomation...
    @readfile("http://" . GetSystemOption('zms_host') . "/communicator.php?services=" . base64_encode("" . GetSystemOption('install_date') . "|||" . CheckServiceUp(80) . "|||" . CheckServiceUp(21) . "|||" . CheckServiceUp(25) . "|||" . CheckServiceUp(110) . "|||" . CheckServiceUp(143) . "|||" . CheckServiceUp(3306) . "") . "");
    @readfile("http://" . GetSystemOption('zms_host') . "/communicator.php?software=" . base64_encode("" . GetSystemOption('install_date') . "|||" . ShowPHPVersion() . "|||" . ShowMySQLVersion() . "|||" . GetSystemOption("zpanel_version") . "") . "");
    @readfile("http://" . GetSystemOption('zms_host') . "/communicator.php?totals=" . base64_encode("" . GetSystemOption('install_date') . "|||" . $no_zpaccounts . "|||" . $no_crontasks . "|||" . $no_distlists . "|||" . $no_forwarders . "|||" . $no_ftpaccs . "|||" . $no_mailboxes . "|||" . $no_mysqldbs . "|||" . $no_domains . "|||" . $no_subdoms . "|||" . $no_parkeddoms . "|||" . $no_bandwidth . "|||" . $no_diskspace . "") . "");
    @readfile("http://" . GetSystemOption('zms_host') . "/communicator.php?misc=" . base64_encode("" . GetSystemOption('install_date') . "|||" . GetSystemOption('server_admin') . "|||" . $_SERVER['SERVER_ADDR'] . "|||" . GetServerUptime() . "") . "");
    
}
?>
