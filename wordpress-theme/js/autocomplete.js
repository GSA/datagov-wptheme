$(function() {
var availableTags = [
  "education",
  "health",
  "finance",
  "development",
  "energy",
  "schools",
  "hospital quality scores",
  "charge data"
];
$( "#search-textbox" ).autocomplete({
  source: availableTags
});
});