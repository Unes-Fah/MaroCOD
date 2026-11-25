<?php
namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Exceptions\MissingTrackingNumberException;
use App\Exceptions\OrderLockedException;
use App\Exceptions\CarrierDoesNotServeCityException;
use App\Models\Order;
use App\Models\Carrier;
use App\Exceptions\MissingCarrierException;

class OrderLogisticsService {
    public function transition(Order $order, OrderStatus $next, ?string $trackingNumber=null):Order
    {
        $current = $order->status;
        
        if (! $current->canTransitionTo($next)){
            throw new InvalidOrderTransitionException($current, $next);
        }

        if ($next === OrderStatus::Shipped) {

            $trackingNumber ??= $order->tracking_number;

            if (blank($order->carrier_id)){
                throw new MissingCarrierException();
            }

            if (blank($trackingNumber)) {
                throw new MissingTrackingNumberException();
            }

            $order->tracking_number = $trackingNumber;
        }

        $order->status = $next;
        $order->save();

        return $order;
    }

    public function assignCarrier(Order $order, Carrier $carrier):Order{
        if (! in_array($order->status, [OrderStatus::Pending,OrderStatus::Confirmed],true)){
            throw new OrderLockedException($order->status);
        }

        $rate= $carrier->rates()->where('city',$order->city)->first();

        if (! $rate){
            throw new CarrierDoesNotServeCityException($carrier->name, $order->city);
        }

        $order->carrier_id= $carrier->id;
        $order->shipping_cost=$rate->price;
        $order->save();
        
        return $order;
    }
}
