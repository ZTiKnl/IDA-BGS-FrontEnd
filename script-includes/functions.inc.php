<?php
function datediff($date1, $date2) {

  // Declare and define two dates
  $date1 = strtotime($date1);
  $date2 = strtotime($date2);

  // Formulate the Difference between two dates
  $diff = abs($date2 - $date1);

  // To get the year divide the resultant date into
  // total seconds in a year (365*60*60*24)
  $years = floor($diff / (365*60*60*24));
  
  // To get the month, subtract it with years and
  // divide the resultant date into
  // total seconds in a month (30*60*60*24)
  $months = floor(($diff - $years * 365*60*60*24)
                                 / (30*60*60*24));

  // To get the day, subtract it with years and
  // months and divide the resultant date into
  // total seconds in a days (60*60*24)
  $days = floor(($diff - $years * 365*60*60*24 -
               $months*30*60*60*24)/ (60*60*24));

  // To get the hour, subtract it with years,
  // months & seconds and divide the resultant
  // date into total seconds in a hours (60*60)
  $hours = floor(($diff - $years * 365*60*60*24
         - $months*30*60*60*24 - $days*60*60*24)
                                     / (60*60));

  // To get the minutes, subtract it with years,
  // months, seconds and hours and divide the
  // resultant date into total seconds i.e. 60
  $minutes = floor(($diff - $years * 365*60*60*24
           - $months*30*60*60*24 - $days*60*60*24
                            - $hours*60*60)/ 60);

  $result = '';
  if ($years > 0) {
    if ($result != '') {$result .= ', ';}
    $result .= $months."Y";
  }

  if ($months > 0) {
    if ($result != '') {$result .= ', ';}
    $result .= $months."M";
  }

  if ($days > 0) {
    if ($result != '') {$result .= ', ';}
    $result .= $days."d";
  }

  if ($hours > 0) {
    if ($result != '') {$result .= ', ';}
    $result .= $hours."h";
  }

  if ($minutes > 0) {
    if ($result != '') {$result .= ', ';}
    $result .= $minutes."m";
  }
  if ($result == '') {
    if ($diff < 60) {
      $result = '< 1m';
    }
  }

  // Print the result
  return $result;

}
?>