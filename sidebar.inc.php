<div id="sidebar">
  <a href="/">
    <div id="sidebarlogo">
    </div>
  </a>

  <div id="sidebaritem">
    <div id="sidebaritem-header">
      Server time
    </div>
    <div id="sidebaritem-content">
      <?PHP
        echo "<span style=\"font-weight: bold;\">".$servertime."</span>\n";
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
          echo "Unknown, not enough data";
        } else {
          echo "<span style=\"font-weight: bold;\">(#".$newtickid.") ".$newtick."</span>\n";
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
          echo "Unknown, not enough data\n";
        } else {
          echo "<span style=\"font-weight: bold;\">(#".$oldtickid.") ".$oldtick."</span>\n";
        }
      ?>
    </div>
  </div>


  <div id="sidebaritem">
    <a href="<?PHP echo $siteurl; ?>systems/">
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
          echo "<span style=\"font-weight: bold;\"><a href=\"".$siteurl."system/".$row['systemaddress']."\">".$row['systemname']."</a></span>\n";
        } else {
          echo "<span style=\"font-weight: bold;\"><a href=\"".$siteurl."system/".$row['systemaddress']."\" style=\"color: red;\">".$row['systemname']."</a></span>\n";
        }
      ?>
    </div>
    <?PHP
        }
      } else {
    ?>
    <div id="sidebaritem-content">
      <?PHP
        echo "No IDA systems found\n";
      ?>
    </div>
    <?PHP
        }
      }
    ?>
  </div>
</div>