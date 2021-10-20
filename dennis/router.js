
var r = Rlite();

// Default route
r.add('', function () {
  document.title = 'Home';
});

// #inbox
r.add('commodity', function () {
  document.title = 'Commodity';

  $.get("templates/commodity.template.html", function (data) {
    $("#app-view").html(data);
  });
  $.getScript("templates/js/commodity.js", function (data, textStatus, jqxhr) {
    //console.log(data); // Data returned
    //console.log(textStatus); // Success
    console.log(jqxhr.status); // 200
    console.log("Load was performed.");
  }).fail(function (jqxhr, settings, exception) {
    alert(exception);
  });

});

// #inbox
r.add('login', function () {
  document.title = 'Login';

  $.get("templates/login.template.html", function (data) {
    $("#app-view").html(data);
  });
  $.getScript("templates/js/login.js", function (data, textStatus, jqxhr) {
    //console.log(data); // Data returned
    //console.log(textStatus); // Success
    console.log(jqxhr.status); // 200
    console.log("Load was performed.");
  }).fail(function (jqxhr, settings, exception) {
    alert(exception);
  });

});

// #sent?to=john -> r.params.to will equal 'john'
r.add('sent', function (r) {
  document.title = 'Out ' + r.params.to;
});

// #users/chris -> r.params.name will equal 'chris'
r.add('users/:name', function (r) {
  document.title = 'User ' + r.params.name;
  console.log(r.params.name);
  $.getScript('./second.js', function (data, textStatus, jqxhr) {
    //console.log(data); // Data returned
    //console.log(textStatus); // Success
    console.log(jqxhr.status); // 200
    console.log("Load was performed.");
  }).fail(function (jqxhr, settings, exception) {
    alert(exception);
  });

});

// #logout
r.add('logout', function () {
  document.title = 'Logout';
});

// Hash-based routing
function processHash() {
  var hash = location.hash || '#';
  r.run(hash.slice(1));
}

window.addEventListener('hashchange', processHash);
processHash();