<?php

namespace App\Http\Controllers;

use App\Contracts\Organization\RelationInserter;
use App\Contracts\Organization\RelationQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class OrganizationsController extends Controller
{

    /**
     * @param Request $request
     * @param RelationQuery $query
     * @return array
     */
    public function show(Request $request, RelationQuery $query):array
    {
        $this->validate($request, ['name' => 'required']);

        return $query->getRelatedByName($request->input('name'));
    }

    /**
     * @return Response
     */
    public function truncate()
    {
        Artisan::call('migrate:refresh');

        return response('', 204);
    }

    /**
     * @param Request $request
     * @param RelationInserter $inserter
     * @return Response
     */
    public function store(Request $request, RelationInserter $inserter)
    {
        $inserter->insert($request->json()->all());

        return response('', 201);
    }
}
