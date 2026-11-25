<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;


class MissingCarrierException extends Exception {

    public function __construct()
    {
        parent::__construct("An order should have a carrier before it can be shipped");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }


}