<?php

namespace App\Db;

use Illuminate\Support\Facades\DB;

class Organization
{

    /**
     * @param string $name
     * @return \stdClass|null
     */
    public function getByName(string $name)
    {
        return DB::table('organizations')
            ->where('name', $name)
            ->first();
    }

    public function hasChild(int $head, int $tail):bool
    {
        return (bool)DB::table('relations')
            ->where('head', $head)
            ->where('tail', $tail)
            ->count();
    }

    /**
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
