<?php

namespace App\Http\Controllers;

use App\KeyRequest;
use App\Key;

use App\Services\NotificationService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;

class KeyRequestController extends Controller
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
        $responseData
            ->index(
                'key_requests',
                KeyRequest::select($this->whitelist('key_requests'))
                    ->with($request->withNested)
                    ->get()
            )
            ->relations($request->withNested);

        return view('keyRequests.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $key_id = Input::get('key_id');
        @$keeper_id = Key::find($key_id)->keeper_id;
        $responseData = new FormDataResponser();
        $data = $responseData
            ->create(KeyRequest::class, 'keyRequests.store')
            ->get();

        return view('key_requests.form', $data)
            ->with('key_id', $key_id)
            ->with('keeper_id', $keeper_id);
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
            'request_user_id' => 'required|exists:users,id',
            'key_id' => 'required|exists:keys,id',
            'request_date' => 'required|max:255',
            'status' => 'required',
            'request_approved' => 'nullable'
        ]);

        $key_requests = KeyRequest::create($validatedData);
        $route = 'keys/' . $request->key_id . '?with=room;keeper;keyRequests';

        return redirect($route);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KeyRequest  $key_request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $key_request = KeyRequest::find($id);
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($key_request->load($request->withNested))
            ->relations($request->withNested);

        return view('key_requests.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KeyRequest  $key_requests
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $key_request = KeyRequest::find($id);
        @$keeper_id = Key::find($key_request->key_id)->keeper_id;
        $responseData = new FormDataResponser();
        return view(
            'key_requests.form',
            $responseData->edit($key_request, 'keyRequests.update')->get()
        )->with('keeper_id', $keeper_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KeyRequest  $key_requests
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $key_request = KeyRequest::find($id);

        $using = $key_request->status == 'using' ? true : false;

        $validatedData = $request->validate([
            'request_user_id' => 'required|exists:users,id',
            'key_id' => 'required|exists:keys,id',
            'request_date' => 'required|max:255',
            'status' => 'required',
            'request_approved' => 'required'
        ]);

        $key_request->update($validatedData);
        if ($using && $validatedData['status'] == "finished") {
            NotificationService::notifyKeyRequestFinished(
                $validatedData['key_id']
            );
        }

        $route =
            'keys/' . $key_request->key_id . '?with=room;keeper;keyRequests';

        return redirect($route);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KeyRequest  $key_requests
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $key_request = KeyRequest::find($id);
        $key_request->delete();
        return response()->json(true);
    }
}
