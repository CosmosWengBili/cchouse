<?php

namespace App\Http\Controllers;

use App\Key;
use App\KeyRequest;
use App\TenantContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;
use Illuminate\Support\Facades\DB;

class KeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $owner_data = new NestedRelationResponser();
        $columns = array_map(function ($column) { return "keys.{$column}"; }, $this->whitelist('keys'));
        $selectColumns = array_merge($columns, Key::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));
        $responseData
            ->index(
                'keys',
                $this->limitRecords(
                    Key::withExtraInfo()->select($selectStr)->with($request->withNested)
                )
            )
            ->relations($request->withNested);

        $owner_query = Key::withExtraInfo()->select($selectStr)->where('keeper_id', Auth::id());
        $owner_data
            ->index('keys',
                $this->limitRecords($owner_query->with($request->withNested))
            )
            ->relations($request->withNested);

        $unapproved_key = $this->limitRecords(
            KeyRequest::whereIn('key_id', $owner_query->pluck('id')),
            false
        )
            ->denied()
            ->pluck('key_id')
            ->toArray();;


        return view('keys.index', $responseData->get())
            ->with('owner_data', $owner_data->get())
            ->with('unapproved_key', $unapproved_key);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $responseData = new FormDataResponser();
        $data = $responseData->create(Key::class, 'keys.store')->get();

        return view('keys.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'keeper_id' => 'required|exists:users,id',
            'key_name' => 'required|max:255'
        ]);

        $key = key::create($validatedData);
        return redirect($request->_redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Key $key)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($key->load($request->withNested))
            ->relations($request->withNested);

        return view('keys.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function edit(Key $key)
    {
        $responseData = new FormDataResponser();
        return view(
            'keys.form',
            $responseData->edit($key, 'keys.update')->get()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Key $key)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'keeper_id' => 'required|exists:users,id',
            'key_name' => 'required|max:255'
        ]);

        $key->update($validatedData);
        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Key  $key
     * @return \Illuminate\Http\Response
     */
    public function destroy(Key $key)
    {
        $key->delete();
        return response()->json(true);
    }
}
