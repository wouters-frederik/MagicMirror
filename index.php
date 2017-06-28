<?php
date_default_timezone_set("Europe/Amsterdam");
setlocale(LC_ALL, 'nl_NL');
setlocale(LC_TIME, 'nl_NL');

function fetch_data($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, $url);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

$weather_url = "https://services.vrt.be/weather/observations/belgische_streken?accept=application%2fvnd.weather.vrt.be.observations_1.0%2Bjson";
$weather_data = json_decode(fetch_data($weather_url));
//var_dump($weather_data);
foreach($weather_data->observations as $observation) {
  if ($observation->location == 'Centrum') {
    $weather_temp = $observation->temperature;
    $weather_img = $observation->weathertype;
  }
}
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <title>Magic Mirror</title>
  <meta name="description" content="The Magic Mirror">
  <meta http-equiv="refresh" content="1800" /> <!-- Updates the whole page every 30 minutes (each 1800 second) -->
  <link rel="stylesheet" href="style.css">
  <link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
  <script language="JavaScript"> <!-- Getting the current date and time and updates them every second -->
    setInterval(function() {
      var currentTime = new Date ( );
      var currentHours = currentTime.getHours ( );
      var currentMinutes = currentTime.getMinutes ( );
      var currentMinutesleadingzero = currentMinutes > 9 ? currentMinutes : '0' + currentMinutes; // If the number is 9 or below we add a 0 before the number.
      var currentDate = currentTime.getDate ( );

      var weekday = new Array(7);
      weekday[0] = "Zondag";
      weekday[1] = "Maandag";
      weekday[2] = "Dinsdag";
      weekday[3] = "Woensdag";
      weekday[4] = "Donderdag";
      weekday[5] = "Vrijdag";
      weekday[6] = "Zaterdag";
      var currentDay = weekday[currentTime.getDay()];

      var actualmonth = new Array(12);
      actualmonth[0] = "Januari";
      actualmonth[1] = "Februari";
      actualmonth[2] = "Maart";
      actualmonth[3] = "April";
      actualmonth[4] = "Mei";
      actualmonth[5] = "Juni";
      actualmonth[6] = "Juli";
      actualmonth[7] = "Augustus";
      actualmonth[8] = "September";
      actualmonth[9] = "Oktober";
      actualmonth[10] = "November";
      actualmonth[11] = "December";
      var currentMonth = actualmonth[currentTime.getMonth ()];

      var currentTimeString = "<h1>" + currentHours + ":" + currentMinutesleadingzero + "</h1><h2>" + currentDay + " " + currentDate + " " + currentMonth + "</h2>";
      document.getElementById("clock").innerHTML = currentTimeString;
    }, 1000);
  </script>
</head>
<body>
<audio controls>
  <source src="http://mp3.streampower.be/ra2vlb-high.mp3" type="audio/mpeg">
</audio>

<div id="wrapper">
  <div id="upper-left">
    <div id="clock"></div> <!-- Including the date/time-script -->
    <br>
    <br>
    <hr>
    <br>
    <div id="weather">
      <h3>
        <?php echo $weather_temp; ?>&#8451;
        <i class="weather-icon helder_wolk night"></i>
      </h3>
    </div>
  </div>

  <div id="upper-right">
    <h2>...</h2>
    <?php // Code for getting the JSON FEED
    $url = 'https://admin.radio1.be/api/1.4/articles';
    $result = fetch_data($url);

    $data = json_decode($result);
    $feed = [];

    //var_dump($data->articles);
    foreach($data->articles as $article) {
            $item = array (
              'title' => $article->title,
              'desc' => $article->intro,
              'date' => $article->created,
            );
            array_push($feed, $item);
    }
    //    $rss = new DOMDocument();
    //    $rss->load('http://feeds.idg.se/idg/vzzs'); // Specify the address to the feed
    //    $feed = array();
    //    foreach ($rss->getElementsByTagName('item') as $node) {
    //      $item = array (
    //        'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
    //        'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
    //        'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
    //      );
    //      array_push($feed, $item);
    //    }

    $limit = 3; // Number of posts to be displayed
    for($x=0;$x<$limit;$x++) {
      $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
      $description = $feed[$x]['desc'];
      $date = date('j F', strtotime($feed[$x]['date']));
      echo '<h2 class="smaller">'.$title.'</h2>';
      echo '<p class="date">'.$date.'</p>';
      echo '<p>'.strip_tags($description, '<p><b>').'</p><h2>...</h2>';
    }
    ?>
    <p>radio2.be</p>
  </div>

  <div id="bottom">
    <h3>
      <?php // Depending on the hour of the day a different message is displayed.
      $now = date('H');
      if (($now > 06) and ($now < 10)) echo 'Goeiemorgen!';
      else if (($now >= 10) and ($now < 12)) echo 'Een goeie dag gewenst!';
      else if (($now >= 12) and ($now < 14)) echo 'Etenstijd!';
      else if (($now >= 14) and ($now < 17)) echo 'Kijk eens aan!';
      else if (($now >= 17) and ($now < 20)) echo 'Bijna etenstijd?';
      else if (($now >= 20) and ($now < 22)) echo 'Goeie avond gewenst!';
      else if (($now >= 22) and ($now < 23)) echo 'Slaap zacht, tot morgen!';
      else if (($now >= 00) and ($now < 06)) echo 'Shh, slaap maar...';
      ?>
    </h3>
  </div>

  <iframe src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;showTz=0&amp;mode=AGENDA&amp;height=600&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=wouters.f%40gmail.com&amp;color=%23333333&amp;ctz=Europe%2FBrussels" style="border-width:0" width="100%" height="600" frameborder="0" scrolling="no"></iframe>
</div>
</body>
</html>