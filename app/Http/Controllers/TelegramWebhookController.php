<?php

namespace App\Http\Controllers;

use App\Gateways\BinanceGateway;
use App\Gateways\ChatGPTGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TelegramNotification;

class TelegramWebhookController extends Controller
{
    /*** @param Request $request */
    public function setWebhook(Request $request): void
    {
        $url = "https://api.telegram.org/bot" . config('services.telegram-bot-api.token') . "/setWebhook";
        $options = [
            'url' => config('services.telegram-bot-api.webhook')
        ];
        $response = file_get_contents($url . '?' . http_build_query($options));
        var_dump($response);
    }
    /*** @param Request $request */
    public function webhook(Request $request): void
    {
        $all_request = $request->all();
        Log::debug('telegram_webhook', $all_request);
        if (!empty($all_request['message'])) {
            $from = $all_request['message']['from'];
            if (!empty($all_request['message']['text'])) {
                $text = $all_request['message']['text'];
                if ($this->has_command($text, '/start')) {
                    Notification::route('telegram', $from['id'])->notify(
                        new TelegramNotification([
                            'message' => "To get started, please choose one of the following options:",
                            'buttonCallbacks' => [
                                [
                                    'label' => 'Potential profits for 1 day',
                                    'data' => '/profit_1_day'
                                ],
                                [
                                    'label' => 'Potential profits for 1 week',
                                    'data' => '/profit_1_week'
                                ],
                                [
                                    'label' => 'Potential profits for Current month',
                                    'data' => '/profit_current_month'
                                ],
                                [
                                    'label' => 'Potential profits for Last month',
                                    'data' => '/profit_last_month'
                                ],
                                [
                                    'label' => 'Help & Support',
                                    'data' => '/support'
                                ]
                            ]
                        ])
                    );
                }else{
                    $answer = ChatGPTGateway::answer($from['id'],$text);
                    Notification::route('telegram', $from['id'])->notify(
                        new TelegramNotification([
                            'message' => $answer
                        ])
                    );
                }
            }
        } elseif (!empty($all_request['callback_query'])) {
            $from = $all_request['callback_query']['from'];
            if (!empty($all_request['callback_query']['data'])) {
                $callback = $all_request['callback_query']['data'];
                if ($this->has_command($callback, '/profit_1_day')) {
                    $this->profit_1_day($callback, $from);
                } elseif ($this->has_command($callback, '/profit_1_week')) {
                    $this->profit_1_week($callback, $from);
                }elseif ($this->has_command($callback, '/profit_current_month')) {
                    $this->profit_current_month($callback, $from);
                }elseif ($this->has_command($callback, '/profit_last_month')) {
                    $this->profit_last_month($callback, $from);
                }elseif ($this->has_command($callback, '/support')) {
                    $this->support($callback, $from);
                }
            }
        }
    }

    private function has_command($string, $command): bool
    {
        return str_starts_with($string, $command);
    }

    private function profit_1_day($callback, $from): void
    {
        $price_now = BinanceGateway::getPriceByDate(Carbon::now()->startOfDay());
        $price_yesterday = BinanceGateway::getPriceByDate(Carbon::yesterday()->startOfDay());
        $sum = number_format(100 / $price_yesterday * $price_now,2);
        Notification::route('telegram', $from['id'])->notify(
            new TelegramNotification([
                'message' => "Great choice! Here's what our AI found:\nIf you bought $100 worth of Bitcoin yesterday and sold it today, you would get about $".$sum.".\nIf you need more information follow the link https://adviser.vasilkoff.info/"
            ])
        );
        $this->from_the_begining($from);
    }

    private function profit_1_week($callback, $from): void
    {
        $price_now = BinanceGateway::getPriceByDate(Carbon::now()->startOfDay());
        $price_prev = BinanceGateway::getPriceByDate(Carbon::now()->subDays(7)->startOfDay());
        $sum = number_format(100 / $price_prev * $price_now,2);
        Notification::route('telegram', $from['id'])->notify(
            new TelegramNotification([
                'message' => "Great choice! Here's what our AI found:\nIf you bought $100 worth of Bitcoin a week ago and sold it today, you would get about $".$sum.".\nIf you need more information follow the link https://adviser.vasilkoff.info/"
            ])
        );
        $this->from_the_begining($from);
    }

    private function profit_current_month($callback, $from): void
    {
        $price_now = BinanceGateway::getPriceByDate(Carbon::now()->startOfDay());
        $price_prev = BinanceGateway::getPriceByDate(Carbon::now()->subMonths(1)->startOfDay());
        $sum = number_format(100 / $price_prev * $price_now,2);
        Notification::route('telegram', $from['id'])->notify(
            new TelegramNotification([
                'message' => "Great choice! Here's what our AI found:\nIf you bought $100 worth of Bitcoin a month ago and sold it today, you would get about $".$sum.".\nIf you need more information follow the link https://adviser.vasilkoff.info/"
            ])
        );
        $this->from_the_begining($from);
    }

    private function profit_last_month($callback, $from): void
    {
        $price_now = BinanceGateway::getPriceByDate(Carbon::now()->startOfDay());
        $price_prev = BinanceGateway::getPriceByDate(Carbon::now()->subMonths(2)->startOfDay());
        $sum = number_format(100 / $price_prev * $price_now,2);
        Notification::route('telegram', $from['id'])->notify(
            new TelegramNotification([
                'message' => "Great choice! Here's what our AI found:\nIf you bought $100 worth of Bitcoin two months ago and sold it today, you would get about $".$sum.".\nIf you need more information follow the link https://adviser.vasilkoff.info/"
            ])
        );
        $this->from_the_begining($from);
    }

    private function support($callback, $from): void
    {
        Notification::route('telegram', $from['id'])->notify(
            new TelegramNotification([
                'message' => "Great choice! Visit our site https://adviser.vasilkoff.info/"
            ])
        );
        $this->from_the_begining($from);
    }

    private function from_the_begining($from): void
    {
        Notification::route('telegram', $from['id'])->notify(
            new TelegramNotification([
                'message' => "Would you like to choose another option?",
                'buttonCallbacks' => [
                    [
                        'label' => 'Potential profits for 1 day',
                        'data' => '/profit_1_day'
                    ],
                    [
                        'label' => 'Potential profits for 1 week',
                        'data' => '/profit_1_week'
                    ],
                    [
                        'label' => 'Potential profits for Current month',
                        'data' => '/profit_current_month'
                    ],
                    [
                        'label' => 'Potential profits for Last month',
                        'data' => '/profit_last_month'
                    ],
                    [
                        'label' => 'Help & Support',
                        'data' => '/support'
                    ]
                ]
            ])
        );
    }
}
