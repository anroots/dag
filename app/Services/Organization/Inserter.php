<?php

namespace App\Services\Organization;

use App\Contracts\Organization\RelationInserter;
use App\Db\Organization;
use Illuminate\Support\Facades\DB;

/**
 * {@inheritdoc}
 */
class Inserter implements RelationInserter
{
    /**
     * @var Organization
     */
    private $organization;

    /**
     * @param Organization $organization
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $rows)
    {

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->insertRow($row);
            }
        });
    }

    /**
     * Recursive function to insert a company and its daughter relations
     *
     * @param array $row The name of the company plus any (recursive) daughters it might have
     *                   Example: ['org_name' => 'Big', 'daughters' => ['org_name' => 'Small']]
     * @param int|null $parent The ID of the row's parent node
     */
    private function insertRow(array $row, int $parent = null)
    {

        $organizationName = $row['org_name'] ?? null;

        if ($organizationName === null) {
            return;
        }

        $organizationId = $this->insertOrFetch($organizationName);

        if ($this->hasParent($organizationId, $parent)) {
            $this->insertRelation($parent, $organizationId);
        }

        if ($this->hasSiblings($row)) {
            foreach ($row['daughters'] as $rowItem) {
                $this->insertRow($rowItem, $organizationId);
            }
        }

    }

    /**
     * Return organization ID by its name. If the organization does not exist, create it
     *
     * @param string $organizationName
     * @return int The ID of the organization
     */
    private function insertOrFetch(string $organizationName) : int
    {
        $organization = $this->organization->getByName($organizationName);

        if ($organization !== null) {
            return $organization->id;
        }

        return DB::table('organizations')->insertGetId(['name' => $organizationName]);
    }

    /**
     * Check if the organization has a parent with the specified ID
     *
     * @param int $parentId
     * @param int $organizationId
     * @return bool
     */
    private function hasParent(int $organizationId, int $parentId = null): bool
    {
        return $parentId !== null && !$this->organization->hasChild($parentId, $organizationId);
    }

    /**
     * Connect two organizations with a parent-child relation
     *
     * @param int $parentId
     * @param int $childId
     */
    private function insertRelation(int $parentId, int $childId)
    {
        DB::table('relations')->insert(['head' => $parentId, 'tail' => $childId]);
    }

    /**
     * @param array $row
     * @return bool True if the input row has sibling rows
     */
    private function hasSiblings(array $row)
    {
        return array_key_exists('daughters', $row);
    }
}
