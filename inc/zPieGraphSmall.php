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
include ("../lib/jpgraph/jpgraph.php");
include ("../lib/jpgraph/jpgraph_pie.php");
include ("../lib/jpgraph/jpgraph_pie3d.php");
include ("zCoreFunctions.php");

$quota = $_GET['quota'];
$diplayquota = $quota;
if ($quota == 0) {
    $quota = -1;
    $diplayquota = 0;
}

if (strstr($_GET['used'], 'FFS')) {
    $used = str_replace('FFS', '', $_GET['used']);
    if ($used > $quota) {
        $value = "" . ($quota - $used) . ",0";
        $key = "Free: (" . FormatFileSize(0) . "),Used: (" . FormatFileSize(($used)) . ")";
        $data = explode(",", "" . $value . "");

        $graph = new PieGraph(200, 125, "auto");
        #$graph->SetAntiAlias(true);
        $graph->title->Set("");
        $graph->legend->Pos(0.0, 0.0);
        $p1 = new PiePlot3d($data);
        $p1->SetSliceColors(array('#333333', '#333333'));
        $p1->SetCenter(0.5);
        $p1->SetSize(50);
        $p1->SetAngle(45);
        $p1->value->SetColor("#666666");
        $p1->SetLegends(explode(",", "" . $key . ""));
        $graph->Add($p1);
        $graph->Stroke();
    } else {
        $value = "" . ($quota - $used) . "," . ($used) . "";
        $key = "Free: (" . FormatFileSize(($diplayquota - $used)) . "),Used: (" . FormatFileSize(($used)) . ")";
        $data = explode(",", "" . $value . "");

        $graph = new PieGraph(200, 125, "auto");
        #$graph->SetAntiAlias(true);
        $graph->title->Set("");
        $graph->legend->Pos(0.0, 0.0);
        $p1 = new PiePlot3d($data);
        $p1->SetSliceColors(array('#039ACA', '#333333'));
        $p1->SetCenter(.5);
        $p1->SetSize(50);
        $p1->SetAngle(45);
        $p1->value->SetColor("#666666");
        $p1->SetLegends(explode(",", "" . $key . ""));
        $graph->Add($p1);
        $graph->Stroke();
    }
} else {

    if ($_GET['used'] > $quota) {
        $value = "" . ($quota - $_GET['used']) . ",0";
        $key = "Free: (0),Used: (" . ($_GET['used']) . ")";
        $data = explode(",", "" . $value . "");

        $graph = new PieGraph(200, 125, "auto");
        #$graph->SetAntiAlias(true);
        $graph->title->Set("");
        $graph->legend->Pos(0.0, 0.0);
        $p1 = new PiePlot3d($data);
        $p1->SetSliceColors(array('#333333', '#333333'));
        $p1->SetCenter(0.5);
        $p1->SetSize(50);
        $p1->SetAngle(45);
        $p1->value->SetColor("#666666");
        $p1->SetLegends(explode(",", "" . $key . ""));
        $graph->Add($p1);
        $graph->Stroke();
    } else {
        $value = "" . ($quota - $_GET['used']) . "," . ($_GET['used']) . "";
        $key = "Free: (" . ($diplayquota - $_GET['used']) . "),Used: (" . ($_GET['used']) . ")";
        $data = explode(",", "" . $value . "");

        $graph = new PieGraph(200, 125, "auto");
        #$graph->SetAntiAlias(true);
        $graph->title->Set("");
        $graph->legend->Pos(0.0, 0.0);
        $p1 = new PiePlot3d($data);
        $p1->SetSliceColors(array('#039ACA', '#333333'));
        $p1->SetCenter(.5);
        $p1->SetSize(50);
        $p1->SetAngle(45);
        $p1->value->SetColor("#666666");
        $p1->SetLegends(explode(",", "" . $key . ""));
        $graph->Add($p1);
        $graph->Stroke();
    }
}
?>
