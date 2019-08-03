<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Responser\NestedRelationResponser;
use App\Responser\FormDataResponser;


class UserController extends Controller
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
            ->index('Users', User::select($this->whitelist('users'))->with($request->withNested)->get())
            ->relations($request->withNested);

        return view('users.index', $responseData->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responseData = new FormDataResponser();
        return view('users.form', $responseData->create(User::class, 'users.store')->get());
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
            'name' => 'nullable|max:255',
            'mobile' => 'nullable',
            'email' => 'required',
            'password' => 'required' 
        ]);

        User::create($validatedData);

        return redirect('users');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($user->load($request->withNested))
            ->relations($request->withNested);

        return view('users.show', $responseData->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $responseData = new FormDataResponser();
        return view('users.form', $responseData->edit($user, 'users.update')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $input = $request->input();

        $request->validate([
            'name' => 'nullable|max:255',
            'mobile' => 'nullable',
            'email' => 'required',
        ]);

        $input['password'] = Hash::make($input['password']);
        $user->update($input);
        
        return redirect('/users/'.$user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(true);
    }
}
