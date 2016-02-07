<?php

namespace App\Services\Organization;

use App\Contracts\Organization\RelationQuery;
use App\Db\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * {@inheritdoc}
 */
class Query implements RelationQuery
{
    /**
     * Maximum number of results to show on one 'page' of the response
     */
    const MAX_RECORDS_PER_PAGE = 100;

    /**
     * @var Organization
     */
    protected $organization;

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
    public function getRelatedByName(string $name):array
    {
        $organization = $this->organization->getByName($name);

        if ($organization === null) {
            throw new ModelNotFoundException;
        }

        return $this->getRelated($organization->id);
    }

    /**
     * Get a list of related organizations by an organization ID
     *
     * @param int $organizationId
     * @return array Example: [['org_name' => 'Big', 'relationship_type' => 'sister']]
     */
    protected function getRelated(int $organizationId):array
    {

        $parents = $this->organization->getParentIds($organizationId);
        $children = $this->organization->getChildIds($organizationId);
        $sisters = $this->organization->getSisterIds($organizationId);

        $organizations = DB::table('organizations')
            ->whereIn('id', array_merge($parents, $children, $sisters))
            ->orderBy('name')
            ->paginate(self::MAX_RECORDS_PER_PAGE);

        $relatedOrganizations = [];
        foreach ($organizations as $organization) {
            if (in_array($organization->id, $parents)) {
                $relation = 'parent';
            } elseif (in_array($organization->id, $children)) {
                $relation = 'daughter';
            } else {
                $relation = 'sister';
            }

            $relatedOrganizations[] = [
                'org_name' => $organization->name,
                'relationship_type' => $relation
            ];
        }

        return $relatedOrganizations;
    }
}
