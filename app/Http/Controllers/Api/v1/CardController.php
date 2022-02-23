<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Http\Resources\DeskResource;
use App\Models\Card;
use App\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function store(Request $request)
    {
        $new_desk = Card::create($request->all());
        return new CardResource($new_desk);
    }


    public function show(Card $card)
    {
        return new CardResource($card);

    }


    public function update(Request $request, Card $card)
    {
        $card->update($request->all());

        return new CardResource($card);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        $card->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
