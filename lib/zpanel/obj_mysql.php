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
$sql = "SELECT * FROM z_mysql WHERE my_acc_fk=" . $useraccount['ac_id_pk'] . " AND my_deleted_ts IS NULL";
$listmysql = DataExchange("r", $z_db_name, $sql);
$rowmysql = mysql_fetch_assoc($listmysql);
$totalmysql = DataExchange("t", $z_db_name, $sql);

if ($_POST['inAction'] == 'new') {
    # Check to make sure the database name is not blank before we go any further...
    if ($_POST['inDatabase'] == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
    # Fist lets make sure it doesnt exist before we create the database and continue...
    $sql = "SELECT * FROM z_mysql WHERE my_name_vc='" . Cleaner('i', $useraccount['ac_user_vc'] . "_" . $_POST['inDatabase']) . "' AND my_deleted_ts IS NULL";
    $doesexist = DataExchange("t", $z_db_name, $sql);
    if ($doesexist < 1) {

        # Ok so the database doesnt exist, so lets create the database...
        $api_resault = zapi_mysqldb_add($useraccount['ac_user_vc'], $_POST['inDatabase'], "utf8", "utf8_general_ci", $zdb);
        if ($api_resault == false) {
            # The cronjob was not added for some reason!
            TriggerLog($useraccount['ac_id_pk'], $b = "Unable to create mysql database (" . $_POST['inDatabase'] . ").");
        }

        # If the user submitted a 'new' request then we will simply add the cron task to the database...
        $sql = "INSERT INTO z_mysql (my_acc_fk,
										my_name_vc,
										my_created_ts) VALUES (
										" . $acc_fk . ",
										'" . Cleaner('i', $useraccount['ac_user_vc'] . "_" . $_POST['inDatabase']) . "',
										" . time() . ")";
        DataExchange("w", $z_db_name, $sql);
        # Now we have to add the entry to the cron file.
        # Now we add some infomation to the system log.
        TriggerLog($useraccount['ac_id_pk'], $b = "New MySQL database added by user (" . Cleaner('i', $_POST['inDatabase']) . ").");
        header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    } else {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=exists");
    }
    exit;
}


if ($_POST['inAction'] == 'delete') {
# User has choosen to delete the task...
    do {
        if (isset($_POST['inDelete_' . $rowmysql['my_id_pk']])) {

            # Ok so lets drop the MySQL database...
            $api_resault = zapi_mysqldb_remove($rowmysql['my_name_vc'], $zdb);
            if ($api_resault == false) {
                # The cronjob was not added for some reason!
                TriggerLog($useraccount['ac_id_pk'], $b = "Unable to remove mysql database (" . $rowmysql['my_name_vc'] . ").");
            }

            # Log the action in the database...
            TriggerLog($useraccount['ac_id_pk'], $b = "User MySQL Database ID: " . $rowmysql['my_id_pk'] . " was deleted.");
            # Do all other account deleted related stuff here!!!
            $sql = "UPDATE z_mysql SET my_deleted_ts=" . time() . " WHERE my_id_pk=" . $rowmysql['my_id_pk'] . "";
            DataExchange("w", $z_db_name, $sql);
            # Now we do the rest, which is to go and delete the cron entry from the file...
        }
    } while ($rowmysql = mysql_fetch_assoc($listmysql));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}
?>