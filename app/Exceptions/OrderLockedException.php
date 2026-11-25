<?php
namespace App\Exceptions;

use App\Enums\OrderStatus;
use Exception;
use Illuminate\Http\JsonResponse;


class OrderLockedException extends Exception {

    public function __construct(public readonly OrderStatus $status,)
    {
        parent::__construct("Order status {$status->value} is terminal and cannot have a carrier assigned");
    }

     public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }


}