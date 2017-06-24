/* Display Labels */
// function displayText(event, ctx, config, data, other) {
//   if (other != null) {
//     if (typeof other.type != "undefined" && other.values[0] == "XAXIS_TEXTMOUSE" || "YAXIS_TEXTMOUSE") {
//       var annotateDIV = document.getElementById('divCursor');
//       if (typeof other.type != "undefined" && other.values[1]) {
//         var otherValue = other.values[1];
//         var labelToolTip = cleanTooltip(otherValue);
//         var show = false;
//         var rect = ctx.canvas.getBoundingClientRect();
//         annotateDIV.innerHTML = labelToolTip;
//         annotateDIV.style.display = annotateDIV.style.display === 'block' ? '' : 'block';
//         annotateDIV.style.left = event.clientX + 'px';
//         annotateDIV.style.top = event.clientY + 'px';
//         annotateDIV.style.position = "absolute";
//       }
//     }
//   }
// }
/*
 * Regex
 * Abbreviations
 */
// function cleanTooltip(value) {
//   var cleanedValue = value.replace(/\s*\([^)]*\)/gi, '');
//   var dataDictionary = [{
//     "Tid": "Sc. Exc. Meetings",
//     "Name": "Scientic Exchange Meetings"
//   }, {
//     "Tid": "MET",
//     "Name": "Meetings to Educate"
//   }, {
//     "Tid": "Indep Sponsorships",
//     "Name": "Independent Sponserships"
//   }, {
//     "Tid": "Int. Consult",
//     "Name": "Internal Consultancy Meetings"
//   }, {
//     "Tid": "ST",
//     "Name": "Speaker Training"
//   }, {
//     "Tid": "PT",
//     "Name": "Partnerships"
//   }, {
//     "Tid": "Int. Conslt. Meetings",
//     "Name": "Internal Consultancy Meetings"
//   }, {
//     "Tid": "Ext. Consultancy",
//     "Name": "External Consultancy"
//   }, {
//     "Tid": "Consult. Meetings",
//     "Name": "Consultancy Meetings"
//   }, {
//     "Tid": "ADB",
//     "Name": "Ad Boards"
//   }]
//   angular.forEach(dataDictionary, function(value, key) {
//     if (value['Tid'] == cleanedValue) {
//       cleanedValue = value['Name'];
//     } else {}
//   });
//   return (cleanedValue);
// }
