<?php
namespace App\Test\Http\Controllers;

use App\Services\Organization\Query;
use App\Test\TestCase;
use Illuminate\Support\Facades\DB;

/**
 * @coversDefaultClass App\Http\Controllers\OrganizationsController
 */
class OrganizationsControllerTest extends TestCase
{

    /**
     * @covers ::store
     */
    public function testStoreAppendsNotOverwrites()
    {
        $this->delete('/organizations');

        $this->json('POST', 'organization', [['org_name' => 'Yellow Banana']])
            ->seeStatusCode(201);

        $this->json('POST', 'organization', [['org_name' => 'Brown Banana']])
            ->seeStatusCode(201);

        $this->seeInDatabase('organizations', ['name' => 'Yellow Banana']);
        $this->seeInDatabase('organizations', ['name' => 'Brown Banana']);
    }

    /**
     * @covers ::truncate
     */
    public function testTruncateDeletesAllData()
    {
        $this->assertNotEmpty(DB::table('organizations')->count());
        $this->assertNotEmpty(DB::table('relations')->count());

        $this->delete('/organizations');

        $this->assertEmpty(DB::table('organizations')->count());
        $this->assertEmpty(DB::table('relations')->count());
    }

    /**
     * @covers ::store
     */
    public function testStoreSavesOrganizationsAndRelations()
    {

        $this->delete('/organizations');
        $this->json('POST', 'organization', $this->getExampleInputGraph())
            ->seeStatusCode(201);


        $this->assertEquals(10, DB::table('relations')->count());
        $this->assertEquals(8, DB::table('organizations')->count());
    }

    /**
     * @covers ::show
     */
    public function testShowSortsResultsAlphabetically()
    {
        $this->delete('/organizations');

        $graph = [
            [
                'org_name' => 'Banana Island',
                'daughters' => [
                    ['org_name' => 'Fishy Banana'],
                    ['org_name' => 'Able Banana'],
                    ['org_name' => 'Angry Banana']
                ]
            ]
        ];

        $this->json('POST', 'organization', $graph)
            ->seeStatusCode(201);

        $this->json('GET', route('organization.show', ['name' => 'Banana Island']))
            ->seeJsonEquals([
                ['org_name' => 'Able Banana', 'relationship_type' => 'daughter'],
                ['org_name' => 'Angry Banana', 'relationship_type' => 'daughter'],
                ['org_name' => 'Fishy Banana', 'relationship_type' => 'daughter']
            ]);
    }

    /**
     * @covers ::show
     */
    public function testShowReturnsOrganizationRelations()
    {
        $expected = [
            ['org_name' => 'Banana Tree', 'relationship_type' => 'parent'],
            ['org_name' => 'Big Banana Tree', 'relationship_type' => 'parent'],
            ['org_name' => 'Brown Banana', 'relationship_type' => 'sister'],
            ['org_name' => 'Green Banana', 'relationship_type' => 'sister'],
            ['org_name' => 'Phoneutria Spider', 'relationship_type' => 'daughter'],
            ['org_name' => 'Yellow Banana', 'relationship_type' => 'sister'],
        ];

        $this->json('GET', route('organization.show', ['name' => 'Black Banana']))
            ->seeJsonEquals($expected);
    }

    /**
     * @covers ::show
     */
    public function testShowThrows404IfOrganizationDoesNotExist()
    {
        $this->json('GET', route('organization.show', ['name' => 'Purple Banana']))->seeStatusCode(404);
    }

    /**
     * @covers ::show
     */
    public function testShowPaginatesRecords()
    {

        $this->delete('/organizations');
        $this->json('POST', 'organization', $this->getPaginatedGraph())
            ->seeStatusCode(201);

        $expectedResultCounts = [
            Query::MAX_RECORDS_PER_PAGE,
            Query::MAX_RECORDS_PER_PAGE,
            Query::MAX_RECORDS_PER_PAGE,
            1,
            0
        ];

        foreach ($expectedResultCounts as $page => $expectedCount) {

            $url = route('organization.show', ['name' => 'Paradise Island', 'page' => $page + 1]);

            $this->assertCount(
                $expectedCount,
                $this->json('GET', $url)->decodeResponseJson()
            );
        }
    }

    /**
     * @covers ::show
     * @group debug
     */
    public function testShowRequiresNameParameter()
    {

        $this->json('GET', route('organization.show'))
            ->shouldReturnJson(['The name field is required.'])
            ->seeStatusCode(422);
    }

    /**
     * @return array
     */
    protected function getExampleInputGraph():array
    {
        return [
            [
                'org_name' => 'Paradise Island',
                'daughters' => [
                    [
                        'org_name' => 'Banana Tree',
                        'daughters' => [
                            [
                                'org_name' => 'Yellow Banana',
                            ],
                            [
                                'org_name' => 'Brown Banana',
                            ],
                            [
                                'org_name' => 'Black Banana',
                            ],
                        ]
                    ],
                    [
                        'org_name' => 'Big Banana Tree',
                        'daughters' => [
                            [
                                'org_name' => 'Yellow Banana',
                            ],
                            [
                                'org_name' => 'Brown Banana',
                            ],
                            [
                                'org_name' => 'Green Banana',
                            ],
                            [
                                'org_name' => 'Black Banana',
                                'daughters' => [
                                    [
                                        'org_name' => 'Phoneutria Spider'
                                    ]
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    private function getPaginatedGraph() : array
    {
        $daughters = array_map(function () {
            return ['org_name' => $this->faker->unique()->company];
        }, range(0, Query::MAX_RECORDS_PER_PAGE * 3));

        $graph = [
            [
                'org_name' => 'Paradise Island',
                'daughters' => $daughters

            ]
        ];

        return $graph;
    }
}
