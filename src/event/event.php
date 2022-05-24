<?php

namespace StarkInfra;

use StarkInfra\Utils\Parse;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\API;


class Event extends Resource
{
    /**
    # Webhook Event object

    An Event is the notification received from the subscription to the Webhook.
    Events cannot be created, but may be retrieved from the Stark Infra API to
    list all generated updates on entities.

    ## Attributes:
        - id [string]: unique id returned when the event is created. ex: "5656565656565656"
        - log [Log]: a Log object from one the subscription services (Transfer\Log, Boleto\Log, BoletoPayment\log or UtilityPayment\Log)
        - created [DateTime]: creation datetime for the notification event.
        - isDelivered [bool]: true if the event has been successfully delivered to the user url. ex: false
        - subscription [string]: service that triggered this event. ex: "transfer", "utility-payment"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->isDelivered = Checks::checkParam($params, "isDelivered");
        $this->subscription = Checks::checkParam($params, "subscription");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->log = Event::buildLog($this->subscription, Checks::checkParam($params, "log"));
        $this->workspaceId = Checks::checkParam($params, "workspaceId");

        Checks::checkParams($params);
    }

    private static function buildLog($subscription, $log)
    {
        $makerOptions = [
            "pix-claim" => Event::pixClaimLogResource(),
            "pix-key" => Event::pixKeyLogResource(),
            "pix-infraction" => Event::pixInfractionLogResource(),
            "pix-chargeback" => Event::pixChargebackLogResource(),
            "pix-request.in" => Event::pixRequestLogResource(),
            "pix-request.out" => Event::pixRequestLogResource(),
            "pix-reversal.in" => Event::pixReversalLogResource(),
            "pix-reversal.out" => Event::pixReversalLogResource(),
            "issuing-card" => Event::issuingCardLogResource(),
            "issuing-invoice" => Event::issuingInvoiceLogResource(),
            "issuing-purchase" => Event::issuingPurchaseLogResource()
        ];

        if (!isset($makerOptions[$subscription])) {
            return $log;
        }

        return $makerOptions[$subscription]($log);
    }

    private static function issuingCardLogResource()
    {
        return function ($array) {
            $card = function ($array) {
                return new IssuingCard($array);
            };
            $array["card"] = API::fromApiJson($card, $array["card"]);
            $log = function ($array) {
                return new IssuingCard\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function issuingInvoiceLogResource()
    {
        return function ($array) {
            $invoice = function ($array) {
                return new IssuingInvoice($array);
            };
            $array["invoice"] = API::fromApiJson($invoice, $array["invoice"]);
            $log = function ($array) {
                return new IssuingInvoice\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function issuingPurchaseLogResource()
    {
        return function ($array) {
            $purchase = function ($array) {
                return new issuingPurchase($array);
            };
            $array["purchase"] = API::fromApiJson($purchase, $array["purchase"]);
            $log = function ($array) {
                return new issuingPurchase\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function pixReversalLogResource()
    {
        return function ($array) {
            $reversal = function ($array) {
                return new PixReversal($array);
            };
            $array["reversal"] = API::fromApiJson($reversal, $array["reversal"]);
            $log = function ($array) {
                return new PixReversal\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function pixRequestLogResource()
    {
        return function ($array) {
            $request = function ($array) {
                return new PixRequest($array);
            };
            $array["request"] = API::fromApiJson($request, $array["request"]);
            $log = function ($array) {
                return new PixRequest\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function pixClaimLogResource()
    {
        return function ($array) {
            $claim = function ($array) {
                return new PixRequest($array);
            };
            $array["claim"] = API::fromApiJson($claim, $array["claim"]);
            $log = function ($array) {
                return new PixClaim\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function pixKeyLogResource()
    {
        return function ($array) {
            $key = function ($array) {
                return new PixRequest($array);
            };
            $array["key"] = API::fromApiJson($key, $array["key"]);
            $log = function ($array) {
                return new PixKey\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function pixInfractionLogResource()
    {
        return function ($array) {
            $infraction = function ($array) {
                return new PixRequest($array);
            };
            $array["infraction"] = API::fromApiJson($infraction, $array["infraction"]);
            $log = function ($array) {
                return new PixInfraction\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    private static function pixChargebackLogResource()
    {
        return function ($array) {
            $chargeback = function ($array) {
                return new PixRequest($array);
            };
            $array["chargeback"] = API::fromApiJson($chargeback, $array["chargeback"]);
            $log = function ($array) {
                return new PixChargeback\Log($array);
            };
            return API::fromApiJson($log, $array);
        };
    }

    /**
    # Create single notification Event from a content string

    Create a single Event object received from event listening at subscribed user endpoint.
    If the provided digital signature does not check out with the Stark public key, a
    stark.exception.InvalidSignatureException will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Event object with updated attributes
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, Event ::resource(), $user);
    }

    private static function resource()
    {
        $event = function ($array) {
            return new Event($array);
        };
        return [
            "name" => "Event",
            "maker" => $event,
        ];
    }
}
