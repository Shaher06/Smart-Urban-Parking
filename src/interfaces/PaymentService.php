<?php

/**
 * PaymentService Interface
 * Implemented by: PaymentGatewayService
 */
interface PaymentService
{
    /**
     * Charge the user for an amount.
     *
     * @param  float  $amount         Amount in system currency.
     * @param  array  $paymentDetails Must contain: user_id, method.
     *                                Optional: reservation_id, fine_id.
     * @return array  ['success'=>bool, 'payment_id'=>int, 'transaction_ref'=>string, 'status'=>string]
     */
    public function charge(float $amount, array $paymentDetails): array;

    /**
     * Refund a previous transaction.
     *
     * @param  string $transactionRef The original transaction reference.
     * @param  float  $amount         Amount to refund.
     * @return array  ['success'=>bool, 'refund_ref'=>string, 'amount'=>float]
     */
    public function refund(string $transactionRef, float $amount): array;

    /**
     * Get the current status of a transaction.
     *
     * @param  string $transactionRef
     * @return string 'completed'|'pending'|'failed'|'refunded'|'unknown'
     */
    public function getStatus(string $transactionRef): string;
}