<?php
global $qa;

function frequency_info() {
  return(array(
    "name" => "Frequency",
    "desc" => "Search by frequency",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "frequency_html",
    "where" => "frequency_where",
    "tab" => "frequency_tab"
  ));
}

function frequency_tab($qa, $taxon) {
  $taxon = str_replace(" ", "_", $taxon);
  $output  = "<div id='tab-frequency-$taxon' class='tab-frequency'>";
  $output .= "</div";
  return($output);
}

function frequency_where($qa) {
  if ($qa["frequency"]["freq"] == "") {
    return(false);
  } else {
    $freq = $qa["frequency"]["freq"];
    $margin = $qa["frequency"]["margin"];
    $min = 1 - $margin/100;
    $max = 1 + $margin/100;
    $output = " taxon IN (SELECT `Taxonomic.name`
                            FROM `traits-taxa`
                            WHERE Trait LIKE '%frequency%'
                             AND (
                               (min < $freq AND max > $freq)
                               OR
                               (Value * $min < $freq AND Value * $max > $freq)
                             )
    )";
    return($output);
  }
}

function frequency_update_filter($qa, $activity, $value) {
  if ($activity == "freq") {
    $qa["frequency"]["freq"] = $value;
  }
  if ($activity == "margin") {
    $qa["frequency"]["margin"] = $value;
  }
  return($qa);
}

function frequency_init($qa) {
  $qa["frequency"] = array(
    "freq" => "",
    "margin" =>5
  );
  return($qa);
}

function frequency_html($qa) {
  $output  = '<div id="filter_frequency" class="filter">';
  $output .= '<h2>Frequency</h2>';
  $output .= '<input type="text" id="frequency-text" name="frequency-text" value="'.$qa["frequency"]["freq"].'">kHz';
  $output .= '<input type="button" value="Submit" onclick="updateFilter(\'frequency\',\'freq\', document.getElementById(\'frequency-text\').value);";><br/>';

  $output .= '+/-<input type="text" id="margin-text" name="margin-text" value="'.$qa["frequency"]["margin"].'">%';
  $output .= '<input type="button" value="Submit" onclick="updateFilter(\'frequency\',\'margin\', document.getElementById(\'margin-text\').value);";>';
 

  $output .= '</div>';

  $qa["filter_html"][] = $output;

  $qa["javascript"][] = '
    $(".tab-frequency").each(function() {
      if (window.qa["frequency"]["freq"] == "") { return(false); }
      var taxon = $(this).attr("id").replace("tab-frequency-", "");
      taxon = taxon.replace(/_/g, " ");
      var id = $(this).attr("id");
      var req = new XMLHttpRequest();
      req.open(
        "GET",
        "https://api.audioblast.org/frequency/taxon/?name="+taxon+"&f="+window.qa["frequency"]["freq"]+"&margin="+window.qa["frequency"]["margin"],
        true
      );
      req.onreadystatechange = function (event) {
        if(req.readyState === 4) {
          if(req.status === 200) {
            var res = JSON.parse(req.responseText);
            var output = "";
            res.forEach(function(item, index) {
             output += "<strong>"+item["Trait"]+":</strong> "+item["Value"]+"<br/>";
            });
            document.getElementById(id).innerHTML = output;
          } else {
            console.log("Error", req.responseText);
          }
        }
      }
      req.send();
    })
  ';
  return($qa);
}
