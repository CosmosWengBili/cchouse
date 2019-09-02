@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        系統變數
                    </div>
                    <form action="{{ route('system_variables.update', ['group' => $group]) }}" method="POST">
                        @csrf
                        @method('put')
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>名稱</th>
                                <th>Code</th>
                                <th>值</th>
                                <th>Order</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($defaultVariables as $defaultVariable)
                                @php
                                    $name = $defaultVariable['name'];
                                    $code = $defaultVariable['code'];
                                    $defaultOrder = $defaultVariable['order'];
                                    $defaultValue = $defaultVariable['defaultValue'];
                                @endphp
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $code }}</td>
                                    <td>
                                        @if($defaultVariable['type'] == 'boolean')
                                            {{-- unchecked value for checkbox--}}
                                            <input type="hidden" value="0" name="system_variables[{{ $code }}][value]" />
                                            <input
                                                type="checkbox"
                                                name="system_variables[{{ $code }}][value]"
                                                value="1"
                                                {{ ($codeToValue[$code] ?? $defaultValue) ? 'checked' : '' }}
                                            />
                                        @else
                                            <input
                                                class="form-control form-control-sm"
                                                type="text"
                                                name="system_variables[{{ $code }}][value]"
                                                value="{{ isset($codeToValue[$code]) ? $codeToValue[$code] : $defaultValue}}"
                                            />
                                        @endif
                                    </td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="system_variables[{{ $code }}][order]"
                                            value="{{ isset($codeToOrder[$code]) ? $codeToOrder[$code] : $defaultOrder}}"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <button class="mt-5 btn btn-success" type="submit">更新</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
