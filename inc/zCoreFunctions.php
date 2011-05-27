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
function GetSystemOption($a=" ") {
    # DESCRIPTION: Gets and returns a value from the 'z_settings' table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $setting_name = $a;
    include('conf/zcnf.php');
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

function Cleaner($a="i", $b=" ") {
    # DESCRIPTION: Cleans and makes safe any SQL transactions bi-directionally.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $mode = $a;
    $string = $b;
    if ($mode <> 'o') {
        $fretval = addslashes($string);
    } else {
        $fretval = stripslashes($string);
    }
    return $fretval;
}

function TriggerLog($a=0, $b="No details.") {
    # DESCRIPTION: Logs an event, for debugging or audit purposes in the 'z_logs' table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $acc_key = $a;
    $log_details = Cleaner('i', $b);
    include('conf/zcnf.php');
    $sql = "INSERT INTO z_logs (lg_acc_fk, lg_when_ts, lg_ipaddress_vc, lg_details_tx) VALUES (" . $acc_key . ", '" . time() . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $log_details . "')";
    DataExchange("w", $z_db_name, $sql);
    return;
}

function MakeDateFromTimestamp($a=0) {
    # DESCRIPTION: Displays a user-friendly date and time from a given timestamp and uses the ZPanel date format set in the cp_settings table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $timestamp = $a;
    $dformat = GetSystemOption('zpanel_df');
    $fretval = date($dformat, $timestamp);
    return $fretval;
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
    $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    if (!$fp) {
        $fretval = 0;
    } else {
        $fretval = 1;
    }
    return $fretval;
}

function ChangeSafeSlashesToWin($a=0) {
    # DESCRIPTION: Changes MySQL safe directory slashes '\\' to Windows PHP slashes '/'.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $path = $a;
    $fretval = str_replace("/", "\\", $path);
    return $fretval;
}

function ShowApacheVersion() {
    # DESCRIPTION: Returns the ApacheVersion number.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if (preg_match('|Apache\/(\d+)\.(\d+)\.(\d+)|', apache_get_version(), $apver)) {
        $apacheversion = str_replace("Apache/", "", $apver[0]);
        $fretval = $apacheversion;
    } else {
        $fretval = "Not found";
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

function ShowServerPlatform() {
    # DESCRIPTION: Returns the Server OS platform.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        case 'WIN':
            return 'Windows';
            break;
        case 'LIN':
            return 'Linux';
            break;
        case 'FRE':
            return 'FreeBSD';
            break;
    }
}

function ShowKernelVersion($a) {
    # DESCRIPTION: Returns the Linux OS Kernal Version, Only works if running linux!
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $serverplatform = $a;
    if ($serverplatform == 'Linux') {
        $version = exec('uname -r');
        $fretval = $version;
    } else {
        $fretal = "N/A";
    }
    return $fretval;
}

function SendAccountMail($a, $b, $c) {
    # DESCRIPTION: Sends an email to an email address.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $to = $a;
    $subject = $b;
    $message = $c;
    $from = GetSystemOption('server_email');
    $headers = 'From: ' . $from . '' . "\r\n" .
            'Reply-To: ' . $from . '' . "\r\n" .
            'X-Mailer: ZPanel-PHP/' . phpversion();
    if (mail($to, $subject, $message, $headers)) {
        $fretval = 1;
    } else {
        $fretval = 0;
    }
    return $fretval;
}

function FormatFileSize($a) {
    # DESCRIPTION: Formats bytes into a human readable string.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $size = $a;
    if ($size / 1024000000 > 1) {
        $fretval = round($size / 1024000000, 1) . ' GB';
    } elseif ($size / 1024000 > 1) {
        $fretval = round($size / 1024000, 1) . ' MB';
    } elseif ($size / 1024 > 1) {
        $fretval = round($size / 1024, 1) . ' KB';
    } else {
        $fretval = round($size, 1) . ' bytes';
    }
    return $fretval;
}

function GetQuotaUsages($a, $b=0) {
    # DESCRIPTION: Returns the current usage of a particular resource.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $resource = $a;
    $acc_key = $b;
    include('conf/zcnf.php');
    if ($resource == 'domains') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_vhosts WHERE vh_acc_fk=" . $acc_key . " AND vh_type_in=1 AND vh_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'subdomains') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_vhosts WHERE vh_acc_fk=" . $acc_key . " AND vh_type_in=2 AND vh_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'parkeddomains') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_vhosts WHERE vh_acc_fk=" . $acc_key . " AND vh_type_in=3 AND vh_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'mailboxes') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_mailboxes WHERE mb_acc_fk=" . $acc_key . " AND mb_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'forwarders') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_forwarders WHERE fw_acc_fk=" . $acc_key . " AND fw_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'distlists') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_distlists WHERE dl_acc_fk=" . $acc_key . " AND dl_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'ftpaccounts') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_ftpaccounts WHERE ft_acc_fk=" . $acc_key . " AND ft_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'mysql') {
        $fretval = DataExchange("l", $z_db_name, "SELECT COUNT(*) AS amount FROM z_mysql WHERE my_acc_fk=" . $acc_key . " AND my_deleted_ts IS NULL");
        $fretval = $fretval['amount'];
    }
    if ($resource == 'diskspace') {
        $fretval = DataExchange("l", $z_db_name, "SELECT bd_diskamount_bi FROM z_bandwidth WHERE bd_acc_fk=" . $acc_key . " AND bd_month_in=" . date("Ym", time()) . "");
        $fretval = $fretval['bd_diskamount_bi'];
    }
    if ($resource == 'bandwidth') {
        $fretval = DataExchange("l", $z_db_name, "SELECT bd_transamount_bi FROM z_bandwidth WHERE bd_acc_fk=" . $acc_key . " AND bd_month_in=" . date("Ym", time()) . "");
        $fretval = $fretval['bd_transamount_bi'];
    }
    return $fretval;
}

function GetFullURL() {
    # DESCRIPTION: Returns the full URL of the current PHP script.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if ($_SERVER['HTTPS'] == 'on') {
        $protocol = 'https';
    } else {
        $protocol = 'http';
    }
    $fretval = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
    if (ShowServerPlatform() == "Linux") {
        #BEGIN UPTIME
        $uptime = trim(exec("cat /proc/uptime"));
        $uptime = explode(" ", $uptime);
        $uptime = $uptime[0];
        $day = 86400;
        $days = floor($uptime / $day);
        $utdelta = $uptime - ($days * $day);
        $hour = 3600;
        $hours = floor($utdelta / $hour);
        $utdelta-=$hours * $hour;
        $minute = 60;
        $minutes = floor($utdelta / $minute);
        $days = CheckForNullValue($days != 1, $days . ' days', $days . ' day');
        $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
        $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
        $fretval = $days . ", " . $hours . ", " . $minutes . "";
    } elseif (ShowServerPlatform() == "Windows") {
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
        $days = CheckForNullValue($days != 1, $days . ' days', $days . ' day');
        $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
        $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
        $fretval = $days . ", " . $hours . ", " . $minutes . "";
    } elseif (ShowServerPlatform() == "FreeBSD") {
		$uptime = explode( " ", exec("/sbin/sysctl -n kern.boottime") );
		$uptime = str_replace( ",", "", $uptime[3]);
		$uptime = time() - $uptime;
		$min   = $uptime / 60;
		$hours = $min / 60;
		$days  = floor( $hours / 24 );
		$hours = floor( $hours - ($days * 24) );
		$minutes   = floor( $min - ($days * 60 * 24) - ($hours * 60) );
        $days = CheckForNullValue($days != 1, $days . ' days', $days . ' day');
        $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
        $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
        $fretval = $days . ", " . $hours . ", " . $minutes . "";
    } else {
        $fretval = "Unsupported Operating System";
    }
    return $fretval;
}

function CheckModuleCatForPerms($a, $permissionset) {
    # DESCRIPTION: Checks to see if the module category has the correct permissions before allowing the user to see it.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $permission_required = $a;
    $freturn = 0;
    if ($permission_required == 'user') {
        $freturn = 1;
    }
    if ($permission_required == 'reseller') {
        if ($permissionset['pr_reseller_in'] == 1) {
            $freturn = 1;
        } else {
            $freturn = 0;
        }
    }
    if ($permission_required == 'admin') {
        if ($permissionset['pr_admin_in'] == 1) {
            $freturn = 1;
        } else {
            $freturn = 0;
        }
    }
    return $freturn;
}

function GetCheckboxValue($a) {
    # DESCRIPTION: Checks the value of a checkbox and returns if 0 if not ticked or 1 if it is ticked.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $checkbox_status = $a;
    if ($checkbox_status == 1) {
        $fretval = 1;
    } else {
        $fretval = 0;
    }
    return $fretval;
}

function GetNormalModuleURL($a) {
    # DESCRIPTION: Returns the correct Module Loader URL.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $spliton = explode("&", $a);
    $fretval = $spliton['0'] . "&" . $spliton['1'];
    return $fretval;
}

function GenerateRandomPassword($a=9, $b=0) {
    # DESCRIPTION: Generate a random password.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $length = $a;
    $strength = $b;
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }
    $fretval = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $fretval .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $fretval .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $fretval;
}

function RemoveDirectory($a) {
    # DESCRIPTION: Removes an entire directory.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if ($dir = @opendir($a)) {
        while (($f = readdir($dir)) !== false) {
            if ($f > '0' and filetype($a . $f) == "file") {
                unlink($a . $f);
            } elseif ($f > '0' and filetype($a . $f) == "dir") {
                RemoveDirectory($a . $f . "\\");
            }
        }
        closedir($dir);
        rmdir($a);
    }
}

function ShowAccountType($a) {
    # DESCRIPTION: Shows the account type based on the permission set.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $permissionset = $a;
    if ($permissionset['pr_admin_in'] == 1) {
        $fretval = "Admin";
    } elseif ($permissionset['pr_reseller_in'] == 1) {
        $fretval = "Reseller";
    } else {
        $fretval = "User";
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
    $i = 1;
    $each_rec = 0;
    $cur_month = date("/M/Y");
    while ($i < $sizerecs) {
        $ip = $records[$i];
        $all = $records[$i + 1];
        // Parse other fields
        preg_match("/\[(.+)\]/", $all, $match);
        $access_time = $match[1];
        $all = str_replace($match[1], "", $all);
        preg_match("/\"GET (.[^\"]+)/", $all, $match);
        $http = $match[1];
        $link = explode(" ", $http);
        $all = str_replace("\"GET $match[1]\"", "", $all);
        preg_match("/([0-9]{3})/", $all, $match);
        $success_code = $match[1];
        $all = str_replace($match[1], "", $all);
        preg_match("/\"(.[^\"]+)/", $all, $match);
        $ref = $match[1];
        $all = str_replace("\"$match[1]\"", "", $all);
        preg_match("/\"(.[^\"]+)/", $all, $match);
        $browser = $match[1];
        $all = str_replace("\"$match[1]\"", "", $all);
        preg_match("/([0-9]+\b)/", $all, $match);
        $bytes = $match[1];
        $all = str_replace($match[1], "", $all);
        // The following code is to collect bandwidth usage per user and assign each usage to the user,
        // Successful match, now we need to check if the access date matches the current month.
        // If yes, we'll sum all the sizes from access pages only from the current month
        // and put in database... 
        $arb[$j] = $arb[$j] + $bytes;
        // Advance to next record
        $i = $i + 1;
        $each_rec++;
    }
    return $arb[$j];
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

function RemoveDoubleSlash($var) {
    # DESCRIPTION: Remove the last character from a string.
    # FUNCTION RELEASE: 6.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $retval = str_replace("\\\\", "\\", $var);
    return $retval;
}

function GetPrefdLang($langsetting) {
    # DESCRIPTION: Get preferred language.
    # FUNCTION RELEASE: 6.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    if (!empty($langsetting)) {
        if (file_exists(GetSystemOption('zpanel_root') . 'lang/' . $langsetting . '_override.php')) {
            $language = $langsetting . '_override';
        } else {
            $language = $langsetting;
        }
    } else {
        if (file_exists(GetSystemOption('zpanel_root') . 'lang/' . GetSystemOption('zpanel_lang') . '_override.php')) {
            $language = GetSystemOption('zpanel_lang') . '_override';
        } else {
            $language = GetSystemOption('zpanel_lang');
        }
    }
    return $language;
}

function IsValidDomainName($a) {
    # DESCRIPTION: Check for invalid characters in domain creation.
    # FUNCTION RELEASE: 6.0.0
    # FUNCTION AUTHOR: RusTus
    $part = explode(".", $a);
    foreach ($part as $check) {
        if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $check) || preg_match('/-$/', $check)) {
            return false;
        }
    }
    return true;
}

function ShowServerOSName() {
    # DESCRIPTION: Gets and returns the Operating system OS name.
    # FUNCTION RELEASE: 10.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $uname = strtolower(php_uname());
    $os = "";
    if (strpos($uname, "darwin") !== false) {
        $os = "MacOSX";
    } else if (strpos($uname, "win") !== false) {
        $os = "Windows";
    } else if (strpos($uname, "freebsd") !== false) {
        $os = "FreeBSD";
    } else if (strpos($uname, "openbsd") !== false) {
        $os = "OpenBSD";
    } else {
        $list = @parse_ini_file("lib/zpanel/os.ini", true);
        foreach ($list as $section => $distribution) {
            if (!isset($distribution["Files"])) {
                
            } else {
                $intBytes = 4096;
                $intLines = 0;
                $intCurLine = 0;
                $strFile = "";
                foreach (preg_split("/;/", $distribution["Files"], -1, PREG_SPLIT_NO_EMPTY) as $filename) {
                    if (file_exists($filename)) {
                        if (isset($distribution["Name"])) {
                            $os = $distribution["Name"];
                        }
                    }
                }
                if ($os == null) {
                    $os = "Unknown";
                }
            }
        }
    }
    return $os;
}

function ChangeWinSlashesToNIX($a=0) {
    # DESCRIPTION: Changes WindowsFS directory slashes '\' to *NIX FS slashes '/'.
    # FUNCTION RELEASE: 10.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $path = $a;
    $fretval = str_replace("\\", "/", $path);
    return $fretval;
}

?>
