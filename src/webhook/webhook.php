<?php

namespace StarkInfra;

use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;

class Webhook extends Resource
{
    /**
    # Webhook subscription object
    
    A Webhook is used to subscribe to notification events on a user-selected endpoint.
    Currently, available services for subscription are credit-note, issuing-card, issuing-invoice, issuing-purchase, pix-request.in, pix-request.out, pix-reversal.in, pix-reversal.out, pix-claim, pix-key, pix-infraction, pix-chargeback
    
    ## Parameters (required):
        - url [string]: Url that will be notified when an event occurs.
        - subscriptions [array of strings]: list of any non-empty combination of the available services. ex: ["credit-note", "issuing-card", "issuing-invoice", "issuing-purchase", "pix-request.in", "pix-request.out", "pix-reversal.in", "pix-reversal.out", "pix-claim", "pix-key", "pix-infraction", "pix-chargeback"]
    
    ## Attributes:
        - id [string, default null]: unique id returned when the webhook is created. ex: "5656565656565656"
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> url = Checks::checkParam($params, "url");
        $this-> subscriptions = Checks::checkParam($params, "subscriptions");
        
        Checks::checkParams($params);
    }

    /**
    # Create Webhook subscription
    
    Send a single Webhook subscription for creation in the Stark Infra API
    
    ## Parameters (required):
        - url [string]: url to which notification events will be sent to. ex: "https://webhook.site/60e9c18e-4b5c-4369-bda1-ab5fcd8e1b29"
        - subscriptions [array of strings]: list of any non-empty combination of the available services. ex: ["credit-note"]
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - Webhook object with updated attributes
    */
    public static function create($webhooks, $user = null)
    {
        return Rest::postSingle($user, Webhook::resource(), $webhooks);
    }

    /**
    # Retrieve a specific Webhook subscription
    
    Receive a single Webhook subscription object previously created in the Stark Infra API by its id
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - Webhook object with updated attributes
    */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Webhook::resource(), $id);
    }

    /**
    # Retrieve Webhook subcriptions
    
    Receive an enumerator of Webhook subcription objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - enumerator of Webhook objects with updated attributes
    */
    public static function query($options = [], $user = null)
    {   
        return Rest::getList($user, Webhook::resource(), $options);
    }

    /**
    # Retrieve paged Webhooks
    
    Receive a list of up to 100 Webhook objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if starkinfra.user was set before function call

    ## Return:
        - list of Webhook objects with updated attributes
    */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, Webhook::resource(), $options);
    }
    
    /**
    # Delete a Webhook subscription entity
    
    Delete a Webhook subscription entity previously created in the Stark Infra API
    
    ## Parameters (required):
        - id [string]: Webhook unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - deleted Webhook object
     */
    public static function delete($id = [], $user = null)
    {
        return Rest::deleteId($user, Webhook::resource(), $id);
    }

    private static function resource()
    {
        $webhook = function ($array) {
            return new Webhook($array);
        };
        return [
            "name" => "Webhook",
            "maker" => $webhook,
        ];
    }
}
