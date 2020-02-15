<?PHP
$pagetitle = 'Home';

// include config variables
include('config.inc.php');

// connect to db
include($securedbcreds);
$con = mysqli_connect($servername,$username,$password,$database) or die("SQL connection error");

// include php functions
include('functions.inc.php');

// include tickdata
include('tickdata.inc.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?PHP echo $sitetitle; ?></title>
  <link rel="stylesheet" href="<?PHP echo $siteurl; ?>css/style.css" type="text/css">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="<?PHP echo $siteurl; ?>functions.inc.js"></script>
</head>
<body>
  <div id="page">
    <?PHP include('header.inc.php'); ?>
    <div id="pagecontainer">
      <?PHP include('sidebar.inc.php'); ?>
      <?PHP include('pagetitle.inc.php'); ?>
      <div id="articles">

        <?PHP
          // INFLUENCE WARNING SYSTEM
          $systemquery = "SELECT systemname, systemaddress FROM systemlist ORDER BY systemname ASC";
          if ($systemresult = mysqli_query($con, $systemquery)){
            if (mysqli_num_rows($systemresult) > 0) {
              $systemcounter = 0;
              while($row = mysqli_fetch_array($systemresult, MYSQLI_ASSOC)) {
                $systemname = addslashes($row['systemname']);
                $systemaddress = $row['systemaddress'];
                $datainactivesnapshot = false;
                $datainsnapshots = false;
                $factioninfluencearray = array();
                $checktickid = array();
                $checktimestamps = array();
                $activesnapshotquery = "SELECT tickid, timestamp, Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$highlight_pmfname."' ORDER BY tickid DESC LIMIT 1";

                if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                  if (mysqli_num_rows($activesnapshotresult) > 0) {
                    $datainactivesnapshot = true;
                    while($row2 = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $factioninfluencearray[] = $row2['Influence'];
                      $checktickid[] = $row2['tickid'];
                      $checktimestamps[] = $row2['timestamp'];
                    }
                  }
                }

                if ($datainactivesnapshot) {
                  $limiter = 1;
                } else {
                  $limiter = 2;
                }
                $snapshotquery = "SELECT tickid, timestamp, Influence FROM snapshots WHERE isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$highlight_pmfname."' ORDER BY tickid DESC LIMIT ".$limiter; 
                if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                  if (mysqli_num_rows($snapshotresult) > 0) {
                    // use data from activesnapshot
                    $datainsnapshots = true;
                    while($row3 = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                      $factioninfluencearray[] = $row3['Influence'];
                      $checktickid[] = $row3['tickid'];
                      $checktimestamps[] = $row3['timestamp'];
                    }
                    if ($limiter == 2) {
                      $factioninfluencearray = array_reverse($factioninfluencearray);
                    }
                  }
                }
                $direction;
                if ($factioninfluencearray[0] > $factioninfluencearray[1]) {
                  $direction = 'down';
                } elseif ($factioninfluencearray[0] < $factioninfluencearray[1]) {
                  $direction = 'up';
                } elseif ($factioninfluencearray[0] == $factioninfluencearray[1]) {
                  $direction = 'stable';
                }
                $influencechangeamount = round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2);
                if ($influencechangeamount > $systeminfluencewarningpercentage) {
                  ?>
                    <div id="article">
                      <?PHP
                        if ($direction == 'up') {
                          echo "<div id=\"articlenotice\">";
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence increase"; 
                          echo "</div>\n";
                        } elseif ($direction == 'down') {
                          echo "<div id=\"articlewarning\">";
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence drop"; 
                          echo "</div>\n";
                        }
                      ?>
                      <div id="articlecontents">
                        <script type="text/javascript">
                          google.charts.load("current", {packages:["corechart"]});
                          google.charts.setOnLoadCallback(drawChart);
                          function drawChart() {

                            var data = google.visualization.arrayToDataTable([
                              ['Faction', 'Influence'],
                              <?PHP


                              if ($datainactivesnapshot) {
                                $influencechangefactionquery = "SELECT Name, Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
                              } else {
                                $influencechangefactionquery = "SELECT Name, Influence FROM snapshots WHERE tickid = '".$checktickid[0]."' AND isfaction = '1' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
                              }

                              if ($influencechangefactionresult = mysqli_query($con, $influencechangefactionquery)){


                                $influencechangefactioncounter = 0;
                                $influencechangefactioncount = mysqli_num_rows($influencechangefactionresult);
                                $idacolumn = 0;

                                while($row4 = mysqli_fetch_array($influencechangefactionresult, MYSQLI_ASSOC)) {
                                  if ($row4['Name'] == $highlight_pmfname) {
                                    $idacolumn = $influencechangefactioncounter;
                                  }
                                  echo "['".addslashes($row4['Name'])."',".round(($row4['Influence'] * 100), 2)."]";
                                  if ($influencechangefactioncounter < ($influencechangefactioncount-1)) {
                                    echo ",";
                                  }
                                  $influencechangefactioncounter++;
                                }
                              }
                              ?>
                            ]);
                            var options = {
                              is3D: true,
                              pieSliceText: 'IDA',
                              slices: {
                                <?PHP echo $idacolumn; ?>: {offset: 0.5, color: '#4942CC'}
                              }
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('<?PHP echo $systemaddress; ?>_influencechange_piechart_3d'));
                            chart.draw(data, options);
                          }
                        </script>
                        <div id="<?PHP echo $systemaddress; ?>_influencechange_piechart_3d" style="width: 450px; height: 200px;"></div>

                      </div>
                      <div id="articlefooter">
                      <?PHP
                        echo "This information is ";
                        if ($checktimestamps[0] > $checktimestamps[1]) {
                          echo datediff($servertime, $checktimestamps[0]);
                        } else {
                          echo datediff($servertime, $checktimestamps[1]);
                        }
                        echo " old.";
                      ?>
                      </div>
                    </div>
                  <?PHP
                }
              }
            }
          }
        ?>

        <?PHP
        /*
          // CONFLICT WARNING SYSTEM
          $systemquery = "SELECT systemname, systemaddress FROM systemlist ORDER BY systemname ASC";
          if ($systemresult = mysqli_query($con, $systemquery)){
            if (mysqli_num_rows($systemresult) > 0) {
              $systemcounter = 0;
              while($row = mysqli_fetch_array($systemresult, MYSQLI_ASSOC)) {
                $systemname = addslashes($row['systemname']);
                $systemaddress = $row['systemaddress'];
                $datainactivesnapshot = false;
                $datainsnapshots = false;
                $factioninfluencearray = array();
                $activesnapshotquery = "SELECT Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$highlight_pmfname."' ORDER BY tickid DESC LIMIT 1";
                if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                  if (mysqli_num_rows($activesnapshotresult) > 0) {
                    $datainactivesnapshot = true;
                    while($row2 = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $factioninfluencearray[] = $row2['Influence'];
                    }
                  }
                }
                if ($datainactivesnapshot) {
                  $limiter = 1;
                } else {
                  $limiter = 2;
                }
                $snapshotquery = "SELECT Influence FROM snapshots WHERE isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$highlight_pmfname."' ORDER BY tickid DESC LIMIT $limiter"; 
                if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                  if (mysqli_num_rows($snapshotresult) > 0) {
                    // use data from activesnapshot
                    $datainsnapshots = true;
                    while($row3 = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                      $factioninfluencearray[] = $row3['Influence'];
                    }
                    if ($limiter == 2) {
                      $factioninfluencearray = array_reverse($factioninfluencearray);
                    }
                  }
                }
                $direction;
                if ($factioninfluencearray[0] > $factioninfluencearray[1]) {
                  $direction = 'down';
                } elseif ($factioninfluencearray[0] < $factioninfluencearray[1]) {
                  $direction = 'up';
                } elseif ($factioninfluencearray[0] == $factioninfluencearray[1]) {
                  $direction = 'stable';
                }
                $influencechangeamount = round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2);
                if ($influencechangeamount > $systeminfluencewarningpercentage) {
                  ?>
                    <div id="article">
                      <?PHP
                        if ($direction == 'up') {
                          echo "<div id=\"articlenotice\">";
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence increase"; 
                          echo "</div>\n";
                        } elseif ($direction == 'down') {
                          echo "<div id=\"articlewarning\">";
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence drop"; 
                          echo "</div>\n";
                        }
                      ?>
                      <div id="articlecontents">
                        <?PHP
                          if ($direction == 'up') {
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence increase"; 
                          } elseif ($direction == 'down') {
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence drop"; 
                          }
                        ?>
                      </div>
                    </div>
                  <?PHP
                }
              }
            }
          }
        */
        ?>

      </div>
    </div>
  </div>
</body>
</html>
