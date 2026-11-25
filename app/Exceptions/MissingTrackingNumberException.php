<?php
namespace App\Exceptions;

use App\Enums\OrderStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;
use Override;

class MissingTrackingNumberException extends Exception {

    public function __construct()
    {
        parent::__construct("A tracking number is required before an order can be shipped");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }


}