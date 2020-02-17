<?php
include('database.php');
$plugins = array(
  "limits" => "limits.php"
);

foreach ($plugins as $name => $path) {
  include("plugins/".$path);
  $info = $name."_info";
  $plugins[$name] = $info();
  if (isset($plugins[$name]["filter html"])) {
    $filt = $plugins[$name]["filter html"];
    $response["filter_html"][] = $filt();
  }
}

$query = "SELECT * FROM `audioblast-traits`.`taxa`";

$num_limits = 0;
foreach ($plugins as $name => $data) {
  if (isset($data["limits"])) {
    if ($num_limits == 0) {
      $func = $name."_limits";
      $query = $func($query);
    } else {
      $exec_notes[] = "SQL LIMIT suggested by plugin $name was not used as LIMIT had already been set.";
    }
    $num_limits++;
  }
}

$result = mysqli_query($db,$query);

$taxa = array();

while($row = mysqli_fetch_assoc($result)) {
  $taxa[] = $row;
}

$response["taxa"] = $taxa;

echo json_encode($response);

exit;

