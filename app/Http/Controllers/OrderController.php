<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Validation\Rule;
use App\Enums\OrderStatus;
use App\Models\Carrier;
use App\Services\OrderLogisticsService;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request){
        return response()->json($request->user()->orders);
    }
    public function store(Request $request){
        $data=$request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:13|min:10',
            'city' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01|max:99999999.99',
        ]);
        $order= $request->user()->orders()->create($data);
        return  response()->json($order->refresh(),201);
    }
    
    public function updateStatus(Request $request, Order $order, OrderLogisticsService $logistics){
        Gate::authorize('update', $order);

        $data = $request->validate([
            'status'          => ['required', Rule::enum(OrderStatus::class)],
            'tracking_number' => ['nullable', 'string', 'max:50'],
        ],['status.enum' => 'Status must be one of: pending, confirmed, shipped, delivered, returned, cancelled, expired.']);

        $order = $logistics->transition(
            $order,
            OrderStatus::from($data['status']),
            $data['tracking_number'] ?? null,
        );

        return response()->json($order);
    }

    public function assignCarrier(Request $request, Order $order, OrderLogisticsService $logistics){

        Gate::authorize('update', $order);

        $data = $request->validate([
        'carrier_id' => ['required', 'integer', 'exists:carriers,id'],
        ]);
        
        $order = $logistics->assignCarrier($order, Carrier::findOrFail($data['carrier_id']));

        return response()->json($order->load('carrier'));
    }
}
