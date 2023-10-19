/* var hash = location.hash;

setInterval(function()
{
    if (location.hash != hash)
    {
        alert("Changed from " + hash + " to " + location.hash);
        hash = location.hash;
    }
}, 100); */
function test(){
	    if(window.event)
    {
          if(window.event.clientX < 40 && window.event.clientY < 0)
         {
             alert("Browser back button is clicked...");
         }
         else
         {
             alert("Browser refresh button is clicked...");
         }
     }
     else
     {
          if(event.currentTarget.performance.navigation.type == 1)
         {
              alert("Browser refresh button is clicked...");
         }
         if(event.currentTarget.performance.navigation.type == 2)
        {
              alert("Browser back button is clicked...");
        }
     }
}

/*  window.onbeforeunload=function()
 {
    if(window.event)
    {
          if(window.event.clientX < 40 && window.event.clientY < 0)
         {
             alert("Browser back button is clicked...");
         }
         else
         {
             alert("Browser refresh button is clicked...");
         }
     }
     else
     {
          if(event.currentTarget.performance.navigation.type == 1)
         {
              alert("Browser refresh button is clicked...");
         }
         if(event.currentTarget.performance.navigation.type == 2)
        {
              alert("Browser back button is clicked...");
        }
     }
 } */
 
/*  var $ = function (s) { return document.getElementById(s); },
    state = $('status'),
    lastevent = $('lastevent'),
    urlhistory = $('urlhistory'),
    examples = $('examples'),
    output = $('output'),
    template = '<p>URL: <strong>{url}</strong>, name: <strong>{name}</strong>, location: <strong>{location}</strong></p>',
    data = { // imagine these are ajax requests :)
      first : {
        name: "Remy",
        location: "Brighton, UK"
      },
      second: {
        name: "John",
        location: "San Francisco, USA"
      },
      third: {
        name: "Jeff",
        location: "Vancover, Canada"
      },
      fourth: {
        name: "Simon",
        location: "London, UK"
      }
    };

function reportEvent(event) {
  lastevent.innerHTML = event.type;
}

function reportData(data) {
  output.innerHTML = template.replace(/(:?\{(.*?)\})/g, function (a,b,c) {
    return data[c];
  });
}

if (typeof history.pushState === 'undefined') {
  state.className = 'fail';
} else {
  state.className = 'success';
  state.innerHTML = 'HTML5 History API available';
}

addEvent(examples, 'click', function (event) {
  var title;
  
  event.preventDefault();
  if (event.target.nodeName == 'A') {
    title = event.target.innerHTML;
    data[title].url = event.target.getAttribute('href'); // slightly hacky (the setting), using getAttribute to keep it short
    history.pushState(data[title], title, event.target.href);
    reportData(data[title]);
  }
});

addEvent(window, 'popstate', function (event) {
  var data = event.state;
  reportEvent(event);
  reportData(event.state || { url: "unknown", name: "undefined", location: "undefined" });
});

addEvent(window, 'hashchange', function (event) {
  reportEvent(event);

  // we won't do this for now - let's stay focused on states
  /*
  if (event.newURL) {
    urlhistory.innerHTML = event.oldURL;
  } else {
    urlhistory.innerHTML = "no support for <code>event.newURL/oldURL</code>";
  }

});

addEvent(window, 'pageshow', function (event) {
  reportEvent(event);
});

addEvent(window, 'pagehide', function (event) {
  reportEvent(event);
}); */