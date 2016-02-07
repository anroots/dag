<?php
namespace App\Contracts\Organization;

interface RelationQuery
{
    public function getRelatedByName(string $name):array;
}
