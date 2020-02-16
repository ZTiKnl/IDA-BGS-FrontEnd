<?PHP
$pagetitle = 'Home';

// include config variables
include('config/config.inc.php');

// connect to db
include($securedbcreds);
$con = mysqli_connect($servername,$username,$password,$database) or die("SQL connection error");

// include php functions
include($siteincludefiles.'functions.inc.php');

// include tickdata
include($siteincludefiles.'tickdata.inc.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?PHP echo $sitetitle; ?></title>
  <link rel="stylesheet" href="<?PHP echo $siteurl; ?>css/style.css" type="text/css">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="<?PHP echo $siteurl.$siteincludefiles; ?>functions.inc.js"></script>
</head>
<body>
  <div id="page">
    <?PHP include($siteincludefiles.'header.inc.php'); ?>
    <div id="pagecontainer">
      <?PHP include($siteincludefiles.'sidebar.inc.php'); ?>
      <?PHP include($siteincludefiles.'pagetitle.inc.php'); ?>
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
                $activesnapshotquery = "SELECT tickid, timestamp, Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$pmfname."' ORDER BY tickid DESC LIMIT 1";

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
                $snapshotquery = "SELECT tickid, timestamp, Influence FROM snapshots WHERE isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$pmfname."' ORDER BY tickid DESC LIMIT ".$limiter; 
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
                if ($factioninfluencearray[0] < $factioninfluencearray[1]) {
                  $direction = 'down';
                } elseif ($factioninfluencearray[0] > $factioninfluencearray[1]) {
                  $direction = 'up';
                } elseif ($factioninfluencearray[0] == $factioninfluencearray[1]) {
                  $direction = 'stable';
                }
                $influencechangeamount = round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2);
                if ($influencechangeamount > $systeminfluencewarningpercentage) {
                  echo "<div id=\"".$systemaddress."_article\" class=\"article\" style=\"margin-bottom: 10px;\">";
                      if ($direction == 'up') {
                        echo "<div class=\"articlenotice\" onclick=\"toggleArticledisplay('".$systemaddress."')\">";
                          echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% <a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlenotice\">".$systemname."</a> Influence increase"; 
                        echo "</div>\n";
                      } elseif ($direction == 'down') {
                        echo "<div class=\"articlewarning\" onclick=\"toggleArticledisplay('".$systemaddress."')\">";
                          echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% <a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlewarning\">".$systemname."</a> Influence drop"; 
                        echo "</div>\n";
                      }
                      echo "<div id=\"".$systemaddress."_articlecontents\" class=\"articlecontents\" style=\"display: none;\">";
                    ?>
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
                              if ($row4['Name'] == $pmfname) {
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
                          chartArea: {
                            top: 25,
                            left: 0,
                            width: '350'
                          },
                          width: 350,
                          height: 350,
                          legend: 'none',
                          is3D: true,
                          slices: {
                            <?PHP echo $idacolumn; ?>: {offset: 0.5, color: '#4942CC'}
                          }
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('<?PHP echo $systemaddress; ?>_influencechange_piechart_3d'));
                        chart.draw(data, options);
                      }
                    </script>
                    <div id="<?PHP echo $systemaddress; ?>_influencechange_piechart_3d" style="width: 350px; float:left;"></div>




                    <script type="text/javascript">
                      google.load('visualization', '1.1', { packages: ['corechart'] });
                      google.setOnLoadCallback(drawChart);

                      function drawChart() {

                      var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Days back');
                        data.addColumn('number', 'Influence');
                        
                        data.addRows([
                          <?PHP
                            $influencetickcounter = 0;
                            $influencetickcount = 5;
                            while ($influencetickcounter <= $influencetickcount) {
                              if ($influencetickcounter < 5) {
                                $influencetickquery = "SELECT Influence FROM snapshots WHERE tickid = '".($oldtickid - ($influencetickcount - ($influencetickcounter+1)))."' AND isfaction = '1' AND Name = '".$pmfname."' AND factionaddress = '$systemaddress' ORDER BY timestamp DESC LIMIT 1";
                              } else {
                                $influencetickquery = "SELECT Influence FROM activesnapshot WHERE tickid = '".$newtickid."' AND isfaction = '1' AND Name = '".$pmfname."' AND factionaddress = '$systemaddress' ORDER BY timestamp DESC LIMIT 1";
                              }
                              if ($influencetickresult = mysqli_query($con, $influencetickquery)){
                                if (mysqli_num_rows($influencetickresult) === 1) {
                                  while($row = mysqli_fetch_array($influencetickresult, MYSQLI_ASSOC)) {
                                    echo "['".($influencetickcount - $influencetickcounter)."', ".round(($row['Influence'] * 100), 2)."]";
                                  }
                                } else {
                                  echo "['".($influencetickcount - $influencetickcounter)."', null]";
                                }
                              }                                

                              if ($influencetickcounter < $influencetickcount) {
                                echo ", ";
                              }
                              $influencetickcounter++;
                            }
                          ?>  
                        ]);

                        var options = {
                          chartArea: {
                            top: 25,
                            left: 50,
                            width: '100%'
                          },
                          height: 350,
                          legend: 'none',
                          hAxis: {
                            title: 'Days back'
                          },
                          vAxis: {
                            title: 'Influence'
                          },
                          interpolateNulls: true,
                          curveType: 'function'

                        };

                        var chart = new google.visualization.LineChart(document.getElementById('<?PHP echo $systemaddress; ?>_influencechange_linechart_material'));
                        chart.draw(data, options);
                      }
                    </script>
                    <div id="<?PHP echo $systemaddress; ?>_influencechange_linechart_material" style="width: 625px; float:left;"></div>






                    </div>
                    <?PHP
                      echo "<div id=\"".$systemaddress."_articlefooter\" class=\"articlefooter\" style=\"display: none;\">";
                        echo "This information is ";
                        if ($checktimestamps[0] > $checktimestamps[1]) {
                          echo datediff($servertime, $checktimestamps[0]);
                        } else {
                          echo datediff($servertime, $checktimestamps[1]);
                        }
                        echo " old.";
                    echo "</div>";
                  echo "</div>";
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
                $activesnapshotquery = "SELECT Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$pmfname."' ORDER BY tickid DESC LIMIT 1";
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
                $snapshotquery = "SELECT Influence FROM snapshots WHERE isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$pmfname."' ORDER BY tickid DESC LIMIT $limiter"; 
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
                    <div class="article">
                      <?PHP
                        if ($direction == 'up') {
                          echo "<div class=\"articlenotice\">";
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence increase"; 
                          echo "</div>\n";
                        } elseif ($direction == 'down') {
                          echo "<div class=\"articlewarning\">";
                            echo round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% ".$systemname." Influence drop"; 
                          echo "</div>\n";
                        }
                      ?>
                      <div class="articlecontents">
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
