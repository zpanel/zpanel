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
function DXErrorLog($a) {
    # DESCRIPTION: Special error reporting for the DataExchange function.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $error = $a;
    include('conf/zcnf.php');
    mysql_select_db($z_db_name, $zdb) or die("Unable to select database, database (" . $z_db_name . ") appears to not exsist!");
    $sqlquery = "INSERT INTO z_logs (lg_acc_fk,lg_when_ts,lg_ipaddress_vc,lg_details_tx) VALUES (0," . time() . ",'" . $_SERVER['REMOTE_ADDR'] . "','" . $error . "')";
    $sql = mysql_query($sqlquery, $zdb);
    echo "<strong>ZPanel Application Stack</strong><br> " . $error . "";
    exit();
}

function DataExchange($a, $b, $c) {
    # DESCRIPTION: Safely queries the MySQL database as well as reports any issues with the Query into the logs table.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $querytype = $a;
    $databaseconn = $b;
    $sqlquery = $c;
    include('conf/zcnf.php');
    mysql_select_db($databaseconn, $zdb) or die(DXErrorLog("Unable to select database, database (" . $z_db_name . ") appears to not exsist!"));
    $fretval = false;

    if ($querytype == 'r') {
        $sql = mysql_query($sqlquery, $zdb) or die(DXErrorLog("MySQL database error, The [READ] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . ""));
        $fretval = $sql;
    }
    if ($querytype == 'l') {
        $sql = mysql_query($sqlquery, $zdb) or die(DXErrorLog("MySQL database error, The [LIST] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . ""));
        $fretval = mysql_fetch_assoc($sql);
    }
    if ($querytype == 'w') {
        $sql = mysql_query($sqlquery, $zdb) or die(DXErrorLog("MySQL database error, The [WRITE] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . ""));
        $fretval = true;
    }
    if ($querytype == 't') {
        $sql = mysql_query($sqlquery, $zdb) or die(DXErrorLog("MySQL database error, The [SUM] query had an issue:\n\r" . $sqlquery . "\n\rMySQL Said:" . mysql_error() . ""));
        $fretval = mysql_num_rows($sql);
    }
    return $fretval;
}

?>