<?php
namespace App\Exceptions;

use App\Enums\OrderStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;
use Override;

class InvalidOrderTransitionException extends Exception {

    public function __construct(public readonly OrderStatus $from, public readonly OrderStatus $to,)
    {
        parent::__construct("Cannot change order status from {$from->value} to {$to->value}.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'from'    => $this->from->value,
            'to'      => $this->to->value,
            'allowed' => array_map(fn ($s) => $s->value, $this->from->allowedTransitions()),
        ], 422);
    }


}