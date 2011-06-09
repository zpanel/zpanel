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

$sql = "SELECT * FROM z_forwarders WHERE fw_acc_fk=" . $useraccount['ac_id_pk'] . " AND fw_deleted_ts IS NULL";
$listforwarders = DataExchange("r", $z_db_name, $sql);
$rowforwarders = mysql_fetch_assoc($listforwarders);
$totalforwarders = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_mailboxes WHERE mb_acc_fk=" . $useraccount['ac_id_pk'] . " AND mb_deleted_ts IS NULL";
$listexdomains = DataExchange("r", $z_db_name, $sql);
$rowexdomains = mysql_fetch_assoc($listexdomains);
$totalexdomains = DataExchange("t", $z_db_name, $sql);

echo $lang['186'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['190'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'noemail')) {
    echo "<br><div class=\"zannouce\">" . $lang['246'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'nodomain')) {
    echo "<br><div class=\"zannouce\">" . $lang['191'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'nodest')) {
    echo "<br><div class=\"zannouce\">" . $lang['192'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'exists')) {
    echo "<br><div class=\"zannouce\">" . $lang['180'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'notexists')) {
    echo "<br><div class=\"zannouce\">" . $lang['190'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'notvalid')) {
    echo "<br><div class=\"zannouce\">" . $lang['421'] . "</div>";
}
echo "<br><h2>" . $lang['187'] . "</h2>";
if ($totalforwarders > 0) {
    ?><form id="frmMailboxes" name="frmMailboxes" method="post" action="runner.php?load=obj_mail">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['181']; ?></th>
                <th><?php echo $lang['185']; ?></th>
                <th></th>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowforwarders['fw_address_vc']); ?></td>
                    <td><?php echo Cleaner('o', $rowforwarders['fw_destination_vc']); ?></td>
                    <td><input type="submit" name="inDelete_<?php echo $rowforwarders['fw_id_pk']; ?>" id="inDelete_<?php echo $rowforwarders['fw_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /><input type="hidden" name="ForwardMailbox" value="<?php echo $rowforwarders['fw_address_vc']; ?>" /><input type="hidden" name="fw_address_vc" value="<?php echo $rowforwarders['fw_address_vc']; ?>" /></td>
                </tr>
            <?php } while ($rowforwarders = mysql_fetch_assoc($listforwarders)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete_forwarder" />
    </form>
    <?php
} else {
    echo $lang['239'];
}
if ($quotainfo['qt_fowarders_in'] > GetQuotaUsages('forwarders', $useraccount['ac_id_pk'])) {
    echo "<br><h2>" . $lang['188'] . "</h2>";
    ?>
    <?php if (GetQuotaUsages('domains', $useraccount['ac_id_pk']) > 0) { ?>
        <form id="frmNewForwarder" name="frmNewForwarder" method="post" action="runner.php?load=obj_mail">
            <table class="zform">
                <tr>
                    <th><?php echo $lang['14']; ?></th>
                    <td><select name="inAddress" id="inAddress">
                            <option value="">-- <?php echo $lang['245']; ?> --</option>
                            <?php
                            do {
                                $sql = "SELECT fw_address_vc FROM z_forwarders WHERE fw_address_vc='" . $rowexdomains['mb_address_vc'] . "' AND fw_deleted_ts IS NULL";
                                $checkmailbox = DataExchange("r", $z_db_name, $sql);
                                $rowcheckmailbox = mysql_fetch_assoc($checkmailbox);
                                if ($rowcheckmailbox['fw_address_vc'] != $rowexdomains['mb_address_vc']) {
                                    echo "<option value=\"" . $rowexdomains['mb_address_vc'] . "\">" . $rowexdomains['mb_address_vc'] . "</option>";
                                }
                            } while ($rowexdomains = mysql_fetch_assoc($listexdomains));
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <th><?php echo $lang['185']; ?>:</th>
                    <td><input name="inDestinationName" type="text" id="inDestinationName"/> @ <input name="inDestinationDomain" type="text" id="inDestinationDomain"/></td>
                </tr>
                <tr>
                    <th><?php echo $lang['244']; ?>:</th>
                    <td><input type="checkbox" name="inLeaveOnServer" id="inLeaveOnServer" value="1"/></td>
                </tr>
                <tr>
                    <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                        <input type="hidden" name="inAction" value="NewForwarder" />
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
                </tr>
            </table>
        </form><?php
                } else {
                    echo $lang['233'];
                }
            }?>