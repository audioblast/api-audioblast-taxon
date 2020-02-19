<?php
global $qa;

function basic_info() {
  return(array(
    "name" => "Basic Filters",
    "desc" => "Some common filters",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "basic_html",
    "where" => "basic_where",
    "tabs" => array(
      "Traits" => "basic_tab_traits"
    )
  ));
}

function basic_where($qa) {
  $sql = "";
  if ($qa["basic"]["traits"] != "") {
    $sql .= " id IN (SELECT DISTINCT taxonID FROM traits)";
  }
  if ($qa["basic"]["speciesGroup"] != "") {
    if ($sql != "") { $sql .= " AND"; }
    $sql .= " `Rank` IN ('Subspecies', 'Species')";
  }
  if ($qa["basic"]["recordings"] != "") {
    if ($sql != "") { $sql .= " AND"; }
    $sql .= " taxon IN (SELECT DISTINCT taxon FROM recordings)";
  }
  if ($sql == "") {return(false);}
  return($sql);
}

function basic_update_filter($qa, $activity, $value) {
  global $db;
  if ($activity == "traits") {
    $qa["basic"]["traits"] = $value;
  }
  if ($activity == "speciesGroup") {
    $qa["basic"]["speciesGroup"] = $value;
  }
  if ($activity == "recordings") {
    $qa["basic"]["recordings"] = $value;
  }
  return($qa);
}

function basic_init($qa) {
  $qa["basic"] = array(
    "traits" => "checked=checked",
    "speciesGroup" => "checked=checked",
    "recordings" => ""
  );
  return($qa);
}

function basic_html($qa) {
  $output  = '<div id="filter_basic" class="filter">';
  $output .= '<h2>Basic Filters</h2>';

  $output .= '<input type="checkbox" id="basic-hasTraits" name="basic-hasTraits" '.$qa["basic"]["traits"].'">';
  $output .= '<label for="basic-hasTraits">Taxa with traits</label>';
  $output .= '<br>';

  $output .= '<input type="checkbox" id="basic-speciesGroup" name="basic-speciesGroup" '.$qa["basic"]["speciesGroup"].'">';
  $output .= '<label for="basic-speciesGroup">Species group taxa</label>';
  $output .= '<br>';

  $output .= '<input type="checkbox" id="basic-recordings" name="basic-recordings" '.$qa["basic"]["recordings"].'">';
  $output .= '<label for="basic-recordings">Taxa with recordings</label>';

  $output .= '</div>';

  $javascript = '
    $("#basic-hasTraits").click(function() {
      if ($(this).is(":checked")) {
        updateFilter("basic", "traits", "checked=checked");
      } else {
        updateFilter("basic", "traits", "");
      }
    });

    $("#basic-speciesGroup").click(function() {
      if ($(this).is(":checked")) {
        updateFilter("basic", "speciesGroup", "checked=checked");
      } else {
        updateFilter("basic", "speciesGroup", "");
      }
    });

    $("#basic-recordings").click(function() {
      if ($(this).is(":checked")) {
        updateFilter("basic", "recordings", "checked=checked");
      } else {
        updateFilter("basic", "recordings", "");
      }
    });
  ';
  $qa["javascript"][] = $javascript;
  $qa["filter_html"][] = $output;
  return($qa);
}
