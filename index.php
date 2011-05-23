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
session_start();
include('conf/zcnf.php');
include('inc/zDataExchange.php');
include('inc/zCoreFunctions.php');
include('inc/zCheckAuth.php');
include('inc/zAccountDetails.php');
include('inc/zWebLoader.php');
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');

# We check that a module is to be run if not we'll display a list of the module icons.
if (!isset($_GET['p'])) {
    $body = "inc/zModuleLoader.php";
    echo eval(BuildApp($body, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo));
} else {
    if (!isset($_GET['e']) || $_GET['e'] == '') {
        if ($_GET['p'] == "modules") {
            $body = "inc/zModuleLoader.php";
            echo eval(BuildApp($body, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo));
        } else {
            # Check the permissions before displaying the module content.
            require_once('modules/' . $_GET['c'] . '/catinfo.zp.php');
            if (CheckModuleCatForPerms($thiscat['level_required'], $permissionset) == 1) {
                $body = "modules/" . $_GET['c'] . "/" . $_GET['p'] . "/index.php";
                require("modules/" . $_GET['c'] . "/" . $_GET['p'] . "/modinfo.zp.php");
                echo eval(BuildApp($body, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo));
            } else {
                echo $lang['73'];
            }
        }
    } else {
        require_once('modules/' . $_GET['c'] . '/catinfo.zp.php');
        if (CheckModuleCatForPerms($thiscat['level_required'], $permissionset) == 1) {
            $body = "modules/" . $_GET['c'] . "/" . $_GET['p'] . "/" . $_GET['e'] . ".php";
            require("modules/" . $_GET['c'] . "/" . $_GET['p'] . "/modinfo.zp.php");
            echo eval(BuildApp($body, $useraccount, $packageinfo, $quotainfo, $permissionset, $personalinfo));
        } else {
            echo $lang['73'];
        }
    }
}
?>