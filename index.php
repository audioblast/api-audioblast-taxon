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
  "limits" => "limits.php"
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
  if (isset($plugins[$name]["filter html"])) {
    $filt = $plugins[$name]["filter html"];
    $qa["filter_html"][] = $filt($qa);
  }
}

if (isset($qa["update filter"])) {
  $plugin = $qa["update filter"]["plugin"]."_update_filter";
  $qa = $plugin($qa, $qa["update filter"]["activity"], $qa["update filter"]["value"]);
  unset($qa["update filter"]);
}

//Base query that is modifed by the plugins
$query = "SELECT SQL_CALC_FOUND_ROWS * FROM `audioblast-traits`.`taxa`";

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

$qa["taxa"] = $taxa;
$qa["init"] = false;
echo json_encode($qa);

exit;
