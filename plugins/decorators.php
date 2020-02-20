<?php
global $qa;

function decorators_info() {
  return(array(
    "name" => "decorators",
    "desc" => "Provide links to resources",
    "auth" => "edwbaker@gmail.com",
    "tab" => "decorators_tab",
    "filter html" => "decorators_html"
  ));
}

function decorators_init($qa) {
  return($qa);
}

function decorators_tab($qa, $taxon) {
  $taxon = str_replace(" ", "_", $taxon);
  $output  = "<div id='tab-decorators-$taxon' class='tab-decorators'>";
  $output .= "<div id='tab-decorators-$taxon-traits'></div>";
  $output .= "<div id='tab-decorators-$taxon-recordings'></div>";
  $output .= "</div>";
  return($output);
}

function decorators_html($qa) {
  $qa["javascript"][] = '
    $(".tab-decorators").each(function() {
      var taxon = $(this).attr("id").replace("tab-decorators-", "");
      taxon = taxon.replace(/_/g, " ");
      var id = $(this).attr("id");
      var req = new XMLHttpRequest();
      req.open(
        "GET",
        "https://api.audioblast.org/trait/bytaxon/?name="+taxon,
        true
      );
      req.onreadystatechange = function (event) {
        if(req.readyState === 4) {
          if(req.status === 200) {
            var res = JSON.parse(req.responseText);
            if (res.length == 0) { return(false); }
            var output = "<h3>Bioacoustic traits</h3>";
            output += "<div class=\'tab-content\'>";
            res.forEach(function(item, index) {
             output += "<strong>"+item["Trait"]+":</strong> "+item["Value"]+"<br/>";
            });
            output += "</div>";

            var target = id + "-traits";
            document.getElementById(target).innerHTML += output;
            makeAccordion("#"+target);
          } else {
            console.log("Error", req.responseText);
          }
        }
      }
      req.send();
    })
  ';

  $qa["javascript"][] = '
    $(".tab-decorators").each(function() {
      var taxon = $(this).attr("id").replace("tab-decorators-", "");
      taxon = taxon.replace(/_/g, " ");
      var id = $(this).attr("id");
      var req = new XMLHttpRequest();
      req.open(
        "GET",
        "https://api.audioblast.org/recordings/bytaxon/?name="+taxon,
        true
      );
      req.onreadystatechange = function (event) {
        if(req.readyState === 4) {
          if(req.status === 200) {
            var res = JSON.parse(req.responseText);
            if (res.length == 0) { return(false); }
            var output = "<h3>Recordings</h3>";
            output += "<div class=\'tab-content\'>";
            res.forEach(function(item, index) {
             output += "<a href=\'"+item["file"]+"\'>"+item["Title"]+"</a><br/>";
            });
            output += "</div>";

            var target = id + "-recordings";
            document.getElementById(target).innerHTML += output;
            makeAccordion("#"+target);
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
