<?php

namespace App\Http\Controllers;

use App\Contracts\Organization\RelationInserter;
use App\Contracts\Organization\RelationQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrganizationsController extends Controller
{

    /**
     * Return relations to the specified company
     *
     * @param Request $request
     * @param RelationQuery $query
     * @return array
     */
    public function show(Request $request, RelationQuery $query):array
    {
        $this->validate($request, ['name' => 'required|max:255']);

        return $query->getRelatedByName($request->input('name'));
    }

    /**
     * Delete everything from the database
     *
     * @return Response
     */
    public function truncate()
    {
        DB::table('relations')->delete();
        DB::table('organizations')->delete();

        return response('', 204);
    }

    /**
     * Insert new organizations to the database
     *
     * @param Request $request
     * @param RelationInserter $inserter
     * @return Response
     */
    public function store(Request $request, RelationInserter $inserter)
    {

        // Check for valid / not empty request body
        if (!$request->json()->all()) {
            return response('', 400);
        }

        $inserter->insert($request->json()->all());

        return response('', 201);
    }
}
