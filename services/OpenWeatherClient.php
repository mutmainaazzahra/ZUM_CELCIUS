<?php
class OpenWeatherClient {
    private $apiKey;
    private $cacheDir = '../cache/';

    public function __construct() {
        $this->apiKey = $_ENV['OPENWEATHER_API_KEY'];
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function searchCity($city) {
        $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $this->apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($res, true);
        
        if ($data && isset($data['cod']) && $data['cod'] == 200) {
            return $data; 
        }
        return false;
    }

    public function getForecast($lat, $lon) {
        $cacheFile = $this->cacheDir . "weather_" . md5("$lat-$lon") . ".json";
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        $url = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=id&appid=" . $this->apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($res, true);
        
        if ($data && isset($data['cod']) && $data['cod'] == "200") {
            file_put_contents($cacheFile, json_encode($data));
        }

        return $data;
    }
}