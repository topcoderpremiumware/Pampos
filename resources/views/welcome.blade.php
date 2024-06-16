<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pampos AI</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <!-- Styles -->
        <style>
            *,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}html{line-height:1.5;font-family:Figtree, sans-serif;font-feature-settings:normal}body{margin:0;line-height:inherit}
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row flex-md-row flex-column-reverse">
                <div class="col-md-8 col-12" style="height: 100dvh;">
                    <!-- TradingView Widget BEGIN -->
                    <div class="tradingview-widget-container" style="height:100%;width:100%">
                        <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                        <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                            {
                                "autosize": true,
                                "symbol": "BINANCE:BTCUSDT",
                                "interval": "1",
                                "timezone": "Etc/UTC",
                                "theme": "light",
                                "style": "1",
                                "locale": "en",
                                "allow_symbol_change": false,
                                "save_image": false,
                                "calendar": false,
                                "hide_volume": true,
                                "support_host": "https://www.tradingview.com"
                            }
                        </script>
                    </div>
                    <!-- TradingView Widget END -->
                </div>
                <div class="col-md-4 col-12 py-3 overflow-auto" style="height: 100dvh;">
                    <ul>
                        <li class="mb-3">{{\App\Gateways\ChatGPTGateway::story('Describe to me what the candlestick chart of the exchange means. And how to use it.')}}</li>
                        <li class="mb-3">{{\App\Gateways\ChatGPTGateway::story('When the price was the highest and when the lowest. When I could buy and when I could sell to make a profit.')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
