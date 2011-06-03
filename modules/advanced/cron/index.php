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

$sql = "SELECT * FROM z_cronjobs WHERE ct_acc_fk=" . $useraccount['ac_id_pk'] . " AND ct_deleted_ts IS NULL";
$listtasks = DataExchange("r", $z_db_name, $sql);
$rowtasks = mysql_fetch_assoc($listtasks);
$totaltasks = DataExchange("t", $z_db_name, $sql);

echo $lang['120'] . "<br>";

if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['394'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'blank')) {
    echo "<br><div class=\"zannouce\">" . $lang['300'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'noexists')) {
    echo "<br><div class=\"zannouce\">" . $lang['301'] . " ".$useraccount['ac_user_vc']."</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'error')) {
    echo "<br><div class=\"zannouce\">" . $lang['395'] . "</div>";
}

echo "<br><h2>" . $lang['121'] . "</h2>";

if ($totaltasks > 0) {
    ?><form id="frmCronTasks" name="frmCronTasks" method="post" action="runner.php?load=obj_cronjobs">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['123']; ?></th>
                <th><?php echo $lang['124']; ?></th>
                <th></th>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowtasks['ct_script_vc']); ?></td>
                    <td><?php echo Cleaner('o', $rowtasks['ct_description_tx']); ?></td>
                    <td><input type="submit" name="inDelete_<?php echo $rowtasks['ct_id_pk']; ?>" id="inDelete_<?php echo $rowtasks['ct_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                </tr>
            <?php } while ($rowtasks = mysql_fetch_assoc($listtasks)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete" />
    </form>
    <?php
} else {
    echo $lang['125'];
}
echo "<br><h2>" . $lang['122'] . "</h2>";
?>
<form id="frmNewCron" name="frmNewCron" method="post" action="runner.php?load=obj_cronjobs">
    <table class="zform">
        <tr>
            <th><?php echo $lang['126']; ?>:</th>
            <td><input name="inScript" type="text" id="inScript" size="50" /><br /><?php echo $lang['129']; ?></td>
        </tr>
        <tr>
            <th><?php echo $lang['127']; ?>:</th>
            <td><input name="inDescription" type="text" id="inDescription" size="50" maxlength="50" /></td>
        </tr>
        <tr>
            <th><?php echo $lang['150']; ?>:</th>
            <td><select name="inTiming" id="inTiming">
                    <option value="* * * * *">Every 1 minute</option>
                    <option value="0,5,10,15,20,25,30,35,40,45,50,55 * * * *">Every 5 minutes</option>
                    <option value="0,10,20,30,40,50 * * * *">Every 10 minutes</option>
                    <option value="0,30 * * * *">Every 30 minutes</option>
                    <option value="0 * * * *">Every 1 hour</option>
                    <option value="0 0,2,4,6,8,10,12,14,16,18,20,22 * * *">Every 2 hours</option>
                    <option value="0 0,8,16 * * *">Every 8 hours</option>
                    <option value="0 0,12 * * *">Every 12 hours</option>
                    <option value="0 0 * * *">Every 1 day</option>
                    <option value="0 0 * * 0">Every week</option>
                </select></td>
        </tr>
        <tr>
            <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                <input type="hidden" name="inAction" value="new" />
                <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
        </tr>
    </table>
</form>