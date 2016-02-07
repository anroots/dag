<?php

namespace App\Test\Services;

use App\Db\Organization;
use App\Services\Organization\Query;
use App\Test\TestCase;

/**
 * @coversDefaultClass App\Services\Organization\Query
 */
class OrganizationQueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $organizationQuery;

    public function setUp()
    {
        parent::setUp();
        $this->organizationQuery = new Query(new Organization);
    }

    /**
     * @return array
     */
    public function provideRelatedOrganizationIds():array
    {
        return [
            [
                'Paradise Island',
                [
                    ['relationship_type' => 'daughter', 'org_name' => 'Banana Tree'],
                    ['relationship_type' => 'daughter', 'org_name' => 'Big Banana Tree']
                ]
            ],
            ['Phoneutria Spider', [['relationship_type' => 'parent', 'org_name' => 'Black Banana']]],
            [
                'Black Banana',
                [
                    ['org_name' => 'Banana Tree', 'relationship_type' => 'parent'],
                    ['org_name' => 'Big Banana Tree', 'relationship_type' => 'parent'],
                    ['org_name' => 'Brown Banana', 'relationship_type' => 'sister'],
                    ['org_name' => 'Green Banana', 'relationship_type' => 'sister'],
                    ['org_name' => 'Phoneutria Spider', 'relationship_type' => 'daughter'],
                    ['org_name' => 'Yellow Banana', 'relationship_type' => 'sister'],
                ]
            ]
        ];
    }

    /**
     * @covers ::getRelated
     * @dataProvider provideRelatedOrganizationIds
     * @param string $organizationName
     * @param array $expected
     */
    public function testGetRelatedReturnsCorrectValues(string $organizationName, array $expected)
    {
        $relatedOrganizations = $this->organizationQuery->getRelatedByName($organizationName);
        $this->assertEquals($expected, $relatedOrganizations);
    }
}
