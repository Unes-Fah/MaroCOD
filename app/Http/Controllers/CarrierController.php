<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrier;
use App\Models\CarrierRate;
use Illuminate\Support\Facades\DB;

class CarrierController extends Controller
{
    public function index(){
        return response()->json(Carrier::with(['rates' => fn ($q) => $q->orderBy('city')])->get());
    }
    public function show(Carrier $carrier){
        return response()->json($carrier->load(['rates' => fn ($q) => $q->orderBy('city')]));
    }
    
   
    public function cheapestCarrier(Request $request)
    {
 
        $data = $request->validate([
            'city' => ['nullable', 'string', 'max:255'],
        ]);

        $minPrices = CarrierRate::select('city', DB::raw('MIN(price) as min_price'))
            ->groupBy('city');

        $rates = CarrierRate::query()
            ->joinSub($minPrices, 'min_rates', function ($join) {
                $join->on('carrier_rates.city', '=', 'min_rates.city')
                    ->on('carrier_rates.price', '=', 'min_rates.min_price');
            })
            ->when($data['city'] ?? null, fn ($query, $city) => $query->where('carrier_rates.city', $city))
            ->select('carrier_rates.*')
            ->with('carrier:id,name')
            ->orderBy('carrier_rates.city')
            ->get()
            ->map(fn ($rate) => [
                'city'    => $rate->city,
                'carrier' => $rate->carrier->name,
                'price'   => $rate->price,
            ]);
            
        if ($rates->isEmpty() && isset($data['city'])) {
            return response()->json(['message' => "No carrier serves {$data['city']}."], 404);
        }

        return response()->json($rates);
    }
}
