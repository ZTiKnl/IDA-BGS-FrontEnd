<?PHP
$pagetitle = 'Home';

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

        <div id="article">
          <div id="articletitle">
            Under construction
          </div>
          <div id="articlecontents">
            Select a system in the sidebar, or wait until this page is ready to display IDA faction overview/statistics.
          </div>
          <div id="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

      </div>
    </div>
    <?PHP // include('footer.inc.php'); ?>
    </div>
</body>
</html>
