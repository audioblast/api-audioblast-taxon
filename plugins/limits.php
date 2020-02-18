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
    $content .= '<a class="pager-page" href="" onclick="updateFilter(\'limits\', \'page\', '.$i.');">'.$i.'</a> ';
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
    "value" => 100,
    "page" => 1
  );
  return($qa);
}

function limits_html($qa) {
  $limits = array(25,50,100);
  $output  = '<div id="filter_limits" class="filter">';
  $output .= '<h2>Show results</h2>';
  foreach ($limits as $limit) {
    $output .= '<input type="radio" id="limits'.$limit.'" name="limits" value="'.$limit.'" ';
    if ($qa["limits"]["value"] == $limit) {$output .= "checked ";}
    $output .= 'onclick="updateFilter(\'limits\', \'value\', '.$limit.');"><label for="'.$limit.'">'.$limit.'</label><br>';
  }
  $output .= '</div>';

  $output.= "<script>alert('Hi');</script>";
  return($output);
}

function limits_limits($sql, $qa) {
  $offset =  ($qa["limits"]["page"] - 1) * $qa["limits"]["value"];
  $sql .= " LIMIT ".$offset.", ".$qa["limits"]["value"];
  return($sql);
}
