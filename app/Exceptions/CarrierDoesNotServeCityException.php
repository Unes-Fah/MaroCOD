<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;


class CarrierDoesNotServeCityException extends Exception {

    public function __construct(public readonly string $carrier_name, public readonly string $city,)
    {
        parent::__construct("{$carrier_name} does not deliver to {$city}.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }


}