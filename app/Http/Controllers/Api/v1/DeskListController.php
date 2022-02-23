<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeskListStoreRequest;
use App\Http\Resources\DeskListResource;
use App\Models\DeskList;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeskListController extends Controller
{

    public function index(Request $request)
    {
        return DeskListResource::collection(
            DeskList::orderBy('created_at', 'desc')
                ->where('desk_id', $request->desk_id)
                ->get()
        );

    }


    public function store(DeskListStoreRequest $request)
    {
        $crested_desk_list = DeskList::create($request->validated());

        return new DeskListResource($crested_desk_list);
    }


    public function show($id)
    {
        //
    }


    public function update(DeskListStoreRequest $request, DeskList $deskList)
    {
        $deskList->update($request->validated());

        return new DeskListResource($deskList);
    }


    public function destroy(DeskList $deskList)
    {
        $deskList->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

