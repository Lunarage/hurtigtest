<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * Renders admin.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */
require_once "../../include/init.php";
$arguments["title"] = "Hurtigtest";

$query = "
  SELECT t.id, t.start_tid, t.slutt_tid, t.plasser, count(p.*) AS antall
  FROM hurtigtest.tidspunkter AS t
  LEFT JOIN hurtigtest.paameldinger AS p
    ON t.id = p.tidspunkt_id
  WHERE age(t.slutt_tid) < '0 hours'
  GROUP BY t.id
  ORDER BY t.start_tid;
";
$result = pg_query($query);

$arguments["tidspunkter"] = [];
while($row = pg_fetch_array($result)){
  $start_tid = strtotime($row["start_tid"]);
  $slutt_tid = strtotime($row["slutt_tid"]);
  $row["weekday"] =  weekdayToNorwegian((int) date("N", $start_tid));
  $row["day"] = date("j", $start_tid);
  $row["month"] =  monthToNorwegian((int) date("n", $start_tid));
  $row["startTime"] = date("H:i", $start_tid);
  $row["endTime"] = date("H:i", $slutt_tid);
  $arguments["tidspunkter"][] = $row;
}

$twig->load("admin.twig")->display($arguments);
?>
