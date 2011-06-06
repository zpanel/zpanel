<?php
ini_set("memory_limit", "64M");
error_reporting(0); 
set_time_limit(0); 
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

# Lets grab and archive the user's web data....
$homedir = GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'];
$backupname = $useraccount['ac_user_vc'] . "_" . date("dmy_Gi", time());
$dbstamp = date("dmy_Gi", time());

# We now see what the OS is before we work out what compression command to use..
if (ShowServerPlatform() == "Windows") {
    $resault = exec(ChangeSafeSlashesToWin(GetSystemOption('7z_exe') . " a -tzip -y-r " . GetSystemOption('temp_dir') . $backupname . ".zip " . $homedir . ""));
} else {
    $resault = exec(GetSystemOption('7z_exe') . " -r9 " . GetSystemOption('temp_dir') . $backupname . " " . $homedir . "/*");
    @chmod(GetSystemOption('temp_dir') . $backupname . ".zip", 0777);
}

# Now lets backup all MySQL datbases for the user and add them to the archive...
$sql = "SELECT * FROM z_mysql WHERE my_acc_fk=" . $useraccount['ac_id_pk'] . " AND my_deleted_ts IS NULL";
$mysql = DataExchange("r", $z_db_name, $sql);
$row_mysql = mysql_fetch_assoc($mysql);
$totalmysql = DataExchange("t", $z_db_name, $sql);
if ($totalmysql > 0) {
    do {
        $bkcommand = GetSystemOption('mysqldump_exe') . " -h " . $z_db_host . " -u " . $z_db_user . " -p" . $z_db_pass . " --no-create-db " . $row_mysql['my_name_vc'] . " > " . GetSystemOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql";
        passthru($bkcommand);
        # Add it to the ZIP archive...
        if (ShowServerPlatform() == "Windows") {
            $resault = exec(ChangeSafeSlashesToWin(GetSystemOption('7z_exe') . " u " . GetSystemOption('temp_dir') . $backupname . ".zip " . GetSystemOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql"));
        } else {
            $resault = exec(GetSystemOption('7z_exe') . " " . GetSystemOption('temp_dir') . $backupname . "  " . GetSystemOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
        }
        unlink(GetSystemOption('temp_dir') . $row_mysql['my_name_vc'] . "_" . $dbstamp . ".sql");
    } while ($row_mysql = mysql_fetch_assoc($mysql));
}

TriggerLog($useraccount['ac_id_pk'], "User full hosting account backup was created.");

# Now we send the output...
$file = GetSystemOption('temp_dir') . $backupname . ".zip"; 
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Description: File Transfer');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/force-download'); 
header('Content-Length: ' . filesize($file)); 
header('Content-Disposition: attachment; filename=' . $backupname . '.zip');  
readfile_chunked($file);
unlink(GetSystemOption('temp_dir') . $backupname . ".zip ");

function readfile_chunked($filename) { 
 $chunksize = 1*(1024*1024);
 $buffer = ''; 
 $handle = fopen($filename, 'rb'); 
 if ($handle === false) { 
   return false; 
 } 
 while (!feof($handle)) { 
   $buffer = fread($handle, $chunksize); 
   print $buffer; 
 } 
 return fclose($handle); 
} 

header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
exit;
?>
