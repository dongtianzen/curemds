jQuery(document).ready(function() {
  if(jQuery("#navInfoBase").is(':visible')) {
    if(jQuery("#pageInfoBase").length != 0) {
      angular.bootstrap(document.getElementById("pageInfoBase"), ['pageInfoBase']);
    }
  }
});