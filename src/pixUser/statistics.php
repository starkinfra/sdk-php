<?php

namespace StarkInfra\PixUser;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Statistics extends SubResource
{

    public $source;
    public $type;
    public $value;
    public $after;
    public $updated;

    /**
    # PixUser\Statistics object
    
    The PixUser\Statistics object are used to see fraud statistics of a user.
    
    ## Parameters (return-only):
        - source [string]: source of PixUser. ex: "pix-fraud"
        - type [string]: type of PixUser. Options: "settled", "registered", "denied", "mule", "scam", "unknown", "other"
        - value [string]: value of PixUser. ex: "0"
        - after [Datetime]: after datetime for the PixUser. ex: "2020-03-10 10:30:00.000000+00:00"
        - updated [Datetime]: latest update datetime for the PixUser. ex: "2020-03-10 10:30:00.000000+00:00"
    */
    function __construct(array $params)
    {
        $this->source = Checks::checkParam($params, "source");
        $this->type = Checks::checkParam($params, "type");
        $this->value = Checks::checkParam($params, "value");
        $this->after = Checks::checkDateTime(Checks::checkParam($params, "after"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    public static function parseStatistics($statistics) {
        if (is_null($statistics)){
            return null;
        }
        $parsedStatistics = [];
        foreach($statistics as $statistic) {
            if($statistic instanceof Statistics) {
                array_push($parsedStatistics, $statistic);
                continue;
            }
            $parsedStatistic = function ($array) {
                $statisticMaker = function ($array) {
                    return new Statistics($array);
                };
                return API::fromApiJson($statisticMaker, $array);
            };
            array_push($parsedStatistics, $parsedStatistic($statistic));
        }    
        return $parsedStatistics;
    }
}
