<?php

namespace App\Services\Organization;

use App\Contracts\Organization\RelationInserter;
use App\Db\Organization;
use Illuminate\Support\Facades\DB;

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

    public function insert(array $rows)
    {
        foreach ($rows as $row) {
            $this->insertRow($row);
        }
    }

    /**
     * @param array $row
     * @param int|null $parent
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
     * @param $parent
     * @param $organizationId
     * @return bool
     */
    private function hasParent(int $organizationId, int $parent = null): bool
    {
        return $parent !== null && !$this->organization->hasChild($parent, $organizationId);
    }

    /**
     * @param $parent
     * @param $organizationId
     */
    private function insertRelation($parent, $organizationId)
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
