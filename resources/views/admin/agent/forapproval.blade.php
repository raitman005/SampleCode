@extends('admin.layouts.app')
@section('title', 'Agents')
@section('content')
<div class="container-fluid text-white">
    <div class="row">
        <div class="col-md-12">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has($msg))
                    <div class="alert alert-{{ $msg }}">{{ Session::get($msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
                @endif
            @endforeach
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="row  mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-2 text-white">For Approval Agents</h1>        
        </div>
        <div class="col-md-6 text-right">
            <a class="btn btn-secondary" href="{{ route('admin.agents') }}"><span class="fa fa-arrow-left"></span> Back to agents</a>
        </div>
    </div>

    <div class="card shadow mb-4 text-gray-800">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table agent-table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Agent</th>
                            <th>USQPROP Email</th>
                            <th>Gmail</th>
                            <th>Phone</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingAgents as $agent)
                            <tr>
                                <td class="text-center">{{ $agent->id }}</td>
                                <td>
                                    <strong style="font-size: 18px;">{{ $agent->firstname . " " . $agent->lastname }}</strong>
                                </td>
                                <td>{{ $agent->email }} </td>
                                <td>{{ $agent->gmail }} </td>
                                <td>{{ $agent->phone }} </td>
                                <td>{{ \Carbon\Carbon::parse($agent->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.agent.approval.decision',  [$agent->id, 'action' => 'approve']) }}" class="btn btn-success">Approve</a>
                                    <a href="{{ route('admin.agent.approval.decision',  [$agent->id, 'action' => 'reject']) }}" onclick="return confirm('are you sure you want to reject this agent?')" class="btn btn-danger">Reject</a>
                                </td>
                            </tr>
                        @endforeach  
                    </tbody>
                </table>
            </div>
        </div>
    </div>



</div>


@endsection

@section('extra_scripts')

<script>
    $(document).ready(function() {
        $('.agent-table').DataTable({
            "pageLength": 100,
            "aaSorting": [],
        });
    });

</script>
@endsection