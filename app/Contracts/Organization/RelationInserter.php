<?php
namespace App\Contracts\Organization;

/**
 * Insert companies and relations to the database
 *
 * A collection of rows is given to the inserter class. The collection models a directed graph where companies
 * are identified as vertexes with edges modelled as sub-arrays ('daughters'). The inserter is responsible
 * for figuring out how to store this to the database.
 */
interface RelationInserter
{

    /**
     * @param array $rows Array of companies with keys 'org_name' and 'daughters' where daughters can be a nested set.
     */
    public function insert(array $rows);
}
