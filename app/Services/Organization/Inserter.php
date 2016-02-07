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
        foreach ($rows as $row) {
            $this->insertRow($row);
        }
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

        $organizationId = $this->insertOrganization($organizationName);

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
     * @param string $orgName
     * @return int
     */
    private function insertOrganization(string $orgName) : int
    {
        $organization = $this->organization->getByName($orgName);

        if ($organization !== null) {
            return $organization->id;
        }

        return DB::table('organizations')->insertGetId(['name' => $orgName]);
    }

    /**
     * @param int $parent
     * @param int $organizationId
     * @return bool
     */
    private function hasParent(int $organizationId, int $parent = null): bool
    {
        return $parent !== null && !$this->organization->hasChild($parent, $organizationId);
    }

    /**
     * @param int $parent
     * @param int $organizationId
     */
    private function insertRelation(int $parent, int $organizationId)
    {
        DB::table('relations')->insert(['head' => $parent, 'tail' => $organizationId]);
    }

    /**
     * @param array $row
     * @return bool
     */
    private function hasSiblings(array $row)
    {
        return array_key_exists('daughters', $row);
    }
}
