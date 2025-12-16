<?php
class AnalyticsService
{
    public static function getActivityRecommendation($weatherId, $temp)
    {
        $result = [
            'type' => 'OUTDOOR',
            'message' => 'Waktu yang tepat untuk beraktivitas di luar ruangan! Seperti bersepeda atau berjalan-jalan.',
            'icon' => 'bi-bicycle',
            'color' => 'success',
            'bg' => '#d1e7dd'
        ];

        // 2xx (Petir), 3xx (Gerimis), 5xx (Hujan)
        if ($weatherId >= 200 && $weatherId < 600) {
            $result = [
                'type' => 'INDOOR',
                'message' => 'Lebih baik beraktivitas di dalam ruangan! Seperti membaca buku atau menonton film favorit.',
                'icon' => 'bi-house-heart-fill',
                'color' => 'primary',
                'bg' => '#cfe2ff'
            ];
        }
        // 6xx (Salju) atau Suhu Dingin Ekstrim (< 15C)
        else if (($weatherId >= 600 && $weatherId < 700) || $temp < 15) {
            $result = [
                'type' => 'INDOOR',
                'message' => 'Suhu dingin. Jaga kehangatan, aktivitas indoor lebih disarankan sambil menikmati minuman hangat.',
                'icon' => 'bi-cup-hot-fill',
                'color' => 'info',
                'bg' => '#cff4fc'
            ];
        }
        // 7xx (Kabut/Polusi/Badai Pasir)
        else if ($weatherId >= 700 && $weatherId < 800) {
            $result = [
                'type' => 'HATI-HATI',
                'message' => 'Ada kabut/polusi. Wajib pakai masker jika keluar dan hindari perjalanan jauh.',
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => 'warning',
                'bg' => '#fff3cd'
            ];
        }
        // 800 (Cerah) tapi PANAS EKSTRIM (> 33C)
        else if ($temp > 33) {
            $result = [
                'type' => 'INDOOR / TEDUH',
                'message' => 'Suhu di atas 33°C. Hindari paparan sinar matahari langsung dan jaga hidrasi tubuh. Jangan lupa minum air putih yang cukup.',
                'icon' => 'bi-thermometer-sun',
                'color' => 'danger',
                'bg' => '#f8d7da'
            ];
        }

        return $result;
    }


    public static function prepareChartData($forecastList)
    {
        $labels = [];
        $temps = [];

        foreach ($forecastList as $item) {
            if (strpos($item['dt_txt'], '12:00:00') !== false) {
                $labels[] = date('D, d', strtotime($item['dt_txt']));
                $temps[] = $item['main']['temp'];
            }
        }

        return [
            'labels' => array_slice($labels, 0, 5),
            'data' => array_slice($temps, 0, 5)
        ];
    }
}
