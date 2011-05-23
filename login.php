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
include('lang/' . GetSystemOption('zpanel_lang') . '.php');

# If the login process has been initiated then lets check the login!
if (isset($_SESSION['zUserID'])) {
    if (isset($_GET['logout'])) {
        # Lets log the user out!
        $_SESSION['zUsername'] = NULL;
        $_SESSION['zUserID'] = NULL;
        unset($_SESSION['zUsername']);
        unset($_SESSION['zUserID']);
    } else {
        header("location: ./");
        exit();
    }
} else {
    if (isset($_POST['inUsername'])) {
        $username = Cleaner('i', $_POST['inUsername']);
        $password = Cleaner('i', $_POST['inPassword']);
        $sql = "SELECT ac_id_pk, ac_user_vc FROM z_accounts WHERE ac_user_vc='" . $username . "' AND ac_pass_vc='" . md5($password) . "' AND ac_deleted_ts IS NULL";
        $checklogin = DataExchange("l", $z_db_name, $sql);
        $accountexists = DataExchange("t", $z_db_name, $sql);
        $lockdown_option = GetSystemOption('zpanel_lockdown');
        $_SESSION['zUsername'] = $checklogin['ac_user_vc'];
        $_SESSION['zUserID'] = $checklogin['ac_id_pk'];
        include('inc/zAccountDetails.php');
        $is_admin = $permissionset['pr_admin_in'];
        if ($accountexists > 0) {
            if ($lockdown_option == 1) {
                if ($is_admin == 1) {

                    TriggerLog($checklogin['ac_id_pk'], "User has logged into ZPanel.");
                    $sql = "UPDATE z_settings SET st_value_tx='http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "' WHERE st_name_vc='login_url';";
                    DataExchange("w", $z_db_name, $sql);
                    header("location: ./index.php");
                    exit;
                } else {
                    $_SESSION['zUsername'] = NULL;
                    $_SESSION['zUserID'] = NULL;
                    unset($_SESSION['zUsername']);
                    unset($_SESSION['zUserID']);
                    header("location: ./login.php?error=noadmin");
                    exit;
                }
            } //end locked down
            elseif ($lockdown_option != 1) {
                $_SESSION['zUsername'] = $checklogin['ac_user_vc'];
                $_SESSION['zUserID'] = $checklogin['ac_id_pk'];
                TriggerLog($checklogin['ac_id_pk'], "User has logged into ZPanel.");
                $sql = "UPDATE z_settings SET st_value_tx='http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "' WHERE st_name_vc='login_url';";
                DataExchange("w", $z_db_name, $sql);
                header("location: ./index.php");
                exit;
            }
        } else {
            $_SESSION['zUsername'] = NULL;
            $_SESSION['zUserID'] = NULL;
            unset($_SESSION['zUsername']);
            unset($_SESSION['zUserID']);
            header("location: ./login.php?error=nologin");
            exit;
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>ZPanel > Account Login</title>
        <style type="text/css">
            <!--
            body {
                background-color: #666;
                background-position:top left;
                background-repeat:no-repeat;
                background-attachment:fixed;
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
            }
            .poweredbox {
                font-family: Georgia, "Times New Roman", Times, serif;
                font-size: 12px;
                color: #FFF;
                background-color: #666;
                padding-top: 10px;
                padding-right: 5px;
                padding-left: 5px;
                text-align: left;
                border-top-width: 1px;
                border-right-width: 1px;
                border-bottom-width: 1px;
                border-left-width: 1px;
                border-top-style: dotted;
                border-right-style: none;
                border-bottom-style: none;
                border-left-style: none;
                border-top-color: #999;
                border-right-color: #999;
                border-bottom-color: #999;
                border-left-color: #999;
            }
            .poweredbox a {
                color: #FFF;
            }
            .poweredbox a:hover {
                color: #FFF;
                text-decoration:underline;
            }
            .login_panel {
                background-color: #FFF;
            }

            -->
        </style></head>
    <body>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="login_panel">
                    <?php
# Load the HTML login screen....
                    include("static/login_template/login.html");
                    ?>
                </td>
            </tr>
            <tr>
                <td class="poweredbox"><p><strong>Powered by <a href="http://www.zpanel.co.uk/" target="_blank" title="ZPanel - Taking hosting to the next level!">ZPanel</a></strong><br>
                        This server is running: ZPanel <?php echo GetSystemOption('zpanel_version'); ?>
                        <br>
                    </p></td>
            </tr>
        </table>
        <script language="javascript">
            document.frmZLogin.inUsername.focus();
        </script>
    </body>
</html>

