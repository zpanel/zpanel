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
# Now we turn the values that are avaliable to be saved...
$sql = "SELECT * FROM z_settings WHERE st_editable_in=1";
$listoptions = DataExchange("r", $z_db_name, $sql);
$rowoptions = mysql_fetch_assoc($listoptions);
if (isset($_POST['inSaveSystem'])) {
    do {
# Now we simply update the account details based on the current session.
        DataExchange("w", $z_db_name, "UPDATE z_settings SET st_value_tx='" . Cleaner('o', $_POST['' . $rowoptions['st_name_vc'] . '']) . "' WHERE st_name_vc = '" . $rowoptions['st_name_vc'] . "'");
    } while ($rowoptions = mysql_fetch_assoc($listoptions));
}
if (isset($_POST['inSaveTemplate'])) {
    DataExchange("w", $z_db_name, "UPDATE z_settings SET st_value_tx='" . Cleaner('o', $_POST['inTemplate']) . "' WHERE st_name_vc = 'zpanel_template'");
    DataExchange("w", $z_db_name, "UPDATE z_settings SET st_value_tx='" . Cleaner('o', str_replace(".php", "", $_POST['inTranslation'])) . "' WHERE st_name_vc = 'zpanel_lang'");
}
$returnurl = GetNormalModuleURL($returnurl) . "&r=ok";
header("location: " . $returnurl . "");
exit;
?>