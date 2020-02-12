<?PHP
$oldtick = '';
$oldtickid = 0;
$newtick = '';
$newtickid = 0;

$tickquery = "SELECT id, timestamp FROM dailyticks ORDER BY id DESC LIMIT 2";
if ($tickresult = mysqli_query($con, $tickquery)){
  if (mysqli_num_rows($tickresult) === 2) {
    $i = 0;
    $rows = array();
    while($row = mysqli_fetch_array($tickresult, MYSQLI_ASSOC)) {
      $rows[$i]['id'] = $row['id'];
      $rows[$i]['timestamp'] = $row['timestamp'];
      $i++;
    }
    $newtick = $rows[0]['timestamp'];
    $newtickid = $rows[0]['id'];
    $oldtick = $rows[1]['timestamp'];
    $oldtickid = $rows[1]['id'];
  } elseif (mysqli_num_rows($tickresult) === 1) {
    $row = mysqli_fetch_array($tickresult, MYSQLI_ASSOC);
    $newtick = $row['timestamp'];
    $newtickid = $row['id'];
  }
}

$servertime = gmdate("Y-m-d H:i:s");
?>