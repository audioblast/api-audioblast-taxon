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

  $rec_start = ($page - 1)  * $qa["limits"]["value"] + 1;
  $rec_end   = ($page) * $qa["limits"]["value"];

  $summary = "<div class='limits-row-count'>Page $page of $page_count (records $rec_start to $rec_end of $count)</div>";

  $pager = "<div class='pager'>";
  for ($i =1; $i <=$page_count; $i++) {
    if ($i == $page) {
      $pager .= '<a class="pager-page current-page" onclick="updateFilter(\'limits\', \'page\', \''.$i.'\');">'.$i.'</a> ';
    } else {
      $pager .= '<a class="pager-page" onclick="updateFilter(\'limits\', \'page\', \''.$i.'\');">'.$i.'</a> ';
    }
  }
  $pager .= "</div>";

  $qa["results_head_html"][] = $summary . $pager;
  $qa["results_foot_html"][] = $pager . $summary;
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
    "value" => 10,
    "page" => 1
  );
  return($qa);
}

function limits_html($qa) {
  $limits = array(10,25,50,100);
  $output  = '<div id="filter_limits" class="filter">';
  $output .= '<h2>Show results</h2>';
  foreach ($limits as $limit) {
    $output .= '<input type="radio" id="limits'.$limit.'" name="limits" value="'.$limit.'" ';
    if ($qa["limits"]["value"] == $limit) {$output .= "checked ";}
    $output .= 'onclick="updateFilter(\'limits\', \'value\', '.$limit.');"><label for="'.$limit.'">'.$limit.'</label><br>';
  }
  $output .= '</div>';

  $qa["filter_html"][] = $output;
  $qa["css"][] = _limits_css();
  return($qa);
}

function limits_limits($sql, $qa) {
  $offset =  ($qa["limits"]["page"] - 1) * $qa["limits"]["value"];
  $sql .= " LIMIT ".$offset.", ".$qa["limits"]["value"];
  return($sql);
}

function _limits_css() {
  $css = "
    .limits-row-count, .pager {
      width: 100%;
      text-align: center;
      margin:5px;
    }
    .pager-page {
      cursor:pointer;
      color:blue;
      text-decoration:underline;
    }
    .current-page {
      cursor:text;
      color:black;
      text-decoration:none;
    }
  ";
  return($css);
}
