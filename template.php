<?PHP
$pagetitle = 'template.php';

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

        <div class="article">
          <div class="articletitle">
            Article with text
          </div>
          <div class="articletabs">
            <button class="tablinkgroup1" onclick="openTab(event, 'test', 'tablinkgroup1', 'articletabcontent')">Test</button>
            <button class="tablinkgroup1" onclick="openTab(event, 'text', 'tablinkgroup1', 'articletabcontent')">Text</button>
            <button class="tablinkgroup1" onclick="openTab(event, 'lorem', 'tablinkgroup1', 'articletabcontent')" id="defaultTab">Lorem Ipsum</button>
          </div>
          <div class="articlecontents">
            <div id="lorem" class="articletabcontent">
              Lorem Ipsum is simply dummy text of the printing and typesetting industry.<br />
              Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.<br />
              It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.<br />
              It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            </div>
            <div id="text" class="articletabcontent">
              text<br />
              <br />
              Lorem Ipsum is simply dummy text of the printing and typesetting industry.
            </div>
            <div id="test" class="articletabcontent">




              <div id="2868635968929_system_table_div"></div>
              


<script type="text/javascript">
google.load('visualization', '1.1', { packages: ['corechart'] });
google.setOnLoadCallback(drawChart);


function drawChart() {

    var data = new google.visualization.DataTable();
                  data.addColumn('date', 'Date');
                  data.addColumn('number', 'Briganii Partners');data.addColumn('number', 'Chelini Jet Raiders');data.addColumn('number', 'HIP 69699 for Equality');data.addColumn('number', 'HIP 69699 Holdings');data.addColumn('number', 'Independent Defence Agency');data.addColumn('number', 'New Juduni Liberals');data.addColumn('number', 'Pilots\' Federation Local Branch');
                  data.addRows([
[new Date("2020-02-10"), null, null, null, null, null, 0.0719280000, null], 
[new Date("2020-02-11"), 0.0689310000, 0.0149850000, 0.0529470000, 0.1710000000, 0.3720000000, 0.0719280000, 0.0000000000], 
[new Date("2020-02-12"), 0.1027940000, 0.0150000000, 0.1228770000, 0.1700000000, 0.3803800000, 0.0700000000, 0.0000000000], 
[new Date("2020-02-13"), , , 0.1230000000, 0.1700000000, 0.2250000000, , 0.0000000000], 
[new Date("2020-02-14"), 0.0679320000, 0.0099900000, 0.0519480000, 0.1418580000, 0.6573430000, 0.0709290000, 0.0000000000]
  
                  ]);

                  var options = {
                    legend: { position: 'bottom', maxLines: 5 },
                    width: '100%',
                    height: '250',
                    interpolateNulls: true
                  };

                  //var formatter = new google.visualization.DateFormat({pattern: 'dd/MM/yyyy'});

                  var chart = new google.visualization.LineChart(document.getElementById('linechart_material'));

                  chart.draw(data, options);
                }
              </script>
              <div id="linechart_material"></div>
              
              
              
              
            </div>
            <script>
              // Get the element with id="defaultTab" and click on it
              document.getElementById("defaultTab").click();
            </script>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

        <div class="article">
          <div class="articletitle">
            Timeline of stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {'packages':['timeline']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {

                var container = document.getElementById('timeline');

                var chart = new google.visualization.Timeline(container);

                var dataTable = new google.visualization.DataTable();
                dataTable.addColumn({ type: 'string', id: 'President' });
                dataTable.addColumn({ type: 'date', id: 'Start' });
                dataTable.addColumn({ type: 'date', id: 'End' });
                dataTable.addRows([
                  [ 'Washington', new Date(1789, 3, 30), new Date(1797, 2, 4) ],
                  [ 'Adams',      new Date(1797, 2, 4),  new Date(1801, 2, 4) ],
                  [ 'Jefferson',  new Date(1801, 2, 4),  new Date(1809, 2, 4) ]]);

                chart.draw(dataTable);
              }
            </script>
            <div id="timeline" style="width: 990px;"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

        <div class="article">
          <div class="articletitle">
            Table with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {'packages':['table']});
              google.charts.setOnLoadCallback(drawTable);

              function drawTable() {

                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('number', 'Salary');
                data.addColumn('boolean', 'Full Time Employee');
                data.addRows([
                  ['Mike',  {v: 10000, f: '$10,000'}, true],
                  ['Jim',   {v:8000,   f: '$8,000'},  false],
                  ['Alice', {v: 12500, f: '$12,500'}, true],
                  ['Bob',   {v: 7000,  f: '$7,000'},  true]
                ]);

                var table = new google.visualization.Table(document.getElementById('table_div'));
                table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
              }
            </script>
            <div id="table_div"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>



        <div class="article">
          <div class="articletitle">
            Treemap with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {'packages':['treemap']});
              google.charts.setOnLoadCallback(drawChart);
              function drawChart() {

                var data = google.visualization.arrayToDataTable([
                  ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
                  ['Global',    null,                 0,                               0],
                  ['America',   'Global',             0,                               0],
                  ['Europe',    'Global',             0,                               0],
                  ['Asia',      'Global',             0,                               0],
                  ['Australia', 'Global',             0,                               0],
                  ['Africa',    'Global',             0,                               0],
                  ['Brazil',    'America',            11,                              10],
                  ['USA',       'America',            52,                              31],
                  ['Mexico',    'America',            24,                              12],
                  ['Canada',    'America',            16,                              -23],
                  ['France',    'Europe',             42,                              -11],
                  ['Germany',   'Europe',             31,                              -2],
                  ['Sweden',    'Europe',             22,                              -13],
                  ['Italy',     'Europe',             17,                              4],
                  ['UK',        'Europe',             21,                              -5],
                  ['China',     'Asia',               36,                              4],
                  ['Japan',     'Asia',               20,                              -12],
                  ['India',     'Asia',               40,                              63],
                  ['Laos',      'Asia',               4,                               34],
                  ['Mongolia',  'Asia',               1,                               -5],
                  ['Israel',    'Asia',               12,                              24],
                  ['Iran',      'Asia',               18,                              13],
                  ['Pakistan',  'Asia',               11,                              -52],
                  ['Egypt',     'Africa',             21,                              0],
                  ['S. Africa', 'Africa',             30,                              43],
                  ['Sudan',     'Africa',             12,                              2],
                  ['Congo',     'Africa',             10,                              12],
                  ['Zaire',     'Africa',             8,                               10]
                ]);

                tree = new google.visualization.TreeMap(document.getElementById('treechart_div'));
                tree.draw(data, {
                  minColor: '#f00',
                  midColor: '#ddd',
                  maxColor: '#0d0',
                  headerHeight: 15,
                  fontColor: 'black',
                  showScale: true
                });
              }
            </script>
            <div id="treechart_div" style="width: 900px; height: 500px;"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>


        <div class="article">
          <div class="articletitle">
            Organisation chart with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {packages:["orgchart"]});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {

                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('string', 'Manager');
                data.addColumn('string', 'ToolTip');

                // For each orgchart box, provide the name, manager, and tooltip to show.
                data.addRows([
                  [{'v':'Mike', 'f':'Mike<div style="color:red; font-style:italic">President</div>'},
                   '', 'The President'],
                  [{'v':'Jim', 'f':'Jim<div style="color:red; font-style:italic">Vice President</div>'},
                   'Mike', 'VP'],
                  ['Alice', 'Mike', ''],
                  ['Bob', 'Jim', 'Bob Sponge'],
                  ['Carol', 'Bob', '']
                ]);

                // Create the chart.
                var chart = new google.visualization.OrgChart(document.getElementById('orgchart_div'));
                // Draw the chart, setting the allowHtml option to true for the tooltips.
                chart.draw(data, {'allowHtml':true});
              }
           </script>
            </head>
            <div id="orgchart_div"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

        <div class="article">
          <div class="articletitle">
            3D Pie chart with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load("current", {packages:["corechart"]});
              google.charts.setOnLoadCallback(drawChart);
              function drawChart() {

                var data = google.visualization.arrayToDataTable([
                  ['Task', 'Hours per Day'],
                  ['Work',     11],
                  ['Eat',      2],
                  ['Commute',  2],
                  ['Watch TV', 2],
                  ['Sleep',    7]
                ]);

                var options = {
                  title: 'My Daily Activities',
                  is3D: true,
                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                chart.draw(data, options);
              }
            </script>
            <div id="piechart_3d" style="width: 900px; height: 500px;"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

        <div class="article">
          <div class="articletitle">
            Pie chart with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {

                var data = google.visualization.arrayToDataTable([
                  ['Task', 'Hours per Day'],
                  ['Work',     11],
                  ['Eat',      2],
                  ['Commute',  2],
                  ['Watch TV', 2],
                  ['Sleep',    7]
                ]);

                var options = {
                  title: 'My Daily Activities'
                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                chart.draw(data, options);
              }
            </script>
            <div id="piechart" style="width: 900px; height: 500px;"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

        <div class="article">
          <div class="articletitle">
            Bubble chart with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawSeriesChart);

              function drawSeriesChart() {

                var data = google.visualization.arrayToDataTable([
                  ['ID', 'Life Expectancy', 'Fertility Rate', 'Region',     'Population'],
                  ['CAN',    80.66,              1.67,      'North America',  33739900],
                  ['DEU',    79.84,              1.36,      'Europe',         81902307],
                  ['DNK',    78.6,               1.84,      'Europe',         5523095],
                  ['EGY',    72.73,              2.78,      'Middle East',    79716203],
                  ['GBR',    80.05,              2,         'Europe',         61801570],
                  ['IRN',    72.49,              1.7,       'Middle East',    73137148],
                  ['IRQ',    68.09,              4.77,      'Middle East',    31090763],
                  ['ISR',    81.55,              2.96,      'Middle East',    7485600],
                  ['RUS',    68.6,               1.54,      'Europe',         141850000],
                  ['USA',    78.09,              2.05,      'North America',  307007000]
                ]);

                var options = {
                  title: 'Correlation between life expectancy, fertility rate ' +
                         'and population of some world countries (2010)',
                  hAxis: {title: 'Life Expectancy'},
                  vAxis: {title: 'Fertility Rate'},
                  bubble: {textStyle: {fontSize: 11}}      };

                var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div'));
                chart.draw(data, options);
              }
            </script>
            <div id="series_chart_div" style="width: 900px; height: 500px;"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>

        <div class="article">
          <div class="articletitle">
            Line chart with stuff
          </div>
          <div class="articlecontents">
            <script type="text/javascript">
              google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {
                var data = google.visualization.arrayToDataTable([
                  ['Year', 'Sales', 'Expenses'],
                  ['2004',  1000,      400],
                  ['2005',  1170,      460],
                  ['2006',  660,       1120],
                  ['2007',  1030,      540]
                ]);

                var options = {
                  title: 'Company Performance',
                  curveType: 'function',
                  legend: { position: 'bottom' }
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                chart.draw(data, options);
              }
            </script>
            <div id="curve_chart" style="width: 900px; height: 500px"></div>
          </div>
          <div class="articlefooter">
            sidenotes, disclaimers and signatures
          </div>
        </div>
      </div>
    </div>
    <?PHP include($siteincludefiles.'footer.inc.php'); ?>
  </div>
</body>
</html>
