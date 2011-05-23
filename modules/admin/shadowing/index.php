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
include('conf/zcnf.php');
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
include('inc/zAccountDetails.php');

# Now we have to get a list of the packages and display current permissions for each package.
if ($permissionset['pr_admin_in'] == 1) {
    $sql = "SELECT * FROM z_accounts WHERE ac_deleted_ts IS NULL AND ac_id_pk<>1";
} else {
    $sql = "SELECT * FROM z_accounts WHERE ac_reseller_fk=" . $useraccount['ac_id_pk'] . " AND ac_deleted_ts IS NULL";
}
$listclients = DataExchange("r", $z_db_name, $sql);
$rowclients = mysql_fetch_assoc($listclients);
$totalclients = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_packages WHERE pk_reseller_fk=" . $useraccount['ac_id_pk'] . " AND pk_deleted_ts IS NULL";
$listpackages = DataExchange("r", $z_db_name, $sql);
$rowpackages = mysql_fetch_assoc($listpackages);

echo $lang['235'] . "<br>";
echo "<br><h2>" . $lang['107'] . "</h2>";
if ($totalclients > 0) {
    ?>
    <form action="runner.php?load=obj_shadow" method="post" name="frmShadow" id="frmShadow">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['109']; ?></th>
                <th><?php echo $lang['110']; ?></th>
                <th><?php echo $lang['111']; ?></th>
                <th><?php echo $lang['112']; ?></th>
                <th>&nbsp;</th>
            </tr>
            <?php
            do {
                # Get package infomation for the user...
                $sql = "SELECT pk_name_vc FROM z_packages WHERE pk_id_pk=" . $rowclients['ac_package_fk'] . "";
                $package = DataExchange("l", $z_db_name, $sql);
                ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowclients['ac_user_vc']); ?></td>
                    <td><?php echo $package['pk_name_vc']; ?></td>
                    <td><?php echo FormatFileSize(GetQuotaUsages('diskspace', $rowclients['ac_id_pk'])); ?></td>
                    <td><?php echo FormatFileSize(GetQuotaUsages('bandwidth', $rowclients['ac_id_pk'])); ?></td>
                    <td><input type="submit" name="inShadow_<?php echo $rowclients['ac_id_pk']; ?>" id="inShadow_<?php echo $rowclients['ac_id_pk']; ?>" value="<?php echo $lang['236']; ?>" /></td>
                </tr>
    <?php } while ($rowclients = mysql_fetch_assoc($listclients)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
    </form>
<?php } ?>