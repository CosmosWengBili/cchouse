@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="my-3">Audit Log</h2>
            <form data-target="#audit-logs-table" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i id="add-query" class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>
            <div class="p-3" style="background-color: #fff; border-radius: 5px;" >
                    <table id="audit-logs-table" class="w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>操作人</th>
                            <th>事件</th>
                            <th>Model</th>
                            <th>Model ID</th>
                            <th>IP</th>
                            <th>操作時間</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $auditLog)
                            <tr>
                                <td>{{ $auditLog->id }}</td>
                                <td>{{ $auditLog->user->name }}</td>
                                <td>{{ $auditLog->event }}</td>
                                <td>{{ $auditLog->auditable_type }}</td>
                                <td>{{ $auditLog->auditable_id }}</td>
                                <td>{{ $auditLog->ip_address }}</td>
                                <td>{{ $auditLog->created_at }}</td>
                                <td>
                                    <a target="_blank" href="{{ route('audits.show', ['audit' => $auditLog]) }}" class="btn btn-info">
                                        詳細資料
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">沒有任何資料</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        renderDataTable(["#audit-logs-table"]);
    });
</script>
@endsection
