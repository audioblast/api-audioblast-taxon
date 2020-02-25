<?php
global $qa;

function traits_info() {
  return(array(
    "name" => "Trait search",
    "desc" => "Search by any trait",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "traits_html",
    "where" => "traits_where",
    "tab" => "traits_tab"
  ));
}

function traits_tab($qa, $taxon) {
  $taxon = str_replace(" ", "_", $taxon);
  $output  = "<div id='tab-traits-$taxon' class='tab-traits'>";
  $output .= "</div>";
  return($output);
}

function traits_where($qa) {
  if (count($qa["traits"]) == 0) {
    return(false);
  } else {
    $output = "";
    $count = 0;
    foreach ($qa["traits"] as $key => $data) {
      if ($count > 0) { $output .= " AND"; }
      $output .= " taxon IN (SELECT `Taxonomic.name`
                            FROM `traits-taxa`
                            WHERE Trait = '".$data["trait"]."'
                             AND (";
      if (is_numeric($data["value"])) {
        $output .= "            Value ".$data["op"]." ".$data["value"];
      } else {
        $output .= "            Value ".$data["op"]." '".$data["value"]."'";
      }
      $output .= "            )
      )";
      $count++;
   }
  }
  return($output);
}

function traits_update_filter($qa, $activity, $value) {
  if ($activity == "add") {
    $t = time();
    $qa["traits"][$t]["trait"] = $value[0];
    $qa["traits"][$t]["op"] = $value[1];
    $qa["traits"][$t]["value"] = $value[2];
  }
  if ($activity =="delete") {
    unset($qa["traits"][$value]);
  }
  return($qa);
}

function traits_init($qa) {
  $qa["traits"] = array();
  return($qa);
}

function traits_html($qa) {
  $output  = '<div id="filter_traits" class="filter">';
  $output .= '<h2>Traits</h2>';
  foreach($qa["traits"] as $key => $data) {
    $output .= '<div class="current-filter">';
    $output .= '<div class="current-filter-value">';
    $output .= implode(" ", $data);
    $output .= '</div><div class="current-filter-delete">';
    $output .= '[<a onclick="updateFilter(\'traits\', \'delete\', \''.$key.'\');">remove</a>]';
    $output .= '</div></div>';
  }

  $options = array('<', '>', '=');

  $output .= '<input type="text" id="traits-text" name="traits-text" value=""><br/>';

  foreach ($options as $option) {
    $output .= '<input type="radio" id="traits-option-'.$option.'" name="traits-option" value="'.$option.'">';
    $output .= '<label for="traits-option-'.$option.'">'.$option.'</label><br>';
  }

  $output .= '<input type="text" id="traits-text-value" name="traits-text-value" value=""><br/>';

  $output .= '<input type="button" value="Submit" onclick="updateFilter(\'traits\', \'add\',
    [
      document.getElementById(\'traits-text\').value,
      document.querySelector(&quot;input[name=\'traits-option\']:checked&quot;).value,
      document.getElementById(\'traits-text-value\').value
    ]
  );">';
  $output .= '</div>';

  $qa["filter_html"][] = $output;

  $qa["javascript"][] = '
    $( function() {
      // Single Select
      $( "#traits-text" ).autocomplete({
        source: function( request, response ) {
          // Fetch data
          $.ajax({
            url: "https://api.audioblast.org/trait/name/autocomplete/",
            type: "post",
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          // Set selection
          $("#traits-text").val(ui.item.label); // display the selected text
          return false;
         },
         appendTo: $( "#filter_taxonscope")
       })
     });
  ';  return($qa);
}
