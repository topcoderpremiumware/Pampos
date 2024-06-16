<?php
namespace App\Gateways;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class BinanceGateway {

    public static function getPriceByDate(Carbon $date)
    {
        return Cache::remember('binance_price_'.$date->format('Y-m-d'), now()->addDay(), function() use ($date) {
            return self::sendRequestDatePrice($date);
        });
    }

    public static function getAllData()
    {
        return Cache::remember('all_binance_price4', now()->addHour(), function() {
            return self::sendRequestAllData();
        });
    }

    private static function sendRequestDatePrice(Carbon $date): ?float
    {
        $baseUrl = "https://api.binance.com";
        $endpoint = "/api/v3/klines";
        $symbol = "BTCUSDT";
        $interval = "1m";
        $startTime = $date->valueOf();
        $url = $baseUrl . $endpoint . "?symbol=" . $symbol . "&interval=" . $interval . "&startTime=" . $startTime . "&limit=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (isset($data[0])) {
            return (float)$data[0][4];
        } else {
            return null;
        }
    }

    private static function sendRequestAllData()
    {
        $baseUrl = "https://api.binance.com";
        $endpoint = "/api/v3/klines";
        $symbol = "BTCUSDT";
        $interval = "1h";
        $startTime = now()->addDays(-1)->valueOf();
        $url = $baseUrl . $endpoint . "?symbol=" . $symbol . "&interval=" . $interval . "&startTime=" . $startTime;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (isset($data[0])) {
            return json_encode($data);
        } else {
            return null;
        }
    }
}
