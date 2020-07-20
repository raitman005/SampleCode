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
            <h1 class="h3 mb-2 text-white">Agents</h1>        
        </div>
        <div class="col-md-6 text-right">
            <a  href="{{ route('admin.agent.create') }}" class="btn btn-primary">New Agent</a>
            @if($pendingAgents->count() > 0)
                <a  href="{{ route('admin.agent.forapproval') }}" class="btn btn-warning">For Approval <span class="badge badge-danger">{{ $pendingAgents->count() }}</span></a>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4 text-gray-800">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Active Agents</h6>
            <div>
                <a class="btn btn-sm btn-primary" href="#" role="button" id="btn-copy-gmails">
                    <i class="fas fa-copy"></i> Copy gmails
                </a>
                <button class="btn btn-sm btn-primary" href="#" role="button" id="btn-1-click-limit" data-toggle="modal" data-target="#modal-1-click">
                    1-click limit
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table agent-table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Agent</th>
                            <th class="text-center">Daily Limit<br/>(initial)</th>
                            <th class="text-center">Daily Limit<br/>(changeup)</th>
                            <th class="text-center">Offpeak Limit<br/>(initial)</th>
                            <th class="text-center">Offpeak Limit<br/>(changeup)</th>
                            <th class="text-center">Status</th>
                            <th>Registered</th>
                            <th class="text-center">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                            <tr>
                                <td class="text-center">{{ $agent->id }}</td>
                                <td>
                                    <strong style="font-size: 18px;"><a href="{{ route('admin.agent.show', $agent->id) }}">{{ $agent->firstname . " " . $agent->lastname }}</a></strong>
                                    @if ($agent->is_lead == 1)
                                        <span class="fa fa-star text-warning" title="Team Leader"></span>
                                    @endif
                                    <br/>
                                    <span class="fas fa-at"></span> {{ $agent->email }} <br/>
                                    <span class="fab fa-google"></span> {{ $agent->gmail }} <br/>
                                    <span class="fa fa-mobile"></span> {{ $agent->phone }} <br/>
                                    <span>Rank <span id="agent-rank-{{ $agent->id}}">{{ $agent->agent_ranking->rank->rank ?? '---' }}</span>
                                    <a href="#" class="small btn-change-rank" data-id="{{ $agent->id }}">(change to {{ $agent->agent_ranking->rank->rank == 'normal' ? 'pro' : 'normal' }})</a></span><br/>
                                    <span>{!! $agent->daily_throttle == 1 ? '<i class="text-success"><small>Daily limit reached</small></i>' : '<i class="text-warning"><small>Daily limit not yet reached</small></i>' !!}</span>                                    
                                </td>
                                <td class="text-center"><input type="text" value="{{ $agent->followup_lead_limit }}" class="text-center textbox-blend txtbox-lead-limit-initial" data-id="{{ $agent->id }}"> </td>
                                <td class="text-center"><input type="text" value="{{ $agent->bank_lead_limit }}" class="text-center textbox-blend txtbox-lead-limit-banked" data-id="{{ $agent->id }}"> </td>
                                <td class="text-center"><input type="text" value="{{ $agent->initial_offpeak_limit }}" class="text-center textbox-blend txtbox-lead-offpeak-limit-initial" data-id="{{ $agent->id }}"> </td>
                                <td class="text-center"><input type="text" value="{{ $agent->bank_offpeak_limit }}" class="text-center textbox-blend txtbox-lead-offpeak-limit-banked" data-id="{{ $agent->id }}"> </td>
                                <td class="text-center">{!! Helper::getCheckinTime($agent) ? '<span class="badge badge-success">in</span>' : '<span class="badge badge-secondary">out</span>' !!}</td>
                                <td>{{ \Carbon\Carbon::parse($agent->created_at)->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div class="dropdown no-arrow mb-4">
                                        <button class="btn btn-white dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start">                                        
                                            <a class="dropdown-item" onclick="return confirm('This will prevent the agent from acquiring a leads for today. Continue?')" href="{{ route('admin.agent.throttle', $agent->id) }}">Prevent sending leads for today</a>
                                            <a class="dropdown-item" href="{{ url('admin/agent/edit/'.$agent->id) }}">Edit</a>
                                            <a class="dropdown-item" href="{{ url('admin/agent/edit/togglelead/'.$agent->id) }}">{{ $agent->is_lead == 1 ? 'Remove as a team leader' : 'Set as team leader' }}</a>
                                            <a class="dropdown-item" href="{{ url('admin/agent/password/edit/'.$agent->id) }}">Change Password</a>
                                            @if ($agent->webmail_password != "")
                                                <a class="dropdown-item btn-reveal-password" data-id="{{ $agent->id }}" href="#">View mail password</a>
                                            @endif
                                            @if (Helper::getCheckinTime($agent))
                                            <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ url('admin/agent/checkout/'.$agent->id) }}">Check out</a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <a onclick="event.preventDefault(); document.getElementById('frm-disable-agent-{{$agent->id}}').submit();" class="dropdown-item" href="{{ url('admin/agent/disable/'.$agent->id) }}">Disable</a>
                                            <form id="frm-disable-agent-{{$agent->id}}" action="{{ url('admin/agent/destroy/'.$agent->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                <input name="_method" type="hidden" value="DELETE">
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach  
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 text-gray-800">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Disabled Agents</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table agent-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Agent</th>
                            <th>Rank</th>
                            <th>Registered at</th>
                            <th>Disabled at</th>
                            <th class="text-center">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($disabledAgents as $agent)
                        <tr>
                            <td>{{ $agent->id }}</td>
                            <td>
                                <strong style="font-size: 18px;"><a href="{{ route('admin.agent.show', $agent->id) }}">{{ $agent->firstname . " " . $agent->lastname }}</a></strong><br/>
                                <span class="fas fa-at"></span> {{ $agent->email }} <br/>
                                <span class="fab fa-google"></span> {{ $agent->gmail }} <br/>
                                <span class="fa fa-mobile"></span> {{ $agent->phone }} <br/>
                            </td>
                            <td>{{ $agent->agent_ranking->rank->rank ?? '---' }}</td>
                            <td>{{ \Carbon\Carbon::parse($agent->created_at)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($agent->deleted_at)->format('Y-m-d') }}</td>
                            <td class="text-center">
                                <div class="dropdown no-arrow mb-4">
                                    <button class="btn btn-white dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start">
                                        <a onclick="event.preventDefault(); document.getElementById('frm-enable-agent-{{$agent->id}}').submit();" class="dropdown-item" href="{{ url('admin/agent/enable/'.$agent->id) }}">Enable</a>
                                        <form id="frm-enable-agent-{{$agent->id}}" action="{{ url('admin/agent/restore/'.$agent->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach  
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <input type="hidden" value="{{ $gmails }}" id="gmails">
</div>

<div class="modal" id="modal-1-click" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <form id="frm-new-lead" method="POST" action="{{ route('admin.agent.setlimit.all') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Set limit to all agent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="txtbox-subject">Type</label>
                        <select name="type" id="" class="form-control form-control-sm" required>
                            <option value="daily_limit_initial">Daily limit (initial)</option>
                            <option value="daily_limit_banked">Daily limit (changeup)</option>
                            <option value="offpeak_limit_initial">Offpeak limit (initial)</option>
                            <option value="offpeak_limit_banked">Offpeak limit (changeup)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Limit value</label>
                        <input type="text" class="form-control form-control-sm" name="limit" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="">Apply changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="modal-reveal-password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content ">
            <div class="modal-body text-center">
                <h5 id="password-txt"></h5>
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

    $(document). on('change', '.txtbox-lead-limit-initial', function(){
        var value = $(this).val();
        var agentId = $(this).attr('data-id');
        var thisButton = $(this);
        updateDailyLimit(agentId, value, 'initial')
    });

    $(document). on('change', '.txtbox-lead-limit-banked', function(){
        var value = $(this).val();
        var agentId = $(this).attr('data-id');
        var thisButton = $(this);
        updateDailyLimit(agentId, value, 'banked')
    });

    $(document). on('change', '.txtbox-lead-offpeak-limit-initial', function(){
        var value = $(this).val();
        var agentId = $(this).attr('data-id');
        var thisButton = $(this);
        updateDailyLimit(agentId, value, 'initial', "offpeak")
    });

    $(document). on('change', '.txtbox-lead-offpeak-limit-banked', function(){
        var value = $(this).val();
        var agentId = $(this).attr('data-id');
        var thisButton = $(this);
        updateDailyLimit(agentId, value, 'banked', "offpeak")
    });

    $(document). on('click', '.btn-change-rank', function(){
        var text = $(this).text();
        var agentId = $(this).attr('data-id');
        var thisButton = $(this);
        $.ajax({
            type: "POST",
            data: {id: agentId, "_token": "{{ csrf_token() }}",},
            dataType: 'json',
            url: '{{ url("/admin/agent/rank/change/") }}/' + agentId,
            beforeSend: function() {
                thisButton.text("please wait...")
            },
            success: function(obj) {
                $('#agent-rank-'+agentId).text(obj.rank);
                thisButton.text(text)
            },
            error: function() {
                alert("Error changing of rank. Please try again.")
            }
        });
    });
    
    $(document). on('click', '#btn-copy-gmails', function(){
        var gmails = document.getElementById("gmails").value;
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = gmails;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $(this).html('<i class="fas fa-copy"></i> Gmail Copied!');
    });

    $(document).on('click', '.btn-reveal-password', function() {
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: "{{ url('/admin/agent/viewpassword') }}/"+id,
            beforeSend: function() {
                $('#modal-reveal-password').modal('show');
                $('#password-txt').text("Extracting Password");
            },
            success: function(e) {
                $('#password-txt').html(e.password);
            },
            error: function() {
                $('#password-txt').html("Error! Please try again!");
            }
        });
    });

    function updateDailyLimit(agentId, value, type, offpeak = 'notoffpeak')
    {
        $.ajax({
            type: "POST",
            data: {id: agentId, "_token": "{{ csrf_token() }}", 'value': value, 'type': type, 'offpeak': offpeak},
            dataType: 'json',
            url: '{{ url("/admin/agent/followuplimit/change/") }}/' + agentId,
            beforeSend: function() {
                
            },
            success: function(obj) {
                
            },
            error: function() {
                alert("Error changing of limit. Please try again.")
            }
        });
    }
</script>
@endsection