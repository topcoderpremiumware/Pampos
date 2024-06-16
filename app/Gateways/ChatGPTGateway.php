<?php

namespace App\Gateways;


use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use LogicException;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\ClientException;
use Tectalic\OpenAi\Manager;
use Tectalic\OpenAi\Models\ChatCompletions\CreateResponse;
use Tectalic\OpenAi\Models\ChatCompletions\CreateRequest as ChatCreateRequest;

class ChatGPTGateway
{
    static function answer($id,$text): string|bool
    {
        $auth = new Authentication(env('CHAT_GPT_API'));
        $httpClient = new \GuzzleHttp\Client();

        try{
            $client = Manager::access();
        }catch (LogicException $e) {
            $client = Manager::build($httpClient, $auth);
        }

            /** @var CreateResponse $response */
        $request = $client->chatCompletions()->create(
            new ChatCreateRequest([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Assistant is an intelligent chatbot designed to help users answer their bitcoin related questions. You know the price of bitcoin was: today = '.BinanceGateway::getPriceByDate(Carbon::now()->startOfDay()).', yesterday = '.BinanceGateway::getPriceByDate(Carbon::yesterday()->startOfDay()).', week ago = '.BinanceGateway::getPriceByDate(Carbon::now()->subDays(7)->startOfDay()).', one month ago = '.BinanceGateway::getPriceByDate(Carbon::now()->subMonths(1)->startOfDay()).', two months ago = '.BinanceGateway::getPriceByDate(Carbon::now()->subMonths(2)->startOfDay()).'. You cannot predict the future.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'User id '.$id.' wrote: '.$text.'. Give him a short answer.'
                    ],
                ],
            ])
        );

        try {
            $response = $request->toModel();
            return $response->choices[0]->message->content;
        } catch (ClientException $e) {
            Log::info('ChatGPTGateway::answer has error {error}', ['error' => $e->getMessage()]);
            return false;
        }
    }

    static function story($text): string|bool
    {
        $auth = new Authentication(env('CHAT_GPT_API'));
        $httpClient = new \GuzzleHttp\Client();

        try{
            $client = Manager::access();
        }catch (LogicException $e) {
            $client = Manager::build($httpClient, $auth);
        }

        /** @var CreateResponse $response */
        $request = $client->chatCompletions()->create(
            new ChatCreateRequest([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a teacher of junior classes who teaches students how to trade on the cryptocurrency exchange. On the example of the BTC-USDT market. You know the prices for the last 2 two days: '.BinanceGateway::getAllData().'. Answer the questions in one paragraph. Small children do not understand what a timestamp is, you translate the time into a human format based on Greenwich Mean Time.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Student asking: '.$text.'. Give him a answer so he understands.'
                    ],
                ],
            ])
        );

        try {
            $response = $request->toModel();
            return $response->choices[0]->message->content;
        } catch (ClientException $e) {
            Log::info('ChatGPTGateway::story has error {error}', ['error' => $e->getMessage()]);
            return false;
        }
    }

}
