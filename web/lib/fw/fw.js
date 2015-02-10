// Recupere les parametres URLS
function getParameters() {
  var searchString = window.location.search.substring(1),
      params = searchString.split("&"),
      hash = {};

  if (searchString == "") return {};
  for (var i = 0; i < params.length; i++) {
    var val = params[i].split("=");
    hash[unescape(val[0])] = unescape(val[1]);
  }
  return hash;
}