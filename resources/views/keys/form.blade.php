@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        新建 / 編輯表單
                    </div>
                    <form action="{{$action}}" method="POST">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>@lang("model.Key.key_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="key_name"
                                        value="{{ isset($data["key_name"]) ? $data['key_name'] : '' }}"
                                    />
                                </td>
                            </tr>                          
                            <tr>
                                <td>@lang("model.Key.keeper_id")</td>
                                <td>
                                    <select 
                                        data-toggle="selectize" 
                                        data-table="users" 
                                        data-text="name" 
                                        data-value="id"
                                        data-selected="{{ isset($data["keeper_id"]) ? $data['keeper_id'] : '0' }}"
                                        name="keeper_id"
                                        class="form-control form-control-sm" 
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Key.room_id")</td>
                                <td>
                                    <select 
                                        data-toggle="selectize" 
                                        data-table="rooms" 
                                        data-text="room_code" 
                                        data-selected="{{ isset($data["room_id"]) ? $data['room_id'] : '0' }}"
                                        name="room_id"
                                        class="form-control form-control-sm" 
                                    >
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
