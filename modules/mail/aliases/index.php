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

$sql = "SELECT * FROM z_aliases WHERE al_acc_fk=" . $useraccount['ac_id_pk'] . " AND al_deleted_ts IS NULL";
$listforwarders = DataExchange("r", $z_db_name, $sql);
$rowforwarders = mysql_fetch_assoc($listforwarders);
$totalforwarders = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
$listexdomains = DataExchange("r", $z_db_name, $sql);
$rowexdomains = mysql_fetch_assoc($listexdomains);
$totalexdomains = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_mailboxes WHERE mb_acc_fk=" . $useraccount['ac_id_pk'] . " AND mb_deleted_ts IS NULL";
$listmbdomains = DataExchange("r", $z_db_name, $sql);
$rowmbdomains = mysql_fetch_assoc($listmbdomains);
$totalmbdomains = DataExchange("t", $z_db_name, $sql);

echo $lang['243'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['238'] . "</div>";
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

echo "<br><h2>" . $lang['242'] . "</h2>";
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
                    <td><?php echo Cleaner('o', $rowforwarders['al_address_vc']); ?></td>
                    <td><?php echo Cleaner('o', $rowforwarders['al_destination_vc']); ?></td>
                    <td><input type="submit" name="inDelete_<?php echo $rowforwarders['al_id_pk']; ?>" id="inDelete_<?php echo $rowforwarders['al_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                </tr>
            <?php } while ($rowforwarders = mysql_fetch_assoc($listforwarders)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete_alias" />
    </form>
    <?php
} else {
    echo $lang['239'];
}
if ($quotainfo['qt_fowarders_in'] > GetQuotaUsages('forwarders', $useraccount['ac_id_pk'])) {
    echo "<br><h2>" . $lang['240'] . "</h2>";
    ?>
    <?php if (GetQuotaUsages('domains', $useraccount['ac_id_pk']) > 0) { ?>
        <form id="frmNewAlias" name="frmNewAlias" method="post" action="runner.php?load=obj_mail">
            <table class="zform">
                <tr>
                    <th><?php echo $lang['247']; ?></th>
                    <td><input name="inAddress" type="text" id="inAddress" />
                        <select name="inDomain" id="inDomain">
                            <option value="">-- <?php echo $lang['29']; ?> --</option>
                            <?php
                            do {
                                echo "<option value=\"" . $rowexdomains['vh_name_vc'] . "\">@" . $rowexdomains['vh_name_vc'] . "</option>";
                            } while ($rowexdomains = mysql_fetch_assoc($listexdomains));
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <th><?php echo $lang['185']; ?>:</th>
                    <td><select name="inDestination" id="inDestination">
                            <option value="">-- <?php echo $lang['245']; ?> --</option>
                            <?php
                            do {
                                echo "<option value=\"" . $rowmbdomains['mb_address_vc'] . "\">" . $rowmbdomains['mb_address_vc'] . "</option>";
                            } while ($rowmbdomains = mysql_fetch_assoc($listmbdomains));
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                        <input type="hidden" name="inAction" value="NewAlias" />
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
                </tr>
            </table>
        </form><?php
                        } else {
                            echo $lang['241'];
                        }
                    }
                    ?>
