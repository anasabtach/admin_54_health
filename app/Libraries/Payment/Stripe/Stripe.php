<?php

namespace App\Libraries\Payment\Stripe;

class Stripe
{
    private $_response, $_stripe;

    public function __construct()
    {
        $this->_stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $this->_response = [
            'code'    => 200,
            'message' => 'success',
            'data'    => [],
            'gateway_response' => []
        ];
    }

    /**
     * Reference Link: https://stripe.com/docs/api/customers/create?lang=php
     * @param array $data
     * @return array
     */
    public function createCustomer($data)
    {
        try{
            $customer = $this->_stripe->customers->create($data);
        }catch (\Exception $e){
            return $this->_response = [
                'code'    => 400,
                'message' => $e->getMessage(),
            ];
        }
        return $this->_response = [
            'code'    => 200,
            'message' =>'Customer added successfully',
            'data' => [
                'customer_id' => $customer->id
            ],
            'gateway_response' => $customer
        ];
    }

    /**
     * Reference Link: https://stripe.com/docs/api/customers/update?lang=php
     * @param string $customer_id
     * @param string $card_token
     * @return array
     */
    public function createCustomerCard($customer_id,$card_token)
    {
        try{
            $card = $this->_stripe->customers->createSource(
                $customer_id,
                ['source' => $card_token]
            );
        }catch (\Exception $e){
            return $this->_response = [
                'code'    => 400,
                'message' => $e->getMessage(),
            ];
        }
        return $this->_response = [
            'code'    => 200,
            'message' =>'Customer card created successfully',
            'data' => [
                'card_id' => $card->id
            ],
            'gateway_response' => $card
        ];
    }

    /**
     * Reference Link: https://stripe.com/docs/api/customers/update?lang=php
     * @param string $customer_id
     * @param string $card_id
     * @return array
     */
    public function makeDefaultCard($customer_id, $card_id)
    {
        try{
            $customer = $this->_stripe->customers->update(
                $customer_id,
                ['default_source' => $card_id]
            );
        }catch (\Exception $e){
            return $this->_response = [
                'code'    => 400,
                'message' => $e->getMessage(),
            ];
        }
        return $this->_response = [
            'code'    => 200,
            'message' =>'Customer card updated successfully',
            'data'    => $customer,
            'gateway_response' => $customer
        ];
    }

    /**
     * Reference Link: https://stripe.com/docs/api/cards/delete?lang=php
     * @param {string} $customer_id
     * @param {string} $card_id
     * @return array
     */
    public function deleteCustomerCard($customer_id, $card_id)
    {
        try{
            $customer = $this->_stripe->customers->deleteSource(
                $customer_id,
                $card_id,
                []
            );
        }catch (\Exception $e){
            return $this->_response = [
                'code'    => 400,
                'message' => $e->getMessage(),
            ];
        }
        return $this->_response = [
            'code'    => 200,
            'message' =>'Customer card deleted successfully',
            'data'    => $customer,
            'gateway_response' => $customer
        ];
    }

    /**
     * Reference Link: https://stripe.com/docs/api/charges/create
     * @param string $customer_id
     * @param float $amount
     * @param string $currency
     * @param string $description
     * @return array
     */
    public function customerCharge($customer_id, $amount, $currency = 'usd', $description = '')
    {
        try{
            $transaction = $this->_stripe->charges->create([
                'amount'      => round($amount * 100,2),
                'currency'    => $currency,
                'customer'    => $customer_id,
                'description' => $description,
            ]);
        }catch (\Exception $e){
            return $this->_response = [
                'code'    => 400,
                'message' => $e->getMessage(),
            ];
        }
        return $this->_response = [
            'code'    => 200,
            'message' =>'Customer has been charged successfully',
            'data'    => [
                'transaction_id' => $transaction->id
            ],
            'gateway_response' => $transaction
        ];
    }

    /**
     * Reference Link: https://stripe.com/docs/api/charges/create
     * @param string $card_token
     * @param float $amount
     * @param string $currency
     * @param string $description
     * @return array
     */
    public function directCharge($card_token, $amount, $description = '')
    {
        try{
            $transaction = $this->_stripe->charges->create([
                'amount'      => round($amount * 100,2),
                'currency'    => env('STRIPE_CHARGE_CURRENCY'),
                'source'      => $card_token,
                'description' => $description,
            ]);
        }catch (\Exception $e){
            return $this->_response = [
                'code'    => 400,
                'message' => $e->getMessage(),
            ];
        }
        return $this->_response = [
            'code'    => 200,
            'message' =>'payment has been charged successfully',
            'data'    => [
                'transaction_id' => $transaction->id
            ],
            'gateway_response' => $transaction
        ];
    }
}
