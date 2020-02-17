function openTab(evt, tabName, tabid, classname) {
  // Declare all variables
  var i, classnameelement, tabidelement;

  // Get all elements with class=classname and hide them
  classnameelement = document.getElementsByClassName(classname);
  for (i = 0; i < classnameelement.length; i++) {
    classnameelement[i].style.display = "none";
  }

  // Get all elements with class=tabid and remove the class "active"
  tabidelement = document.getElementsByClassName(tabid);
  for (i = 0; i < tabidelement.length; i++) {
    tabidelement[i].className = tabidelement[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

function toggleArticledisplay(elementid, setto) {
  var x = document.getElementById(elementid + '_articlecontents'); // article
  var y = document.getElementById(elementid + '_articlefooter'); // footer
  var z = document.getElementById(elementid + '_article'); // margin between items

  if (setto === 'none') {
      x.style.display = "none";
      y.style.display = "none";
      z.style.marginBottom  = "10px";
  } else if (setto === 'block') {
    x.style.display = "block";
    y.style.display = "block";
    z.style.marginBottom  = "50px";
  } else {
    if (x.style.display === "none") {
      x.style.display = "block";
      y.style.display = "block";
      z.style.marginBottom  = "50px";
    } else {
      x.style.display = "none";
      y.style.display = "none";
      z.style.marginBottom  = "10px";
    }
  }
}