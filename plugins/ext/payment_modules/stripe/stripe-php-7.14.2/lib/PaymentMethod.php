<?php

namespace Stripe;

/**
 * Class PaymentMethod
 *
 * @property string $id
 * @property string $object
 * @property mixed $billing_details
 * @property mixed $card
 * @property mixed $card_present
 * @property int $created
 * @property string|null $customer
 * @property mixed|null $ideal
 * @property bool $livemode
 * @property \Stripe\StripeObject $metadata
 * @property mixed|null $sepa_debit
 * @property string $type
 *
 * @package Stripe
 */
class PaymentMethod extends ApiResource
{
    const OBJECT_NAME = 'payment_method';

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return PaymentMethod The attached payment method.
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     */
    public function attach($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/attach';
        [$response, $opts] = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);
        return $this;
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return PaymentMethod The detached payment method.
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     */
    public function detach($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/detach';
        [$response, $opts] = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);
        return $this;
    }
}
