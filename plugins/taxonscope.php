<?php
global $qa;

function taxonscope_info() {
  return(array(
    "name" => "TaxonScope",
    "desc" => "Limit results to a higher taxonomic group",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "taxonscope_html",
    "where" => "taxonscope_where"
  ));
}

function taxonscope_where($qa) {
  $rank = $qa["taxonscope"]["rank"];
  $taxon = $qa["taxonscope"]["taxon"];
  if ($rank == "" || $taxon == "") {
    return(false);
  } else {
    $output = "`$rank` = '$taxon'";
    return($output);
  }
}

function taxonscope_update_filter($qa, $activity, $value) {
  global $db;
  if ($activity == "scope") {
    $query = "SELECT `Rank` FROM `audioblast-traits`.taxa WHERE taxon = '$value';";
    $result = mysqli_query($db, $query);
    $result = mysqli_fetch_assoc($result);
    $qa["taxonscope"]["taxon"] = $value;
    $qa["taxonscope"]["rank"] = $result["Rank"];
  }
  return($qa);
}

function taxonscope_init($qa) {
  $qa["taxonscope"] = array(
    "taxon" => "",
    "rank" => ""
  );
  return($qa);
}

function taxonscope_html($qa) {
  $output  = '<div id="filter_taxonscope" class="filter">';
  $output .= '<h2>Taxonomic Scope</h2>';
  $output .= '<input type="text" id="taxonscope-text" name="taxonscope-text" value="'.$qa["taxonscope"]["taxon"].'">';
  $output .= '</div>';

  $qa["filter_html"][] = $output;

  $qa["javascript"][] = '
    $( function() {
      // Single Select
      $( "#taxonscope-text" ).autocomplete({
        source: function( request, response ) {
          // Fetch data
          $.ajax({
            url: "https://api.audioblast.org/taxon/name/autocomplete/",
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
          $("#taxonscope-text").val(ui.item.label); // display the selected text
          updateFilter("taxonscope", "scope", ui.item.label);
          return false;
       },
       appendTo: $( "#filter_taxonscope")
     })

   });
  ';
  return($qa);
}

