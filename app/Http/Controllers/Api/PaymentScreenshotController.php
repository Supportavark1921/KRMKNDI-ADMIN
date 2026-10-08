<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Donation;
use App\Models\Order;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentScreenshotController extends Controller
{
    public function __construct(private readonly FcmService $fcm) {}

    /** POST /api/v1/appointments/{appointment}/payment-screenshot */
    public function appointment(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($appointment->payment_status === 'screenshot_uploaded' || $appointment->payment_status === 'paid') {
            return response()->json(['message' => 'Screenshot already submitted.'], 409);
        }

        $request->validate(['screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);

        $path = $request->file('screenshot')->store('payment-screenshots/appointments', 'public');
        $appointment->update(['payment_status' => 'screenshot_uploaded', 'payment_screenshot' => $path]);

        return response()->json([
            'data' => ['payment_status' => $appointment->payment_status],
        ]);
    }

    /** POST /api/v1/donations/{donation}/payment-screenshot */
    public function donation(Request $request, Donation $donation): JsonResponse
    {
        if ($donation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($donation->payment_status === 'screenshot_uploaded' || $donation->payment_status === 'success') {
            return response()->json(['message' => 'Screenshot already submitted.'], 409);
        }

        $request->validate(['screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);

        $path = $request->file('screenshot')->store('payment-screenshots/donations', 'public');
        $donation->update(['payment_status' => 'screenshot_uploaded', 'payment_screenshot' => $path]);

        return response()->json([
            'data' => ['payment_status' => $donation->payment_status],
        ]);
    }

    /** POST /api/v1/orders/{order}/payment-screenshot */
    public function order(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($order->payment_status === 'screenshot_uploaded' || $order->payment_status === 'paid') {
            return response()->json(['message' => 'Screenshot already submitted.'], 409);
        }

        $request->validate(['screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);

        $path = $request->file('screenshot')->store('payment-screenshots/orders', 'public');
        $order->update(['payment_status' => 'screenshot_uploaded', 'payment_screenshot' => $path]);

        return response()->json([
            'data' => ['payment_status' => $order->payment_status],
        ]);
    }
}
