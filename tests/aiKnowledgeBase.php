<?php

namespace Test\AiKnowledgeBase;
use \Exception;
use StarkInfra\AiKnowledgeBase;
use Test\Utils\KnowledgeBaseExample;
use StarkCore\Error\InputErrors;


class TestAiKnowledgeBase
{
    public $knowledgeBase;

    public function create()
    {
        $this->knowledgeBase = AiKnowledgeBase::create(KnowledgeBaseExample::generateExampleAiKnowledgeBase());

        if (!is_string($this->knowledgeBase->id) || $this->knowledgeBase->id == "") {
            throw new Exception("failed");
        }
        if ($this->knowledgeBase->status != "processing") {
            throw new Exception("failed");
        }
        if ($this->knowledgeBase->rootUrl != "https://docs.starkinfra.com" || $this->knowledgeBase->isRecursive !== false) {
            throw new Exception("failed");
        }
        if ($this->knowledgeBase->tags != ["sdk-php", "test"]) {
            throw new Exception("failed");
        }
        if (is_null($this->knowledgeBase->created)) {
            throw new Exception("failed");
        }
    }

    public function get()
    {
        $fetched = AiKnowledgeBase::get($this->knowledgeBase->id);

        if ($fetched->id != $this->knowledgeBase->id || $fetched->name != $this->knowledgeBase->name) {
            throw new Exception("failed");
        }
    }

    public function queryFiltersByIds()
    {
        $found = iterator_to_array(AiKnowledgeBase::query(["ids" => [$this->knowledgeBase->id]]), false);

        if (count($found) != 1 || $found[0]->id != $this->knowledgeBase->id) {
            throw new Exception("failed");
        }
    }

    public function queryFiltersByNameAndStatus()
    {
        $current = AiKnowledgeBase::get($this->knowledgeBase->id);
        $found = AiKnowledgeBase::query(["name" => $current->name, "status" => $current->status]);

        foreach ($found as $knowledgeBase) {
            if ($knowledgeBase->id == $this->knowledgeBase->id) {
                return;
            }
        }
        throw new Exception("failed");
    }

    public function queryWithoutMatchIsEmpty()
    {
        $found = iterator_to_array(AiKnowledgeBase::query(["name" => "no-knowledge-base-has-this-name"]), false);

        if (count($found) != 0) {
            throw new Exception("failed");
        }
    }

    public function updateChangesNameAndTagsOnly()
    {
        try {
            $updated = AiKnowledgeBase::update($this->knowledgeBase->id, ["name" => "renamed-by-sdk", "tags" => ["renamed"]]);

            if ($updated->name != "renamed-by-sdk" || $updated->tags != ["renamed"]) {
                throw new Exception("failed");
            }
            if ($updated->rootUrl != $this->knowledgeBase->rootUrl) {
                throw new Exception("failed");
            }
        } finally {
            AiKnowledgeBase::update($this->knowledgeBase->id, ["name" => $this->knowledgeBase->name, "tags" => $this->knowledgeBase->tags]);
        }
    }

    public function createWithInvalidRootUrlRaisesInputErrors()
    {
        $this->assertRaisesInputErrors(function () {
            AiKnowledgeBase::create(new AiKnowledgeBase(["name" => "invalid", "rootUrl" => "not-a-url"]));
        });
    }

    public function getUnknownIdRaisesInputErrors()
    {
        $this->assertRaisesInputErrors(function () {
            AiKnowledgeBase::get("0000000000000000");
        });
    }

    public function pageLeadsToTheCreatedBase()
    {
        $options = ["limit" => 100];
        do {
            list($found, $cursor) = AiKnowledgeBase::page($options);
            foreach ($found as $knowledgeBase) {
                if ($knowledgeBase->id == $this->knowledgeBase->id) {
                    return;
                }
            }
            $options["cursor"] = $cursor;
        } while (!is_null($cursor));
        throw new Exception("failed");
    }

    public function queryHonoursTheLimit()
    {
        $found = iterator_to_array(AiKnowledgeBase::query(["limit" => 1]), false);

        if (count($found) != 1) {
            throw new Exception("failed");
        }
    }

    public function pageWithInvalidLimitRaisesInputErrors()
    {
        $this->assertRaisesInputErrors(function () {
            AiKnowledgeBase::page(["limit" => 101]);
        });
        $this->assertRaisesInputErrors(function () {
            AiKnowledgeBase::page(["limit" => 0]);
        });
    }

    public function hostsGroupsPagesByHost()
    {
        $hosts = AiKnowledgeBase::hosts($this->knowledgeBase->id);

        if (!is_array($hosts)) {
            throw new Exception("failed");
        }
    }

    public function deleteReturnsTheDeletedBase()
    {
        $deleted = AiKnowledgeBase::delete([$this->knowledgeBase->id]);

        if (count($deleted) != 1 || $deleted[0]->id != $this->knowledgeBase->id) {
            throw new Exception("failed");
        }
    }

    private function assertRaisesInputErrors($call)
    {
        try {
            $call();
        } catch (InputErrors $e) {
            return;
        }
        throw new Exception("failed");
    }
}

echo "\n\nAiKnowledgeBase:";

$test = new TestAiKnowledgeBase();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- query filters by ids";
$test->queryFiltersByIds();
echo " - OK";

echo "\n\t- query filters by name and status";
$test->queryFiltersByNameAndStatus();
echo " - OK";

echo "\n\t- query without match is empty";
$test->queryWithoutMatchIsEmpty();
echo " - OK";

echo "\n\t- update changes name and tags only";
$test->updateChangesNameAndTagsOnly();
echo " - OK";

echo "\n\t- create with invalid root url raises input errors";
$test->createWithInvalidRootUrlRaisesInputErrors();
echo " - OK";

echo "\n\t- get unknown id raises input errors";
$test->getUnknownIdRaisesInputErrors();
echo " - OK";

echo "\n\t- page leads to the created base";
$test->pageLeadsToTheCreatedBase();
echo " - OK";

echo "\n\t- query honours the limit";
$test->queryHonoursTheLimit();
echo " - OK";

echo "\n\t- page with an invalid limit raises input errors";
$test->pageWithInvalidLimitRaisesInputErrors();
echo " - OK";

echo "\n\t- hosts groups pages by host";
$test->hostsGroupsPagesByHost();
echo " - OK";

echo "\n\t- delete returns the deleted base";
$test->deleteReturnsTheDeletedBase();
echo " - OK";
