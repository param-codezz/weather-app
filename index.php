<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Document</title>
    </head>
    <?php 
        $searchCity = empty($_GET['city']) ? 'Ahmedabad' : $_GET['city'];
        $searchCity = str_replace(' ', '%20', $searchCity);
        $apiKey = "1300fc69a0b54983a0d130829242102";
        try {
            $json = @file_get_contents("http://api.weatherapi.com/v1/forecast.json?key={$apiKey}&q={$searchCity}&days=6&aqi=yes&alerts=yes");
        } catch (Exception $e) {
            echo 'Location Not found';
        }
        if ($json === false){
            die();
        } else{
            $data = json_decode($json);
        }
        
        function generateFutureWeather($interval){
            global $data;
            return array($data -> forecast -> forecastday[$interval] -> day -> maxtemp_c, $data -> forecast -> forecastday[$interval] -> day -> mintemp_c, $data -> forecast -> forecastday[$interval] -> day -> condition -> code);
        }
        
        function isDay() {
            global $data;
            $day = $data -> current -> is_day;
            return $day ? 'Day' : 'Night';
        }

        function generateWeatherString($weatherNow) {
            global $data;
            // $weatherNow = $data -> current -> condition -> code;
            $sunnyCodes = array(1000);
            $cloudyCodes = array(1003, 1006, 1009);
            $windyCodes = array(1114);
            $rainyCodes = array(1063, 1069, 1072, 1150, 1153, 1168, 1171, 1180, 1183, 1186, 1189, 1192, 1195, 1198, 1201, 1204, 1207, 1240, 1243, 1246, 1249, 1252);
            $snowyCodes = array(1210, 1213, 1216, 1219, 1222, 1225, 1237, 1255, 1258, 1261, 1264);
            $thunderCodes = array(1273, 1276, 1279, 1282, 1087, 1114, 1117);
            $foggyCodes = array(1135, 1147, 1030);
            if (in_array($weatherNow, $sunnyCodes)) {
                return 'sunny';
            } else if(in_array($weatherNow, $cloudyCodes)) {
                return 'cloudy';
            } else if(in_array($weatherNow, $windyCodes)) {
                return 'windy';
            } else if(in_array($weatherNow, $rainyCodes)) {
                return 'rainy';
            } else if(in_array($weatherNow, $snowyCodes)) {
                return 'snowy';
            } else if(in_array($weatherNow, $thunderCodes)) {
                return 'thunderstorm';
            } else if(in_array($weatherNow, $foggyCodes)) {
                return 'foggy';
            }     
        }

        function weatherIcons($weather, $color, $size) {
            if ($weather == "sunnyDay"){
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                light_mode
                </span>
                ";
            } elseif ($weather == "sunnyNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    clear_night
                </span>
                ";
            } elseif ($weather == "cloudyDay") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    partly_cloudy_day
                </span>
                ";
            } elseif ($weather == "cloudyNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    partly_cloudy_night
                </span>
                ";
            } elseif ($weather == "foggyDay") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    foggy
                </span>
                ";
            } elseif ($weather == "foggyNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    foggy
                </span>
                ";
            } elseif ($weather == "rainyDay") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    rainy
                </span>
                ";
            } elseif ($weather == "rainyNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    rainy
                </span>
                ";
            } elseif ($weather == "thunderstormDay") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    thunderstorm
                </span>
                ";
            } elseif ($weather == "thunderstormNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    thunderstorm
                </span>
                ";
            } elseif ($weather == "snowyDay") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    ac_unit
                </span>
                ";
            } elseif ($weather == "snowyNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    ac_unit
                </span>
                ";
            } elseif ($weather == "windyDay") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    air
                </span>
                ";
            } elseif ($weather == "windyNight") {
                return "
                <span class='material-symbols-outlined {$color}' style='font-size: {$size}'>
                    air
                </span>
                ";
            }
        }
        if (! isset($json)) {
            $message = true;
            header("Location: localhost/project/index.php?city=Ahmedabad");
        } else {
            $todayDate = strtotime($data -> location -> localtime);
            $dateCpy = clone date_create($todayDate);
            $time = strftime("%A, %d %b'%y", $todayDate);
            $aqi = $data -> current -> air_quality -> {'gb-defra-index'};
            $uvIndex = $data -> current -> uv;
            $visibility = $data -> current -> vis_km;
            $snowChance = $data -> forecast -> forecastday[0] -> day -> daily_chance_of_snow;
            $snowAmount = $data -> forecast -> forecastday[0] -> day -> totalsnow_cm;
            $rainChance = $data -> forecast -> forecastday[0] -> day -> daily_chance_of_rain;
            $rainAmount = $data -> forecast -> forecastday[0] -> day -> totalprecip_mm;
            $pressure = $data -> current -> pressure_mb;
            $humidity = $data -> current -> humidity;
            $windDirection = $data -> current -> wind_dir;
            $windSpeed = $data -> current -> wind_kph;
            $feelsLikeTemp = $data -> current -> feelslike_c;
            $minTemp = $data -> forecast -> forecastday[0] -> day -> mintemp_c;
            $maxTemp = $data -> forecast -> forecastday[0] -> day -> maxtemp_c;
            $searches = array('Ahmedabad', 'Kolkata', 'Moscow', 'Tokyo');
            $weather = generateWeatherString($data -> current -> condition -> code) . isDay();
            global $image_src;
            $image_src = $weather . '.jpg';
            $currentTemp = round($data -> current -> temp_c);
            $currentCity = $data -> location -> name;
            $currentTime = substr($data -> location -> localtime, 10);
            $nextFiveDays = array();
            $nextFiveDaysMax = array();
            $nextFiveDaysMin = array();
            $givenDate = new DateTime(substr($data->location->localtime, 0, 10));
            $nextFiveDaysWeather = array();
            for ($i = 1; $i <= 5; $i++) {
                $nextDay = clone $givenDate;
                $nextDay->add(new DateInterval("P{$i}D"));
                $nextFiveDays[] = $nextDay->format('d-m');
                array_push($nextFiveDaysMax, round(generateFutureWeather($i)[0]));
                array_push($nextFiveDaysMin, round(generateFutureWeather($i)[1]));
                array_push($nextFiveDaysWeather, generateWeatherString(generateFutureWeather($i)[2]) . 'Day');
            }
            $formattedDate = $time;
        }
        // echo $image_src;
    ?>
<body class="<?php echo"$weather" ?>">
    <div class="flex justify-between">
        <div class="flex">
                <div class="container overflow-y-scroll overflow-x-visible" onscroll="revealOnScroll">
                        <!-- This will show temperature -->
                    <div class="fade-element flex items-center translate-y-72">
                        <div class="text-acrylicWhite px-4">
                            <div class="text-8xl font-medium">
                                <?php echo"$currentTemp" ?>°C
                            </div>
                        </div>
                        <div class="flex flex-col px-4">
                            <div class="text-3xl font-medium">
                                <p class="text-acrylicWhite">
                                <?php echo"$currentCity" ?>
                            </p>
                        </div>
                        <div class="text-xl">
                            <p class="text-acrylicWhite">
                                <?php echo"$formattedDate" ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php 
                        // weatherIcons($weather, 'text-acrylicWhite', '5rem')
                        $func = 'weatherIcons';
                        echo"{$func($weather, 'text-acrylicWhite', '5rem')}";
                        ?>
                    </div>
                </div>
                <!-- This will show forecast of next 5 days -->
                <div class="reveal-element w-full translate-y-96">
                    <div class="mx-4 mt-3 w-[95%] bg-acrylicWhite h-1 rounded-full"></div>
                    <div class="w-full items-center mb-10">

                    
                    <h3 class="font-medium text-acrylicWhite text-2xl text-center my-2">5 Day Forecast</h3>
                    <div class="flex justify-evenly">
                    <?php
                    $func = 'weatherIcons';
                    if ($weather == 'windyDay' || $weather == 'sunnyDay' || $weather == 'sunnyNight') {
                        for ($i=0; $i < 5; $i++) { 
                            echo "
                            <div class='bg-acrylicWeatherCardVariant flex flex-col w-1/5 items-center justify-between backdrop-blur-sm py-2 rounded-md mx-3'>
                            <div class='py-1 px-5'>
                            <h4 class='text-tx-c3 font-bold text-sm'>{$nextFiveDays[$i]}</h4>
                            </div>
                            <div class='flex flex-col py-6 px-5 items-center'>
                            <h5 class='text-3xl font-normal text-white'>{$nextFiveDaysMax[$i]}°</h5>
                            <div class='bg-tx-8b w-full h-px rounded-full my-1'></div>
                                <h5 class='text-3xl font-normal text-tx-a3'>{$nextFiveDaysMin[$i]}°</h5>
                            </div>
                            <div class='w-full flex items-start py-0.5 px-2'>
                            {$func($nextFiveDaysWeather[$i], 'text-tx-8b','1.5rem')}
                            </div>
                            </div>
                            ";
                        }
                    } 
                    else if ($weather == 'snowyNight') {
                        for ($i=0; $i < 5; $i++) { 
                            echo "
                            <div class='bg-acrylicWeatherCardVariant2 flex flex-col w-1/5 items-center justify-between backdrop-blur-sm py-2 rounded-md mx-3'>
                            <div class='py-1 px-5'>
                            <h4 class='text-tx-c3 font-bold text-sm'>{$nextFiveDays[$i]}</h4>
                            </div>
                            <div class='flex flex-col py-6 px-5 items-center'>
                            <h5 class='text-3xl font-normal text-white'>{$nextFiveDaysMax[$i]}°</h5>
                            <div class='bg-tx-8b w-full h-px rounded-full my-1'></div>
                                <h5 class='text-3xl font-normal text-tx-a3'>{$nextFiveDaysMin[$i]}°</h5>
                            </div>
                            <div class='w-full flex items-start py-0.5 px-2'>
                            {$func($nextFiveDaysWeather[$i], 'text-tx-8b','1.5rem')}
                            </div>
                            </div>
                            ";
                        }
                    } else {
                        for ($i=0; $i < 5; $i++) { 
                            echo "
                            <div class='bg-acrylicWeatherCard flex flex-col w-1/5 items-center justify-between backdrop-blur-sm py-2 rounded-md mx-3 min-w-24'>
                            <div class='py-1 px-5'>
                            <h4 class='text-tx-c3 font-bold text-sm'>{$nextFiveDays[$i]}</h4>
                            </div>
                            <div class='flex flex-col py-6 px-5 items-center'>
                            <h5 class='text-3xl font-normal text-white'>{$nextFiveDaysMax[$i]}°</h5>
                            <div class='bg-tx-8b w-full h-px rounded-full my-1'></div>
                                <h5 class='text-3xl font-normal text-tx-a3'>{$nextFiveDaysMin[$i]}°</h5>
                            </div>
                            <div class='w-full flex items-start py-0.5 px-2'>
                            {$func($nextFiveDaysWeather[$i], 'text-tx-8b','1.5rem')}
                            </div>
                            </div>
                            ";
                        }
                    }
                    ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="bg-acrylicCard backdrop-blur-sm overflow-y-scroll h-[101vh] w-1/4 flex flex-col">
            <form action="index.php">
                <div>
                    <div class="flex h-[55px] justify-between">
                        <div class="mx-6 flex items-center border-b border-tx-ba">
                            <input type="text" name="city" id="city" placeholder="Search Location" class="placeholder:text-tx-b3 bg-transparent w-64 text-white">
                        </div>
                        <button class="bg-tx-2e w-[55px] flex justify-center items-center">
                            <span class="material-symbols-outlined text-white">
                                search
                            </span>
                        </button>
                    </div>
                    <div>
                        <?php 
                        foreach ($searches as $item) {
                            echo "<div class='text-tx-bb text-xl my-3 mx-9 font-light'><a href='?city={$item}'>$item</a></div>";
                        }
                        ?>
                </div>
                </div>
            </form>
            <section></section>
            <div>
                <h2 class="h2">Weather Details</h2>
                <div class="flex flex-col my-2">
                    <div class="weather-card">
                        <h3 class="weather-head">Max Today</h3>
                        <p class="weather-info"><?php echo "{$maxTemp}°"?></p>
                    </div>
                    <div class="weather-card">
                        <h3 class="weather-head">Min Today</h3>
                        <p class="weather-info"><?php echo"{$minTemp}°"?></p>
                    </div>
                    <div class="weather-card">
                        <h3 class="weather-head">Feels Like™</h3>
                        <p class="weather-info"><?php echo"{$feelsLikeTemp}°"?></p>
                    </div>
                    <div class="weather-card">
                        <h3 class="weather-head">Wind</h3>
                        <p class="weather-info"><?php echo"{$windSpeed}kmph"?></p>
                    </div>
                    <div class="weather-card">
                        <h3 class="weather-head">Wind Direction</h3>
                        <p class="weather-info"><?php echo"$windDirection"?></p>
                    </div>
                </div>
                <div>
                    <h2 class="h2">Rain</h2>
                    <div class="flex flex-col  my-2">
                        <div class="weather-card">
                            <h3 class="weather-head">Humidity</h3>
                            <p class="weather-info"><?php echo"{$humidity}%"?></p>
                        </div>
                        <div class="weather-card">
                            <h3 class="weather-head">Pressure</h3>
                            <p class="weather-info"><?php echo"{$pressure} mbar"?></p>
                        </div>
                        <div class="weather-card">
                            <h3 class="weather-head">Rain</h3>
                            <p class="weather-info"><?php echo"{$rainAmount} mm"?></p>
                        </div>
                        <div class="weather-card">
                            <h3 class="weather-head">Chance of Rain</h3>
                            <p class="weather-info"><?php echo"{$rainChance}%"?></p>
                        </div>
                    </div>
                    <div>
                        <h2 class="h2">Snow</h2>
                        <div class="flex flex-col my-2">
                            <div class="weather-card">
                                <h3 class="weather-head">Snow</h3>
                                <p class="weather-info"><?php echo"{$snowAmount} cm"?></p>
                            </div>
                            <div class="weather-card">
                                <h3 class="weather-head">Chance of Snow</h3>
                                <p class="weather-info"><?php echo"{$snowChance}%"?></p>
                            </div>
                        </div>
                    </div>
                    <section></section>
                    <div class="pb-20">
                        <h2 class="h2">AQI and Visibility</h2>
                        <div>

                        
                        <div class="weather-card">
                            <h3 class="weather-head">Visibility</h3>
                            <p class="weather-info"><?php echo"{$visibility} km"?></p>
                        </div>
                        <div>
                            <div>
                                <div class="weather-card">
                                    <h3 class="weather-head">UV Index</h3>
                                    <p class="weather-info"><?php echo"{$uvIndex}"?></p>
                                </div>
                                <div class="w-auto mx-9 bg-indicator rounded-full h-2.5">
                                    <div class='h-2.5 rounded-full <?php 
                                    if ($uvIndex <= 3) {
                                        echo "bg-custom-green";
                                    } else if ($uvIndex > 3 && $uvIndex <= 6) {
                                        echo "bg-custom-yellow";
                                    } else if ($uvIndex > 6 && $uvIndex <= 9) {
                                        echo "bg-custom-orange";
                                    } else if ($uvIndex > 9){
                                        echo "bg-custom-red";
                                    } 
                                    ?>'
                                style='width: <?php 
                                if ($uvIndex <= 3) {
                                    echo "15%";
                                } else if ($uvIndex > 3 && $uvIndex <= 6) {
                                    echo "45%";
                                } else if ($uvIndex > 6 && $uvIndex <= 9) {
                                    echo "75%";
                                } else if ($uvIndex > 9) {
                                    echo "100%";
                                } 
                                ?> ;'
                                >
                                    </div>
                                </div>
                            </div>
                            <div>

                            
                            <div class="weather-card">
                                <h3 class="weather-head">AQI</h3>
                                <p class="weather-info"><?php echo"{$aqi}"?></p>
                            </div>
                            <div class="w-auto mx-9 bg-indicator rounded-full h-2.5">
                                <div class='h-2.5 rounded-full <?php 
                                    if ($aqi <= 3) {
                                        echo "bg-custom-green";
                                    } else if ($aqi > 3 && $aqi <= 6) {
                                        echo "bg-custom-orange";
                                    } else if ($aqi > 6 && $aqi <= 9) {
                                        echo "bg-custom-red";
                                    } else if ($aqi > 9){
                                        echo "bg-custom-purple";
                                    }
                                ?>'
                                style='width: <?php 
                                if ($aqi <= 3) {
                                    echo "15%";
                                } else if ($aqi > 3 && $aqi <= 6) {
                                    echo "50%";
                                } else if ($aqi > 6 && $aqi <= 9) {
                                    echo "65%";
                                } else if ($aqi > 9){
                                    echo "100%";
                                } 
                                ?> ;'
                                ></div>
                            </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
    <script>
        function closeMessage(button) {
            var messageDiv = $(button).closest('div');
            messageDiv.slideUp();
        }
        document.addEventListener('DOMContentLoaded', function () {
            const revealElement = document.querySelector('.reveal-element');
            const fadeElement = document.querySelector('.fade-element');
            const container = document.querySelector('.container');
            let lastScrollTop = 0;
            let scrollTimeout;

            function revealOnScroll() {
                const containerTop = container.getBoundingClientRect().top;
                const st = window.pageYOffset || document.documentElement.scrollTop;

                if (st > lastScrollTop) {
                    // Scrolling down
                    if (containerTop < window.innerHeight * 0.75) {
                        revealElement.style.opacity = '1';
                        revealElement.style.transform = 'translateY(0)';
                        fadeElement.style.opacity = '0.4';
                        fadeElement.style.transform = 'translateY(0rem)'
                    }
                } else {
                    // Scrolling up
                    revealElement.style.opacity = '0';
                    revealElement.style.transform = 'translateY(50rem)';
                    fadeElement.style.transform = 'translateY(18rem)'
                }

                lastScrollTop = st <= 0 ? 0 : st; // For mobile or negative scrolling

                // Clear timeout to prevent premature fade restoration
                clearTimeout(scrollTimeout);
                // Set timeout to restore fade after a brief pause in scrolling
                scrollTimeout = setTimeout(function () {
                    fadeElement.style.opacity = '1';
                }, 150); // Adjust the duration as needed
            }

            window.addEventListener('scroll', revealOnScroll);
        });
    </script>
</body>
</html>