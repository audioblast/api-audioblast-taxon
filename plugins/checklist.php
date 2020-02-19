<?php
global $qa;

function checklist_info() {
  return(array(
    "name" => "Checklist",
    "desc" => "filter taxonomic names by checklist",
    "auth" => "edwbaker@gmail.com",
    "filter html" => "checklist_html",
    "where" => "checklist_where"
  ));
}

function checklist_where($qa) {
  $sql = "";
  $n = 0;
  if ($qa["checklist"]["uk_orthoptera"] != "") {
    $sql .= " taxon IN (
      'Leptophyes punctatissima',
      'Acheta domesticus',
      'Gryllodes supplicans',
      'Gryllus Gryllus bimaculatus',
      'Gryllus Gryllus campestris',
      'Pseudomogoplistes vicentae',
      'Nemobius sylvestris',
      'Gryllotalpa gryllotalpa',
      'Tachycines asynamorus',
      'Dolichopoda Chopardina bormansi',
      'Conocephalus Anisoptera fuscus fuscus',
      'Conocephalus Anisoptera dorsalis',
      'Pholidoptera griseoaptera',
      'Platycleis albopunctata',
      'Decticus verrucivorus',
      'Metrioptera brachyptera',
      'Roeseliana roeselii',
      'Cosmoderus maculatus',
      'Phlugiolopsis Phlugiolopsis henryi ',
      'Meconema thalassinum',
      'Meconema meridionale',
      'Phaneroptera Phaneroptera falcata',
      'Jamaicana flava',
      'Jamaicana subguttata',
      'Mastophyllum scabricolle',
      'Nesonotus tricornis',
      'Ruspolia nitidula',
      'Tettigonia viridissima',
      'Tetrix ceperoi',
      'Tetrix subulata',
      'Tetrix undulata',
      'Stethophyma grossum',
      'Locusta migratoria',
      'Oedipoda caerulescens',
      'Schistocerca gregaria',
      'Anacridium aegyptium',
      'Calliptamus italicus',
      'Omocestus Omocestus rufipes',
      'Omocestus Omocestus viridulus',
      'Stenobothrus lineatus',
      'Stenobothrus stigmaticus',
      'Myrmeleotettix maculatus',
      'Chorthippus Chorthippus albomarginatus',
      'Chorthippus Glyptobothrus brunneus',
      'Chorthippus Glyptobothrus vagans',
      'Pseudochorthippus parallelus',
      'Euchorthippus elegantulus'
    )";
    $n++;
  }
  if ($qa["checklist"]["uk_orthoptera_breeding"] != "") {
    if ($n > 0) {$sql .= " AND ";}
    $sql .= " taxon IN (
      'Leptophyes punctatissima',
      'Acheta domesticus',
      'Gryllus Gryllus campestris',
      'Pseudomogoplistes vicentae',
      'Nemobius sylvestris',
      'Gryllotalpa gryllotalpa',
      'Tachycines asynamorus',
      'Conocephalus Anisoptera fuscus fuscus',
      'Conocephalus Anisoptera dorsalis',
      'Pholidoptera griseoaptera',
      'Platycleis albopunctata',
      'Decticus verrucivorus',
      'Metrioptera brachyptera',
      'Roeseliana roeselii',
      'Phlugiolopsis Phlugiolopsis henryi ',
      'Meconema thalassinum',
      'Meconema meridionale',
      'Phaneroptera Phaneroptera falcata',
      'Tettigonia viridissima',
      'Tetrix ceperoi',
      'Tetrix subulata',
      'Tetrix undulata',
      'Stethophyma grossum',
      'Omocestus Omocestus rufipes',
      'Omocestus Omocestus viridulus',
      'Stenobothrus lineatus',
      'Stenobothrus stigmaticus',
      'Myrmeleotettix maculatus',
      'Chorthippus Chorthippus albomarginatus',
      'Chorthippus Glyptobothrus brunneus',
      'Chorthippus Glyptobothrus vagans',
      'Pseudochorthippus parallelus'
    )";
  }
  if ($sql == "") {return(false);}
  return($sql);
}

function checklist_update_filter($qa, $activity, $value) {
  if ($activity == "uk_orthoptera") {
    $qa["checklist"]["uk_orthoptera"] = $value;
  }
  if ($activity == "uk_orthoptera_breeding") {
    $qa["checklist"]["uk_orthoptera_breeding"] = $value;
  }
  return($qa);
}

function checklist_init($qa) {
  $qa["checklist"] = array(
    "uk_orthoptera" => "",
    "uk_orthoptera_breeding" => ""
  );
  return($qa);
}

function checklist_html($qa) {
  $output  = '<div id="filter_basic" class="filter">';
  $output .= '<h2>Checklist</h2>';

  $output .= '<input type="checkbox" id="checklist-uk_orthoptera" name="checklist-uk_orthoptera" '.$qa["checklist"]["uk_orthoptera"].'">';
  $output .= '<label for="checklist-uk_orthoptera">UK Orthoptera</label><br/>';

  $output .= '<input type="checkbox" id="checklist-uk_orthoptera_breeding" name="checklist-uk_orthoptera_breeding" '.$qa["checklist"]["uk_orthoptera_breeding"].'">';
  $output .= '<label for="checklist-uk_orthoptera_breeding">UK Orthoptera (Breeding)</label>';

  $output .= '</div>';

  $javascript = '
    $("#checklist-uk_orthoptera").click(function() {
      if ($(this).is(":checked")) {
        updateFilter("checklist", "uk_orthoptera", "checked=checked");
      } else {
        updateFilter("checklist", "uk_orthoptera", "");
      }
    });

    $("#checklist-uk_orthoptera_breeding").click(function() {
      if ($(this).is(":checked")) {
        updateFilter("checklist", "uk_orthoptera_breeding", "checked=checked");
      } else {
        updateFilter("checklist", "uk_orthoptera_breeding", "");
      }
    });
  ';
  $qa["javascript"][] = $javascript;
  $qa["filter_html"][] = $output;
  return($qa);
}
