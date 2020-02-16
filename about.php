<?PHP
$pagetitle = 'About IDA';

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
            Welcome to:
          </div>
          <div class="articlecontents">
            <span style="font-size: 25px;">The Independent Defence Agency<br /></span>
            <br />
            Our Agency is an Elite Dangerous player group formed by Lawful Veterans and starters alike, dedicated to making human-occupied space a fun and adventurous environment.<br />
            <br />
            Our core players are from the EU and the UK, but we are increasing our numbers around the world, especially in the USA and Canada. We are PC players, and unfortunately, <span style="text-decoration: underline;">we've closed down the applications from PS4 and XBOX until the game allows us to play across platforms</span>.<br />
            <br />
            We are a <a href="https://discordapp.com/">Discord</a> based group. <a href="https://discord.gg/8tkNGXV">Join</a><br />
            <br />
            The IDA aims to be a community where commanders can have fun and enjoy playing the game, as well as being a place where new commanders can learn and ultimately:<br />
            <span style="text-decoration: underline;">Protect the vulnerable.</span><br />
            <br />
            <br />
            <span style="font-size: 20px;">What We Do:<br /></span>
            <br />
            <span style="text-decoration: underline;">- Patrols -</span><br />
            <br />
            As a Lawful group, we fight against Gankers, Griefers and any other kind of Galaxy bullies. We organise weekly patrols to protect New pilots around systems targeted by Gankers and Griefers. If you are a New Pilot interested in this, we strongly recommend our PvE and PvP training clubs. There you will improve your combat skills either in wings for 1v1 combat, in these clubs, you can ask, discuss, upload your videos, and meet with your mentors in the field to polish your skills.<br />
            <br />
            <span style="text-decoration: underline;">- AntiXeno -</span><br />
            <br />
            We have a very capable group of AntiXeno Mentors and Experts. In our AntiXeno channel, you will find very useful information and guides.  If you are interested our Mentors can help you to go through the process of engaging Thargoids, from Scouts to Interceptors. Our great Xeno Mentors will often organise Xeno Raids and Boot Camps for New Pilots.<br />
            <br />
            <span style="text-decoration: underline;">- The Independent Defence Agency Faction -</span><br />
            <br />
            We support our in-game faction, this is totally optional, but we find it adds enjoyment to your game by providing purpose and camaraderie as we seek to increase our influence throughout the galaxy.<br />
            <br />
            We aim to protect our home systems, control and expand our borders whilst maintaining healthy economies within our controlled systems. We are a peaceful independent squadron preferring to create alliances with our neighbours but are not frightened to resort to force if required.<br />
            <br />
            We do not have a PowerPlay allegiance but members may choose to participate in PowerPlay activities as they see fit, provided it does not harm our PMF.<br />
            <br />
            <span style="text-decoration: underline;">- Ship Builds and Advice -</span><br />
            <br />
            We have a special dedicated channel for ship builds where you can post your builds and ask our engineering experts how to improve your builds, no matter what kind, either exploration, mining, trading or combat.<br />
            <br />
            <span style="text-decoration: underline;">- Exploration and Raxxla -</span><br />
            <br />
            Our groups of explorers gather their intel about Raxxla and share their trips here. We also organise exploring sightseeing events for those who love exploring.<br />
            <br />
            <span style="text-decoration: underline;">- Tournaments and Events -</span><br />
            <br />
            We run internal PvP Tournaments and Events to improve our combat skills, with and without engineered ships, like Viper Tag or The Eagle Nest. We also do events just for fun, do you have a crazy idea? come and tell us! It's good to get together and have a good laugh. Let's all jump from a cliff together in our SRV's on to a T10's back!<br />
            <br />
            <br />
            <span style="font-size: 20px;">We encourage:<br /></span>
            -To play in Open (Open play keeps the doctors away)<br />
            -Consensual PvP<br />
            -<span style="text-decoration: underline;">Be active invoice when in-game if possible</span><br />
            -All forms of gameplay, Exploration, Trade, Mining, Bounty Hunting, Engineering, PowerPlay, Combat, Wing Missions, PMF Board Missions, System Expansion and Control<br />
            <br />
            <br />
            <span style="font-size: 20px;">What We Provide:<br /></span>
            -Mentoring and help for newer players (and veterans alike!)<br />
            -Organised events from time to time (Eagle Nest, Viper Tag, Viper King, SRV races, etc)<br />
            -Players on PC, Xbox & PS4<br />
            -An understanding that real life comes first and that your time is your own to do as you please<br />
            -Fun, casual gameplay, and a great group of friends<br />
            <br />
            <br />
            <span style="font-size: 20px;">We Do Not Allow:<br /></span>
            -<span style="text-decoration: underline;">Ganking or Griefing by our members.</span> Whilst we allow and encourage consensual PvP, any form of Ganking or Griefing is not allowed and the member will be removed from the private channels and also from the in-game and Inara's Squadron. The member will be considered an Outlaw.<br />
            -<span style="text-decoration: underline;">Combat/Menu Logging</span> (Using any means to exit the game during combat for any reason)<br />
            -<span style="text-decoration: underline;">Cheats</span>, you will be reported to FDev.<br />
            <br />
            <br />
            Notice: We allow Outlaws in our public channels as long as they are respectful, so don't be surprised if you meet some foes in there.<br />
            <br />
            <br />
            <span style="text-decoration: underline;">Before applying to join the Inara Squadron or In-Game Squadron we need to meet you in our Discord: <a href="https://discord.gg/8tkNGXV">Join</a></span><br />
            <br />
            Looking forward to hearing from you Commanders!<br />
            o7
          </div>
          <div class="articlefooter">
            Ps: If you are here looking to repair stations, you are in the wrong place, this is their link: <a href="https://inara.cz/squadron/4242/">Operation IDA</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>
</html>