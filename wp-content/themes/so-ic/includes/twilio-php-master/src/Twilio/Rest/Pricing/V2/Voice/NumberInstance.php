<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Pricing\V2\Voice;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceResource;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

/**
 * @property string $destinationNumber
 * @property string $originationNumber
 * @property string $country
 * @property string $isoCountry
 * @property string[] $outboundCallPrices
 * @property string $inboundCallPrice
 * @property string $priceUnit
 * @property string $url
 */
class NumberInstance extends InstanceResource {
    /**
     * Initialize the NumberInstance
     *
     * @param Version $version Version that contains the resource
     * @param mixed[] $payload The response payload
     * @param string $destinationNumber The destination number for which to fetch
     *                                  pricing information
     */
    public function __construct(Version $version, array $payload, string $destinationNumber = null) {
        parent::__construct($version);

        // Marshaled Properties
        $this->properties = [
            'destinationNumber' => Values::array_get($payload, 'destination_number'),
            'originationNumber' => Values::array_get($payload, 'origination_number'),
            'country' => Values::array_get($payload, 'country'),
            'isoCountry' => Values::array_get($payload, 'iso_country'),
            'outboundCallPrices' => Values::array_get($payload, 'outbound_call_prices'),
            'inboundCallPrice' => Values::array_get($payload, 'inbound_call_price'),
            'priceUnit' => Values::array_get($payload, 'price_unit'),
            'url' => Values::array_get($payload, 'url'),
        ];

        $this->solution = [
            'destinationNumber' => $destinationNumber ?: $this->properties['destinationNumber'],
        ];
    }

    /**
     * Generate an instance context for the instance, the context is capable of
     * performing various actions.  All instance actions are proxied to the context
     *
     * @return NumberContext Context for this NumberInstance
     */
    protected function proxy(): NumberContext {
        if (!$this->context) {
            $this->context = new NumberContext($this->version, $this->solution['destinationNumber']);
        }

        return $this->context;
    }

    /**
     * Fetch the NumberInstance
     *
     * @param array|Options $options Optional Arguments
     * @return NumberInstance Fetched NumberInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(array $options = []): NumberInstance {
        return $this->proxy()->fetch($options);
    }

    /**
     * Magic getter to access properties
     *
     * @param string $name Property to access
     * @return mixed The requested property
     * @throws TwilioException For unknown properties
     */
    public function __get(string $name) {
        if (\array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        if (\property_exists($this, '_' . $name)) {
            $method = 'get' . \ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown property: ' . $name);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Pricing.V2.NumberInstance ' . \implode(' ', $context) . ']';
    }
}