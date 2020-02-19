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
<?PHP include($siteincludefiles.'head.inc.php'); ?>
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
                $systemname = $row['systemname'];
                $systemaddress = $row['systemaddress'];
                $influencedatainactivesnapshot = false;
                $influencedatainsnapshots = false;
                $factioninfluencearray = array();
                $systemuptodate = false;
                $systemupdatetime = 0;
                $checktickid = array();
                $checktimestamps = array();

                $systemcheckactivesnapshotquery = "SELECT timestamp FROM activesnapshot WHERE tickid = '$newtickid' AND SystemAddress = '$systemaddress'";
                if ($systemcheckactivesnapshotresult = mysqli_query($con, $systemcheckactivesnapshotquery)){
                  if (mysqli_num_rows($systemcheckactivesnapshotresult) > 0) {
                    $systemuptodate = true;
                    while($row2 = mysqli_fetch_array($systemcheckactivesnapshotresult, MYSQLI_ASSOC)) {
                      $systemupdatetime = $row2['timestamp'];
                    }
                  }
                }

                $influenceactivesnapshotquery = "SELECT tickid, timestamp, Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$pmfname."' ORDER BY tickid DESC LIMIT 1";
                if ($influenceactivesnapshotresult = mysqli_query($con, $influenceactivesnapshotquery)){
                  if (mysqli_num_rows($influenceactivesnapshotresult) > 0) {
                    $influencedatainactivesnapshot = true;
                    while($row2 = mysqli_fetch_array($influenceactivesnapshotresult, MYSQLI_ASSOC)) {
                      $factioninfluencearray[] = $row2['Influence'];
                      $checktickid[] = $row2['tickid'];
                      $checktimestamps[] = $row2['timestamp'];
                    }
                  }
                }

                if ($influencedatainactivesnapshot) {
                  $limiter = 1;
                } else {
                  $limiter = 2;
                }
                $influencesnapshotquery = "SELECT tickid, timestamp, Influence FROM snapshots WHERE isfaction = '1' AND factionaddress = '$systemaddress' AND Name = '".$pmfname."' ORDER BY tickid DESC LIMIT ".$limiter; 
                if ($influencesnapshotresult = mysqli_query($con, $influencesnapshotquery)){
                  if (mysqli_num_rows($influencesnapshotresult) > 0) {
                    // use data from activesnapshot
                    $influencedatainsnapshots = true;
                    while($row3 = mysqli_fetch_array($influencesnapshotresult, MYSQLI_ASSOC)) {
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
                  echo "<div id=\"".$systemaddress."_influence_article\" class=\"article\" style=\"margin-bottom: 10px;\">";
                      if ($direction == 'up') {
                        echo "<div class=\"articlenotice\" onclick=\"toggleArticledisplay('".$systemaddress."_influence')\">";
                          echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlenotice\">".$systemname."</a> ".round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% Influence increase"; 
                        echo "</div>\n";
                      } elseif ($direction == 'down') {
                        echo "<div class=\"articlewarning\" onclick=\"toggleArticledisplay('".$systemaddress."_influence')\">";
                          echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlewarning\">".$systemname."</a> ".round(abs(($factioninfluencearray[0] * 100) - ($factioninfluencearray[1] * 100)), 2)."% Influence drop"; 
                        echo "</div>\n";
                      }
                      echo "<div id=\"".$systemaddress."_influence_articlecontents\" class=\"articlecontents\" style=\"display: block;\">";
                    ?>
                    <script type="text/javascript">
                      google.charts.load("current", {packages:["corechart"]});
                      google.charts.setOnLoadCallback(drawChart);
                      function drawChart() {

                        var data = google.visualization.arrayToDataTable([
                          ['Faction', 'Influence'],
                          <?PHP
                            if ($influencedatainactivesnapshot) {
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

                        var chart = new google.visualization.PieChart(document.getElementById('<?PHP echo $systemaddress; ?>_influence_piechart_3d'));
                        chart.draw(data, options);
                      }
                    </script>
                    <div id="<?PHP echo $systemaddress; ?>_influence_piechart_3d" style="width: 350px; float:left;"></div>




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
                            left: 150,
                            width: '50%'
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

                        var chart = new google.visualization.LineChart(document.getElementById('<?PHP echo $systemaddress; ?>_influence_linechart_material'));
                        chart.draw(data, options);
                        toggleArticledisplay('<?PHP echo $systemaddress; ?>_influence', 'none');
                      }
                    </script>
                    <div id="<?PHP echo $systemaddress; ?>_influence_linechart_material" style="width: 625px; float:left;"></div>

                    </div>
                    <?PHP
                      echo "<div id=\"".$systemaddress."_influence_articlefooter\" class=\"articlefooter\" style=\"display: block;\">";
                        echo "This information is ";
                        if ($systemupdatetime == 0) {
                          echo "<span style=\"color: red;\">";
                        }
                        if ($checktimestamps[0] > $checktimestamps[1]) {
                          echo datediff($servertime, $checktimestamps[0]);
                        } else {
                          echo datediff($servertime, $checktimestamps[1]);
                        }
                        if ($systemupdatetime == 0) {
                          echo "</span>";
                        }
                        echo " old.";
                    echo "</div>";
                  echo "</div>";
                }










                // CONFLICT WARNING SYSTEM
                $conflictarray = array();
                $conflictdatainactivesnapshot = false;
                $conflictdatainsnapshots = false;
                $conflictactivesnapshotquery = "SELECT tickid, timestamp, StarSystem, SystemAddress, conflicttype, conflictstatus, conflictfaction1name, conflictfaction1stake, conflictfaction1windays, conflictfaction2name, conflictfaction2stake, conflictfaction2windays FROM activesnapshot WHERE tickid = '$newtickid' AND isconflict = '1' AND SystemAddress = '$systemaddress' AND (conflictfaction1name = '".$pmfname."' OR conflictfaction2name = '".$pmfname."') ORDER BY tickid DESC";
                if ($conflictactivesnapshotresult = mysqli_query($con, $conflictactivesnapshotquery)){
                  if (mysqli_num_rows($conflictactivesnapshotresult) > 0) {
                    $conflictdatainactivesnapshot = true;
                    while($row3 = mysqli_fetch_array($conflictactivesnapshotresult, MYSQLI_ASSOC)) {
                      $conflictarray['tickid'] = $row3['tickid'];
                      $conflictarray['timestamp'] = $row3['timestamp'];
                      $conflictarray['StarSystem'] = $row3['StarSystem'];
                      $conflictarray['SystemAddress'] = $row3['SystemAddress'];
                      $conflictarray['conflicttype'] = $row3['conflicttype'];
                      $conflictarray['conflictstatus'] = $row3['conflictstatus'];
                      $conflictarray['conflictfaction1name'] = $row3['conflictfaction1name'];
                      $conflictarray['conflictfaction1stake'] = $row3['conflictfaction1stake'];
                      $conflictarray['conflictfaction1windays'] = $row3['conflictfaction1windays'];
                      $conflictarray['conflictfaction2name'] = $row3['conflictfaction2name'];
                      $conflictarray['conflictfaction2stake'] = $row3['conflictfaction2stake'];
                      $conflictarray['conflictfaction2windays'] = $row3['conflictfaction2windays'];
                    }
                  }
                }
                if (!$conflictdatainactivesnapshot) {
                  $conflictsnapshotquery = "SELECT tickid, timestamp, StarSystem, SystemAddress, conflicttype, conflictstatus, conflictfaction1name, conflictfaction1stake, conflictfaction1windays, conflictfaction2name, conflictfaction2stake, conflictfaction2windays FROM snapshots WHERE  tickid = '$oldtickid' AND isconflict = '1' AND SystemAddress = '$systemaddress' AND (conflictfaction1name = '".$pmfname."' OR conflictfaction2name = '".$pmfname."') ORDER BY tickid DESC";
                  if ($conflictsnapshotresult = mysqli_query($con, $conflictsnapshotquery)){
                    if (mysqli_num_rows($conflictsnapshotresult) > 0) {
                      $conflictdatainsnapshots = true;
                      while($row4 = mysqli_fetch_array($conflictsnapshotresult, MYSQLI_ASSOC)) {
                        $conflictarray['tickid'] = $row4['tickid'];
                        $conflictarray['timestamp'] = $row4['timestamp'];
                        $conflictarray['StarSystem'] = $row4['StarSystem'];
                        $conflictarray['SystemAddress'] = $row4['SystemAddress'];
                        $conflictarray['conflicttype'] = $row4['conflicttype'];
                        $conflictarray['conflictstatus'] = $row4['conflictstatus'];
                        $conflictarray['conflictfaction1name'] = $row4['conflictfaction1name'];
                        $conflictarray['conflictfaction1stake'] = $row4['conflictfaction1stake'];
                        $conflictarray['conflictfaction1windays'] = $row4['conflictfaction1windays'];
                        $conflictarray['conflictfaction2name'] = $row4['conflictfaction2name'];
                        $conflictarray['conflictfaction2stake'] = $row4['conflictfaction2stake'];
                        $conflictarray['conflictfaction2windays'] = $row4['conflictfaction2windays'];
                      }
                    }
                  }
                }

                if ($conflictdatainactivesnapshot || $conflictdatainsnapshots) {
                  $direction;
                  if (
                    ($conflictarray['conflictfaction1name'] == $pmfname && $conflictarray['conflictfaction1windays'] < $conflictarray['conflictfaction2windays'])
                    || 
                    ($conflictarray['conflictfaction2name'] == $pmfname && $conflictarray['conflictfaction2windays'] < $conflictarray['conflictfaction1windays'])
                  ) {
                    $direction = 'down';
                  } elseif (
                    ($conflictarray['conflictfaction1name'] == $pmfname && $conflictarray['conflictfaction1windays'] > $conflictarray['conflictfaction2windays'])
                    || 
                    ($conflictarray['conflictfaction2name'] == $pmfname && $conflictarray['conflictfaction2windays'] > $conflictarray['conflictfaction1windays'])
                  ) {
                    $direction = 'up';
                  } elseif ($conflictarray['conflictfaction1windays'] == $conflictarray['conflictfaction2windays']) {
                    $direction = 'draw';
                  }

                  echo "<div id=\"".$systemaddress."_conflict_article\" class=\"article\" style=\"margin-bottom: 10px;\">";
                    if ($conflictarray['conflictstatus'] == 'Pending') {
                      echo "<div class=\"articleinfo\" onclick=\"toggleArticledisplay('".$systemaddress."_conflict')\">";
                        echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articleinfo\">".$systemname."</a> ".$conflictarray['conflicttype']." pending"; 
                      echo "</div>\n";
                    } else {
                      if ($direction == 'up') {
                        echo "<div class=\"articlenotice\" onclick=\"toggleArticledisplay('".$systemaddress."_conflict')\">";
                          if ((!$conflictdatainactivesnapshot && $systemuptodate) || $conflictarray['conflictstatus'] == '') {
                            echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlenotice\">".$systemname."</a> ".$conflictarray['conflicttype']." ended"; 
                          } else {
                            echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlenotice\">".$systemname."</a> ".$conflictarray['conflicttype']." ongoing"; 
                          }
                        echo "</div>\n";
                      } elseif ($direction == 'down' || $direction == 'draw') {
                        echo "<div class=\"articlewarning\" onclick=\"toggleArticledisplay('".$systemaddress."_conflict')\">";
                          if ((!$conflictdatainactivesnapshot && $systemuptodate) || $conflictarray['conflictstatus'] == '') {
                            echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlewarning\">".$systemname."</a> ".$conflictarray['conflicttype']." ended"; 
                          } else {
                            echo "<a href=\"".$siteurl."system/".$systemaddress."\" class=\"articlewarning\">".$systemname."</a> ".$conflictarray['conflicttype']." ongoing"; 
                          }
                        echo "</div>\n";
                      }
                    }
                    echo "<div id=\"".$systemaddress."_conflict_articlecontents\" class=\"articlecontents\" style=\"display: block;\">";
                      if (!$conflictdatainactivesnapshot && $systemuptodate) {
                        echo "This conflict disappeared with last tick update.<br />";
                        echo "Last known status: <br /><br />";
                      }
                      if ($conflictarray['conflictstatus'] != 'Pending') {
?>
                      <script type="text/javascript">
                        google.charts.load('current', {packages: ['corechart', 'bar']});
                        google.charts.setOnLoadCallback(drawBasic);

                        function drawBasic() {

                          var data = google.visualization.arrayToDataTable([
                            ['Faction', 'Win days', { role: 'style' }],
                            <?PHP
                              if ($conflictarray['conflictfaction1name'] == $pmfname) {
                                echo "['".addslashes($conflictarray['conflictfaction1name'])."', ".$conflictarray['conflictfaction1windays'].", '#335C81'],";
                                echo "['".addslashes($conflictarray['conflictfaction2name'])."', ".$conflictarray['conflictfaction2windays'].", '#5e0f18']";
                              } else {
                                echo "['".addslashes($conflictarray['conflictfaction2name'])."', ".$conflictarray['conflictfaction2windays'].", '#335C81'],";
                                echo "['".addslashes($conflictarray['conflictfaction1name'])."', ".$conflictarray['conflictfaction1windays'].", '#5e0f18']";
                              }
                            ?>
                          ]);

                          var options = {
                            chartArea: {width: '75%'},
                            hAxis: {
                              minValue: 0,
                              ticks: [0, 1, 2, 3, 4]
                            },
                            legend: {position: 'none'}
                          };

                          var chart = new google.visualization.BarChart(document.getElementById('<?PHP echo $systemaddress; ?>_conflict_barchart'));

                          chart.draw(data, options);
                          toggleArticledisplay('<?PHP echo $systemaddress; ?>_conflict', 'none');
                        }
                      </script>
                      <div id="<?PHP echo $systemaddress; ?>_conflict_barchart" style="width: 100%;"></div>
                      <?PHP
                        }
                      ?>

                      <script type="text/javascript">
                        google.charts.load('current', {'packages':['table']});
                        google.charts.setOnLoadCallback(drawTable);

                        function drawTable() {
                          var cssClassNames = {
                            'headerRow': 'leftalignheader blueheader'
                          };

                          var data = new google.visualization.DataTable();
                          data.addColumn('string', 'Faction');
                          data.addColumn('string', 'Stake');
                          data.addColumn('number', 'Score');
                          data.addRows([
                            <?PHP
                              if ($conflictarray['conflictfaction1name'] == $pmfname) {
                                echo "[{v: '".addslashes($conflictarray['conflictfaction1name'])."', p: {'className': 'highlightcol'}}, {v: '".addslashes($conflictarray['conflictfaction1stake'])."', p: {'className': 'highlightcol'}}, {v: ".$conflictarray['conflictfaction1windays'].", p: {'className': 'highlightcol'}}], \n";
                              } else {
                                echo "['".addslashes($conflictarray['conflictfaction1name'])."', '".addslashes($conflictarray['conflictfaction1stake'])."', ".$conflictarray['conflictfaction1windays']."], \n";
                              }
                              if ($conflictarray['conflictfaction2name'] == $pmfname) {
                                echo "[{v: '".addslashes($conflictarray['conflictfaction2name'])."', p: {'className': 'highlightcol'}}, {v: '".addslashes($conflictarray['conflictfaction2stake'])."', p: {'className': 'highlightcol'}}, {v: ".$conflictarray['conflictfaction2windays'].", p: {'className': 'highlightcol'}}]\n";
                              } else {
                                echo "['".addslashes($conflictarray['conflictfaction2name'])."', '".addslashes($conflictarray['conflictfaction2stake'])."', ".$conflictarray['conflictfaction2windays']."]\n";
                              }
                            ?>
                          ]);

                          var table = new google.visualization.Table(document.getElementById('<?PHP echo $systemaddress."_conflict_table"; ?>'));

                          table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, 'cssClassNames': cssClassNames});
                          toggleArticledisplay('<?PHP echo $systemaddress; ?>_conflict', 'none');
                        }
                      </script>
                      <div id="<?PHP echo $systemaddress."_conflict_table"; ?>"></div>
                      <?PHP
                      echo "</div>";
                      echo "<div id=\"".$systemaddress."_conflict_articlefooter\" class=\"articlefooter\" style=\"display: block;\">";
                        echo "This information is ";
                          if ($systemupdatetime != 0) {
                            echo datediff($servertime, $systemupdatetime);
                          } else {
                            echo "<span style=\"color: red;\">".datediff($servertime, $conflictarray['timestamp'])."</span>";
                          }
                        echo " old.";
                      echo "</div>";
                    echo "</div>";
                  }

                echo "<div style=\"margin-bottom: 25px;\";></div>";


              }
            }
          }
        ?>
      </div>
    </div>
  </div>
</body>
</html>
