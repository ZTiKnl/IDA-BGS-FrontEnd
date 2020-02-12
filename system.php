<?PHP
$pagetitle = 'System overview';

if (!empty($_GET['id'])) {
  $systemget = $_GET['id'];
} else {
  $systemget = '5068196095409';
}

// connect to db
include('../private/db.inc.php');
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
  <title>Independent Defense Agency</title>
  <link rel="stylesheet" href="css/style.css" type="text/css">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="https://ida-bgs.ztik.nl/functions.inc.js"></script>
</head>
<body>
  <div id="page">
    <?PHP include('header.inc.php'); ?>
    <div id="pagecontainer">
      <?PHP include('sidebar.inc.php'); ?>
      <?PHP include('pagetitle.inc.php'); ?>
      <div id="articles">
<?PHP
$systemquery = "SELECT systemname, systemaddress FROM systemlist WHERE systemaddress = '$systemget' ORDER BY systemname ASC";
if ($systemresult = mysqli_query($con, $systemquery)){
  if (mysqli_num_rows($systemresult) > 0) {
    $systemcounter = 0;
    while($row = mysqli_fetch_array($systemresult, MYSQLI_ASSOC)) {
      $systemname = addslashes($row['systemname']);
      $systemaddress = $row['systemaddress'];
      $datainactivesnapshot = false;
      $datainsnapshots = false;
      $datasnapshottickid = 0;
      $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND issystem = '1' AND StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY tickid DESC LIMIT 1";
      $snapshotquery = "SELECT * FROM snapshots WHERE issystem = '1' AND StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY tickid DESC LIMIT 1";
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
      $lastupdatequery = "SELECT timestamp FROM systemdata WHERE StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY timestamp DESC LIMIT 1";
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
            <?PHP echo $systemname; ?>
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
            <span class="<?PHP echo "tablinkgroup".$systemcounter; ?>" style="<?PHP echo $agestyle; ?>";>Last update: <?PHP echo $timediff; ?></span>
          </div>
          <div id="articlecontents">




            <div id="<?PHP echo $systemaddress."systemdetails"; ?>" class="<?PHP echo "articletabcontent".$systemcounter; ?>" style="display: none; animation: fadeEffect 1s;">
<?PHP
      $nodata = true;
      $systemdataarray;
      if ($datainactivesnapshot) {
        $activesnapshotquery = "SELECT * FROM activesnapshot WHERE tickid = '$newtickid' AND issystem = '1' AND StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY StarSystem ASC";
        if ($activesnapshotresult = mysqli_query($con, $activesnapshotquery)){
          while($row = mysqli_fetch_array($activesnapshotresult, MYSQLI_ASSOC)) {
            $systemdataarray = $row;
            $nodata = false;
          }
        }
      } elseif ($datainsnapshots) {
        $snapshotquery = "SELECT * FROM snapshots WHERE tickid = '$datasnapshottickid' AND issystem = '1' AND StarSystem = '$systemname' AND SystemAddress = '$systemaddress' ORDER BY StarSystem ASC";
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
  echo "['Address', '".$systemaddress."'],";
  echo "['Population', '".$systemdataarray['Population']."'],";
  echo "['Allegiance', '".$systemdataarray['SystemAllegiance']."'],";
  echo "['Government', '".$systemdataarray['SystemGovernment']."'],";
  echo "['Security', '".$systemdataarray['SystemSecurity']."'],";
  echo "['Economy', '".$systemdataarray['SystemEconomy']."'],";
  echo "['Secondary economy', '".$systemdataarray['SystemSecondEconomy']."'],";
  echo "['Controlling faction', '".$systemdataarray['ControllingFaction']."'],";
  echo "['Controlling faction state', '".$systemdataarray['FactionState']."']";
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
  if ($systemfactiondataarray[$factioncounter]['Name'] == 'Independent Defence Agency') {
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
$factioncount = count($systemstatesdataarray);
$factioncounter = 0;
$pendingstates = array();
$activestates = array();
$recoveringstates = array();
while($factioncounter < $factioncount) {
  if ($systemstatesdataarray[$factioncounter]['stateBlight'] == 1) {
    $activestates[] = 'Blight';
  }
  if ($systemstatesdataarray[$factioncounter]['stateBoom'] == 1) {
    $activestates[] = 'Boom';
  }
  if ($systemstatesdataarray[$factioncounter]['stateBust'] == 1) {
    $activestates[] = 'Bust';
  }
  if ($systemstatesdataarray[$factioncounter]['stateCivilLiberty'] == 1) {
    $activestates[] = 'Civil Liberty';
  }
  if ($systemstatesdataarray[$factioncounter]['stateCivilUnrest'] == 1) {
    $activestates[] = 'Civil Unrest';
  }
  if ($systemstatesdataarray[$factioncounter]['stateCivilWar'] == 1) {
    $activestates[] = 'Civil War';
  }
  if ($systemstatesdataarray[$factioncounter]['stateColdWar'] == 1) {
    $activestates[] = 'Cold War';
  }
  if ($systemstatesdataarray[$factioncounter]['stateColonisation'] == 1) {
    $activestates[] = 'Colonisation';
  }
  if ($systemstatesdataarray[$factioncounter]['stateDamaged'] == 1) {
    $activestates[] = 'Damaged';
  }
  if ($systemstatesdataarray[$factioncounter]['stateDrought'] == 1) {
    $activestates[] = 'Drought';
  }
  if ($systemstatesdataarray[$factioncounter]['stateElection'] == 1) {
    $activestates[] = 'Election';
  }
  if ($systemstatesdataarray[$factioncounter]['stateExpansion'] == 1) {
    $activestates[] = 'Expansion';
  }
  if ($systemstatesdataarray[$factioncounter]['stateFamine'] == 1) {
    $activestates[] = 'Famine';
  }
  if ($systemstatesdataarray[$factioncounter]['stateHistoricEvent'] == 1) {
    $activestates[] = 'HistoricEvent';
  }
  if ($systemstatesdataarray[$factioncounter]['stateInfrastructureFailure'] == 1) {
    $activestates[] = 'Infrastructure Failure';
  }
  if ($systemstatesdataarray[$factioncounter]['stateInvestment'] == 1) {
    $activestates[] = 'Investment';
  }
  if ($systemstatesdataarray[$factioncounter]['stateLockdown'] == 1) {
    $activestates[] = 'Lockdown';
  }
  if ($systemstatesdataarray[$factioncounter]['stateNaturalDisaster'] == 1) {
    $activestates[] = 'Natural Disaster';
  }
  if ($systemstatesdataarray[$factioncounter]['stateOutbreak'] == 1) {
    $activestates[] = 'Outbreak';
  }
  if ($systemstatesdataarray[$factioncounter]['statePirateAttack'] == 1) {
    $activestates[] = 'Pirate Attack';
  }
  if ($systemstatesdataarray[$factioncounter]['statePublicHoliday'] == 1) {
    $activestates[] = 'Public Holiday';
  }
  if ($systemstatesdataarray[$factioncounter]['stateRetreat'] == 1) {
    $activestates[] = 'Retreat';
  }
  if ($systemstatesdataarray[$factioncounter]['stateRevolution'] == 1) {
    $activestates[] = 'Revolution';
  }
  if ($systemstatesdataarray[$factioncounter]['stateTechnologicalLeap'] == 1) {
    $activestates[] = 'Technological Leap';
  }
  if ($systemstatesdataarray[$factioncounter]['stateTerroristAttack'] == 1) {
    $activestates[] = 'Terrorist Attack';
  }
  if ($systemstatesdataarray[$factioncounter]['stateTradeWar'] == 1) {
    $activestates[] = 'Trade War';
  }
  if ($systemstatesdataarray[$factioncounter]['stateUnderRepairs'] == 1) {
    $activestates[] = 'Under Repairs';
  }
  if ($systemstatesdataarray[$factioncounter]['stateWar'] == 1) {
    $activestates[] = 'War';
  }

  if ($systemstatesdataarray[$factioncounter]['recBlight'] == 1) {
    $recoveringstates[] = 'Blight';
  }
  if ($systemstatesdataarray[$factioncounter]['recBoom'] == 1) {
    $recoveringstates[] = 'Boom';
  }
  if ($systemstatesdataarray[$factioncounter]['recBust'] == 1) {
    $recoveringstates[] = 'Bust';
  }
  if ($systemstatesdataarray[$factioncounter]['recCivilLiberty'] == 1) {
    $recoveringstates[] = 'Civil Liberty';
  }
  if ($systemstatesdataarray[$factioncounter]['recCivilUnrest'] == 1) {
    $recoveringstates[] = 'Civil Unrest';
  }
  if ($systemstatesdataarray[$factioncounter]['recCivilWar'] == 1) {
    $recoveringstates[] = 'Civil War';
  }
  if ($systemstatesdataarray[$factioncounter]['recColdWar'] == 1) {
    $recoveringstates[] = 'Cold War';
  }
  if ($systemstatesdataarray[$factioncounter]['recColonisation'] == 1) {
    $recoveringstates[] = 'Colonisation';
  }
  if ($systemstatesdataarray[$factioncounter]['recDamaged'] == 1) {
    $recoveringstates[] = 'Damaged';
  }
  if ($systemstatesdataarray[$factioncounter]['recDrought'] == 1) {
    $recoveringstates[] = 'Drought';
  }
  if ($systemstatesdataarray[$factioncounter]['recElection'] == 1) {
    $recoveringstates[] = 'Election';
  }
  if ($systemstatesdataarray[$factioncounter]['recExpansion'] == 1) {
    $recoveringstates[] = 'Expansion';
  }
  if ($systemstatesdataarray[$factioncounter]['recFamine'] == 1) {
    $recoveringstates[] = 'Famine';
  }
  if ($systemstatesdataarray[$factioncounter]['recHistoricEvent'] == 1) {
    $recoveringstates[] = 'Historic Event';
  }
  if ($systemstatesdataarray[$factioncounter]['recInfrastructureFailure'] == 1) {
    $recoveringstates[] = 'Infrastructure Failure';
  }
  if ($systemstatesdataarray[$factioncounter]['recInvestment'] == 1) {
    $recoveringstates[] = 'Investment';
  }
  if ($systemstatesdataarray[$factioncounter]['recLockdown'] == 1) {
    $recoveringstates[] = 'Lockdown';
  }
  if ($systemstatesdataarray[$factioncounter]['recNaturalDisaster'] == 1) {
    $recoveringstates[] = 'Natural Disaster';
  }
  if ($systemstatesdataarray[$factioncounter]['recOutbreak'] == 1) {
    $recoveringstates[] = 'Outbreak';
  }
  if ($systemstatesdataarray[$factioncounter]['recPirateAttack'] == 1) {
    $recoveringstates[] = 'Pirate Attack';
  }
  if ($systemstatesdataarray[$factioncounter]['recPublicHoliday'] == 1) {
    $recoveringstates[] = 'Public Holiday';
  }
  if ($systemstatesdataarray[$factioncounter]['recRetreat'] == 1) {
    $recoveringstates[] = 'Retreat';
  }
  if ($systemstatesdataarray[$factioncounter]['recRevolution'] == 1) {
    $recoveringstates[] = 'Revolution';
  }
  if ($systemstatesdataarray[$factioncounter]['recTechnologicalLeap'] == 1) {
    $recoveringstates[] = 'Technological Leap';
  }
  if ($systemstatesdataarray[$factioncounter]['recTerroristAttack'] == 1) {
    $recoveringstates[] = 'Terrorist Attack';
  }
  if ($systemstatesdataarray[$factioncounter]['recTradeWar'] == 1) {
    $recoveringstates[] = 'Trade War';
  }
  if ($systemstatesdataarray[$factioncounter]['recUnderRepairs'] == 1) {
    $recoveringstates[] = 'Under Repairs';
  }
  if ($systemstatesdataarray[$factioncounter]['recWar'] == 1) {
    $recoveringstates[] = 'War';
  }

  if ($systemstatesdataarray[$factioncounter]['pendingBlight'] == 1) {
    $pendingstates[] = 'Blight';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingBoom'] == 1) {
    $pendingstates[] = 'Boom';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingBust'] == 1) {
    $pendingstates[] = 'Bust';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingCivilLiberty'] == 1) {
    $pendingstates[] = 'Civil Liberty';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingCivilUnrest'] == 1) {
    $pendingstates[] = 'Civil Unrest';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingCivilWar'] == 1) {
    $pendingstates[] = 'Civil War';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingColdWar'] == 1) {
    $pendingstates[] = 'Cold War';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingColonisation'] == 1) {
    $pendingstates[] = 'Colonisation';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingDamaged'] == 1) {
    $pendingstates[] = 'Damaged';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingDrought'] == 1) {
    $pendingstates[] = 'Drought';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingElection'] == 1) {
    $pendingstates[] = 'Election';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingExpansion'] == 1) {
    $pendingstates[] = 'Expansion';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingFamine'] == 1) {
    $pendingstates[] = 'Famine';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingHistoricEvent'] == 1) {
    $pendingstates[] = 'Historic Event';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingInfrastructureFailure'] == 1) {
    $pendingstates[] = 'Infrastructure Failure';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingInvestment'] == 1) {
    $pendingstates[] = 'Investment';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingLockdown'] == 1) {
    $pendingstates[] = 'Lockdown';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingNaturalDisaster'] == 1) {
    $pendingstates[] = 'Natural Disaster';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingOutbreak'] == 1) {
    $pendingstates[] = 'Outbreak';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingPirateAttack'] == 1) {
    $pendingstates[] = 'Pirate Attack';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingPublicHoliday'] == 1) {
    $pendingstates[] = 'Public Holiday';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingRetreat'] == 1) {
    $pendingstates[] = 'Retreat';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingRevolution'] == 1) {
    $pendingstates[] = 'Revolution';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingTechnologicalLeap'] == 1) {
    $pendingstates[] = 'Technological Leap';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingTerroristAttack'] == 1) {
    $pendingstates[] = 'Terrorist Attack';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingTradeWar'] == 1) {
    $pendingstates[] = 'Trade War';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingUnderRepairs'] == 1) {
    $pendingstates[] = 'Under Repairs';
  }
  if ($systemstatesdataarray[$factioncounter]['pendingWar'] == 1) {
    $pendingstates[] = 'War';
  }
  $activestate = join(", ", $activestates);
  $recoveringstate = join(", ", $recoveringstates);
  $pendingstate = join(", ", $pendingstates);

  if ($systemstatesdataarray[$factioncounter]['Name'] == 'Independent Defence Agency') {
    echo "[
{v: '".$systemstatesdataarray[$factioncounter]['Name']."', p: {'className': 'highlightcol'}}, 
{v: '".$recoveringstate."', p: {'className': 'highlightcol'}}, 
{v: '".$activestate."', p: {'className': 'highlightcol'}}, 
{v: '".$pendingstate."', p: {'className': 'highlightcol'}}
]";
  } else {
    echo "['".addslashes($systemstatesdataarray[$factioncounter]['Name'])."', '".$recoveringstate."', '".$activestate."', '".$pendingstate."']";
  }
  if ($factioncounter < ($factioncount-1)) {
    echo ", ";
  }
  $activestates = array();
  $activestate = '';
  $recoveringstates = array();
  $recoveringstate = '';
  $pendingstates = array();
  $pendingstate = '';
  $factioncounter++;
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
  if (addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction1name']) == 'Independent Defence Agency' || addslashes($systemconflictdataarray[$conflictcounter]['conflictfaction2name'] == 'Independent Defence Agency')) {
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
        </div>
<?PHP
      $systemcounter++;
    }
  }
}
?>
      </div>
    </div>
    <?PHP // include('footer.inc.php'); ?>
    </div>
</body>
</html>
