<?php

namespace Knuckles\Scribe\Tests\Extracting\Strategies\Responses;

use Knuckles\Scribe\Extracting\Strategies\Responses\UseTransformerTags;
use Knuckles\Scribe\ScribeServiceProvider;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Mpociot\Reflection\DocBlock\Tag;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Orchestra\Testbench\TestCase;

class UseTransformerTagsTest extends TestCase
{
    use ArraySubsetAsserts;

    protected function getPackageProviders($app)
    {
        return [
            ScribeServiceProvider::class,
        ];
    }

    /**
     * @param $serializer
     * @param $expected
     *
     * @test
     * @dataProvider dataResources
     */
    public function can_parse_transformer_tag($serializer, $expected)
    {
        $config = new DocumentationConfig(['fractal' => ['serializer' => $serializer]]);

        $strategy = new UseTransformerTags($config);
        $tags = [
            new Tag('transformer', '\Knuckles\Scribe\Tests\Fixtures\TestTransformer'),
        ];
        $results = $strategy->getTransformerResponse($tags);

        $this->assertArraySubset([
            [
                'status' => 200,
                'content' => $expected,
            ],
        ], $results);
    }

    /** @test */
    public function can_parse_transformer_tag_with_model()
    {
        $strategy = new UseTransformerTags(new DocumentationConfig([]));
        $tags = [
            new Tag('transformer', '\Knuckles\Scribe\Tests\Fixtures\TestTransformer'),
            new Tag('transformermodel', '\Knuckles\Scribe\Tests\Fixtures\TestModel'),
        ];
        $results = $strategy->getTransformerResponse($tags);

        $this->assertArraySubset([
            [
                'status' => 200,
                'content' => '{"data":{"id":1,"description":"Welcome on this test versions","name":"TestName"}}',
            ],
        ], $results);
    }

    /** @test */
    public function can_parse_transformer_tag_with_status_code()
    {
        $strategy = new UseTransformerTags(new DocumentationConfig([]));
        $tags = [
            new Tag('transformer', '201 \Knuckles\Scribe\Tests\Fixtures\TestTransformer'),
        ];
        $results = $strategy->getTransformerResponse($tags);

        $this->assertArraySubset([
            [
                'status' => 201,
                'content' => '{"data":{"id":1,"description":"Welcome on this test versions","name":"TestName"}}',
            ],
        ], $results);

    }

    /** @test */
    public function can_parse_transformercollection_tag()
    {
        $strategy = new UseTransformerTags(new DocumentationConfig([]));
        $tags = [
            new Tag('transformercollection', '\Knuckles\Scribe\Tests\Fixtures\TestTransformer'),
        ];
        $results = $strategy->getTransformerResponse($tags);

        $this->assertArraySubset([
            [
                'status' => 200,
                'content' => '{"data":[{"id":1,"description":"Welcome on this test versions","name":"TestName"},' .
                    '{"id":1,"description":"Welcome on this test versions","name":"TestName"}]}',
            ],
        ], $results);

    }

    /** @test */
    public function can_parse_transformercollection_tag_with_model()
    {

        $strategy = new UseTransformerTags(new DocumentationConfig([]));
        $tags = [
            new Tag('transformercollection', '\Knuckles\Scribe\Tests\Fixtures\TestTransformer'),
            new Tag('transformermodel', '\Knuckles\Scribe\Tests\Fixtures\TestModel'),
        ];
        $results = $strategy->getTransformerResponse($tags);

        $this->assertArraySubset([
            [
                'status' => 200,
                'content' => '{"data":[{"id":1,"description":"Welcome on this test versions","name":"TestName"},' .
                    '{"id":1,"description":"Welcome on this test versions","name":"TestName"}]}',
            ],
        ], $results);
    }


    public function dataResources()
    {
        return [
            [
                null,
                '{"data":{"id":1,"description":"Welcome on this test versions","name":"TestName"}}',
            ],
            [
                'League\Fractal\Serializer\JsonApiSerializer',
                '{"data":{"type":null,"id":"1","attributes":{"description":"Welcome on this test versions","name":"TestName"}}}',
            ],
        ];
    }
}
