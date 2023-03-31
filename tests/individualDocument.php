<?php

namespace Test\IndividualDocument;
use \Exception;
use StarkInfra\IndividualDocument;
use StarkInfra\IndividualIdentity;

class TestIndividualDocument
{
    public function create()
    {

        $identity = IndividualIdentity::create([TestIndividualDocument::exampleIdentity()])[0];
        if (is_null($identity->id)) {
            throw new Exception("failed");
        }

        $individualDocument = IndividualDocument::create([TestIndividualDocument::exampleDocumentFront($identity->id)]);

        if (is_null($individualDocument[0]->id)) {
            throw new Exception("failed");
        }

        $individualDocument = IndividualDocument::create([TestIndividualDocument::exampleDocumentBack($identity->id)]);

        if (is_null($individualDocument[0]->id)) {
            throw new Exception("failed");
        }

        $individualDocument = IndividualDocument::create([TestIndividualDocument::exampleDocumentSelfie($identity->id)]);

        if (is_null($individualDocument[0]->id)) {
            throw new Exception("failed");
        }

        $individual = IndividualIdentity::update($identity->id, "processing");

        if ($individual->status != "processing") {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $individualDocuments = iterator_to_array(IndividualDocument::query(["limit" => 1]));
        
        if (count($individualDocuments) != 1) {
            throw new Exception("failed");
        }

        $individualDocument = IndividualDocument::get($individualDocuments[0]->id);

        if ($individualDocuments[0]->id != $individualDocument->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IndividualDocument::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $individualDocument) {
                if (in_array($individualDocument->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $individualDocument->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function exampleDocumentFront($id)
    {
        $params = [
            "content" => file_get_contents('tests/utils/identity/identity-front-face.png'),
            "contentType" => "image/png",
            "type" => "identity-front",
            "identityId" => $id,
        ];
        return new IndividualDocument($params);
    }

    public function exampleDocumentBack($id)
    {
        $params = [
            "content" => file_get_contents('tests/utils/identity/identity-back-face.png'),
            "contentType" => "image/png",
            "type" => "identity-back",
            "identityId" => $id,
        ];
        return new IndividualDocument($params);
    }

    public function exampleDocumentSelfie($id)
    {
        $params = [
            "content" => file_get_contents('tests/utils/identity/walter-white.png'),
            "contentType" => "image/png",
            "type" => "selfie",
            "identityId" => $id,
        ];
        return new IndividualDocument($params);
    }

    public function exampleIdentity()
    {
        $params = [
            "name" => "Tony Stark",
            "tags" => [
                'War supply',
                'Invoice #1234'
            ],
            "taxId" => "012.345.678-90",
        ];
        return new IndividualIdentity($params);
    }
}

echo "\n\nIndividualDocument:";

$test = new TestIndividualDocument();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
