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
$current_pass = Cleaner("i", $_POST['inCurPass']);
$newpass = Cleaner("i", $_POST['inNewPass']);
$conpass = Cleaner("i", $_POST['inConPass']);
$doresetmysql = Cleaner("i", $_POST['inResMySQL']);
$returnurl = $_POST['inReturnURL'];

if (md5($current_pass) <> $useraccount['ac_pass_vc'] || (empty($newpass))) {
    # Current password does not match!
    $endonerror = "&r=error";
} else {
    if ($newpass == $conpass) {
        # Check that the new password matches the confirmation box.
        if ($doresetmysql <> '1') {
            # User has selected to update ZPanel account password only!
            $sql = "UPDATE z_accounts SET ac_pass_vc='" . md5($newpass) . "' WHERE ac_id_pk=" . $acc_fk . "";
            DataExchange("w", $z_db_name, $sql);
            TriggerLog($acc_fk, "User has updated their ZPanel account password.");
            $endonerror = "&r=ok";
        } else {
            # User has selected to change both passwords.
            $sql = "UPDATE z_accounts SET ac_pass_vc='" . md5($newpass) . "' WHERE ac_id_pk=" . $acc_fk . "";
            DataExchange("w", $z_db_name, $sql);
            zapi_mysqluser_setpass($useraccount['ac_user_vc'], $newpass, $zdb);
            TriggerLog($acc_fk, "User has updated both their ZPanel and MySQL account passwords.");
            $endonerror = "&r=ok-both";
        }
    } else {
        $endonerror = "&r=error";
    }
}
$returnurl = GetNormalModuleURL($returnurl) . "" . $endonerror . "";
header("location: " . $returnurl . "");
exit;
?>