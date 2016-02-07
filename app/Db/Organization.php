<?php

namespace App\Db;

use Illuminate\Support\Facades\DB;

/**
 * Database operations for an organization
 */
class Organization
{

    /**
     * @param string $name Company name
     * @return \stdClass|null Company record or null if not found
     */
    public function getByName(string $name)
    {
        return DB::table('organizations')
            ->where('name', $name)
            ->first();
    }

    /**
     * Check if `tail` is a child of `head` node
     *
     * @param int $head ID of the parent
     * @param int $tail ID of the child
     * @return bool True if an edge exists between the child and the parent
     */
    public function hasChild(int $head, int $tail):bool
    {
        return (bool)DB::table('relations')
            ->where('head', $head)
            ->where('tail', $tail)
            ->count();
    }

    /**
     * Get a list of organization ID-s that have a 'sister' relationship with the specified input company
     *
     * A 'sister' is the child of a parent node
     *
     * @param int|array $organizationId
     * @return array
     */
    public function getSisterIds($organizationId):array
    {
        $sisters = $this->getChildIds($this->getParentIds($organizationId));

        return array_values(array_filter(array_unique($sisters), function ($id) use ($organizationId) {
            return $organizationId != $id;
        }));
    }

    /**
     * Get a list of organization ID-s that are the children of the specified companies
     *
     * @param int|array $ids
     * @return array
     */
    public function getChildIds($ids):array
    {
        return DB::table('relations')
            ->select('tail')
            ->whereIn('head', collect($ids))
            ->lists('tail');
    }

    /**
     * Get a list of organization ID-s that are the parents for a given organization
     *
     * @param int|array $ids
     * @return array
     */
    public function getParentIds($ids):array
    {
        return DB::table('relations')
            ->select('head')
            ->whereIn('tail', collect($ids))
            ->lists('head');
    }
}
