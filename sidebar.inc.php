<a href="/">
      <div id="sidebar">
        <div id="sidebarlogo">
        </div>
</a>


        <div id="sidebaritem">
          <div id="sidebaritem-header">
            Server time
          </div>
          <div id="sidebaritem-content">
<?PHP
echo "<span style=\"font-weight: bold;\">".$servertime."</span><br />";
?>
          </div>
        </div>

        <div id="sidebaritem">
          <div id="sidebaritem-header">
            Last tick
          </div>
          <div id="sidebaritem-content">
<?PHP
if (!$newtick) {
  echo "Unknown, not enough data<br />";
} else {
  echo "<span style=\"font-weight: bold;\">(#".$newtickid.") ".$newtick."</span><br />";
}
?>
          </div>
        </div>

        <div id="sidebaritem">
          <div id="sidebaritem-header">
            Second to last tick
          </div>
          <div id="sidebaritem-content">
<?PHP
if (!$oldtick) {
  echo "Unknown, not enough data<br />";
} else {
  echo "<span style=\"font-weight: bold;\">(#".$oldtickid.") ".$oldtick."</span><br />";
}
?>
          </div>
        </div>


        <div id="sidebaritem">
          <a href="systems.php">
            <div id="sidebaritem-header">
              IDA System list
            </div>
          </a>
<?PHP
$systemlistquery = "SELECT systemname, systemaddress FROM systemlist ORDER BY systemname ASC";
if ($systemlistresult = mysqli_query($con, $systemlistquery)){
  if (mysqli_num_rows($systemlistresult) > 0) {
    $systemcounter = 0;
    while($row = mysqli_fetch_array($systemlistresult, MYSQLI_ASSOC)) {

?>
          <div id="sidebaritem-content">
<?PHP
$lastupdatetime = '';
$timediff = '';
$updatedaftertick = '';
$lastupdatequery = "SELECT timestamp FROM systemdata WHERE StarSystem = '".$row['systemname']."' AND SystemAddress = '".$row['systemaddress']."' ORDER BY timestamp DESC LIMIT 1";
if ($lastupdateresult = mysqli_query($con, $lastupdatequery)){
  if (mysqli_num_rows($lastupdateresult) > 0) {
    while($row2 = mysqli_fetch_array($lastupdateresult, MYSQLI_ASSOC)) {
      $lastupdatetime = $row2['timestamp'];
    }
    $timediff = datediff($servertime, $lastupdatetime);

    if (strtotime($newtick) > strtotime($lastupdatetime)) {
      $updatedaftertick = false;
    } else {
      $updatedaftertick = true;
    }
  }
}
if ($updatedaftertick) {
  echo "<span style=\"font-weight: bold;\"><a href=\"system.php?id=".$row['systemaddress']."\">".$row['systemname']."</a></span><br />";
} else {
  echo "<span style=\"font-weight: bold;\"><a href=\"system.php?id=".$row['systemaddress']."\" style=\"color: red;\">".$row['systemname']."</a></span><br />";
}
?>
          </div>
<?PHP
    }
  } else {
?>
          <div id="sidebaritem-content">
<?PHP
  echo "No IDA systems found";
?>
          </div>
<?PHP
  }
}
?>
        </div>
      </div>