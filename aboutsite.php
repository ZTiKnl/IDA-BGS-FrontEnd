<?PHP
$pagetitle = 'About Site';

// include config variables
include('config/config.inc.php');

// connect to db
include($securedbcreds);
$con = mysqli_connect($servername,$username,$password,$database) or die("SQL connection error");

// include php functions
include($siteincludefiles.'functions.inc.php');

// include tickdata
include($siteincludefiles.'tickdata.inc.php');

// include Parsedown
include($siteincludefiles.'Parsedown.php');
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

        <div class="article">
          <div class="articletitle">
            README for IDA-BGS FrontEnd website
          </div>
          <div class="articlecontents">
            <?PHP
              $Parsedown = new Parsedown();
              $readmecontents = file_get_contents($siteurl.'README.md');

              echo $Parsedown->text($readmecontents);
            ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>
</html>