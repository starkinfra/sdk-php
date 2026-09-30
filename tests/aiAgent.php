<?php

namespace Test\AiAgent;
use \Exception;
use StarkInfra\AiAgent;
use StarkInfra\AiKnowledgeBase;
use Test\Utils\AgentExample;
use Test\Utils\KnowledgeBaseExample;
use StarkCore\Error\InputErrors;

include_once("tests/utils/aiAgent.php");
include_once("tests/utils/aiKnowledgeBase.php");


class TestAiAgent
{
    public $knowledgeBase;
    public $agent;

    public function setUp()
    {
        $this->knowledgeBase = AiKnowledgeBase::create(KnowledgeBaseExample::generateExampleAiKnowledgeBase());
        $this->agent = AiAgent::create(AgentExample::generateExampleAiAgent([$this->knowledgeBase->id]));
    }

    public function tearDown()
    {
        AiAgent::delete([$this->agent->id]);
        AiKnowledgeBase::delete([$this->knowledgeBase->id]);
    }

    public function create()
    {
        if (!is_string($this->agent->id) || $this->agent->model != "bender-1.0" || !($this->agent->created instanceof \DateTime)) {
            throw new Exception("failed");
        }
        if ($this->agent->knowledgeBaseIds != [$this->knowledgeBase->id] || array_keys($this->agent->metadataSchema) != ["order_id"]) {
            throw new Exception("failed");
        }
    }

    public function getAndExpand()
    {
        $plain = AiAgent::get($this->agent->id);
        if ($plain->id != $this->agent->id || !is_null($plain->knowledgeBases)) {
            throw new Exception("failed");
        }

        $expanded = AiAgent::get($this->agent->id, ["expand" => ["knowledgeBases"]]);
        if (count($expanded->knowledgeBases) != 1 || !($expanded->knowledgeBases[0] instanceof AiKnowledgeBase)) {
            throw new Exception("failed");
        }
        if ($expanded->knowledgeBases[0]->id != $this->knowledgeBase->id) {
            throw new Exception("failed");
        }
    }

    public function queryWithExpand()
    {
        foreach (AiAgent::query(["expand" => ["knowledgeBases"]]) as $agent) {
            if ($agent->id != $this->agent->id) {
                continue;
            }
            if (count($agent->knowledgeBases) != 1) {
                throw new Exception("failed");
            }
            return;
        }
        throw new Exception("failed");
    }

    public function pageLeadsToTheCreatedAgent()
    {
        $options = ["limit" => 100];
        do {
            list($agents, $cursor) = AiAgent::page($options);
            foreach ($agents as $agent) {
                if ($agent->id == $this->agent->id) {
                    return;
                }
            }
            $options["cursor"] = $cursor;
        } while (!is_null($cursor));
        throw new Exception("failed");
    }

    public function updateKeepsWhatItWasNotAskedToChange()
    {
        $renamed = AiAgent::update($this->agent->id, ["name" => "renamed-by-sdk"]);

        if ($renamed->name != "renamed-by-sdk" || $renamed->knowledgeBaseIds != [$this->knowledgeBase->id]) {
            throw new Exception("failed");
        }
        if (array_keys($renamed->metadataSchema) != ["order_id"] || $renamed->systemPrompt != $this->agent->systemPrompt) {
            throw new Exception("failed");
        }
    }

    public function updateWithEmptyValuesClearsTheFields()
    {
        $cleared = AiAgent::update($this->agent->id, ["systemPrompt" => "", "knowledgeBaseIds" => [], "metadataSchema" => new \stdClass()]);

        if ($cleared->knowledgeBaseIds !== [] || $cleared->systemPrompt != "" || count($cleared->metadataSchema) != 0) {
            throw new Exception("failed");
        }
    }

    public function delete()
    {
        $agent = AiAgent::create(AgentExample::generateExampleAiAgent());

        $deleted = AiAgent::delete([$agent->id]);

        if (count($deleted) != 1 || $deleted[0]->id != $agent->id) {
            throw new Exception("failed");
        }
    }

    public function queryWithInvalidLimit()
    {
        $error = false;
        try {
            iterator_to_array(AiAgent::query(["limit" => -1]));
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function pageWithInvalidLimit()
    {
        $error = false;
        try {
            AiAgent::page(["limit" => 101]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function createWithInvalidModel()
    {
        $error = false;
        try {
            AiAgent::create(new AiAgent(["name" => "invalid", "model" => "gpt"]));
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function getUnknownId()
    {
        $error = false;
        try {
            AiAgent::get("0000000000000000");
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nAiAgent:";

$test = new TestAiAgent();
$test->setUp();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- get and expand";
$test->getAndExpand();
echo " - OK";

echo "\n\t- query with expand";
$test->queryWithExpand();
echo " - OK";

echo "\n\t- page leads to the created agent";
$test->pageLeadsToTheCreatedAgent();
echo " - OK";

echo "\n\t- update keeps what it was not asked to change";
$test->updateKeepsWhatItWasNotAskedToChange();
echo " - OK";

echo "\n\t- update with empty values clears the fields";
$test->updateWithEmptyValuesClearsTheFields();
echo " - OK";

echo "\n\t- delete";
$test->delete();
echo " - OK";

echo "\n\t- query with invalid limit";
$test->queryWithInvalidLimit();
echo " - OK";

echo "\n\t- page with invalid limit";
$test->pageWithInvalidLimit();
echo " - OK";

echo "\n\t- create with invalid model";
$test->createWithInvalidModel();
echo " - OK";

echo "\n\t- get unknown id";
$test->getUnknownId();
echo " - OK";

$test->tearDown();
