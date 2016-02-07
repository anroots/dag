<?php
namespace App\Contracts\Organization;

/**
 * Query a database for all relations of a given company name
 */
interface RelationQuery
{
    /**
     * @param string $name The name of the company
     * @return array
     */
    public function getRelatedByName(string $name):array;
}
