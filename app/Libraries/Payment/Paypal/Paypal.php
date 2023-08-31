<?php

namespace  App\Libraries\Payment\Paypal;

class Paypal
{
    private $_gateway ,$_response;

    function __construct()
    {
        $this->_gateway = new \Braintree\Gateway([
            'environment' => env('BRAINTREE_ENV'),
            'merchantId'  => env('BRAINTREE_MERCHANT_ID'),
            'publicKey'   => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey'  => env('BRAINTREE_PRIVATE_KEY')
        ]);
        $this->_response = [
            'code'    => 200,
            'message' => 'success',
            'data'    => [],
            'gateway_response' => []
        ];
    }

    public function clientToken($customer_id)
    {
        try{
            $result = $this->_gateway->clientToken()->generate([
                "customerId" => $customer_id
            ]);
        } catch( \Exception $e ){
            return  $this->_response = [
                'code'   => 400,
                'message' => $e->getMessage(),
                'data'    => []
            ];
        }
        $this->_response = [
            'code'    => 200,
            'message' => 'Client token generated successfully',
            'data'    => [
                'client_token' => $result
            ]
        ];
        return $this->_response;
    }

    /**
     * For adding customer in braintree account
     * @params array(firstName, lastName, email, phone)
     * @return customer object or exception
     */
    public function createCustomer($data)
    {
        $result = $this->_gateway->customer()->create($data);
        if ($result->success)
        {
            $this->_response = [
                'code'    => 200,
                'message' => 'success',
                'data'    => [
                    'customer_id' => $result->customer->id,
                ],
                'gateway_response' => $result
            ];
        } else {
            $this->_response = [
                'error'   => 400,
                'message' => $result->message,
                'data'    => []
            ];
        }
        return $this->_response;
    }

    public function createCustomerCard($customerId, $card_token)
    {
        $result = $this->_gateway->paymentMethod()->create([
            'customerId' => $customerId,
            'paymentMethodNonce' => $card_token
        ]);
        if( empty($result->success) ){
            $response = [
                'code'    => 400,
                'message' => $result->message,
                'data'    => []
            ];
        }else{
            $response = [
                'code'    => 200,
                'message' => 'success',
                'data'    => $result
            ];
        }
        return $response;
    }

    public function deleteCard($card_token)
    {
        try{
            $result = $this->_gateway->paymentMethod()->delete($card_token);
            if( $result->success ){
                $response = [
                    'code'    => 200,
                    'message' => 'success',
                    'data'    => $result
                ];
            }else{
                $response = [
                    'code'    => 400,
                    'message' => $result->message,
                    'data'    => []
                ];
            }
        }catch(\Exception $e){
            $response = [
                'code'    => 400,
                'message' => 'Invalid card token',
                'data'    => []
            ];
        }
        return $response;
    }

    public function makeDefaultCard($card_token)
    {
        try{
            $response = $this->_gateway->paymentMethod()->update(
                $card_token,
                [
                    'options' => [
                        'makeDefault' => true
                    ]
                ]
            );
            if( $response->success ){
                $response = [
                    'code'    => 200,
                    'message' => 'success',
                    'data'    => $response
                ];
            }else{
                $response = [
                    'code'    => 400,
                    'message' => 'Invalid card token',
                    'data'    => []
                ];
            }
        } catch ( \Exception $e ){
            $response = [
                'code'    => 400,
                'message' => 'Invalid card token',
                'data'    => []
            ];
        }
        return $response;
    }

    public function customerCharge($params)
    {
        $result = $this->_gateway->transaction()->sale([
            'amount'     => $params['amount'],
            'customerId' => $params['customer_id'],
            'options' => [
                'submitForSettlement' => True
            ]
        ]);
        if( $result->success ){
            $response = [
                'code'    => 200,
                'message' => 'success',
                'data'    => $result
            ];
        } else {
            $response = [
                'code'    => 400,
                'message' => $result->message,
                'data'    => []
            ];
        }
        return $response;
    }

    public function charge($param)
    {
        try {
            $data = \Braintree_Transaction::sale([
                'amount' => $param['amount'],
                'paymentMethodNonce' => $param['payment_token'],
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);
            if ($data->success) {
                $response = [
                    'error'   => 0,
                    'message' => 'success',
                    'data'    => $data->transaction,
                ];
            } else{
                $response = [
                    'error'   => 1,
                    'message' => $data->message,
                    'data'    => $data->transaction,
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'error'   => 1,
                'message' => $e->getMessage(),
                'data'    => [],
            ];
        }
        return $response;
    }



}
