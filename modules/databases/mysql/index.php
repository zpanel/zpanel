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

$sql = "SELECT * FROM z_mysql WHERE my_acc_fk=" . $useraccount['ac_id_pk'] . " AND my_deleted_ts IS NULL";
$listmysql = DataExchange("r", $z_db_name, $sql);
$rowmysql = mysql_fetch_assoc($listmysql);
$totalmysql = DataExchange("t", $z_db_name, $sql);

echo $lang['135'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['138'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'exists')) {
    echo "<br><div class=\"zannouce\">" . $lang['137'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'blank')) {
    echo "<br><div class=\"zannouce\">" . $lang['298'] . "</div>";
}
echo "<br><h2>" . $lang['134'] . "</h2>";
if ($totalmysql > 0) {
    ?><form id="frmMySQL" name="frmMySQL" method="post" action="runner.php?load=obj_mysql">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['130']; ?></th>
                <th><?php echo $lang['131']; ?></th>
                <th></th>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowmysql['my_name_vc']); ?></td>
                    <td><?php echo Cleaner('o', FormatFileSize($rowmysql['my_usedspace_bi'])); ?></td>
                    <td><input type="submit" name="inDelete_<?php echo $rowmysql['my_id_pk']; ?>" id="inDelete_<?php echo $rowmysql['my_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                </tr>
            <?php } while ($rowmysql = mysql_fetch_assoc($listmysql)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete" />
    </form>
    <?php
} else {
    echo $lang['132'];
}
if ($quotainfo['qt_mysql_in'] > GetQuotaUsages('mysql', $useraccount['ac_id_pk'])) {
    echo "<br><h2>" . $lang['133'] . "</h2>";
    ?>
    <form id="frmNewCron" name="frmNewCron" method="post" action="runner.php?load=obj_mysql">
        <table class="zform">
            <tr>
                <th><?php echo $lang['136']; ?>:</th>
                <td><?php echo $useraccount['ac_user_vc']; ?>_<input name="inDatabase" type="text" id="inDatabase" size="30" /></td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                    <input type="hidden" name="inAction" value="new" />
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
            </tr>
        </table>
    </form><?php } ?>