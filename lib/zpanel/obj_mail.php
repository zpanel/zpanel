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
error_reporting(E_ALL);
ini_set('display_errors', '1');
# Now we need to declare and cleanup some variables
$acc_fk = $useraccount['ac_id_pk'];
$returnurl = Cleaner('o', $_POST['inReturn']);

if ($_POST['inAction'] == 'NewMailbox') {
    $fulladdress = Cleaner('i', $_POST['inAddress'] . "@" . $_POST['inDomain']);
    # Check for spaces and remove if found...
    $fulladdress = str_replace(' ', '', $fulladdress);
    # Lets check that the user specified an email domain...
    if ($_POST['inAddress'] == '' || $_POST['inDomain'] == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nodomain");
        exit;
    }
    # Lets check that the user specified an email password...
    if ($_POST['inPassword'] == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nopassword");
        exit;
    }
    # Firstly we check that a mailbox, forwarder or dist list doesnt already exist in ZPanel....
    $sql = "SELECT * FROM z_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
    $totalmailboxes = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_aliases WHERE al_address_vc='" . $fulladdress . "' AND al_deleted_ts IS NULL";
    $totalforwarders = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_distlists WHERE dl_address_vc='" . $fulladdress . "' AND dl_deleted_ts IS NULL";
    $totaldistlists = DataExchange("t", $z_db_name, $sql);
    # Now we run a check to see if it already exists...
    if (($totalmailboxes > 0) or ($totalforwarders > 0) or ($totaldistlists > 0)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
        exit;
    }
    # Only add the mail to hmailserver if there is a value in the database:-
    if (GetSystemOption('hmailserver_db') <> "") {
        # Platform is Windows, we use hMailServer
        if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
            # Now we get the hMailDatabase from the system options ready to insert the row...
            $hmaildatabase = GetSystemOption('hmailserver_db');
            $password = md5($_POST['inPassword']);
            $encryption_type = GetSystemOption('hmailserver_et');
            $max_mailbox_size = GetSystemOption('hmailserver_mms');
            # Now lets get the hMailServer domain ID...
            $sql = "SELECT domainid FROM hm_domains WHERE domainname='" . $_POST['inDomain'] . "'";
            $hmdomainid = DataExchange("l", $hmaildatabase, $sql);
            $domain_id = $hmdomainid['domainid'];
            # Now we insert the mailbox data into the hMailServer database...
            $sql = "INSERT INTO hm_accounts (accountdomainid,
									 	accountadminlevel,
									 	accountaddress,
									 	accountpassword,
									 	accountactive,
									 	accountisad,
									 	accountaddomain,
									 	accountadusername,
									 	accountmaxsize,
									 	accountvacationmessageon,
									 	accountvacationmessage,
									 	accountvacationsubject,
									 	accountpwencryption,
									 	accountforwardenabled,
									 	accountforwardaddress,
									 	accountforwardkeeporiginal,
									 	accountenablesignature,
									 	accountsignatureplaintext,
									 	accountsignaturehtml,
									 	accountlastlogontime,
									 	accountvacationexpires,
									 	accountvacationexpiredate,
									 	accountpersonfirstname,
									 	accountpersonlastname) VALUES (
									 	" . $domain_id . ",
									 	0,
									 	'" . $fulladdress . "',
									 	'" . $password . "',
									 	1,
									 	0,
									 	'',
									 	'',
									 	0,
									 	0,
									 	'',
									 	'',
									 	" . $encryption_type . ",
									 	0,
									 	'',
									 	0,
									 	0,
									 	'',
									 	'',
									 	'',
									 	0,
									 	'',
									 	'',
									 	'')";
            DataExchange("w", $hmaildatabase, $sql);
            # Lets grab the accountid of the mailbox...
            $sql = "SELECT accountid FROM hm_accounts WHERE accountaddress='" . $fulladdress . "'";
            $hmmailboxid = DataExchange("l", $hmaildatabase, $sql);
            $mailbox_id = $hmmailboxid['accountid'];
            # Now we create the hm_imapfolders row...
            $sql = "INSERT INTO hm_imapfolders(folderaccountid,
									   	folderparentid,
									   	foldername,
									   	folderissubscribed,
									   	foldercreationtime,
									   	foldercurrentuid) VALUES (
									   	" . $mailbox_id . ",
									   	-1,
									   	'INBOX',
									   	1,
									   	NOW(),
									   	1)";
            DataExchange("w", $hmaildatabase, $sql);
        } else { ################### POSIX
            # Platform is POSIX, we use Postfix
            # Now we get the Postfix database from the system options ready to insert the row...
            $postfixdatabase = GetSystemOption('hmailserver_db');
            $password = md5($_POST['inPassword']);
            $encryption_type = GetSystemOption('hmailserver_et');
            $max_mailbox_size = GetSystemOption('hmailserver_mms');
            # Now we insert the mailbox data into the Postfix database...
            $sql = "INSERT INTO mailbox (username,
								 	password,
								 	name,
									maildir,
								 	local_part,
								 	quota,
								 	domain,
								 	created,
								 	modified,
								 	active) VALUES (
								 	'" . $fulladdress . "',
								 	'{PLAIN-MD5}" . $password . "',
								 	'" . $_POST['inAddress'] . "',
								 	'" . $_POST['inDomain'] . "/" . $_POST['inAddress'] . "/',
								 	'" . $_POST['inAddress'] . "',
								 	'" . $max_mailbox_size . "',
								 	'" . $_POST['inDomain'] . "',
								 	NOW(),
								 	NOW(),
								 	'1')";
            DataExchange("w", $postfixdatabase, $sql);
            $sql = "INSERT INTO alias  (address,
								 	goto,
								 	domain,
									created,
								 	modified,
								 	active) VALUES (
								 	'" . $fulladdress . "',
								 	'" . $fulladdress . "',
								 	'" . $_POST['inDomain'] . "',
								 	NOW(),
								 	NOW(),
								 	'1')";
            DataExchange("w", $postfixdatabase, $sql);
        }###################################### ENDIF
    }
    # Now we update the zpanel database...
    $sql = "INSERT INTO z_mailboxes (mb_acc_fk, mb_address_vc, mb_created_ts) VALUES (" . $useraccount['ac_id_pk'] . ", '" . $fulladdress . "', " . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    TriggerLog($useraccount['ac_id_pk'], $b = "User created a mailbox: " . $fulladdress . ".");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'NewAlias') {
    $fulladdress = Cleaner('i', $_POST['inAddress'] . "@" . $_POST['inDomain']);
    # Check for spaces and remove if found...
    $fulladdress = str_replace(' ', '', $fulladdress);
    $destination = Cleaner('i', $_POST['inDestination']);
    # Lets check that the user specified an email domain...
    if ($_POST['inDomain'] == "" || $_POST['inAddress'] == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nodomain");
        exit;
    }
    if ($_POST['inDestination'] == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nodest");
        exit;
    }
    # Firstly we check that a mailbox, forwarder or dist list doesnt already exist in ZPanel....
    $sql = "SELECT * FROM z_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
    $totalmailboxes = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_aliases WHERE al_address_vc='" . $fulladdress . "' AND al_deleted_ts IS NULL";
    $totalforwarders = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_distlists WHERE dl_address_vc='" . $fulladdress . "' AND dl_deleted_ts IS NULL";
    $totaldistlists = DataExchange("t", $z_db_name, $sql);
    # Now we run a check to see if it already exists...
    if (($totalmailboxes > 0) or ($totalforwarders > 0) or ($totaldistlists > 0)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
        exit;
    }
    # Only add the mail to hmailserver if there is a value in the database:-
    if (GetSystemOption('hmailserver_db') <> "") {
        if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
            # Now we get the hMailDatabase from the system options ready to insert the row...
            $hmaildatabase = GetSystemOption('hmailserver_db');
            # Now lets get the hMailServer domain ID...
            $sql = "SELECT domainid FROM hm_domains WHERE domainname='" . $_POST['inDomain'] . "'";
            $hmdomainid = DataExchange("l", $hmaildatabase, $sql);
            $domain_id = $hmdomainid['domainid'];
            # Now we insert the mailbox data into the hMailServer database...
            $sql = "INSERT INTO hm_aliases (aliasdomainid,
										aliasname,
										aliasvalue,
										aliasactive) VALUES (
									 	'" . $domain_id . "',
									 	'" . $fulladdress . "',
									 	'" . $destination . "',
									 	'1')";
            DataExchange("w", $hmaildatabase, $sql);
        } else { ################### POSIX
            # Now we get the Postfix database from the system options ready to insert the row...
            $hmaildatabase = GetSystemOption('hmailserver_db');
            # Now we insert the mailbox data into the hMailServer database...
            $sql = "INSERT INTO alias  (address,
								 	goto,
								 	domain,
									created,
								 	modified,
								 	active) VALUES (
								 	'" . $fulladdress . "',
								 	'" . $destination . "',
								 	'" . $_POST['inDomain'] . "',
								 	NOW(),
								 	NOW(),
								 	'1')";
            DataExchange("w", $hmaildatabase, $sql);
        }###################################### ENDIF
    }
    # Now we update the zpanel database...
    $sql = "INSERT INTO z_aliases (al_acc_fk, al_address_vc, al_destination_vc, al_created_ts) VALUES (" . $useraccount['ac_id_pk'] . ", '" . $fulladdress . "', '" . $destination . "', " . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    TriggerLog($useraccount['ac_id_pk'], $b = "User created a new forwarder: " . $fulladdress . " -> " . $destination . "");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'NewForwarder') {
    $fulladdress = Cleaner('i', $_POST['inAddress']);
    # Check for spaces and remove if found...
    $fulladdress = str_replace(' ', '', $fulladdress);
    $destination = Cleaner('i', $_POST['inDestinationName'] . "@" . $_POST['inDestinationDomain']);
    $destination = trim($destination);
    $leaveonserver = Cleaner('i', $_POST['inLeaveOnServer']);
    # Lets check that the user specified an email address...
    if (!strstr($fulladdress, '@')) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=noemail");
        exit;
    }
    # Lets check that the user specified an email domain...
    if ($_POST['inDestinationDomain'] == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nodomain");
        exit;
    }
    if ($_POST['inDestinationName'] == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nodest");
        exit;
    }
    # Firstly we check that a mailbox, forwarder or dist list doesnt already exist in ZPanel....
    $sql = "SELECT * FROM z_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
    $totalmailboxes = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_forwarders WHERE fw_address_vc='" . $fulladdress . "' AND fw_deleted_ts IS NULL";
    $totalforwarders = DataExchange("t", $z_db_name, $sql);
    # Now we run a check to see the email already exists...
    if (($totalmailboxes == 0)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=notexists");
        exit;
    }
    # Now we run a check to see if a forward already exists...
    if (($totalforwarders > 0)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
        exit;
    }
    # Only add the mail to hmailserver if there is a value in the database:-
    if (GetSystemOption('hmailserver_db') <> "") {
        if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
            # Now we get the hMailDatabase from the system options ready to insert the row...
            $hmaildatabase = GetSystemOption('hmailserver_db');
            # Now we insert the mailbox data into the hMailServer database...
            $sql = "UPDATE hm_accounts SET accountforwardenabled='1', accountforwardaddress='" . $destination . "', accountforwardkeeporiginal='" . $leaveonserver . "' WHERE accountaddress='" . $fulladdress . "'";
            DataExchange("w", $hmaildatabase, $sql);
        } else { ################### POSIX
            # Now we get the Postfix database from the system options ready to insert the row...
            $hmaildatabase = GetSystemOption('hmailserver_db');
            # Now we update the mailbox data into the Postfix database...
            if ($leaveonserver == 1) {
                $copy = "," . $fulladdress;
            } else {
                $copy = "";
            }
            $sql = "UPDATE alias SET goto='" . $destination . $copy . "', modified=NOW() WHERE address = '" . $fulladdress . "'";
            DataExchange("w", $hmaildatabase, $sql);
        }###################################### ENDIF
    }
    # Now we update the zpanel database...
    $sql = "INSERT INTO z_forwarders (fw_acc_fk, fw_address_vc, fw_destination_vc, fw_created_ts) VALUES (" . $useraccount['ac_id_pk'] . ", '" . $fulladdress . "', '" . $destination . "', " . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    TriggerLog($useraccount['ac_id_pk'], $b = "User created a new forwarder: " . $fulladdress . " -> " . $destination . "");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'NewDistList') {
    $fulladdress = Cleaner('i', $_POST['inAddress'] . "@" . $_POST['inDomain']);
    # Check for spaces and remove if found...
    $fulladdress = str_replace(' ', '', $fulladdress);
    # Lets check that the user specified an email domain...
    if ($_POST['inDomain'] == "" || $_POST['inAddress'] == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=nodomain");
        exit;
    }
    # Firstly we check that a mailbox, forwarder or dist list doesnt already exist in ZPanel....
    $sql = "SELECT * FROM z_mailboxes WHERE mb_address_vc='" . $fulladdress . "' AND mb_deleted_ts IS NULL";
    $totalmailboxes = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_aliases WHERE al_address_vc='" . $fulladdress . "' AND al_deleted_ts IS NULL";
    $totalforwarders = DataExchange("t", $z_db_name, $sql);
    $sql = "SELECT * FROM z_distlists WHERE dl_address_vc='" . $fulladdress . "' AND dl_deleted_ts IS NULL";
    $totaldistlists = DataExchange("t", $z_db_name, $sql);
    # Now we run a check to see if it already exists...
    if (($totalmailboxes > 0) or ($totalforwarders > 0) or ($totaldistlists > 0)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
        exit;
    }
    # Only add the mail to hmailserver if there is a value in the database:-
    if (GetSystemOption('hmailserver_db') <> "") {
        # Now we get the hMailDatabase from the system options ready to insert the row...
        $hmaildatabase = GetSystemOption('hmailserver_db');
        if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
            # Now lets get the hMailServer domain ID...
            $sql = "SELECT domainid FROM hm_domains WHERE domainname='" . $_POST['inDomain'] . "'";
            $hmdomainid = DataExchange("l", $hmaildatabase, $sql);
            $domain_id = $hmdomainid['domainid'];
            # Now we insert the mailbox data into the hMailServer database...
            $sql = "INSERT INTO hm_distributionlists (distributionlistdomainid,
									distributionlistaddress,
									distributionlistenabled,
									distributionlistrequireauth,
									distributionlistrequireaddress,
									distributionlistmode) VALUES (
									 " . $domain_id . ",
									 '" . $fulladdress . "',
									 1,
									 0,
									 '',
									 0)";
            DataExchange("w", $hmaildatabase, $sql);
        } else { ################### POSIX
            # Now we insert the mailbox data into the Postfix database...
            $sql = "INSERT INTO alias  (address,
								 	goto,
								 	domain,
									created,
								 	modified,
								 	active) VALUES (
								 	'" . $fulladdress . "',
								 	'',
								 	'" . $_POST['inDomain'] . "',
								 	NOW(),
								 	NOW(),
								 	'1')";
            DataExchange("w", $hmaildatabase, $sql);
        }###################################### ENDIF
    }
    # Now we update the zpanel database...
    $sql = "INSERT INTO z_distlists (dl_acc_fk, dl_address_vc, dl_created_ts) VALUES (" . $useraccount['ac_id_pk'] . ", '" . $fulladdress . "', " . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    TriggerLog($useraccount['ac_id_pk'], $b = "User created a new distrubution list: (" . $fulladdress . ")");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == "ResetPassword") {
    $hmaildatabase = GetSystemOption('hmailserver_db');
    # Check that the mailbox exsists before we reset the password...
    $sql = "SELECT * FROM z_mailboxes WHERE mb_acc_fk=" . $useraccount['ac_id_pk'] . " AND mb_address_vc='" . Cleaner('i', $_POST['inMailbox']) . "' AND mb_deleted_ts IS NULL";
    $listmailboxes = DataExchange("r", $z_db_name, $sql);
    $rowmailboxes = mysql_fetch_assoc($listmailboxes);
    $totalmailboxes = DataExchange("t", $z_db_name, $sql);
    if ($totalmailboxes > 0) {
        # Ok the mailbox exists so lets change the mailbox password...
        $newpassword = md5($_POST['inPassword']);
        if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
            $sql = "UPDATE hm_accounts SET accountpassword='" . $newpassword . "' WHERE accountaddress='" . $rowmailboxes['mb_address_vc'] . "'";
        } else { ################### POSIX
            $sql = "UPDATE mailbox SET password='{PLAIN-MD5}" . $newpassword . "' WHERE username='" . $rowmailboxes['mb_address_vc'] . "'";
        } ###################################### ENDIF
        DataExchange("w", $hmaildatabase, $sql);
        # Log some info...
        TriggerLog($useraccount['ac_id_pk'], $b = "User has reset a mailbox password!");
        header("location: " . GetNormalModuleURL($returnurl) . "&r=passrs");
        exit;
    }
    header("location: " . GetNormalModuleURL($returnurl) . "&r=passnr");
    exit;
}

if ($_POST['inAction'] == 'delete_mailbox') {
# User has choosen to delete the task...
    $sql = "SELECT * FROM z_mailboxes WHERE mb_acc_fk=" . $useraccount['ac_id_pk'] . " AND mb_deleted_ts IS NULL";
    $listmailboxes = DataExchange("r", $z_db_name, $sql);
    $rowmailboxes = mysql_fetch_assoc($listmailboxes);
    $totalmailboxes = DataExchange("t", $z_db_name, $sql);
    do {
        # Check that the user doesnt want to reset the password instead?!!
        if (isset($_POST['inReset_' . $rowmailboxes['mb_id_pk']])) {
            header("location: " . $returnurl . "&r=off&reset=" . $rowmailboxes['mb_address_vc'] . "");
            exit;
        }
        # Check that the user doesnt want to edit the mailbox options instead?!!
        if (isset($_POST['inEdit_' . $rowmailboxes['mb_id_pk']])) {
            header("location: " . $returnurl . "&r=off&edit=" . $rowmailboxes['mb_address_vc'] . "");
            exit;
        }
        if (isset($_POST['inDelete_' . $rowmailboxes['mb_id_pk']])) {
            # Ok so lets drop the mailbox from the hmailserver (if its set in the system table)...
            if (GetSystemOption('hmailserver_db') <> "") {
                $hmaildatabase = GetSystemOption('hmailserver_db');
                if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
                    $sql = "SELECT accountid FROM hm_accounts WHERE accountaddress='" . $rowmailboxes['mb_address_vc'] . "'";
                    $hmisdomain = DataExchange("t", $hmaildatabase, $sql);
                    # Lets delete the domain (if it exists in the hMailServer database....
                    if ($hmisdomain > 0) {
                        # Delete the mailbox now...
                        $sql = "DELETE FROM hm_accounts WHERE accountaddress='" . $rowmailboxes['mb_address_vc'] . "'";
                        DataExchange("w", $hmaildatabase, $sql);
                    }
                } else { ################### POSIX
                    # Delete the mailbox now...
                    $sql = "DELETE FROM mailbox WHERE username='" . $rowmailboxes['mb_address_vc'] . "'";
                    DataExchange("w", $hmaildatabase, $sql);
                    $sql = "DELETE FROM alias WHERE address='" . $rowmailboxes['mb_address_vc'] . "'";
                    DataExchange("w", $hmaildatabase, $sql);
                } ###################################### ENDIF
            }
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User Mailbox ID: " . $rowmailboxes['my_id_pk'] . " was deleted.");
            # Do all other account deleted related stuff here!!!
            $sql = "UPDATE z_mailboxes SET mb_deleted_ts=" . time() . " WHERE mb_id_pk=" . $rowmailboxes['mb_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            # Now we do the rest, which is to go and delete the cron entry from the file...
        }
    } while ($rowmailboxes = mysql_fetch_assoc($listmailboxes));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'delete_alias') {
# User has choosen to delete the alias...
    $sql = "SELECT * FROM z_aliases WHERE al_acc_fk=" . $useraccount['ac_id_pk'] . " AND al_deleted_ts IS NULL";
    $listforwarders = DataExchange("r", $z_db_name, $sql);
    $rowforwarders = mysql_fetch_assoc($listforwarders);
    $totalforwarders = DataExchange("t", $z_db_name, $sql);
    do {
        if (isset($_POST['inDelete_' . $rowforwarders['al_id_pk']])) {
            # Ok so lets drop the alias from the hmailserver (if its set in the system table)...
            if (GetSystemOption('hmailserver_db') <> "") {
                $hmaildatabase = GetSystemOption('hmailserver_db');
                if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
                    $sql = "SELECT aliasid FROM hm_aliases WHERE aliasname='" . $rowforwarders['al_address_vc'] . "'";
                    $hmisforwarder = DataExchange("t", $hmaildatabase, $sql);
                    # Lets delete the alias (if it exists in the hMailServer database....
                    if ($hmisforwarder > 0) {
                        # Delete the alias now...
                        $sql = "DELETE FROM hm_aliases WHERE aliasname='" . $rowforwarders['al_address_vc'] . "'";
                        DataExchange("w", $hmaildatabase, $sql);
                    }
                } else { ################### POSIX
                    $sql = "DELETE FROM alias WHERE address='" . $rowforwarders['al_address_vc'] . "'";
                    DataExchange("w", $hmaildatabase, $sql);
                } ###################################### ENDIF
            }
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User Alias ID: " . $rowforwarders['al_id_pk'] . " was deleted.");
            # Do all other account deleted related stuff here!!!
            $sql = "UPDATE z_aliases SET al_deleted_ts=" . time() . " WHERE al_id_pk=" . $rowforwarders['al_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
        }
    } while ($rowforwarders = mysql_fetch_assoc($listforwarders));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'delete_forwarder') {
# User has choosen to delete the forward...
    $sql = "SELECT * FROM z_forwarders WHERE fw_acc_fk='" . $useraccount['ac_id_pk'] . "' AND fw_deleted_ts IS NULL";
    $listfowarders = DataExchange("r", $z_db_name, $sql);
    $rowfowarders = mysql_fetch_assoc($listfowarders);
    $totalfowarders = DataExchange("t", $z_db_name, $sql);
    do {
        if (isset($_POST['inDelete_' . $rowfowarders['fw_id_pk']])) {
            # Ok so lets drop the forward from the hmailserver (if its set in the system table)...
            if (GetSystemOption('hmailserver_db') <> "") {
                if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
                    $hmaildatabase = GetSystemOption('hmailserver_db');
                    $sql = "SELECT accountid FROM hm_accounts WHERE accountaddress='" . $rowfowarders['fw_address_vc'] . "'";
                    $listhmforwarders = DataExchange("r", $hmaildatabase, $sql);
                    $rowhmforwarders = mysql_fetch_assoc($listhmforwarders);
                    $hmisfowarder = DataExchange("t", $hmaildatabase, $sql);
                    # Lets clear the forwards in hmail user account (if it exists in the hMailServer database....
                    if ($hmisfowarder > 0) {
                        # Update the account now...
                        $sql = "UPDATE hm_accounts SET accountforwardenabled='0', accountforwardaddress='', accountforwardkeeporiginal='0' WHERE accountid='" . $rowhmforwarders['accountid'] . "'";
                        DataExchange("w", $hmaildatabase, $sql);
                    }
                } else { ################### POSIX
                    $hmaildatabase = GetSystemOption('hmailserver_db');
                    $sql = "UPDATE alias SET goto='" . $rowfowarders['fw_address_vc'] . "', modified=NOW() WHERE address = '" . $rowfowarders['fw_address_vc'] . "'";
                    DataExchange("w", $hmaildatabase, $sql);
                } ###################################### ENDIF
            }
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User Forwarder ID: " . $rowfowarders['fw_id_pk'] . " was deleted.");
            # Do all other account deleted related stuff here!!!
            $sql = "UPDATE z_forwarders SET fw_deleted_ts=" . time() . " WHERE fw_id_pk=" . $rowfowarders['fw_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            # Now we do the rest, which is to go and delete the cron entry from the file...
        }
    } while ($rowfowarders = mysql_fetch_assoc($listfowarders));
    # Log the action in the database...
    TriggerLog($useraccount['ac_id_pk'], $b = "User Forward ID: " . $rowfowarders['fk_id_pk'] . " was deleted.");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'delete_distlist') {
    # User has choosen to delete the task...
    $sql = "SELECT * FROM z_distlists WHERE dl_acc_fk=" . $useraccount['ac_id_pk'] . " AND dl_deleted_ts IS NULL";
    $listdistlists = DataExchange("r", $z_db_name, $sql);
    $rowdistlists = mysql_fetch_assoc($listdistlists);
    $totaldistlists = DataExchange("t", $z_db_name, $sql);
    do {
        # Check to ensure that user doesnt actually want to edit the dist list, before we go to check deleting instead...
        if (isset($_POST['inEdit_' . $rowdistlists['dl_id_pk']])) {
            header("location: " . $returnurl . "&edit=" . $rowdistlists['dl_address_vc'] . "");
            exit;
        }
        if (isset($_POST['inDelete_' . $rowdistlists['dl_id_pk']])) {
            # Ok so lets drop the mailbox from the hmailserver (if its set in the system table)...
            if (GetSystemOption('hmailserver_db') <> "") {
                $hmaildatabase = GetSystemOption('hmailserver_db');
                if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
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
                } else { ################### POSIX
                    # Delete the distributionlist alias
                    $sql = "DELETE FROM alias WHERE address='" . $rowdistlists['dl_address_vc'] . "'";
                    DataExchange("w", $hmaildatabase, $sql);
                } ###################################### ENDIF
            }
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User Distrubution List ID: " . $rowdistlists['dl_id_pk'] . " was deleted.");
            # Do all other account deleted related stuff here!!!
            $sql = "UPDATE z_distlists SET dl_deleted_ts=" . time() . " WHERE dl_id_pk=" . $rowdistlists['dl_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            # Now we should recurse through all the current distlistusers and delete them too!!!
            # Now we do the rest, which is to go and delete the cron entry from the file...
        }
    } while ($rowdistlists = mysql_fetch_assoc($listdistlists));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'edit_distlists') {
    $sql = "SELECT * FROM z_distlists WHERE dl_acc_fk=" . $useraccount['ac_id_pk'] . " AND dl_id_pk=" . $_POST['inDLID'] . " AND dl_deleted_ts IS NULL";
    $listdistlists = DataExchange("r", $z_db_name, $sql);
    $rowdistlists = mysql_fetch_assoc($listdistlists);
    $totaldistlists = DataExchange("t", $z_db_name, $sql);

    if (isset($_POST['inSubmit'])) {
        # User wants to create a new dist list address, so lets add the list list user...
        $list_id = $rowdistlists['dl_id_pk'];
        # Lets just quickly grab the ZPanel dist list foreign key
        $sql = "SELECT * FROM z_distlists WHERE dl_acc_fk=" . $useraccount['ac_id_pk'] . " AND dl_id_pk=" . $rowdistlists['dl_id_pk'] . " AND dl_deleted_ts IS NULL";
        $listzpfkdu = DataExchange("r", $z_db_name, $sql);
        $rowzpfkdu = mysql_fetch_assoc($listzpfkdu);
        # Create the distlist user in hMailServer...
        if (GetSystemOption('hmailserver_db') <> "") {
            # Lets just quickly grab the hMailServer dist list foreign key
            $hmaildatabase = GetSystemOption('hmailserver_db');
            if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
                $sql = "SELECT distributionlistid FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdistlists['dl_address_vc'] . "'";
                $listhmfkdu = DataExchange("r", $hmaildatabase, $sql);
                $rowhmfkdu = mysql_fetch_assoc($listhmfkdu);
                $sql = "INSERT INTO hm_distributionlistsrecipients (
																	distributionlistrecipientlistid,
																	distributionlistrecipientaddress) VALUES (
																	" . $rowhmfkdu['distributionlistid'] . ",
																	'" . Cleaner('i', $_POST['inDistListAddress']) . "')";
                DataExchange("w", $hmaildatabase, $sql);
            } else { ################### POSIX
                $sql = "SELECT * FROM alias WHERE address='" . $rowdistlists['dl_address_vc'] . "'";
                $listhmfkdu = DataExchange("r", $hmaildatabase, $sql);
                $rowhmfkdu = mysql_fetch_assoc($listhmfkdu);
                $find = $_POST['inDistListAddress'];
                $match = '/^.*?' . $find . '.*\n?/m';
                $matchresult = preg_match_all($match, $rowhmfkdu['goto'], $matches);
                if (!empty($matchresult)) {
                    header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
                    exit;
                }
                $newlist = $rowhmfkdu['goto'] . "," . Cleaner('i', $_POST['inDistListAddress']);
                $newlist = str_replace(",,", ",", $newlist);
                $sql = "UPDATE alias SET goto='" . $newlist . "', modified=NOW() WHERE address='" . $rowdistlists['dl_address_vc'] . "'";
                DataExchange("w", $hmaildatabase, $sql);
            } ###################################### ENDIF
        }
        # Now we insert it into the ZPanel database...
        $sql = "INSERT INTO z_distlistusers (
											du_distlist_fk,
											du_address_vc,
											du_created_ts) VALUES (
											" . $rowzpfkdu['dl_id_pk'] . ",
											'" . Cleaner('i', $_POST['inDistListAddress']) . "',
											" . time() . ")";
        DataExchange("w", $z_db_name, $sql);
        header("location: " . GetNormalModuleURL($returnurl) . "&edit=" . $rowzpfkdu['dl_address_vc'] . "&r=ok");
        exit;
    } else {
        # User has choosen to delete the distrubution list addres...
        $sql = "SELECT * FROM z_distlistusers WHERE du_deleted_ts IS NULL";
        $listdistlistusers = DataExchange("r", $z_db_name, $sql);
        $rowdistlistusers = mysql_fetch_assoc($listdistlistusers);
        $totaldistlistusers = DataExchange("t", $z_db_name, $sql);
        do {
            # Check to ensure that user doesnt actually want to edit the dist list, before we go to check deleting instead...
            if (isset($_POST['inDelete_' . $rowdistlistusers['du_id_pk']])) {
                # Ok so lets drop the distrubution list user from the hmailserver (if its set in the system table)...
                if (GetSystemOption('hmailserver_db') <> "") {
                    $hmaildatabase = GetSystemOption('hmailserver_db');
                    if (ShowServerPlatform() == "Windows") {###################################### WINDOWS
                        # Lets get the distrubution list id from hMailServer...
                        $sql = "SELECT * FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdistlists['dl_address_vc'] . "'";
                        $listhmdistlist = DataExchange("r", $hmaildatabase, $sql);
                        $rowhmdistlist = mysql_fetch_assoc($listhmdistlist);
                        $totalhmdistlist = DataExchange("t", $hmaildatabase, $sql);
                        $hmdistlistid = $rowhmdistlist['distributionlistid'];
                        # Delete the domain now...
                        $sql = "DELETE FROM hm_distributionlistsrecipients WHERE distributionlistrecipientaddress='" . $rowdistlistusers['du_address_vc'] . "' AND distributionlistrecipientlistid=" . $hmdistlistid . "";
                        DataExchange("w", $hmaildatabase, $sql);
                    } else { ################### POSIX
                        $sql = "SELECT * FROM alias WHERE address='" . $rowdistlists['dl_address_vc'] . "'";
                        $listhmfkdu = DataExchange("r", $hmaildatabase, $sql);
                        $rowhmfkdu = mysql_fetch_assoc($listhmfkdu);
                        $newlist = str_replace($rowdistlistusers['du_address_vc'], "", $rowhmfkdu['goto']);
                        $newlist = str_replace(",,", ",", $newlist);
                        $sql = "UPDATE alias SET goto='" . $newlist . "', modified=NOW() WHERE address='" . $rowdistlists['dl_address_vc'] . "'";
                        DataExchange("w", $hmaildatabase, $sql);
                    } ###################################### ENDIF
                }
                # Log the action in the database...
                TriggerLog($useraccount['ac_id_pk'], $b = "User Distrubution List User ID: " . $rowdistlistusers['du_id_pk'] . " was deleted.");
                # Do all other account deleted related stuff here!!!
                $sql = "UPDATE z_distlistusers SET du_deleted_ts=" . time() . " WHERE du_id_pk=" . $rowdistlistusers['du_id_pk'] . "";
                DataExchange("w", $z_db_name, $sql);

                # Now we do the rest, which is to go and delete the cron entry from the file...
            }
        } while ($rowdistlistusers = mysql_fetch_assoc($listdistlistusers));
    }
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'EditMailbox') {
# User has choosen to edit individual mailboxes...
    $status = Cleaner('i', $_POST['inStatus']);
    $PFname = Cleaner('i', $_POST['inPFname']);
    $PLname = Cleaner('i', $_POST['inPLname']);
    $SigEnable = Cleaner('i', $_POST['inSigEnable']);
    $SigHTML = Cleaner('i', $_POST['inSigHTML']);
    $SigPlainTXT = Cleaner('i', $_POST['inSigPlainTXT']);
    $AutoReplyEnabled = Cleaner('i', $_POST['inAutoReplyEnabled']);
    $AutoReplySubject = Cleaner('i', $_POST['inAutoReplySubject']);
    $AutoReplyTXT = Cleaner('i', $_POST['inAutoReplyTXT']);
    $AutoReplyExpire = Cleaner('i', $_POST['inAutoReplyExpire']);
    $AutoReplyExpireDate = Cleaner('i', $_POST['inAutoReplyExpireDate']);
    $ExMessageSubject = Cleaner('i', $_POST['inExMessageSubject']);
    $ExMessageAddress = Cleaner('i', $_POST['inExMessageAddress']);
    $ExMessagePort = Cleaner('i', $_POST['inExMessagePort']);
    $ExMessageUser = Cleaner('i', $_POST['inExMessageUser']);
    $ExMessagePass = Cleaner('i', $_POST['inExMessagePass']);
    $ExMessageExpire = Cleaner('i', $_POST['inExMessageExpire']);
    $ExMessageOption = Cleaner('i', $_POST['inExMessageOption']);
    $Usermailbox = Cleaner('i', $_POST['inUsermailbox']);

    if ($AutoReplyExpireDate == "") {
        $AutoReplyExpireDate = "0000-00-00 00:00:00";
    }
    if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
        #Update hmailserver with new user inforamtion
        $hmaildatabase = GetSystemOption('hmailserver_db');
        $sql = "UPDATE hm_accounts SET  accountactive		      ='" . $status . "',
									accountpersonfirstname	  ='" . $PFname . "',
									accountpersonlastname	  ='" . $PLname . "',
									accountenablesignature	  ='" . $SigEnable . "',
									accountsignaturehtml	  ='" . $SigHTML . "',
									accountsignatureplaintext ='" . $SigPlainTXT . "',
									accountvacationmessageon  ='" . $AutoReplyEnabled . "',
									accountvacationsubject	  ='" . $AutoReplySubject . "',
									accountvacationmessage	  ='" . $AutoReplyTXT . "',
									accountvacationexpires	  ='" . $AutoReplyExpire . "',
									accountvacationexpiredate ='" . $AutoReplyExpireDate . "'
									WHERE accountaddress	  ='" . $Usermailbox . "'";
        #Update now...
        DataExchange("w", $hmaildatabase, $sql);
    } else { ################### POSIX
        $hmaildatabase = GetSystemOption('hmailserver_db');
        $sql = "UPDATE mailbox SET 	name          ='" . $PFname . " " . $PLname . "',
								modified      =NOW(),
								active        ='" . $status . "'
								WHERE username ='" . $Usermailbox . "'";
        DataExchange("w", $hmaildatabase, $sql);
        $sql = "UPDATE alias SET modified      =NOW(),
							 active        ='" . $status . "'
							 WHERE address ='" . $Usermailbox . "'";
        DataExchange("w", $hmaildatabase, $sql);
    } ###################################### ENDIF
    #everything is good...
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'EditMailboxExternalAccounts') {
# User has choosen to configure mailboxe external accounts...
    #set up some variables we will need...
    $Faaccountid = Cleaner('i', $_POST['inFaAccountId']);
    $hmaildatabase = GetSystemOption('hmailserver_db');
    #Get all external accounts from selected users mailbox
    $sql = "SELECT * FROM hm_fetchaccounts WHERE faaccountid='" . $Faaccountid . "'";
    $listEXentries = DataExchange("r", $hmaildatabase, $sql);
    $rowEXentries = mysql_fetch_assoc($listEXentries);
    $totalEXentries = DataExchange("t", $hmaildatabase, $sql);
    do {
        #delete external accounts	
        if (isset($_POST['inDelete_' . $rowEXentries['faid']])) {
            $sql = "DELETE FROM hm_fetchaccounts WHERE faid='" . $rowEXentries['faid'] . "'";
            DataExchange("w", $hmaildatabase, $sql);
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "Deleted external email acount " . $rowEXentries['fausername'] . "");
        }
        #disble external accounts
        if (isset($_POST['inDisable_' . $rowEXentries['faid']])) {
            $sql = "UPDATE hm_fetchaccounts SET faactive='0' WHERE faid='" . $rowEXentries['faid'] . "'";
            DataExchange("w", $hmaildatabase, $sql);
        }
        #enable external accounts
        if (isset($_POST['inEnable_' . $rowEXentries['faid']])) {
            $sql = "UPDATE hm_fetchaccounts SET faactive='1' WHERE faid='" . $rowEXentries['faid'] . "'";
            DataExchange("w", $hmaildatabase, $sql);
        }
    } while ($rowEXentries = mysql_fetch_assoc($listEXentries));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}




if ($_POST['inAction'] == 'AddMailboxExternalAccounts') {
    #set up some variables we will need...
    $ExMessageAccount = Cleaner('i', $_POST['inExMessageAccount']);
    $ExMessageAddress = Cleaner('i', $_POST['inExMessageAddress']);
    $ExMessagePort = Cleaner('i', $_POST['inExMessagePort']);
    $ExMessageUser = Cleaner('i', $_POST['inExMessageUser']);
    $ExMessagePass = Cleaner('i', $_POST['inExMessagePass']);
    $ExMessageSSL = Cleaner('i', $_POST['inExMessageSSL']);
    $ExMessageOption = Cleaner('i', $_POST['inExMessageOption']);
    $ExMessageOption2 = Cleaner('i', $_POST['inExMessageOption2']);
    $Usermailbox = Cleaner('i', $_POST['inUsermailbox']);
    $faaccountid = Cleaner('i', $_POST['inHMAccountID']);
    $ExMessageAuth = Cleaner('i', $_POST['inExMessageAuth']);
    $ExMessagecheck = Cleaner('i', $_POST['inExMessagecheck']);
    $ExMessageMIME = Cleaner('i', $_POST['inExMessageMIME']);
    #Return if user did not input a name for the external account...
    if ($ExMessageAccount == "") {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=hmnameerror");
        exit;
    }
    #Check some variables so that hmail wont freak out when putting them in the database...
    if ($ExMessageOption == "skip") {
        $ExMessageOption = $ExMessageOption2;
    }
    if ($ExMessageOption == "") {
        $ExMessageOption = '0';
    }
    if ($ExMessageSSL == "" or !is_numeric($ExMessageSSL)) {
        $ExMessageSSL = '0';
    }
    if ($ExMessageMIME == "" or !is_numeric($ExMessageMIME)) {
        $ExMessageMIME = '0';
    }
    if (!is_numeric($ExMessagePort)) {
        $ExMessagePort = '110';
    }
    #Get the hmail domain id for the users mailbox
    $hmaildatabase = GetSystemOption('hmailserver_db');
    $sql = "SELECT accountdomainid FROM hm_accounts WHERE accountid='" . $faaccountid . "'";
    $listaccountdomainid = DataExchange("r", $hmaildatabase, $sql);
    $rowaccountdomainid = mysql_fetch_assoc($listaccountdomainid);
    $faadomainid = $rowaccountdomainid['accountdomainid'];

    #we try to connect to the hmailserver 
    $obBaseApp = new COM("hMailServer.Application");
    if (!$obBaseApp) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=hmcomerror");
        exit;
    }
    #connection established, now we authenticate
    $obBaseApp->Connect();
    // Authenticate the user
    $obBaseApp->Authenticate($Usermailbox, $ExMessageAuth);
    if (!$obBaseApp->Authenticate($Usermailbox, $ExMessageAuth)) {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=hmautherror&mb=" . $Usermailbox . "");
        exit;
    }
    #we have authenticated, and all our variables set up so lets move on...
    $domainid = $faadomainid;
    $accountid = $faaccountid;
    $obDomain = $obBaseApp->Domains->ItemByDBID($domainid);
    $obAccount = $obDomain->Accounts->ItemByDBID($accountid);
    $obFetchAccounts = $obAccount->FetchAccounts();
    $obFA = $obFetchAccounts->Add();

    $obFA->Enabled = '1';
    $obFA->Name = $ExMessageAccount;
    $obFA->MinutesBetweenFetch = $ExMessagecheck;
    $obFA->Port = $ExMessagePort;
    $obFA->ProcessMIMERecipients = $ExMessageMIME;
    $obFA->ProcessMIMEDate = '1';
    $obFA->ServerAddress = $ExMessageAddress;
    $obFA->ServerType = '1';
    $obFA->Username = $ExMessageUser;
    $obFA->UseAntiVirus = '0';
    $obFA->UseAntiSpam = '0';
    $obFA->EnableRouteRecipients = '0';
    $obFA->DaysToKeepMessages = $ExMessageOption;
    $obFA->UseSSL = $ExMessageSSL;

    $Password = $ExMessagePass;

    if (strlen($Password) > 0) {
        $obFA->Password = $Password;
    }
    $obFA->Save();

    $faid = $obFA->ID;
    # Log the action in the database...
    TriggerLog($useraccount['ac_id_pk'], $b = "Added external email acount " . $ExMessageUser . " for mailbox: " . $Usermailbox . "");
    #Life is good, lets inform the user all is ok...
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}

if ($_POST['inAction'] == 'filter_mailbox') {
    # Filter the mailbox listing
    $filter = Cleaner('i', $_POST['inFilter']);

    header("location: " . $returnurl . "&r=off&rfilter=" . $filter . "");
    exit;
}
?>