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