<?php
function limits_info() {
  return(array(
    "name" => "Limit results",
    "desc" => "Provies the results pager",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "limits_html",
    "limits" => "limits_limits"
  ));
}

function limits_html() {
  $output  = '<div id="filter_limits" class="filter">';
  $output .= '<h2>Show results</h2>';
  $output .= '<input type="radio" id="limits25" name="limits" value="25"><label for="25">25</label><br>';
  $output .= '<input type="radio" id="limits50" name="limits" value="50"><label for="50">50</label><br>';
  $output .= '</div>';
  return($output);
}

function limits_limits($sql) {
  $sql .= " LIMIT 25";
  return($sql);
}
