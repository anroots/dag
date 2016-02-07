<?php

namespace App\Services\Organization;

use App\Contracts\Organization\RelationQuery;
use App\Db\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class Query implements RelationQuery
{
    const MAX_RECORDS_PER_PAGE = 100;

    /**
     * @var Organization
     */
    protected $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getRelatedByName(string $name):array
    {
        $organization = $this->organization->getByName($name);

        if ($organization === null) {
            throw new ModelNotFoundException;
        }

        return $this->getRelated($organization->id);
    }

    protected function getRelated(int $organizationId):array
    {

        $parents = $this->organization->getParentIds($organizationId);
        $children = $this->organization->getChildIds($organizationId);
        $sisters = $this->organization->getSisterIds($organizationId);

        $organizations = DB::table('organizations')
            ->whereIn('id', array_merge($parents, $children, $sisters))
            ->orderBy('name')
            ->paginate(self::MAX_RECORDS_PER_PAGE);

        $result = [];
        foreach ($organizations as $organization) {
            if (in_array($organization->id, $parents)) {
                $type = 'parent';
            } elseif (in_array($organization->id, $children)) {
                $type = 'daughter';
            } else {
                $type = 'sister';
            }

            $result[] = [
                'org_name' => $organization->name,
                'relationship_type' => $type
            ];
        }

        return $result;
    }
}
