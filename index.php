<?php
	// написано на коленке UR3ZLT aka thescs для сервера ХарьковСтрой проект metrostroi
	// структура получаемых данных в чистом виде: id, ip, country, city, lat, lon
	// {"id":"CommunitySteamID","ip":"айпи клиента","country":"страна","city":"название города","lat":"широта","lon":"долгота"},
	// строку в JSON мы получаем от скрипта, который работает с базой данных
	// декодируем, инициализируем переменные для скриптов

	$rows = file_get_contents("http://localhost/gmod-screens/stat/data.php");
	$rows = json_decode($rows, true);
	$points = "";
	$citys = "";
	$ccount;
	$countrys;
	$cocount;
	// составление строк для javascript
	// точки для карты
	foreach ($rows as $key => $value) {
		$points .= "new google.maps.LatLng($value[lat], $value[lon]),\n";
	}
	// подсчет одинковых вхожений в массиве и составление строк - города
	foreach (array_count_values(array_column($rows, 'city')) as $key => $value) {
		if (empty($key)) continue;
		$citys .= "'$key',";
		$ccount .= "$value,";
	}
	// то же, страны
	foreach (array_count_values(array_column($rows, 'country')) as $key => $value) {
		if (empty($key)) continue;
		$countrys .= "'$key',";
		$cocount .= "$value,";
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>KharkovStroi Statistic</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=бебебебе&callback=initMap&libraries=visualization&v=weekly"
      defer
    ></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <link rel="stylesheet" type="text/css" href="./style.css" />
    <script src="./index.js"></script>
    <script language="javascript">
		// всовуем точки на карте в функцию, она потом будет подтянута конструктором гугла
		function getPoints() {
			return [
			<?php echo substr($points, 0, -2); ?>
			  ];
		}
	</script>
  </head>
  <body>
    <div id="floating-panel"> <!-- менюшка -->
	  <canvas id="city"></canvas>
	  <canvas id="countries"></canvas>
	  <button onclick="togglePanel();">Спрятать панель</button>
    </div>
	<div id="showfloat">
		<button onclick="togglePanel();">Показать графики</button>
		<button onclick="alert('пока не работает');">Информация по SteamID</button>
		<button onclick="alert('ой, ваш IP не похож на требуемый, доступа нет');">админ-панель</button>
		<!-- ы -->
	</div>
    <div id="map"></div>
  </body>
      <script language="javascript">
	  // инициаилизируем чарты, скармливаем им готовые данные
		var cityctx = document.getElementById('city').getContext('2d');
		var cityChart = new Chart(cityctx, {
			type: 'bar',
			data: {
				labels: [<?php echo substr($citys, 0, -1); ?>],
				datasets: [{
					label: 'Города',
					backgroundColor: 'rgb(51, 51, 255)',
					borderColor: 'rgb(51, 51, 255)',
					data: [<?php echo substr($ccount, 0, -1); ?>]
				}]
			},
			options: {}
		});
		var countryctx = document.getElementById('countries').getContext('2d');
		var cityChart = new Chart(countryctx, {
			type: 'bar',
			data: {
				labels: [<?php echo substr($countrys, 0, -1); ?>],
				datasets: [{
					label: 'Страны',
					backgroundColor: 'rgb(0, 153, 153)',
					borderColor: 'rgb(0, 153, 153)',
					data: [<?php echo substr($cocount, 0, -1); ?>]
				}]
			},
			options: {}
		});
		// пряталка контейнера с канвасами
		function togglePanel() {
		var x = document.getElementById("floating-panel");
		if (x.style.display === "none") {
			x.style.display = "block";
		} else {
			x.style.display = "none";
		}
		}
	</script>
</html>