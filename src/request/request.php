<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;

class Request
{
    /**
    # Retrieve any StarkInfra resource
        - Receive a json of resources previously created in StarkInfra's API
	
    ## Parameters (required):
        - path [string]: StarkInfra resource's route. ex: "/pix-request/"
     
    ## Parameters (optional):
        - user [Organization/Project object, default nil]: Organization or Project object. Not necessary if starkinfra.user was set before function call
        - query [array of strings, default null]: Query parameters. ex: ["limit" => 1, "status" => "created"]
	
	## Return:
        - Retrieve paged resources
    */
    public static function get($path, $query = [], $user = null)
    {
        return Rest::getRaw($user, $path, $query, "joker", false);
    }

    /**
	# Create any StarkInfra resource
        - Send an array of strings and create any StarkInfra resource objects
	
    ## Parameters (required):
        - path [string]: StarkInfra resource's route. ex: "/pix-request/"
        - body [array of strings]: request parameters. ex: ["pix-requests" => [["amount" => 100, "name" => "Iron Bank S.A.", "taxId" => "20.018.183/0001-80"]]]
    
	## Parameters (optional):
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
	
	Return:
        - Retrieve created resources
     */
    public static function post($path, $body = [], $user = null)
    {
        return Rest::postRaw($user, $path, $body, "joker", false);
    }

    /**
	# Update any StarkInfra resource
        - Send a json with parameters of a single StarkInfra resource object and update it
	
    # Parameters (required):
    	- path [string]: StarkInfra resource's route. ex: "/pix-request/5699165527090460"
    	- body [array of strings, default null]: request parameters. ex: ["amount" => 100]
    
    # Parameters (optional):
    	- user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    # Return:
    	- Retrieve updated resource
     */
    public static function patch($path, $body = [], $user = null)
    {
        return Rest::patchRaw($user, $path, $body, "joker", false);
    }

    /**
	# Delete any StarkInfra resource
	    - Send a json with parameters of a single StarkInfra resource object and delete it
	
	# Parameters (required):
	    - path [string]: StarkInfra resource's route. ex: "/pix-request/5699165527090460"
	
	# Parameters (optional):
	    - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
        - body [array of strings, default null]: request parameters. ex: ["amount" => 100]
	
	# Return:
	    - json of the resource with updated attributes
     */
    public static function delete($path, $body = [], $user = null)
    {
        return Rest::deleteRaw($user, $path, $body, "Joker", false);
    }
}
