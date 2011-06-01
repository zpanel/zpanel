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

if(ShowServerPlatform()=="Windows"){
    $filezilla_reload = "\"" . GetSystemOption('filezilla_root') . "FileZilla server.exe\" /reload-config";
} else {
    $filezilla_reload = "/etc/zpanel/bin/zsudo service " .GetSystemOption('lsn_proftpd'). " reload";
}

# Lets get database ID's for all packages.
$sql = "SELECT * FROM z_ftpaccounts WHERE ft_acc_fk=" . $useraccount['ac_id_pk'] . " AND ft_deleted_ts IS NULL";
$listftpaccounts = DataExchange("r", $z_db_name, $sql);
$rowftpaccounts = mysql_fetch_assoc($listftpaccounts);
$totalftpaccounts = DataExchange("t", $z_db_name, $sql);



if ($_POST['inAction'] == 'NewFTPAccount') {
    # Declare the domain name as a string...
    $username = $_POST['inUsername'];
    $password = $_POST['inPassword'];
    $returnurl = $_POST['inReturn'];
    $destination = $_POST['inDestination'];
    $access_type = $_POST['inAccess'];

    # Check to make sure the username and password is not blank before we go any further...
    if ($username == '' || $password == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }

    # Just need to check that an account doesnt already exist with the same username....
    $sql = "SELECT * FROM z_ftpaccounts WHERE ft_user_vc='" . Cleaner('i', $username) . "' AND ft_deleted_ts IS NULL";
    $existsftp = DataExchange("t", $z_db_name, $sql);

    # If an account alrady exists we are going to dump the user abck with an error message.
    if ($existsftp > 0) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
        exit;
    }

    # Check to see if its a new home directory or use a current one...
    if ($_POST['inAutoHome'] == 1) {
        $homedirectoy_to_use = "/" . str_replace(".", "_", Cleaner('i', $username));
        # Create the new home directory... (If it doesnt already exist.)
        if (!file_exists(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . homedirectoy_to_use . "/")) {
            @mkdir(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/", 777);
            @chmod(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use . "/", 0777);
        }
    } else {
        $homedirectoy_to_use = "/" . $destination;
    }

    #Here we write the changes to the FileZilla system file...
    $accessmode = "Read List";
    if ($_POST['inAccess'] == 'RO') {
        $permissionset = "<Option Name=\"FileRead\">1</Option>
	<Option Name=\"FileWrite\">0</Option>
	<Option Name=\"FileDelete\">0</Option>
	<Option Name=\"FileAppend\">0</Option>
	<Option Name=\"DirCreate\">0</Option>
	<Option Name=\"DirDelete\">0</Option>
	<Option Name=\"DirList\">1</Option>
	<Option Name=\"DirSubdirs\">1</Option>";
        $accessmode = "Read access";
    }
    if ($_POST['inAccess'] == 'WO') {
        $permissionset = "<Option Name=\"FileRead\">0</Option>
	<Option Name=\"FileWrite\">1</Option>
	<Option Name=\"FileDelete\">0</Option>
	<Option Name=\"FileAppend\">0</Option>
	<Option Name=\"DirCreate\">1</Option>
	<Option Name=\"DirDelete\">0</Option>
	<Option Name=\"DirList\">0</Option>
	<Option Name=\"DirSubdirs\"0</Option>";
        $accessmode = "Write access";
    }
    if ($_POST['inAccess'] == 'RW') {
        $permissionset = "<Option Name=\"FileRead\">1</Option>
	<Option Name=\"FileWrite\">1</Option>
	<Option Name=\"FileDelete\">1</Option>
	<Option Name=\"FileAppend\">1</Option>
	<Option Name=\"DirCreate\">1</Option>
	<Option Name=\"DirDelete\">1</Option>
	<Option Name=\"DirList\">1</Option>
	<Option Name=\"DirSubdirs\">1</Option>";
        $accessmode = "Full access";
    }
    $permission = "ALL";
    $status = 1;


    # Call the API!
    $api_resault = zapi_ftpaccount_add(GetSystemOption('filezilla_root'), $username, $password, GetSystemOption('zpanel_version'), ChangeSafeSlashesToWin(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . $homedirectoy_to_use), $permissionset);
    if ($api_resault == false) {
        # FTP account was not added!
    } else {
        $reboot = system($filezilla_reload);
    }

    # If all has gone well we need to now create the domain in the database...
    $sql = "INSERT INTO z_ftpaccounts (ft_acc_fk,
										ft_user_vc,
										ft_directory_vc,
										ft_access_vc,
										ft_created_ts) VALUES (
									" . $acc_fk . ",
									'" . Cleaner('i', $username) . "',
									'" . Cleaner('i', $homedirectoy_to_use) . "',
									'" . Cleaner('i', $access_type) . "',
									" . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    # Now we add some infomation to the system log.
    TriggerLog($useraccount['ac_id_pk'], $b = "New FTP account has been added by the user (" . Cleaner('i', $username) . ").");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'reset') {
    $sql = "SELECT * FROM z_ftpaccounts WHERE ft_user_vc='" . $_POST['inAccount'] . "' AND ft_acc_fk=" . $acc_fk . " AND ft_deleted_ts IS NULL";
    $listisowner = DataExchange("r", $z_db_name, $sql);
    $rowisowner = mysql_fetch_assoc($listisowner);
    $totalisowner = DataExchange("t", $z_db_name, $sql);
    if ($totalisowner > 0) {

        # Call the API!
        $api_resault = zapi_ftpaccount_edit(GetSystemOption('filezilla_root'), $_POST['inAccount'], $_POST['inPassword']);
        if ($api_resault == false) {
            # The cronjob was not added for some reason!
            TriggerLog($useraccount['ac_id_pk'], $b = "FTP password for user (" . Cleaner('i', $_POST['inAccount']) . ") could not be reset.");
        } else {
            TriggerLog($useraccount['ac_id_pk'], $b = "FTP password for user (" . Cleaner('i', $_POST['inAccount']) . ") has been reset.");
            $reboot = system($filezilla_reload);
        }
    } else {
        TriggerLog($useraccount['ac_id_pk'], $b = "FTP password for user (" . Cleaner('i', $_POST['inAccount']) . ") not been reset as you are not the owner.");
    }
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'delete') {
    # User has choosen to delete the task...
    do {
        #Check to make sure this isnt a password reset...
        if (isset($_POST['inReset_' . $rowftpaccounts['ft_id_pk']])) {
            header("location: " . GetNormalModuleURL($returnurl) . "&reset=" . $rowftpaccounts['ft_user_vc'] . "");
            exit;
        }
        # Ok so lets just go and delete the FTP account now...
        if (isset($_POST['inDelete_' . $rowftpaccounts['ft_id_pk']])) {

            # Call the API!
            $api_resault = zapi_ftpaccount_remove(GetSystemOption('filezilla_root'), $rowftpaccounts['ft_user_vc']);
            if ($api_resault == false) {
                # The cronjob was not added for some reason!
                TriggerLog($useraccount['ac_id_pk'], $b = "FTP user (" . $rowftpaccounts['ft_user_vc'] . ") could not be fully deleted.");
            } else {
                TriggerLog($useraccount['ac_id_pk'], $b = "FTP user (" . $rowftpaccounts['ft_user_vc'] . ") has been deleted.");
                $reboot = system($filezilla_reload);
            }


            # Remove the FTP account from the MySQL database now..
            $sql = "UPDATE z_ftpaccounts SET ft_deleted_ts=" . time() . " WHERE ft_id_pk=" . $rowftpaccounts['ft_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User FTP account ID: " . $rowftpaccounts['ft_id_pk'] . " was deleted.");
        }
    } while ($rowftpaccounts = mysql_fetch_assoc($listftpaccounts));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}
?>
