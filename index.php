<?php
global $_POST;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Connect to audioblast SQL server
include('database.php');

//Have we been sent a query array?
if (isset($_GET["query"])) {
  $qa = urldecode($_GET["query"]);
  $qa =  json_decode($qa, true);
} else {
  $qa = array();
}

if (!isset($qa["init"])) {
  $qa["init"] = true;
}


//Array of plugins to include
$plugins = array(
  "basic" => "basic.php",
  "checklist" => "checklist.php",
  "limits" => "limits.php",
  "taxonscope" => "taxonscope.php",
  "frequency" => "frequency.php"
);

//Get info about plugins that is needed to process them
foreach ($plugins as $name => $path) {
  include("plugins/".$path);
  $info = $name."_info";
  $plugins[$name] = $info();
  if ($qa["init"] == true) {
    $init = $name."_init";
    $qa = $init($qa);
  }
}

if (isset($qa["updatefilter"])) {
  $plugin = $qa["updatefilter"]["plugin"]."_update_filter";
  $qa = $plugin($qa, $qa["updatefilter"]["activity"], $qa["updatefilter"]["value"]);
  unset($qa["updatefilter"]);
}

foreach ($plugins as $name => $path) {
  if (isset($plugins[$name]["filter html"])) {
    $filt = $plugins[$name]["filter html"];
    $qa = $filt($qa);
  }
}

//Base query that is modifed by the plugins
$query = "SELECT SQL_CALC_FOUND_ROWS * FROM `audioblast-traits`.`taxa`";

//Process WHERE filters
$num_wheres = 0;
foreach ($plugins as $name => $data) {
  if (isset($data["where"])) {
    $func = $data["where"];
    $res = $func($qa);
    if ($res != false) {
      if ($num_wheres > 0) { $query .= " AND "; } else { $query .= " WHERE "; }
      $query .= $res;
      $num_wheres++;
    }
  }
}

$query .= " ORDER BY taxa.taxon";

//Only allow one plugin to set a limit
$num_limits = 0;
foreach ($plugins as $name => $data) {
  if (isset($data["limits"])) {
    if ($num_limits == 0) {
      $func = $name."_limits";
      $query = $func($query, $qa);
    } else {
      $qa["exec_notes"][] = "SQL LIMIT suggested by plugin $name was not used as LIMIT had already been set.";
    }
    $num_limits++;
  }
}

//print $query;exit;

//Execute modified query
$query .= "; SELECT FOUND_ROWS();";
$results = mysqli_multi_query($db,$query);

$taxa = array();
$result = mysqli_store_result($db);
while($row = mysqli_fetch_assoc($result)) {
  $taxa[] = $row;
}

mysqli_next_result($db);
$counts = mysqli_store_result($db);
$count = mysqli_fetch_assoc($counts);

//Process plugins that need the result count
foreach ($plugins as $name => $path) {
  if (isset($plugins[$name]["result count"])) {
    $filt = $plugins[$name]["result count"];
    $qa = $filt($qa, $count["FOUND_ROWS()"]);
  }
}


$tabs = array();
foreach ($taxa as $row) {
  foreach($plugins as $name => $data) {
    if (isset($plugins[$name]["tab"])) {
      $func = $plugins[$name]["tab"];
      $tabs[str_replace(" ", "_", $row["taxon"])][] = $func($qa, $row["taxon"]);
    }
  }
}

$qa["taxa"] = $taxa;
$qa["init"] = false;
$qa["tabs"] = $tabs;
$qa["query"] = $query;
echo json_encode($qa);
exit;
