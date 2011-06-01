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
$sql = "SELECT * FROM z_accounts WHERE ac_reseller_fk=" . $useraccount['ac_id_pk'] . " AND ac_deleted_ts IS NULL";
$listclients = DataExchange("r", $z_db_name, $sql);
$rowclients = mysql_fetch_assoc($listclients);

if ($_POST['inAction'] == 'new') {
    $username = $_POST['inUserName'];
    $packagename = $_POST['inPackage'];
    # Check for spaces and remove if found...
    $username = str_replace(' ', '', $username);
    # Check to make sure the username is not blank before we go any further...
    if ($username == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
    # Check to make sure the packagename is not blank before we go any further...
    if ($packagename == '' or strstr($packagename, '--')) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=error");
        exit;
    }
    # If the user submitted a 'new' request then we will simply add the client to the database...
    $sql = "INSERT INTO z_accounts (ac_user_vc,
									ac_pass_vc,
									ac_package_fk,
									ac_reseller_fk,
									ac_created_ts) VALUES (
									'" . Cleaner('i', $username) . "',
									'" . md5($_POST['inPassword']) . "',
									" . Cleaner('i', $_POST['inPackage']) . ",
									" . $acc_fk . ",
									" . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    # Now lets pull back the client ID so that we can add their personal address details etc...
    $sql = "SELECT * FROM z_accounts WHERE ac_reseller_fk=" . $acc_fk . " ORDER BY ac_id_pk DESC";
    $clientid = DataExchange("l", $z_db_name, $sql);

    $sql = "INSERT INTO z_personal (ap_acc_fk,
									ap_fullname_vc,
									ap_email_vc,
									ap_address_tx,
									ap_postcode_vc,
									ap_phone_vc) VALUES (
									" . $clientid['ac_id_pk'] . ",
									'" . Cleaner('i', $_POST['inFullName']) . "',
									'" . Cleaner('i', $_POST['inEmailAddress']) . "',
									'" . Cleaner('i', $_POST['inAddress']) . "',
									'" . Cleaner('i', $_POST['inPostCode']) . "',
									'" . Cleaner('i', $_POST['inPhone']) . "')";
    DataExchange("w", $z_db_name, $sql);
    # Now we add an entry into the bandwidth table, for the user for the upcoming month.
    $sql = "INSERT INTO z_bandwidth (bd_acc_fk, bd_month_in, bd_transamount_bi, bd_diskamount_bi) VALUES (" . $clientid['ac_id_pk'] . "," . date("Ym", time()) . ", 0, 0)";
    DataExchange("w", $z_db_name, $sql);

    # Create the MySQL account for the user...
    zapi_mysqluser_add(Cleaner('i', $username), $zdb);
    zapi_mysqluser_setpass(Cleaner('i', $username), Cleaner('i', $_POST['inPassword']), $zdb);

    # Now we create the user's home directory if it doesnt already exsist...
    zapi_filesystem_add(GetSystemOption('hosted_dir') . $username . "/");

    # Create the domain logs folder read for Apache...
    zapi_filesystem_add(GetSystemOption('logfile_dir') . $username . "/");

    # Create a default FTP account if set in the system options...
    if (GetSystemOption('auto_ftpuser') == "true") {
        zapi_ftpaccount_add(GetSystemOption('filezilla_root'), $username, $_POST['inPassword'], GetSystemOption('zpanel_version'), $directorytouse, $permissionset);


        # Get the current account ID for the new user...
        $acc_fk = $clientid['ac_id_pk'];
        $password = $_POST['inPassword'];
        $access_type = "RW";
        $homedirectoy_to_use = "/";
        # Just need to check that an account doesnt already exist with the same username....
        $sql = "SELECT * FROM z_ftpaccounts WHERE ft_user_vc='" . Cleaner('i', $username) . "' AND ft_deleted_ts IS NULL";
        $existsftp = DataExchange("t", $z_db_name, $sql);

        $permissionset = "		<Option Name=\"FileRead\">1</Option>
		<Option Name=\"FileWrite\">1</Option>
		<Option Name=\"FileDelete\">1</Option>
		<Option Name=\"FileAppend\">1</Option>
		<Option Name=\"DirCreate\">1</Option>
		<Option Name=\"DirDelete\">1</Option>
		<Option Name=\"DirList\">1</Option>
		<Option Name=\"DirSubdirs\">1</Option>";



        if ($existsftp < 1) {
            zapi_ftpaccount_add(GetSystemOption('filezilla_root'), $username, $password, GetSystemOption('zpanel_version'), ChangeSafeSlashesToWin(GetSystemOption('hosted_dir') . $username), $permissionset);

            # If all has gone well we need to now create the domain in the database...
            $sql = "INSERT INTO z_ftpaccounts (ft_acc_fk,
											ft_user_vc,
											ft_directory_vc,
											ft_access_vc,
											ft_created_ts) VALUES (
										" . $acc_fk . ",
										'" . Cleaner('i', $username) . "',
										'" . Cleaner('i', "/") . "',
										'" . Cleaner('i', "RW") . "',
										" . time() . ")";
            DataExchange("w", $z_db_name, $sql);
            # Now we add some infomation to the system log.
            TriggerLog($useraccount['ac_id_pk'], $b = "> New FTP account has been created for the new user (" . Cleaner('i', $username) . ").");
        } else {
            TriggerLog($useraccount['ac_id_pk'], $b = "> Could not auto create new FTP user (" . Cleaner('i', $username) . ") as a duplicate account exists on the server.");
        }
    }

    # Send the user account details via. email (if requested)...
    if ($_POST['inSWE'] == 1) {
        if ($_POST['inEmailAddress'] != '') {
            include("lang/" . GetSystemOption('zpanel_lang') . ".php");
            $messagesubject = $lang['225'];
            $messagebody = $lang['224'];
            $messagebody = str_replace("{{username}}", $username, $messagebody);
            $messagebody = str_replace("{{password}}", $_POST['inPassword'], $messagebody);
            $messagebody = str_replace("{{fullname}}", $_POST['inFullName'], $messagebody);
            if (SendAccountMail($_POST['inEmailAddress'], $messagesubject, $messagebody) == 1) {
                TriggerLog(1, "> Welcome email was sent to the user successfully!");
            } else {
                TriggerLog(1, "> Welcome email failed to send to the recipient.\r\rError was:\r\rCheck that you are running an SMTP server and that you can send from this address, likely to be that the SMTP server requires outgoing authentication!");
            }
        }
    }


    TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $clientid['ac_id_pk'] . " (" . Cleaner('i', $username) . ") was created.");
    header("location: " . $returnurl . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'edit') {
# If the user submitted an 'edit' request then we will simply update the accounts and personal tables in the database...
    $sql = "UPDATE z_accounts SET ac_package_fk=" . Cleaner('i', $_POST['inPackage']) . " WHERE ac_id_pk=" . $_POST['inClientID'] . "";
    DataExchange("w", $z_db_name, $sql);
    $sql = "UPDATE z_personal SET ap_fullname_vc='" . Cleaner('i', $_POST['inFullName']) . "',
									ap_email_vc='" . Cleaner('i', $_POST['inEmailAddress']) . "',
									ap_address_tx='" . Cleaner('i', $_POST['inAddress']) . "',
									ap_postcode_vc='" . Cleaner('i', $_POST['inPostCode']) . "',
									ap_phone_vc='" . Cleaner('i', $_POST['inPhone']) . "'
									WHERE ap_acc_fk=" . $_POST['inClientID'] . "";
    DataExchange("w", $z_db_name, $sql);
    # See if a password reset has been initiated! - Added in ZPanel 5.1.0
    if ((isset($_POST['inNewPassword'])) && ($_POST['inNewPassword'] <> "")) {
        # Get account username...
        $sql = "SELECT * FROM z_accounts WHERE ac_id_pk=" . $_POST['inClientID'] . " AND ac_deleted_ts IS NULL";
        $listclientid = DataExchange("r", $z_db_name, $sql);
        $rowclientid = mysql_fetch_assoc($listclientid);
        $resetforuser = $rowclientid['ac_user_vc'];
        $sql = "UPDATE z_accounts SET ac_pass_vc='" . md5(Cleaner("i", $_POST['inNewPassword'])) . "' WHERE ac_id_pk=" . $_POST['inClientID'] . "";
        DataExchange("w", $z_db_name, $sql);
        $sql = "UPDATE z_accounts SET ac_pass_vc='" . md5(Cleaner("i", $_POST['inNewPassword'])) . "' WHERE ac_id_pk=" . $_POST['inClientID'] . "";
        DataExchange("w", $z_db_name, $sql);
        zapi_mysqluser_setpass($resetforuser, Cleaner("i", $_POST['inNewPassword']), $zdb);
        TriggerLog($useraccount['ac_id_pk'], "Account password for (" . $resetforuser . ") has been reset by the account admin.");
    }
    $returnurl = GetNormalModuleURL($returnurl) . "&r=ok";
    TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $_POST['inClientID'] . " was updated.");
    header("location: " . $returnurl . "");
    exit;
}

if ($_POST['inAction'] == 'delete') {
# User has choosen to delete a package...
    do {
        if (isset($_POST['inEdit_' . $rowclients['ac_id_pk']])) {
            header("location: " . $returnurl . "&edit=" . $rowclients['ac_id_pk'] . "");
            exit;
        }
        if (isset($_POST['inDelete_' . $rowclients['ac_id_pk']])) {
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " requested for deletion.");

            # Delete all cron jobs
            $sql = "SELECT * FROM z_cronjobs WHERE ct_acc_fk=" . $rowclients['ac_id_pk'] . " AND ct_deleted_ts IS NULL";
            $listcronjobs = DataExchange("r", $z_db_name, $sql);
            $rowcronjobs = mysql_fetch_assoc($listcronjobs);
            $totalRows_cronjobs = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_cronjobs > 0) {
                do {
                    # Go through and delete each entry from the crontab file for the user!
                    zapi_cronjob_remove(GetSystemOption('cron_file'), $rowcronjobs['ct_id_pk']);
                    $total_deleted = ($total_deleted + 1);
                } while ($rowcronjobs = mysql_fetch_assoc($listcronjobs));
            }
            $sql = "UPDATE z_cronjobs SET ct_deleted_ts=" . time() . " WHERE ct_acc_fk=" . $rowclients['ac_id_pk'] . " AND ct_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x Crontasks)  has been deleted!");


            # Delete all mailboxes
            $sql = "SELECT * FROM z_mailboxes WHERE mb_acc_fk=" . $rowclients['ac_id_pk'] . " AND mb_deleted_ts IS NULL";
            $listmailboxes = DataExchange("r", $z_db_name, $sql);
            $rowmailboxes = mysql_fetch_assoc($listmailboxes);
            $totalRows_mailboxes = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_mailboxes > 0) {
                do {
                    # Go through and delete each entry from the hmailserver database!
                    if (GetSystemOption('hmailserver_db') <> "") {
                        $hmaildatabase = GetSystemOption('hmailserver_db');
                        $sql = "SELECT accountid FROM hm_accounts WHERE accountaddress='" . $rowmailboxes['mb_address_vc'] . "'";
                        $hmisdomain = DataExchange("t", $hmaildatabase, $sql);
                        # Lets delete the domain (if it exists in the hMailServer database....
                        if ($hmisdomain > 0) {
                            # Delete the mailbox now...
                            $sql = "DELETE FROM hm_accounts WHERE accountaddress='" . $rowmailboxes['mb_address_vc'] . "'";
                            DataExchange("w", $hmaildatabase, $sql);
                        }
                    }
                    $total_deleted = ($total_deleted + 1);
                } while ($rowmailboxes = mysql_fetch_assoc($listmailboxes));
            }
            $sql = "UPDATE z_mailboxes SET mb_deleted_ts=" . time() . " WHERE mb_acc_fk=" . $rowclients['ac_id_pk'] . " AND mb_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x Mailboxes)  has been deleted!");


            # Delete all forwarders
            $sql = "SELECT * FROM z_forwarders WHERE fw_acc_fk=" . $rowclients['ac_id_pk'] . " AND fw_deleted_ts IS NULL";
            $listforwarders = DataExchange("r", $z_db_name, $sql);
            $rowforwarders = mysql_fetch_assoc($listforwarders);
            $totalRows_forwarders = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_forwarders > 0) {
                do {
                    # Go through and delete each entry from the hmailserver database!
                    if (GetSystemOption('hmailserver_db') <> "") {
                        $hmaildatabase = GetSystemOption('hmailserver_db');
                        $sql = "SELECT aliasid FROM hm_aliases WHERE aliasname='" . $rowforwarders['fw_address_vc'] . "'";
                        $hmisfowarder = DataExchange("t", $hmaildatabase, $sql);
                        # Lets delete the domain (if it exists in the hMailServer database....
                        if ($hmisfowarder > 0) {
                            # Delete the domain now...
                            $sql = "DELETE FROM hm_aliases WHERE aliasname='" . $rowforwarders['fw_address_vc'] . "'";
                            DataExchange("w", $hmaildatabase, $sql);
                        }
                    }
                    $total_deleted = ($total_deleted + 1);
                } while ($rowforwarders = mysql_fetch_assoc($listforwarders));
            }
            $sql = "UPDATE z_forwarders SET fw_deleted_ts=" . time() . " WHERE fw_acc_fk=" . $rowclients['ac_id_pk'] . " AND fw_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x Forwarders)  has been deleted!");


            # Delete all distrubution lists
            $sql = "SELECT * FROM z_distlists WHERE dl_acc_fk=" . $rowclients['ac_id_pk'] . " AND dl_deleted_ts IS NULL";
            $listdistlists = DataExchange("r", $z_db_name, $sql);
            $rowdistlists = mysql_fetch_assoc($listdistlists);
            $totalRows_distlists = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_distlists > 0) {
                do {
                    # Go through and delete each entry from the hmailserver database!
                    if (GetSystemOption('hmailserver_db') <> "") {
                        $hmaildatabase = GetSystemOption('hmailserver_db');
                        $sql = "SELECT distributionlistid FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdistlists['dl_address_vc'] . "'";
                        $hmisdistlist = DataExchange("t", $hmaildatabase, $sql);
                        $rowhmdistlist = DataExchange("l", $hmaildatabase, $sql);
                        # Lets delete the domain (if it exists in the hMailServer database....
                        if ($hmisdistlist > 0) {
                            # Delete all recipient address from hMailServer....
                            $sql = "DELETE FROM hm_distributionlistsrecipients WHERE distributionlistrecipientlistid='" . $rowhmdistlist['distributionlistid'] . "'";
                            DataExchange("w", $hmaildatabase, $sql);
                            # Delete the domain now...
                            $sql = "DELETE FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdistlists['dl_address_vc'] . "'";
                            DataExchange("w", $hmaildatabase, $sql);
                            $sql = "UPDATE z_distlistusers SET du_deleted_ts=" . time() . " WHERE du_distlist_fk=" . $rowdistlists['dl_id_pk'] . "";
                            DataExchange("w", $z_db_name, $sql);
                        }
                    }
                    $total_deleted = ($total_deleted + 1);
                } while ($rowdistlists = mysql_fetch_assoc($listdistlists));
            }
            $sql = "UPDATE z_distlists SET dl_deleted_ts=" . time() . " WHERE dl_acc_fk=" . $rowclients['ac_id_pk'] . " AND dl_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x Distrubution lists)  has been deleted!");


            # Delete all VHOSTs (parked, sub and tld)
            $sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $rowclients['ac_id_pk'] . " AND vh_deleted_ts IS NULL";
            $listvhosts = DataExchange("r", $z_db_name, $sql);
            $rowvhosts = mysql_fetch_assoc($listvhosts);
            $totalRows_vhosts = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_vhosts > 0) {
                do {
                    # Go through and delete VHOST container from the Apache VHOST file!
                    zapi_vhost_remove(GetSystemOption('apache_vhost'), $rowvhosts['vh_name_vc']);

                    # Lets now go and try removing the domain from hMailServer (if configured in the ZPanel system settings:-
                    $hmaildatabase = GetSystemOption('hmailserver_db');
                    if (GetSystemOption('hmailserver_db') <> "") {
                        # Lets delete all hMailServer accounts...
                        $sql = "SELECT domainid FROM hm_domains WHERE domainname='" . $rowvhosts['vh_name_vc'] . "'";
                        $hmdomainid = DataExchange("l", $hmaildatabase, $sql);
                        $hmisdomain = DataExchange("t", $hmaildatabase, $sql);
                        $domain_id = $hmdomainid['domainid'];
                        # Lets delete the domain (if it exists in the hMailServer database....
                        if ($hmisdomain > 0) {
                            # Delete the domain now...
                            $sql = "DELETE FROM hm_domains WHERE domainid=" . $domain_id . "";
                            DataExchange("w", $hmaildatabase, $sql);

                            # Delete the domain folder from the hMailServer program directory...
                            # Check both locations...
                            zapi_filesystem_remove("C:/Zpanel/bin/hmailserver/Data/" . $rowvhosts['vh_name_vc'] . "/");
                            zapi_filesystem_remove("C:/Program Files/hMailServer/Data/" . $rowvhosts['vh_name_vc'] . "/");
                        }
                    }
                    $total_deleted = ($total_deleted + 1);
                } while ($rowvhosts = mysql_fetch_assoc($listvhosts));
            }
            $sql = "UPDATE z_vhosts SET vh_deleted_ts=" . time() . " WHERE vh_acc_fk=" . $rowclients['ac_id_pk'] . " AND vh_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x vhost containers)  has been deleted!");


            # Delete all MySQL databases that the user has
            $sql = "SELECT * FROM z_mysql WHERE my_acc_fk=" . $rowclients['ac_id_pk'] . " AND my_deleted_ts IS NULL";
            $listmysql = DataExchange("r", $z_db_name, $sql);
            $rowmysql = mysql_fetch_assoc($listmysql);
            $totalRows_mysql = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_mysql > 0) {
                do {
                    # Go through and delete all MySQL databases that the user has setup.
                    zapi_mysqldb_remove($rowmysql['my_name_vc'], $zdb);
                    $total_deleted = ($total_deleted + 1);
                } while ($rowmysql = mysql_fetch_assoc($listmysql));
            }
            $sql = "UPDATE z_mysql SET my_deleted_ts=" . time() . " WHERE my_acc_fk=" . $rowclients['ac_id_pk'] . " AND my_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x MySQL databases)  has been deleted!");


            # Delete the MySQL user account for the user
            zapi_mysqluser_remove($rowclients['ac_user_vc'], $zdb);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (MySQL user account)  has been deleted!");

            # Delete all FTP accounts that the user has.
            $sql = "SELECT * FROM z_ftpaccounts WHERE ft_acc_fk=" . $rowclients['ac_id_pk'] . " AND ft_deleted_ts IS NULL";
            $listftpaccounts = DataExchange("r", $z_db_name, $sql);
            $rowftpaccounts = mysql_fetch_assoc($listftpaccounts);
            $totalRows_ftpaccounts = DataExchange("t", $z_db_name, $sql);
            $total_deleted = 0;
            if ($totalRows_ftpaccounts > 0) {
                do {
                    # Go through and delete all FTP accounts that the user has setup.
                    zapi_ftpaccount_remove(GetSystemOption('filezilla_root'), $username);
                    $total_deleted = ($total_deleted + 1);
                } while ($rowftpaccounts = mysql_fetch_assoc($listftpaccounts));
                # Then obviously we should go and reload FileZilla's configuration.... Due to removal of FTP accounts!
            }
            $sql = "UPDATE z_ftpaccounts SET ft_deleted_ts=" . time() . " WHERE ft_acc_fk=" . $rowclients['ac_id_pk'] . " AND ft_deleted_ts IS NULL";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (" . $total_deleted . "x FTP accounts)  has been deleted!");

            # Delete the user's home directory!
            zapi_filesystem_remove(GetSystemOption('hosted_dir') . $rowclients['ac_user_vc'] . "/");
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (Home directory and contents for \"" . $rowclients['ac_user_vc'] . "\")  has been deleted!");

            # Delete the user's ZPanel login account
            $sql = "UPDATE z_accounts SET ac_deleted_ts=" . time() . " WHERE ac_id_pk=" . $rowclients['ac_id_pk'] . "";
            $packageid = DataExchange("w", $z_db_name, $sql);
            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " (ZPanel login account \"" . $rowclients['ac_user_vc'] . "\")  has been deleted!");

            TriggerLog($useraccount['ac_id_pk'], $b = "User account ID: " . $rowclients['ac_id_pk'] . " has been deleted!");
        }
    } while ($rowclients = mysql_fetch_assoc($listclients));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}
# We reload the FTP server here as there will be the requirement to do so...
if (ShowServerPlatform() == "Windows") {
    $filezilla_reload = GetSystemOption('filezilla_root') . "FileZilla server.exe /reload-config";
} else {
    $filezilla_reload = "/etc/zpanel/bin/zsudo service " . GetSystemOption('lsn_proftpd') . " reload";
}
$reboot = system($filezilla_reload);
?>
