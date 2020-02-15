<?PHP
$pagetitle = 'System overview';

// include config variables
include('config.inc.php');

// connect to db
include($securedbcreds);
$con = mysqli_connect($servername,$username,$password,$database) or die("SQL connection error");

// include php functions
include('functions.inc.php');

// include tickdata
include('tickdata.inc.php');

// Page specific stuff
if (!empty($_GET['id'])) {
  $systemget = $_GET['id'];
} else {
  $systemget = '5068196095409';
}
$systemname;
$systemaddress;
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
          $systemquery = "SELECT systemname, systemaddress FROM systemlist WHERE systemaddress = '$systemget' ORDER BY systemname ASC LIMIT 1";
          if ($systemresult = mysqli_query($con, $systemquery)){
            if (mysqli_num_rows($systemresult) > 0) {
              $systemcounter = 0;
              while($row = mysqli_fetch_array($systemresult, MYSQLI_ASSOC)) {
                $systemname = addslashes($row['systemname']);
                $systemaddress = $row['systemaddress'];
                $datainactivesnapshot = false;
                $datainsnapshots = false;
                $datasnapshottickid = 0;
                $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND issystem = '1' AND StarSystem = '".addslashes($systemname)."' AND SystemAddress = '$systemaddress' ORDER BY tickid DESC LIMIT 1";
                $snapshotquery = "SELECT * FROM snapshots WHERE issystem = '1' AND StarSystem = '".addslashes($systemname)."' AND SystemAddress = '$systemaddress' ORDER BY tickid DESC LIMIT 1";
                if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                  if (mysqli_num_rows($activesnapshotresult) > 0) {
                    $datainactivesnapshot = true;
                    while($row3 = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $datasnapshottickid = $row3['tickid'];
                    }
                  } else {
                    if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                      if (mysqli_num_rows($snapshotresult) > 0) {
                        // use data from activesnapshot
                        $datainsnapshots = true;
                        while($row4 = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                          $datasnapshottickid = $row4['tickid'];
                        }
                      }
                    }
                  }
                }

                $lastupdatetime = '';
                $timediff = '';
                $updatedaftertick = '';
                $lastupdatequery = "SELECT timestamp FROM systemdata WHERE StarSystem = '".addslashes($systemname)."' AND SystemAddress = '$systemaddress' ORDER BY timestamp DESC LIMIT 1";
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
        ?>
        <div id="article">
          <div id="articletitle">
            <?PHP
              echo $systemname;
            ?>
          </div>
          <div id="articletabs">
            <button class="<?PHP echo "tablinkgroup".$systemcounter; ?>" onclick="openTab(event, '<?PHP echo $systemaddress."systemdetails"; ?>', '<?PHP echo "tablinkgroup".$systemcounter; ?>', '<?PHP echo "articletabcontent".$systemcounter; ?>')" id="<?PHP echo $systemaddress."_defaultTab"; ?>">System details</button>
            <button class="<?PHP echo "tablinkgroup".$systemcounter; ?>" onclick="openTab(event, '<?PHP echo $systemaddress."factiondetails"; ?>', '<?PHP echo "tablinkgroup".$systemcounter; ?>', '<?PHP echo "articletabcontent".$systemcounter; ?>')">Faction details</button>
            <button class="<?PHP echo "tablinkgroup".$systemcounter; ?>" onclick="openTab(event, '<?PHP echo $systemaddress."states"; ?>', '<?PHP echo "tablinkgroup".$systemcounter; ?>', '<?PHP echo "articletabcontent".$systemcounter; ?>')">States</button>
            <button class="<?PHP echo "tablinkgroup".$systemcounter; ?>" onclick="openTab(event, '<?PHP echo $systemaddress."conflicts"; ?>', '<?PHP echo "tablinkgroup".$systemcounter; ?>', '<?PHP echo "articletabcontent".$systemcounter; ?>')">Conflicts</button>
            <?PHP
              if (!$updatedaftertick ) {
                $agestyle = 'color: red; font-weight: bold;';
              } else {
                $agestyle = '';
              }
            ?> 
            <span class="
              <?PHP 
                echo "tablinkgroup".$systemcounter; ?>" style="<?PHP echo $agestyle; ?>";>Last update: <?PHP echo $timediff."\n";
              ?>
            </span>
          </div>
          <div id="articlecontents">




            <div id="<?PHP echo $systemaddress."systemdetails"; ?>" class="<?PHP echo "articletabcontent".$systemcounter; ?>" style="display: none; animation: fadeEffect 1s;">
              <?PHP
                $nodata = true;
                $systemdataarray;
                if ($datainactivesnapshot) {
                  $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND issystem = '1' AND StarSystem = '".addslashes($systemname)."' AND SystemAddress = '$systemaddress' ORDER BY StarSystem ASC";
                  if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                    while($row = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $systemdataarray = $row;
                      $nodata = false;
                    }
                  }
                } elseif ($datainsnapshots) {
                  $snapshotquery = "SELECT * FROM snapshots WHERE tickid = '$datasnapshottickid' AND issystem = '1' AND StarSystem = '".addslashes($systemname)."' AND SystemAddress = '$systemaddress' ORDER BY StarSystem ASC";
                  if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                    while($row = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                      $systemdataarray = $row;
                      $nodata = false;
                    }
                  }
                }
                if ($nodata) {
                  echo "No data found for this system.";
                } else {
              ?>
              <script type="text/javascript">
                google.charts.load('current', {'packages':['table']});
                google.charts.setOnLoadCallback(drawTable);

                function drawTable() {
                  var cssClassNames = {
                    'headerRow': 'leftalignheader blueheader hiddenheader'
                  };

                  var data = new google.visualization.DataTable();
                  data.addColumn('string', 'Parameter');
                  data.addColumn('string', 'Value');
                  data.addRows([
                    <?PHP
                      echo "['Address', '".$systemaddress."'], \n";
                      echo "['Population', '".$systemdataarray['Population']."'], \n";
                      echo "['Allegiance', '".$systemdataarray['SystemAllegiance']."'], \n";
                      echo "['Government', '".$systemdataarray['SystemGovernment']."'], \n";
                      echo "['Security', '".$systemdataarray['SystemSecurity']."'], \n";
                      echo "['Economy', '".$systemdataarray['SystemEconomy']."'], \n";
                      echo "['Secondary economy', '".$systemdataarray['SystemSecondEconomy']."'], \n";
                      echo "['Controlling faction', '".$systemdataarray['ControllingFaction']."'], \n";
                      echo "['Controlling faction state', '".$systemdataarray['FactionState']."']\n";
                    ?>
                  ]);

                  var table = new google.visualization.Table(document.getElementById('<?PHP echo $systemaddress."_system_table_div"; ?>'));

                  table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, 'cssClassNames': cssClassNames});
                }
              </script>
              <div id="<?PHP echo $systemaddress."_system_table_div"; ?>"></div>
              <?PHP
                }
              ?>
            </div>









            <div id="<?PHP echo $systemaddress."factiondetails"; ?>" class="<?PHP echo "articletabcontent".$systemcounter; ?>" style="display: none; animation: fadeEffect 1s;">
              <?PHP
                $nodata = true;
                $systemfactiondataarray = array();
                if ($datainactivesnapshot) {
                  $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionsystem = '$systemname' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
                  if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                    while($row = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $systemfactiondataarray[] = $row;
                      $nodata = false;
                    }
                  }
                } elseif ($datainsnapshots) {
                  $snapshotquery = "SELECT * FROM snapshots WHERE tickid = '$datasnapshottickid' AND isfaction = '1' AND factionsystem = '$systemname' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
                  if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                    while($row = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                      $systemfactiondataarray[] = $row;
                      $nodata = false;
                    }
                  }
                }
                if ($nodata) {
                  echo "No data found for this system.";
                } else {
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
                  data.addColumn('string', 'Government');
                  data.addColumn('string', 'Influence');
                  data.addColumn('string', 'Allegiance');
                  data.addColumn('string', 'Happiness');
                  data.addRows([
                    <?PHP
                      $factioncount = count($systemfactiondataarray);
                      $factioncounter = 0;
                      while($factioncounter < $factioncount) {
                        if ($systemfactiondataarray[$factioncounter]['Name'] == addslashes($highlight_pmfname)) {
                          echo "[
                      {v: '".addslashes($systemfactiondataarray[$factioncounter]['Name'])."', p: {'className': 'highlightcol'}}, 
                      {v: '".$systemfactiondataarray[$factioncounter]['Government']."', p: {'className': 'highlightcol'}}, 
                      {v: '".round(($systemfactiondataarray[$factioncounter]['Influence'] * 100), 2)."%', p: {'className': 'highlightcol'}}, 
                      {v: '".$systemfactiondataarray[$factioncounter]['Allegiance']."', p: {'className': 'highlightcol'}}, 
                      {v: '".$systemfactiondataarray[$factioncounter]['Happiness']."', p: {'className': 'highlightcol'}}
                      ]";
                        } else {
                          echo "['".addslashes($systemfactiondataarray[$factioncounter]['Name'])."', '".$systemfactiondataarray[$factioncounter]['Government']."', '".round(($systemfactiondataarray[$factioncounter]['Influence'] * 100), 2)."%', '".$systemfactiondataarray[$factioncounter]['Allegiance']."', '".$systemfactiondataarray[$factioncounter]['Happiness']."']";
                        }
                        if ($factioncounter < ($factioncount-1)) {
                          echo ", ";
                        }
                        $factioncounter++;
                      }
                    ?>
                  ]);

                  var table = new google.visualization.Table(document.getElementById('<?PHP echo $systemaddress."_faction_table_div"; ?>'));
                  table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, 'cssClassNames': cssClassNames});
                }
              </script>
              <div id="<?PHP echo $systemaddress."_faction_table_div"; ?>"></div>
              <?PHP
                }
              ?>
            </div>








            <div id="<?PHP echo $systemaddress."states"; ?>" class="<?PHP echo "articletabcontent".$systemcounter; ?>" style="display: none; animation: fadeEffect 1s;">
              <?PHP
                $nodata = true;
                $systemstatesdataarray = array();
                if ($datainactivesnapshot) {
                  $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionsystem = '$systemname' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
                  if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                    while($row = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $systemstatesdataarray[] = $row;
                      $nodata = false;
                    }
                  }
                } elseif ($datainsnapshots) {
                  $snapshotquery = "SELECT * FROM snapshots WHERE tickid = '$datasnapshottickid' AND isfaction = '1' AND factionsystem = '$systemname' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
                  if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                    while($row = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                      $systemstatesdataarray[] = $row;
                      $nodata = false;
                    }
                  }
                }
                if ($nodata) {
                  echo "No data found for this system.";
                } else {
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
                  data.addColumn('string', 'Recovering');
                  data.addColumn('string', 'Active');
                  data.addColumn('string', 'Pending');
                  data.addRows([
                    <?PHP
                      $statecount = count($systemstatesdataarray);
                      $statecounter = 0;
                      $pendingstates = array();
                      $activestates = array();
                      $recoveringstates = array();
                      while($statecounter < $statecount) {
                        if ($systemstatesdataarray[$statecounter]['stateBlight'] == 1) {
                          $activestates[] = 'Blight';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateBoom'] == 1) {
                          $activestates[] = 'Boom';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateBust'] == 1) {
                          $activestates[] = 'Bust';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateCivilLiberty'] == 1) {
                          $activestates[] = 'Civil Liberty';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateCivilUnrest'] == 1) {
                          $activestates[] = 'Civil Unrest';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateCivilWar'] == 1) {
                          $activestates[] = 'Civil War';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateColdWar'] == 1) {
                          $activestates[] = 'Cold War';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateColonisation'] == 1) {
                          $activestates[] = 'Colonisation';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateDamaged'] == 1) {
                          $activestates[] = 'Damaged';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateDrought'] == 1) {
                          $activestates[] = 'Drought';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateElection'] == 1) {
                          $activestates[] = 'Election';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateExpansion'] == 1) {
                          $activestates[] = 'Expansion';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateFamine'] == 1) {
                          $activestates[] = 'Famine';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateHistoricEvent'] == 1) {
                          $activestates[] = 'HistoricEvent';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateInfrastructureFailure'] == 1) {
                          $activestates[] = 'Infrastructure Failure';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateInvestment'] == 1) {
                          $activestates[] = 'Investment';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateLockdown'] == 1) {
                          $activestates[] = 'Lockdown';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateNaturalDisaster'] == 1) {
                          $activestates[] = 'Natural Disaster';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateOutbreak'] == 1) {
                          $activestates[] = 'Outbreak';
                        }
                        if ($systemstatesdataarray[$statecounter]['statePirateAttack'] == 1) {
                          $activestates[] = 'Pirate Attack';
                        }
                        if ($systemstatesdataarray[$statecounter]['statePublicHoliday'] == 1) {
                          $activestates[] = 'Public Holiday';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateRetreat'] == 1) {
                          $activestates[] = 'Retreat';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateRevolution'] == 1) {
                          $activestates[] = 'Revolution';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateTechnologicalLeap'] == 1) {
                          $activestates[] = 'Technological Leap';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateTerroristAttack'] == 1) {
                          $activestates[] = 'Terrorist Attack';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateTradeWar'] == 1) {
                          $activestates[] = 'Trade War';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateUnderRepairs'] == 1) {
                          $activestates[] = 'Under Repairs';
                        }
                        if ($systemstatesdataarray[$statecounter]['stateWar'] == 1) {
                          $activestates[] = 'War';
                        }

                        if ($systemstatesdataarray[$statecounter]['recBlight'] == 1) {
                          $recoveringstates[] = 'Blight';
                        }
                        if ($systemstatesdataarray[$statecounter]['recBoom'] == 1) {
                          $recoveringstates[] = 'Boom';
                        }
                        if ($systemstatesdataarray[$statecounter]['recBust'] == 1) {
                          $recoveringstates[] = 'Bust';
                        }
                        if ($systemstatesdataarray[$statecounter]['recCivilLiberty'] == 1) {
                          $recoveringstates[] = 'Civil Liberty';
                        }
                        if ($systemstatesdataarray[$statecounter]['recCivilUnrest'] == 1) {
                          $recoveringstates[] = 'Civil Unrest';
                        }
                        if ($systemstatesdataarray[$statecounter]['recCivilWar'] == 1) {
                          $recoveringstates[] = 'Civil War';
                        }
                        if ($systemstatesdataarray[$statecounter]['recColdWar'] == 1) {
                          $recoveringstates[] = 'Cold War';
                        }
                        if ($systemstatesdataarray[$statecounter]['recColonisation'] == 1) {
                          $recoveringstates[] = 'Colonisation';
                        }
                        if ($systemstatesdataarray[$statecounter]['recDamaged'] == 1) {
                          $recoveringstates[] = 'Damaged';
                        }
                        if ($systemstatesdataarray[$statecounter]['recDrought'] == 1) {
                          $recoveringstates[] = 'Drought';
                        }
                        if ($systemstatesdataarray[$statecounter]['recElection'] == 1) {
                          $recoveringstates[] = 'Election';
                        }
                        if ($systemstatesdataarray[$statecounter]['recExpansion'] == 1) {
                          $recoveringstates[] = 'Expansion';
                        }
                        if ($systemstatesdataarray[$statecounter]['recFamine'] == 1) {
                          $recoveringstates[] = 'Famine';
                        }
                        if ($systemstatesdataarray[$statecounter]['recHistoricEvent'] == 1) {
                          $recoveringstates[] = 'Historic Event';
                        }
                        if ($systemstatesdataarray[$statecounter]['recInfrastructureFailure'] == 1) {
                          $recoveringstates[] = 'Infrastructure Failure';
                        }
                        if ($systemstatesdataarray[$statecounter]['recInvestment'] == 1) {
                          $recoveringstates[] = 'Investment';
                        }
                        if ($systemstatesdataarray[$statecounter]['recLockdown'] == 1) {
                          $recoveringstates[] = 'Lockdown';
                        }
                        if ($systemstatesdataarray[$statecounter]['recNaturalDisaster'] == 1) {
                          $recoveringstates[] = 'Natural Disaster';
                        }
                        if ($systemstatesdataarray[$statecounter]['recOutbreak'] == 1) {
                          $recoveringstates[] = 'Outbreak';
                        }
                        if ($systemstatesdataarray[$statecounter]['recPirateAttack'] == 1) {
                          $recoveringstates[] = 'Pirate Attack';
                        }
                        if ($systemstatesdataarray[$statecounter]['recPublicHoliday'] == 1) {
                          $recoveringstates[] = 'Public Holiday';
                        }
                        if ($systemstatesdataarray[$statecounter]['recRetreat'] == 1) {
                          $recoveringstates[] = 'Retreat';
                        }
                        if ($systemstatesdataarray[$statecounter]['recRevolution'] == 1) {
                          $recoveringstates[] = 'Revolution';
                        }
                        if ($systemstatesdataarray[$statecounter]['recTechnologicalLeap'] == 1) {
                          $recoveringstates[] = 'Technological Leap';
                        }
                        if ($systemstatesdataarray[$statecounter]['recTerroristAttack'] == 1) {
                          $recoveringstates[] = 'Terrorist Attack';
                        }
                        if ($systemstatesdataarray[$statecounter]['recTradeWar'] == 1) {
                          $recoveringstates[] = 'Trade War';
                        }
                        if ($systemstatesdataarray[$statecounter]['recUnderRepairs'] == 1) {
                          $recoveringstates[] = 'Under Repairs';
                        }
                        if ($systemstatesdataarray[$statecounter]['recWar'] == 1) {
                          $recoveringstates[] = 'War';
                        }

                        if ($systemstatesdataarray[$statecounter]['pendingBlight'] == 1) {
                          $pendingstates[] = 'Blight';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingBoom'] == 1) {
                          $pendingstates[] = 'Boom';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingBust'] == 1) {
                          $pendingstates[] = 'Bust';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingCivilLiberty'] == 1) {
                          $pendingstates[] = 'Civil Liberty';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingCivilUnrest'] == 1) {
                          $pendingstates[] = 'Civil Unrest';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingCivilWar'] == 1) {
                          $pendingstates[] = 'Civil War';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingColdWar'] == 1) {
                          $pendingstates[] = 'Cold War';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingColonisation'] == 1) {
                          $pendingstates[] = 'Colonisation';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingDamaged'] == 1) {
                          $pendingstates[] = 'Damaged';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingDrought'] == 1) {
                          $pendingstates[] = 'Drought';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingElection'] == 1) {
                          $pendingstates[] = 'Election';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingExpansion'] == 1) {
                          $pendingstates[] = 'Expansion';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingFamine'] == 1) {
                          $pendingstates[] = 'Famine';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingHistoricEvent'] == 1) {
                          $pendingstates[] = 'Historic Event';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingInfrastructureFailure'] == 1) {
                          $pendingstates[] = 'Infrastructure Failure';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingInvestment'] == 1) {
                          $pendingstates[] = 'Investment';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingLockdown'] == 1) {
                          $pendingstates[] = 'Lockdown';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingNaturalDisaster'] == 1) {
                          $pendingstates[] = 'Natural Disaster';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingOutbreak'] == 1) {
                          $pendingstates[] = 'Outbreak';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingPirateAttack'] == 1) {
                          $pendingstates[] = 'Pirate Attack';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingPublicHoliday'] == 1) {
                          $pendingstates[] = 'Public Holiday';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingRetreat'] == 1) {
                          $pendingstates[] = 'Retreat';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingRevolution'] == 1) {
                          $pendingstates[] = 'Revolution';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingTechnologicalLeap'] == 1) {
                          $pendingstates[] = 'Technological Leap';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingTerroristAttack'] == 1) {
                          $pendingstates[] = 'Terrorist Attack';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingTradeWar'] == 1) {
                          $pendingstates[] = 'Trade War';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingUnderRepairs'] == 1) {
                          $pendingstates[] = 'Under Repairs';
                        }
                        if ($systemstatesdataarray[$statecounter]['pendingWar'] == 1) {
                          $pendingstates[] = 'War';
                        }
                        $activestate = join(", ", $activestates);
                        $recoveringstate = join(", ", $recoveringstates);
                        $pendingstate = join(", ", $pendingstates);

                        if ($systemstatesdataarray[$statecounter]['Name'] == addslashes($highlight_pmfname)) {
                          echo "[
                            {v: '".$systemstatesdataarray[$statecounter]['Name']."', p: {'className': 'highlightcol'}}, 
                            {v: '".$recoveringstate."', p: {'className': 'highlightcol'}}, 
                            {v: '".$activestate."', p: {'className': 'highlightcol'}}, 
                            {v: '".$pendingstate."', p: {'className': 'highlightcol'}}
                          ]";
                        } else {
                          echo "['".addslashes($systemstatesdataarray[$statecounter]['Name'])."', '".$recoveringstate."', '".$activestate."', '".$pendingstate."']";
                        }
                        if ($statecounter < ($statecount-1)) {
                          echo ", ";
                        }
                        $activestates = array();
                        $activestate = '';
                        $recoveringstates = array();
                        $recoveringstate = '';
                        $pendingstates = array();
                        $pendingstate = '';
                        $statecounter++;
                      }
                    ?>
                  ]);

                  var table = new google.visualization.Table(document.getElementById('<?PHP echo $systemaddress."_states_table_div"; ?>'));
                  table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, 'cssClassNames': cssClassNames});
                }
              </script>
              <div id="<?PHP echo $systemaddress."_states_table_div"; ?>"></div>
              <?PHP
                }
              ?>
            </div>















            <div id="<?PHP echo $systemaddress."conflicts"; ?>" class="<?PHP echo "articletabcontent".$systemcounter; ?>" style="display: none; animation: fadeEffect 1s;">
              <?PHP
                $nodata = true;
                $systemconflictdataarray = array();
                if ($datainactivesnapshot) {
                  $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND isconflict = '1' AND StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY conflicttype ASC";
                  if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
                    while($row = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
                      $systemconflictdataarray[] = $row;
                      $nodata = false;
                    }
                  }
                } elseif ($datainsnapshots) {
                  $snapshotquery = "SELECT * FROM snapshots WHERE tickid = '$datasnapshottickid' AND isconflict = '1' AND StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY conflicttype ASC";
                  if ($snapshotresult = mysqli_query($con, $snapshotquery)){
                    while($row = mysqli_fetch_array($snapshotresult, MYSQLI_ASSOC)) {
                      $systemconflictdataarray[] = $row;
                      $nodata = false;
                    }
                  }
                }
                if ($nodata) {
                  echo "No data found for this system.";
                } else {
              ?>

              <script type="text/javascript">
                google.charts.load('current', {'packages':['table']});
                google.charts.setOnLoadCallback(drawTable);

                function drawTable() {

                  var cssClassNames = {
                    'headerRow': 'leftalignheader blueheader'
                  };

                  var data = new google.visualization.DataTable();
                  data.addColumn('string', 'Type');
                  data.addColumn('string', 'Status');
                  data.addColumn('string', 'Faction #1');
                  data.addColumn('string', 'Faction #1 Stake');
                  data.addColumn('string', 'Faction #2');
                  data.addColumn('string', 'Faction #2 Stake');
                  data.addColumn('string', 'score');
                  data.addRows([
                    <?PHP
                      $conflictcount = count($systemconflictdataarray);
                      $conflictcounter = 0;
                      while($conflictcounter < $conflictcount) {
                        if (addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction1name']) == addslashes($highlight_pmfname) || addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction2name'] == addslashes($highlight_pmfname))) {
                          echo "[
                            {v: '".$systemconflictdataarray[$conflictcounter]['conflicttype']."', p: {'className': 'highlightcol'}}, 
                            {v: '".$systemconflictdataarray[$conflictcounter]['conflictstatus']."', p: {'className': 'highlightcol'}}, 
                            {v: '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction1name'])."', p: {'className': 'highlightcol'}}, 
                            {v: '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction1stake'])."', p: {'className': 'highlightcol'}}, 
                            {v: '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction2name'])."', p: {'className': 'highlightcol'}}, 
                            {v: '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction2stake'])."', p: {'className': 'highlightcol'}}, 
                            {v: '".$systemconflictdataarray[$conflictcounter]['conflictfaction1windays']." - ".$systemconflictdataarray[$conflictcounter]['conflictfaction2windays']."', p: {'className': 'highlightcol'}}
                          ]";
                        } else {
                          echo "['".$systemconflictdataarray[$conflictcounter]['conflicttype']."', '".$systemconflictdataarray[$conflictcounter]['conflictstatus']."', '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction1name'])."', '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction1stake'])."', '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction2name'])."', '".addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction2stake'])."', '".$systemconflictdataarray[$conflictcounter]['conflictfaction1windays']." - ".$systemconflictdataarray[$conflictcounter]['conflictfaction2windays']."']";
                        }

                        if ($conflictcounter < ($conflictcount-1)) {
                          echo ", ";
                        }
                        $conflictcounter++;
                      }
                    ?>
                  ]);

                  var table = new google.visualization.Table(document.getElementById('<?PHP echo $systemaddress."_conflict_table_div"; ?>'));
                  table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, 'cssClassNames': cssClassNames});
                }
              </script>
              <div id="<?PHP echo $systemaddress."_conflict_table_div"; ?>"></div>
              <?PHP
                }
              ?>
            </div>

            <script>
              document.getElementById("<?PHP echo $systemaddress."_defaultTab"; ?>").click();
            </script>
          </div>

          <div id="articlecontents">
            <?PHP
              $datearray = array();
              $datearray[] = date('Y-m-d', strtotime('-14 days'));
              $datearray[] = date('Y-m-d', strtotime('-13 days'));
              $datearray[] = date('Y-m-d', strtotime('-12 days'));
              $datearray[] = date('Y-m-d', strtotime('-11 days'));
              $datearray[] = date('Y-m-d', strtotime('-10 days'));
              $datearray[] = date('Y-m-d', strtotime('-9 days'));
              $datearray[] = date('Y-m-d', strtotime('-8 days'));
              $datearray[] = date('Y-m-d', strtotime('-7 days'));
              $datearray[] = date('Y-m-d', strtotime('-6 days'));
              $datearray[] = date('Y-m-d', strtotime('-5 days'));
              $datearray[] = date('Y-m-d', strtotime('-4 days'));
              $datearray[] = date('Y-m-d', strtotime('-3 days'));
              $datearray[] = date('Y-m-d', strtotime('-2 days'));
              $datearray[] = date('Y-m-d', strtotime('-1 days'));
//              if (date('Y-m-d', strtotime($servertime)) > date('Y-m-d', strtotime($newtick))) {
                
//              } else {
                $datearray[] = date('Y-m-d', strtotime('now'));
//              }

              $startperiod = $datearray[0];


              $tempfactionlistarray = array();
              $factionlistactivesnapshotquery = "SELECT DISTINCT Name FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND factionaddress = '$systemaddress' ORDER BY Name ASC";
              if ($factionlistactivesnapshotresult = mysqli_query($con, $factionlistactivesnapshotquery)){
                while($row = mysqli_fetch_array($factionlistactivesnapshotresult, MYSQLI_ASSOC)) {
                  if (!empty($row['Name'])) {
                    array_push($factionlistarray, $row['Name']);
                  }
                }
              }
              $factionlistsnapshotsquery = "SELECT DISTINCT Name FROM snapshots WHERE isfaction = '1' AND factionaddress = '$systemaddress' AND timestamp > '$startperiod' ORDER BY Name ASC";
              if ($factionlistsnapshotsresult = mysqli_query($con, $factionlistsnapshotsquery)){
                while($row = mysqli_fetch_array($factionlistsnapshotsresult, MYSQLI_ASSOC)) {
                  if (!empty($row['Name'])) {
                    array_push($tempfactionlistarray, $row['Name']);
                  }
                }
              }
              $factionlistarray = array_unique($tempfactionlistarray);


              $nodata = true;
              if (!empty($factionlistarray)) {
                $factioninfluencearray = array();
                $factionlistcount = count($factionlistarray);

                $factionlistcounter = 0;
                while ($factionlistcounter < $factionlistcount) {
                  $datecount = count($datearray);
                  $datecounter = 0;
                  while ($datecounter < $datecount-1) {


                    $snapshotstickquery = "SELECT id FROM dailyticks WHERE CAST(timestamp as DATE) = '".$datearray[$datecounter]."' LIMIT 1";
                    $snapshotstickid = 0;
                    if ($snapshotstickresult = mysqli_query($con, $snapshotstickquery)){
                      while($row3 = mysqli_fetch_array($snapshotstickresult, MYSQLI_ASSOC)) {
                        $snapshotstickid = $row3['id'];
                      }
                    }
                    $snapshotsinfluencequery = "SELECT tickid, timestamp, Name, Influence FROM snapshots WHERE isfaction = '1' AND factionaddress = '".$systemaddress."' AND Name = '".addslashes($factionlistarray[$factionlistcounter])."' AND tickid = '".$snapshotstickid."' ORDER BY timestamp DESC LIMIT 1";
                    if ($snapshotsinfluenceresult = mysqli_query($con, $snapshotsinfluencequery)){
                      if (mysqli_num_rows($snapshotsinfluenceresult) > 0) {
                        while($row = mysqli_fetch_array($snapshotsinfluenceresult, MYSQLI_ASSOC)) {
                          $tempdata = array();
                          array_push($tempdata, $row['tickid']);
                          array_push($tempdata, $datearray[($datecounter+1)]);
                          array_push($tempdata, $row['Name']);
                          array_push($tempdata, $row['Influence']);
                          $factioninfluencearray[$factionlistcounter][$datecounter] = $tempdata;
                          $nodata = false;
                        }
                      } else {
                        $factioninfluencearray[$factionlistcounter][$datecounter] = array('', '', '', '');
                      }
                    }
                    $datecounter++;
                  }

                  $activesnapshotinfluencequery = "SELECT tickid, timestamp, Name, Influence FROM activesnapshot WHERE tickid = '$newtickid' AND isfaction = '1' AND Name = '".addslashes($factionlistarray[$factionlistcounter])."' AND factionaddress = '$systemaddress' ORDER BY timestamp DESC LIMIT 1";
                  if ($activesnapshotinfluenceresult = mysqli_query($con, $activesnapshotinfluencequery)){
                    if (mysqli_num_rows($activesnapshotinfluenceresult) > 0) {
                      while($row2 = mysqli_fetch_array($activesnapshotinfluenceresult, MYSQLI_ASSOC)) {
                        $tempdata = array();
                        array_push($tempdata, $row2['tickid']);
                        array_push($tempdata, $datearray[($datecounter)]);
                        array_push($tempdata, $row2['Name']);
                        array_push($tempdata, $row2['Influence']);
                        $factioninfluencearray[$factionlistcounter][$datecounter] = $tempdata;
                        $nodata = false;
                      }
                    } else {
                      $factioninfluencearray[$factionlistcounter][$datecounter] = array('', '', '', '');
                    }
                  }
                  $factionlistcounter++;
                }

                /*
                echo "<br /><br /><pre>";
                print_r($factioninfluencearray);
                echo "</pre><br /><br />";
                */
              }
              if ($nodata) {
                echo "No data found for this system.";
              } else {
              ?>
              <script type="text/javascript">
                google.load('visualization', '1.1', { packages: ['corechart'] });
                google.setOnLoadCallback(drawChart);

                function drawChart() {

                var data = new google.visualization.DataTable();
                  data.addColumn('date', 'Date');
                  <?PHP
                    $factionname;
                    foreach ($factioninfluencearray as $alldates) {
                      foreach ($alldates as $factionnames) {
                        if (!empty($factionnames[2])) {
                          $factionname = $factionnames[2];
                        }
                      }

                      echo "data.addColumn('number', '".addslashes($factionname)."');";

                    }
                  ?>

                  data.addRows([
                    <?PHP
                      $datecount = count($datearray);
                      $datecounter = 0;
                      while ($datecounter < $datecount) {

                        echo "[";

                          $factionlistcount = count($factioninfluencearray);
                          $factionlistcounter = 0;

                          echo "new Date(\"".$datearray[$datecounter]."\"), ";

                            while ($factionlistcounter < $factionlistcount) {
                              if (!empty($factioninfluencearray[$factionlistcounter][$datecounter][3])) {
                                echo round(($factioninfluencearray[$factionlistcounter][$datecounter][3] * 100), 2);
                              } else {
                                echo 'null';
                              }

                              if ($factionlistcounter < ($factionlistcount-1)) {
                                echo ", ";
                              }
                              $factionlistcounter++;
                            }
                        echo "]";

                        if ($datecounter < ($datecount-1)) {
                          echo ", \n";
                        } else {
                          echo "\n";
                        }
                        $datecounter++;
                      }
                    ?>  
                  ]);

                  var options = {
                    legend: { position: 'top', maxLines: 5 },
                    width: '100%',
                    height: '500',
                    interpolateNulls: true,
                    chartArea:{left:50,width:'90%'},
                    curveType: 'function',
                    hAxis: {format:'MMM d', gridlines: {interval: 1}},
                    vAxis: {title: 'Influence'}
                  };

                  var chart = new google.visualization.LineChart(document.getElementById('linechart_material'));
                  chart.draw(data, options);
                }
              </script>
              <div id="linechart_material"></div>
              <?PHP
                }
              ?>
          </div>




        </div>
      <?PHP
            $systemcounter++;
          }
        }
      }
      ?>
      </div>
    </div>
  </div>
</body>
</html>
