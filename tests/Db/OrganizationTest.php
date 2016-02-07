<?php

namespace App\Test\Db;

use App\Test\TestCase;

/**
 * @coversDefaultClass \App\Db\Organization
 */
class OrganizationTest extends TestCase
{

    /**
     * @return array
     */
    public function provideParentRelations():array
    {
        return [
            [7, [2, 3]],
            [1, []],
            [3, [1]],
            [4, [2, 3]],
            [5, [2, 3]]
        ];
    }

    /**
     * @covers ::getParentIds
     * @dataProvider provideParentRelations
     * @param int $organizationId
     * @param array $expectedParents
     */
    public function testGetParentIdsReturnsCorrectValues(int $organizationId, array $expectedParents)
    {
        $actualParents = $this->organization->getParentIds($organizationId);
        $this->assertEquals($expectedParents, $actualParents);
    }

    /**
     * @return array
     */
    public function provideChildRelations():array
    {
        return [
            [7, [8]],
            [1, [2, 3]],
            [2, [4, 5, 7]]
        ];
    }

    /**
     * @covers ::getChildIds
     * @dataProvider provideChildRelations
     * @param int $organizationId
     * @param array $expectedChildren
     */
    public function testGetChildIdsReturnsCorrectValues(int $organizationId, array $expectedChildren)
    {
        $actualChildren = $this->organization->getChildIds($organizationId);
        $this->assertEquals($expectedChildren, $actualChildren);
    }

    /**
     * @return array
     */
    public function provideSisterRelations():array
    {
        return [
            [2, [3]],
            [7, [4, 5, 6]],
            [1, []],
            [6, [4, 5, 7]]
        ];
    }

    /**
     * @covers ::getSisterIds
     * @dataProvider provideSisterRelations
     * @param int $organizationId
     * @param array $expectedSisters
     */
    public function testGetSisterIdsReturnsCorrectValues(int $organizationId, array $expectedSisters)
    {
        $actualSisters = $this->organization->getSisterIds($organizationId);
        $this->assertEquals($expectedSisters, $actualSisters);
    }

    /**
     * @covers ::getByName
     */
    public function testGetByNameReturnsCorrectDbRecord()
    {
        $this->assertEquals(7, $this->organization->getByName('Black Banana')->id);
    }
}
