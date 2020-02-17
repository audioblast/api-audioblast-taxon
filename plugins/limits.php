<?php
global $qa;

function limits_info() {
  return(array(
    "name" => "Limit results",
    "desc" => "Provies the results pager",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "limits_html",
    "limits" => "limits_limits",
    "result count" => "limits_result_count"
  ));
}

function limits_result_count($qa, $count) {
  $page = $qa["limits"]["page"];
  $page_count = ceil($count / $qa["limits"]["value"]);

  $content = "<div id='limits-row-count'>Page $page of $page_count</div>";

  $content .= "<div id='pager'>";
  for ($i =1; $i <=$page_count; $i++) {
    $content .= '<a onclick="updateFilter(\'limits\', \'page\', '.$i.');">'.$i.'</a>';
  }
  $content .= "</div>";

  $qa["results_head_html"][] = $content;
  return($qa);
}

function limits_update_filter($qa, $activity, $value) {
  if ($activity == "value") {
    $qa["limits"]["value"] = $value;
  }
  if ($activity == "page") {
    $qa["limits"]["page"] = $value;
  }
 return($qa);
}

function limits_init($qa) {
  $qa["limits"] = array(
    "value" => 25,
    "page" => 1
  );
  return($qa);
}

function limits_html($qa) {
  $output  = '<div id="filter_limits" class="filter">';
  $output .= '<h2>Show results</h2>';
  $output .= '<input type="radio" id="limits25" name="limits" value="25" ';
  if ($qa["limits"]["value"] == 25) {$output .= "checked ";}
  $output .= 'onclick="updateFilter(\'limits\', \'value\', 25);"><label for="25">25</label><br>';
  $output .= '<input type="radio" id="limits50" name="limits" value="50" ';
  if ($qa["limits"]["value"] == 50) {$output .= "checked ";}
  $output .= 'onclick="updateFilter(\'limits\', \'value\', 50);"><label for="50">50</label><br>';
  $output .= '</div>';


  $output.= "<script>alert('Hi');</script>";
  return($output);
}

function limits_limits($sql, $qa) {
  $offset =  ($qa["limits"]["page"] - 1) * $qa["limits"]["value"];
  $sql .= " LIMIT ".$offset.", ".$qa["limits"]["value"];
  return($sql);
}
