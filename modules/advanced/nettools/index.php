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

if (phpversion() >= "4.2.0") {
    extract($_POST);
    extract($_GET);
    extract($_SERVER);
    extract($_ENV);
}
?>
<script>
    function m(el) {
        if (el.defaultValue==el.value) el.value = ""
    }
</script>
<?php

echo $lang['49'];
echo "<br><br>";
echo "<form method=\"post\" action=\"" . $_SERVER['REQUEST_URI'] . "\">
<table class=\"zform\">
<tr>
<th><b>" . $lang['62'] . "</b></th>
<th><b>" . $lang['63'] . "</b></th>
</tr>
<tr valign=\"top\">
<td>
<input type=\"radio\" name=\"queryType\" value=\"lookup\">" . $lang['64'] . "<br>
<input type=\"radio\" name=\"queryType\" value=\"dig\">" . $lang['65'] . "<br>
<input type=\"radio\" name=\"queryType\" value=\"wwwhois\">" . $lang['66'] . "<br>
<input type=\"radio\" name=\"queryType\" value=\"arin\">" . $lang['67'] . "</td>
<td>
<input type=\"radio\" name=\"queryType\" value=\"checkp\">" . $lang['68'] . "
<input class=\"inputbox\" type=\"text\" name=\"portNum\" size=\"5\" maxlength=\"5\" value=\"80\">
<br>
<input type=\"radio\" name=\"queryType\" value=\"p\">" . $lang['69'] . "<br>
<input type=\"radio\" name=\"queryType\" value=\"tr\">" . $lang['70'] . "<br>
<input type=\"radio\" name=\"queryType\" value=\"all\" checked>" . $lang['71'] . "</td>
</tr>
<tr>
<td>
<input class=\"inputbox\" type=\"text\" name=\"target\" value=\"" . $lang['72'] . "\" onFocus=\"m(this)\">
</td>
<td>
<input class=\"inputbox\" type=\"submit\" name=\"Submit\" value=\"" . $lang['30'] . "\">
</td>
</tr>
</table>
</form>";

if (isset($_POST['Submit'])) {
#Global kludge for new gethostbyaddr() behavior in PHP 4.1x
    $ntarget = "";

#Some functions

    function message($msg) {
        echo $msg;
        flush();
    }

    function lookup($target) {
        global $ntarget;
        $msg = "$target resolved to ";
        if (eregi("[a-zA-Z]", $target))
            $ntarget = gethostbyname($target);
        else
            $ntarget = gethostbyaddr($target);
        $msg .= $ntarget;
        message($msg);
    }

    function getip($target) {
        global $ntarget;
        if (eregi("[a-zA-Z]", $target))
            $ntarget = gethostbyname($target);
        else
            $ntarget = $target;
        $msg .= $ntarget;
        return($msg);
    }

    function dig($target) {
        global $ntarget;
        message("DNS Query Results:");
#$target = gethostbyaddr($target);
#if (! eregi("[a-zA-Z]", ($target = gethostbyaddr($target))) )
        if ((!eregi("[a-zA-Z]", $target) && (!eregi("[a-zA-Z]", $ntarget))))
            $msg .= "Can't do a DNS query without a hostname.";
        else {
            if (!eregi("[a-zA-Z]", $target))
                $target = $ntarget;
            if (!$msg .= trim(nl2br(`dig any '$target'`))) #bugfix
                $msg .= "The <i>dig</i> command is not working at this time.";
        }
#TODO: Clean up output, remove ;;'s and DiG headers
        $msg .= "</blockquote></p>";
        message($msg);
    }

    function wwwhois($target) {
        global $ntarget;
        $server = "whois.crsnic.net";
        message("<p><b>WWWhois Results:</b><blockquote>");
#Determine which WHOIS server to use for the supplied TLD
        if ((eregi("\.com\$|\.net\$|\.edu\$", $target)) || (eregi("\.com\$|\.net\$|\.edu\$", $ntarget)))
            $server = "whois.crsnic.net";
        else if ((eregi("\.info\$", $target)) || (eregi("\.info\$", $ntarget)))
            $server = "whois.afilias.net";
        else if ((eregi("\.org\$", $target)) || (eregi("\.org\$", $ntarget)))
            $server = "whois.corenic.net";
        else if ((eregi("\.name\$", $target)) || (eregi("\.name\$", $ntarget)))
            $server = "whois.nic.name";
        else if ((eregi("\.biz\$", $target)) || (eregi("\.biz\$", $ntarget)))
            $server = "whois.nic.biz";
        else if ((eregi("\.us\$", $target)) || (eregi("\.us\$", $ntarget)))
            $server = "whois.nic.us";
        else if ((eregi("\.cc\$", $target)) || (eregi("\.cc\$", $ntarget)))
            $server = "whois.enicregistrar.com";
        else if ((eregi("\.ws\$", $target)) || (eregi("\.ws\$", $ntarget)))
            $server = "whois.nic.ws";
        else {
            $msg .= "I only support .com, .net, .org, .edu, .info, .name, .us, .cc, .ws, and .biz.</blockquote>";
            message($msg);
            return;
        }

        message("Connecting to $server...<br><br>");
        if (!$sock = fsockopen($server, 43, $num, $error, 10)) {
            unset($sock);
            $msg .= "Timed-out connecting to $server (port 43)";
        } else {
            fputs($sock, "$target\n");
            while (!feof($sock))
                $buffer .= fgets($sock, 10240);
        }
        fclose($sock);
        if (!eregi("Whois Server:", $buffer)) {
            if (eregi("no match", $buffer))
                message("NOT FOUND: No match for $target<br>");
            else
                message("Ambiguous query, multiple matches for $target:<br>");
        }
        else {
            $buffer = split("\n", $buffer);
            for ($i = 0; $i < sizeof($buffer); $i++) {
                if (eregi("Whois Server:", $buffer[$i]))
                    $buffer = $buffer[$i];
            }
            $nextServer = substr($buffer, 17, (strlen($buffer) - 17));
            $nextServer = str_replace("1:Whois Server:", "", trim(rtrim($nextServer)));
            $buffer = "";
            message("Deferred to specific whois server: $nextServer...<br><br>");
            if (!$sock = fsockopen($nextServer, 43, $num, $error, 10)) {
                unset($sock);
                $msg .= "Timed-out connecting to $nextServer (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock))
                    $buffer .= fgets($sock, 10240);
                fclose($sock);
            }
        }
        $msg .= nl2br($buffer);
        $msg .= "</blockquote></p>";
        message($msg);
    }

    function arin($target) {
        $server = "whois.arin.net";
        message("<p><b>IP Whois Results:</b><blockquote>");
        if (!$target = gethostbyname($target))
            $msg .= "Can't IP Whois without an IP address.";
        else {
            message("Connecting to $server...<br><br>");
            if (!$sock = fsockopen($server, 43, $num, $error, 20)) {
                unset($sock);
                $msg .= "Timed-out connecting to $server (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock))
                    $buffer .= fgets($sock, 10240);
                fclose($sock);
            }
            if (eregi("RIPE.NET", $buffer))
                $nextServer = "whois.ripe.net";
            else if (eregi("whois.apnic.net", $buffer))
                $nextServer = "whois.apnic.net";
            else if (eregi("nic.ad.jp", $buffer)) {
                $nextServer = "whois.nic.ad.jp";
                #/e suppresses Japanese character output from JPNIC
                $extra = "/e";
            } else if (eregi("whois.registro.br", $buffer))
                $nextServer = "whois.registro.br";
            if ($nextServer) {
                $buffer = "";
                message("Deferred to specific whois server: $nextServer...<br><br>");
                if (!$sock = fsockopen($nextServer, 43, $num, $error, 10)) {
                    unset($sock);
                    $msg .= "Timed-out connecting to $nextServer (port 43)";
                } else {
                    fputs($sock, "$target$extra\n");
                    while (!feof($sock))
                        $buffer .= fgets($sock, 10240);
                    fclose($sock);
                }
            }
            $buffer = str_replace(" ", "&nbsp;", $buffer);
            $msg .= nl2br($buffer);
        }
        $msg .= "</blockquote></p>";
        message($msg);
    }

    function checkp($target, $portNum) {
        message("<p><b>Checking Port $portNum</b>...<blockquote>");
        if (!$sock = fsockopen($target, $portNum, $num, $error, 5))
            $msg .= "Port $portNum does not appear to be open.";
        else {
            $msg .= "Port $portNum is open and accepting connections.";
            fclose($sock);
        }
        $msg .= "</blockquote></p>";
        message($msg);
    }

    function p($target) {
        message("<p><b>Ping Results:</b><blockquote>");
        if (!$msg .= trim(nl2br(`ping '$target'`))) #bugfix
            $msg .= "Ping failed. Host may not be active.";
        $msg .= "</blockquote></p>";
        message($msg);
    }

    function tr($target) {
        message("<p><b>Traceroute Results:</b><blockquote>");
        $totrace = getip($target);
        if (!$msg .= trim(nl2br(`tracert $totrace`))) #bugfix
            $msg .= "Traceroute failed. Host may not be active.";
        $msg .= "</blockquote></p>";
        message($msg);
    }

#If the form has been posted, process the query, otherwise there's 
#nothing to do yet

    if (!isset($queryType)) {
        exit;
    }

#Make sure the target appears valid

    if ((!$target) || (!preg_match("/^[\w\d\.\-]+\.[\w\d]{1,4}$/i", $target))) { #bugfix
        message("Error: You did not specify a valid target host or IP.");
        exit;
    }

#Figure out which tasks to perform, and do them

    if (($queryType == "all") || ($queryType == "lookup"))
        lookup($target);
    if (($queryType == "all") || ($queryType == "dig"))
        dig($target);
    if (($queryType == "all") || ($queryType == "wwwhois"))
        wwwhois($target);
    if (($queryType == "all") || ($queryType == "arin"))
        arin($target);
    if (($queryType == "all") || ($queryType == "checkp"))
        checkp($target, $portNum);
    if (($queryType == "all") || ($queryType == "p"))
        p($target);
    if (($queryType == "all") || ($queryType == "tr"))
        tr($target);
}
?>