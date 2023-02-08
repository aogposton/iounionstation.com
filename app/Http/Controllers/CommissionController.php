<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendTelegramMessage;
use Illuminate\Support\Facades\Log;
use \Lin\Bybit\BybitLinear;
class CommissionController extends Controller
{
    public function automaticTrade(Request $req){
        $bybit = new BybitLinear($req->api_key,$req->api_secret);
        $leverage = $req->leverage;
        $tradingPercent = $req->trade_percent;
        try {


            $orderInfo = explode('///',$req->text);
            SendTelegramMessage::dispatch('5734934123',$orderInfo);
            $market = str_replace(['[',']'],'',$orderInfo[0]);
            $symbol = str_replace(['[',']'],'',$orderInfo[1]);
            $orderPrice = str_replace(['[',']'],'',$orderInfo[2]);
            $volume = str_replace(['[',']'],'',$orderInfo[3]);
            $orderType = str_replace(['[',']'],'',$orderInfo[4]);
            $stoploss = str_replace(['[',']'],'',$orderInfo[5]);
            $limitOrder = str_replace(['[',']'],'',$orderInfo[6]);

            
            $side ='';
            $message=$req->text;
            $basePrice = $orderPrice;
            $leverage = 10;
            
            $result=$bybit->publics()->getTickers(['symbol'=>'BTCUSDT']);
            $pricePerBtc = $result['result'][0]['mark_price'];
            $result=$bybit->privates()->getWalletBalance();
            
            $currentPositionMarginUSDT = $result['result']['USDT']['position_margin'];
            $currentAvalableUSDT = $result['result']['USDT']['available_balance'];
            
            
            switch($orderType){
                case 'enter-short':
                    $quantity = (1/$pricePerBtc)*($currentAvalableUSDT*($tradingPercent/100))*$leverage;
                    $quantity = substr((float)$quantity,0,8);
                    $stoploss = round((float)$limitOrder,2);
                    $message = "<b>Enter short position</b>\n{$market}:{$symbol} @ {$orderPrice}. \n {$quantity} With all Available Margin({$currentAvalableUSDT})";
                    $side = 'Sell';
                    $exitEntry='Enter';
                    
                    $order = [
                        'side'=>$side, //Buy == long //Sell== short
                        'symbol'=>$symbol,
                        'order_type'=>'Market',
                        'qty'=>$quantity,
                        'time_in_force'=>'GoodTillCancel',
                        'base_price'=>$pricePerBtc,
                        'stop_px'=>$stoploss,
                        'trigger_by'=>'MarkPrice',
                        'stop_loss'=>is_numeric($stoploss)?$stoploss:'',
                        'reduce_only'=>$exitEntry=="Exit"?'true':'false', //true = close, false == open
                        'close_on_trigger'=>$exitEntry=="Exit"?'true':'false'
                    ];
                    $result=$bybit->privates()->postOrderCreate($order);
                    break;

                case 'enter-long':
                    $side='Buy';
                    $exitEntry='Enter';
                    // dump('USDT available_balance: '.$result['result']['USDT']['available_balance']);
                    $stoploss = round((float)$stoploss,2);
                    $quantity = (1/$pricePerBtc)*($currentAvalableUSDT*($tradingPercent/100))*$leverage;
                    $quantity = substr($quantity,0,8);
                    $message = "<b>Enter long position</b>\n{$market}:{$symbol} @ {$orderPrice} \nEnter{$quantity} With all Available USDT({$currentAvalableUSDT})";

                    
                    $order = [
                        'side'=>$side, //Buy == long //sell== short
                        'symbol'=>$symbol,
                        'order_type'=>'Market',
                        'qty'=>$quantity,
                        'time_in_force'=>'GoodTillCancel',
                        'base_price'=>$pricePerBtc,
                        'stop_px'=>$stoploss,
                        'trigger_by'=>'MarkPrice',
                        'reduce_only'=>$exitEntry=="Exit"?'true':'false', //true = close, false == open
                        'stop_loss'=>is_numeric($stoploss)?$stoploss:'',
                        'close_on_trigger'=>'false',
                        'sl_trigger_by'=>'LastPrice'
                        
                    ];
                    $result=$bybit->privates()->postOrderCreate($order);

                    break;

                    case 'exit-short':
                        $side = 'Buy';
                        $quantity = (1/$pricePerBtc)*($currentPositionMarginUSDT*($tradingPercent/100))*$leverage;
                        $quantity = substr((float)$quantity,0,8);
                        $message = "<b>Exit short position</b>\n{$market}:{$symbol} @ {$orderPrice} \nExit {$quantity} With all Available USDT({$currentPositionMarginUSDT})";
                        
                        $order = [
                            'side'=>$side, //Buy == long //sell== short
                            'symbol'=>$symbol,
                            'order_type'=>'Market',
                            'qty'=>$quantity,
                            'time_in_force'=>'GoodTillCancel',
                            'reduce_only'=>'true', //true = close, false == open
                            'close_on_trigger'=>'false'
                        ];
                        
                        $result=$bybit->privates()->postOrderCreate($order);

                    break;

                    case 'exit-long':
                        $side='Sell';
                        $exitEntry='Exit';
                        $quantity = (1/$pricePerBtc)*($currentPositionMarginUSDT)*$leverage;
                        $quantity = substr((float)$quantity,0,8);
                        $message = "<b>Exit long position</b>\n{$market}:{$symbol} @ {$orderPrice}  \nwith all Available Margin({$currentPositionMarginUSDT})";
                        
                        $order = [
                            'side'=>$side, //Buy == long //sell== short
                            'symbol'=>$symbol,
                            'order_type'=>'Market',
                            'qty'=>$quantity,
                            'time_in_force'=>'GoodTillCancel',
                            'reduce_only'=>$exitEntry=="Exit"?'true':'false', //true = close, false == open
                            'close_on_trigger'=>$exitEntry=="Exit"?'true':'false'

                        ];
                        $result=$bybit->privates()->postOrderCreate($order);
                        
                        break;
                    }

            

            $users = [
                '1713684223', //Aaron
                '1492036761', //Harry
                '5734934123' //Amon
            ];
            SendTelegramMessage::dispatch('5734934123',$req->text);
            SendTelegramMessage::dispatch('5734934123',$orderInfo);

            if($result['ret_msg']!=='OK'){
                foreach($users as $user){
                    SendTelegramMessage::dispatch($user,"<b>Error - Trade Prevented:</b>\n\nResults:".json_encode($result)."\n\nOrder Details:".json_encode($order));
                }
            }else{
                foreach($users as $user){
                    SendTelegramMessage::dispatch($user,$message);
                }
            }

        }catch (\Exception $e){
            Log::info($e->getMessage());
            SendTelegramMessage::dispatch('5734934123',$e->getMessage());
            SendTelegramMessage::dispatch('5734934123',$req->text);
            
        }
    }

    public function manualTrade(Request $req){
        $bybit = new BybitLinear($req->api_key,$req->api_secret);
        $leverage = $req->leverage;
        $tradingPercent = $req->trade_percent;
        try {


            $orderInfo = explode('///',$req->text);
            $market = str_replace(['[',']'],'',$orderInfo[0]);
            $symbol = str_replace(['[',']'],'',$orderInfo[1]);
            $orderPrice = str_replace(['[',']'],'',$orderInfo[2]);
            $volume = str_replace(['[',']'],'',$orderInfo[3]);
            $orderType = str_replace(['[',']'],'',$orderInfo[4]);
            $stoploss = str_replace(['[',']'],'',$orderInfo[5]);
            $side ='';
            $message=$req->text;
            $basePrice = $orderPrice;
            $leverage = 10;
            
            $result=$bybit->publics()->getTickers(['symbol'=>'BTCUSDT']);
            $pricePerBtc = $result['result'][0]['mark_price'];
            $result=$bybit->privates()->getWalletBalance();
            
            $currentPositionMarginUSDT = $result['result']['USDT']['position_margin'];
            $currentAvalableUSDT = $result['result']['USDT']['available_balance'];
            
            
            switch($orderType){
                case 'enter-short':
                    $quantity = (1/$pricePerBtc)*($currentAvalableUSDT*($tradingPercent/100))*$leverage;
                    $quantity = substr((float)$quantity,0,8);
                    $message = "<b>Enter short position</b>\n{$market}:{$symbol} @ {$orderPrice}. \n {$quantity} With all Available Margin({$currentAvalableUSDT})";

                    break;

                case 'enter-long':
                    $side='Buy';
                    $exitEntry='Enter';
                    dump('USDT available_balance: '.$result['result']['USDT']['available_balance']);
                    $quantity = (1/$pricePerBtc)*($currentAvalableUSDT*($tradingPercent/100))*$leverage;
                    $quantity = substr($quantity,0,8);
                    $message = "<b>Enter long position</b>\n{$market}:{$symbol} @ {$orderPrice} \nEnter{$quantity} With all Available USDT({$currentAvalableUSDT})";
                    break;

                case 'exit-short':
                    $side = 'Buy';
                    $quantity = (1/$pricePerBtc)*($currentPositionMarginUSDT*($tradingPercent/100))*$leverage;
                    $quantity = substr((float)$quantity,0,8);
                    $message = "<b>Exit short position</b>\n{$market}:{$symbol} @ {$orderPrice} \nExit {$quantity} With all Available USDT({$currentPositionMarginUSDT})";
                break;

                case 'exit-long':
                    $side='Sell';
                    $exitEntry='Exit';
                    $quantity = (1/$pricePerBtc)*($currentPositionMarginUSDT)*$leverage;
                    $quantity = substr((float)$quantity,0,8);
                    $message = "<b>Exit long position</b>\n{$market}:{$symbol} @ {$orderPrice}  \nwith all Available Margin({$currentPositionMarginUSDT})";
                    break;
            }

            

            $users = [
                '1713684223', //Aaron
                '1492036761', //Harry
                '5734934123' //Amon
            ];
            if($result['ret_msg']!=='OK'){
                foreach($users as $user){
                    SendTelegramMessage::dispatch($user,"<b>Error - Trade Prevented:</b>");
                }
            }else{
                foreach($users as $user){
                    SendTelegramMessage::dispatch($user,$message);
                }
            }

        }catch (\Exception $e){
            Log::info($e->getMessage());
            SendTelegramMessage::dispatch('5734934123',$e->getMessage());
        }
    }

}