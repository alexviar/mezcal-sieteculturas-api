<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeWebhookController
{
    public function handleWebhook(Request $request)
    {
        $endpointSecret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $this->processEvent($event);


        return response()->json(['status' => 'success'], 200);
    }

    function processEvent(Event $event)
    {
        Log::info('Stripe webhook event', $event->toArray());
        switch ($event->type) {
            case 'payment_intent.succeeded':
                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;

                logger("Payment succeeded", $paymentIntent->toArray());
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }
    }
}
