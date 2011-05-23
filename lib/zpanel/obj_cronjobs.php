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
$sql = "SELECT * FROM z_cronjobs WHERE ct_acc_fk=" . $useraccount['ac_id_pk'] . " AND ct_deleted_ts IS NULL";
$listtasks = DataExchange("r", $z_db_name, $sql);
$rowtasks = mysql_fetch_assoc($listtasks);
$totaltasks = DataExchange("t", $z_db_name, $sql);

if ($_POST['inAction'] == 'new') {

    # Check to make sure the cron is not blank before we go any further...
    if ($_POST['inScript'] == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
    # Check to make sure the cron script exists before we go any further...
    if (ShowServerPlatform() == 'Windows') {
        if (!is_file(RemoveDoubleSlash(ChangeSafeSlashesToWin(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . '/' . $_POST['inScript'])))) {
            //echo GetSystemOption('hosted_dir').$useraccount['ac_user_vc'].'/'.$_POST['inScript'];
            header("location: " . GetNormalModuleURL($returnurl) . "&r=noexists");
            exit;
        }
    } else {
        if (!is_file(RemoveDoubleSlash(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . '/' . $_POST['inScript']))) {
            //echo GetSystemOption('hosted_dir').$useraccount['ac_user_vc'].'/'.$_POST['inScript'];
            header("location: " . GetNormalModuleURL($returnurl) . "&r=noexists");
            exit;
        }
    }
    # If the user submitted a 'new' request then we will simply add the cron task to the database...
    $sql = "INSERT INTO z_cronjobs (ct_acc_fk,
									ct_script_vc,
									ct_description_tx,
									ct_created_ts) VALUES (
									" . $acc_fk . ",
									'" . Cleaner('i', $_POST['inScript']) . "',
									'" . Cleaner('i', $_POST['inDescription']) . "',
									" . time() . ")";
    DataExchange("w", $z_db_name, $sql);
    # Now we are going to pull back the cron ID to use it as an ancor point.
    $sql = "SELECT * FROM z_cronjobs WHERE ct_acc_fk=" . $acc_fk . " ORDER BY ct_id_pk DESC";
    $cronid = DataExchange("l", $z_db_name, $sql);

    # Call the API function!
    if (ShowServerPlatform() == 'Windows') {
        $api_resault = zapi_cronjob_add(GetSystemOption('cron_file'), $cronid['ct_id_pk'], $_POST['inTiming'], ChangeSafeSlashesToWin(GetSystemOption('php_exer')), RemoveDoubleSlash(ChangeSafeSlashesToWin(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . '/' . $_POST['inScript'])));
    } else {
        $api_resault = zapi_cronjob_add(GetSystemOption('cron_file'), $cronid['ct_id_pk'], $_POST['inTiming'], GetSystemOption('php_exer'), RemoveDoubleSlash(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . '/' . $_POST['inScript']));
    }
    if ($api_resault == false) {
        # The cronjob was not added for some reason!
        # We will remove the cron id from the database so it will not show as active.
        $sql = "UPDATE z_cronjobs SET ct_deleted_ts=" . time() . " WHERE ct_id_pk=" . $cronid['ct_id_pk'] . "";
        DataExchange("w", $z_db_name, $sql);
        TriggerLog($useraccount['ac_id_pk'], $b = "Was unable to write to the crontab file (" . GetSystemOption('cron_file') . "), check that the file is not read-only and that the file path in the ZPanel settings is correct.");
        header("location: " . GetNormalModuleURL($returnurl) . "&r=error");
        exit;
    }

    # Now we add some infomation to the system log.
    TriggerLog($useraccount['ac_id_pk'], $b = "New cron job has been added by user (" . Cleaner('i', $_POST['inScript']) . ")\rDescription:-\r" . Cleaner('i', $_POST['inDescription']) . "");
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}



if ($_POST['inAction'] == 'delete') {
# User has choosen to delete the task...
    do {
        if (isset($_POST['inDelete_' . $rowtasks['ct_id_pk']])) {
            # Call the API function!
            $api_resault = zapi_cronjob_remove(GetSystemOption('cron_file'), $rowtasks['ct_id_pk']);
            if ($api_resault == false) {
                # The cronjob was not deleted for some reason!
                TriggerLog($useraccount['ac_id_pk'], $b = "Was unable to write to the crontab file (" . GetSystemOption('cron_file') . "), check that the file is not read-only and that the file path in the ZPanel settings is correct.");
                header("location: " . GetNormalModuleURL($returnurl) . "&r=error");
                exit;
            }

            # Do all other account deleted related stuff here!!!
            $sql = "UPDATE z_cronjobs SET ct_deleted_ts=" . time() . " WHERE ct_id_pk=" . $rowtasks['ct_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User cron job ID: " . $rowtasks['ct_id_pk'] . " was deleted.");
        }
    } while ($rowtasks = mysql_fetch_assoc($listtasks));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}
?>
