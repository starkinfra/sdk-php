<?php

namespace StarkInfra;

use \Exception;
use EllipticCurve\PublicKey;
use EllipticCurve\Signature;
use EllipticCurve\Ecdsa;
use StarkInfra\Error\InvalidSignatureError;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Request;
use StarkInfra\Utils\Cache;
use StarkInfra\Utils\Parse;
use StarkInfra\Utils\StarkBankDate;


class Event extends Resource
{
    /**
    # Webhook Event object

    An Event is the notification received from the subscription to the Webhook.
    Events cannot be created, but may be retrieved from the Stark Infra API to
    list all generated updates on entities.

    ## Attributes:
        - id [string]: unique id returned when the event is created. ex: "5656565656565656"
        - log [Log]: a Log object from one the subscription services ex: IssuingCard\Log, PixRequest\Log
        - created [DateTime]: creation datetime for the notification event.
        - isDelivered [bool]: true if the event has been successfully delivered to the user url. ex: false
        - subscription [string]: service that triggered this event. ex: "issuing-card", "pix-request.in"
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
                return new IssuingPurchase\Log($array);
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
    # Retrieve a specific notification Event

    Receive a single notification Event object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Event object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Event::resource(), $id);
    }

    /**
    # Retrieve notification Events

    Receive a enumerator of Event objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - isDelivered [bool, default null]: bool to filter successfully delivered events. ex: true or false
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Event objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Event::resource(), $options);
    }

    /**
    # Retrieve paged Events

    Receive a list of up to 100 Event objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
    - cursor [string, default null]: cursor returned on the previous page function call
    - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
    - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
    - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
    - isDelivered [boolean, default None]: bool to filter successfully delivered events. ex: True or False
    - user [Organization/Project object, default null, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
    - list of Event objects with updated attributes
    - cursor to retrieve the next page of Event objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, Event::resource(), $options);
    }

    /**
    # Delete notification Events

    Delete a single Event entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: Event unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - deleted Event object
     */
    public static function delete($id, $user = null)
    {
        return Rest::deleteId($user, Event::resource(), $id);
    }

    /**
    # Update notification Event entity

    Update notification Event by passing id.
    If isDelivered is true, the event will no longer be returned on queries with isDelivered=false.

    ## Parameters (required):
        - id [array of strings]: Event unique ids. ex: "5656565656565656"
        - isDelivered [bool]: If true and event hasn't been delivered already, event will be set as delivered. ex: true

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra/Settings::setUser() was used before function call

    ## Return:
        - target Event with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        return Rest::patchId($user, Event::resource(), $id, $options);
    }

    /**
    # Create single notification Event from a content string

    Create a single Event object received from event listening at subscribed user endpoint.
    If the provided digital signature does not check out with the StarkInfra public key, a
    starkinfra.exception.InvalidSignatureException will be raised.

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

    private static function verifySignature($user, $content, $signature, $refresh = false)
    {
        $publicKey = Cache::getStarkPublicKey();
        if (is_null($publicKey) | $refresh) {
            $pem = Event::getPublicKeyPem($user);
            $publicKey = PublicKey::fromPem($pem);
            Cache::setStarkPublicKey($publicKey);
        }
        return Ecdsa::verify($content, $signature, $publicKey);
    }

    private static function getPublicKeyPem($user)
    {
        return Request::fetch($user, "GET", "/public-key", null, ["limit" => 1])->json()["publicKeys"][0]["content"];
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
