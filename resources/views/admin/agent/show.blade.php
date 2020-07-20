@extends('admin.layouts.app')
@section('title', $user->firstname . " " . $user->lastname )
@section('content')
<div class="container-fluid text-white">
    <div class="row">
        <div class="col-md-12">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has($msg))
                    <div class="alert alert-{{ $msg }}">{{ Session::get($msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="row  mb-4">
        <div class="col-lg-4 col-md-12">
            <div class="card shadow mb-4 text-gray-800">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"> <i class="far fa-user"></i> Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row  mb-2">
                        <div class="col-md-12">
                            <h5 class="h4 mb-2 text-dark">{{ $user->firstname . " " . $user->lastname }}</h5>        
                            <p class="text-dark">
                                <span class="fas fa-at"></span> {{ $user->email }}, {{ $user->gmail }}<br/>
                                <span class="fas fa-mobile-alt"></span> {{ $user->phone }}
                            </p>        
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item">Rank: <span class="float-right badge {{ $user->agent_ranking->rank->rank == 'pro' ? 'badge-warning' : 'badge-secondary' }}">{{ ucfirst($user->agent_ranking->rank->rank) }}</span></li>
                        <li class="list-group-item">Registered on: <span class="float-right">{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</span></li>
                        <li class="list-group-item">Last Checked in: <span class="float-right">{{ $lastCheckIn }}</span></li>
                        <li class="list-group-item">Overall check in count: <span class="float-right">{{ $checkinCount }}</span></li>
                        <li class="list-group-item">Overall lead count: <span class="float-right">{{ $totalLeads }}</span></li>
                        <li class="list-group-item">Throttled: <span class="float-right">{!! $user->daily_throttle == 1 ? 'Yes' : 'No' !!}</span></li>
                    </ul>     
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow mb-4 text-gray-800">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie"> </i> Agent Stats - {{ $type == 'initial' ? 'Initial' : 'Changeup' }} </h6>
                    <div class="dropdown no-arrow">
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'initial']) }}" class="btn btn-outline-primary btn-change-type btn-sm {{ $type == 'initial' ? 'active' : '' }}" data-type="initial">Inital Leads</a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'banked']) }}" class="btn btn-outline-success btn-change-type btn-sm {{ $type == 'banked'  ? 'active' : '' }}" data-type="banked">Changeup Leads</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-5">
                        <div class=" text-center col-lg-5 col-md-12 mb-4 border-left-success">
                            <h3>Daily lead activitiy</h3>
                            <div>
                                <canvas id="agent-stats-daily"></canvas>
                            </div>
                        </div>
                        <div class="text-center col-lg-7 col-md-12 mb-4 border-left-primary">
                            <h3>Last 7 days</h3>
                            <div>
                                <canvas id="agent-stats-seven-days"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-9">
            <div class="card shadow mb-4 text-gray-800">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie"> </i> Assigned Leads  - {{ $type == 'initial' ? 'Initial' : 'Changeup' }} (Last 5 days) </h6>
                    <div class="dropdown no-arrow">
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'initial']) }}" class="btn btn-outline-primary btn-change-type btn-sm {{ $type == 'initial' ? 'active' : '' }}" data-type="initial">Inital Leads</a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'banked']) }}" class="btn btn-outline-success btn-change-type btn-sm {{ $type == 'banked'  ? 'active' : '' }}" data-type="banked">Changeup Leads</a>
                    </div>
                </div>
                <div class="" id="divLeads">
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="all-lead" role="tabpanel" aria-labelledby="all-lead-tab">
                                <div class="table-responsive">
                                    <table class="table tbl-assigned-lead" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($assignedLeads as $assignedLead)
                                                <tr>
                                                    <td><a href="{{ url('admin/leads/'.$assignedLead->followup_email_id) }}">{{ $assignedLead->followup_email->subject ?? '' }}</a></td>
                                                    <td>{{ \Carbon\Carbon::parse($assignedLead->updated_at)->format('Y-m-d h:i A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow mb-4 text-gray-800">
                <a href="#divLeads" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-primary"> <i class="fas fa-tasks"></i> Check in history (Last 25 records)</h6>
                </a>
                <div class="collapse show" id="divLeads">
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="all-lead" role="tabpanel" aria-labelledby="all-lead-tab">
                                <div class="table-responsive">
                                    <table class="table tbl-checkin-history" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Date / Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($checkinHistory as $checkin)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($checkin->created_at)->format('M d, Y h:i A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.tbl-assigned-lead').DataTable({
            'pageLength': 25,
             bSort: false,
        });
    });

    var ctx = document.getElementById("agent-stats-daily");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Accepted", "Declined", "Expired"],
            datasets: [{
                data: [{{ $leadAcceptedCnt }}, {{ $leadDeclinedCnt }}, {{ $leadExpiredCnt }}],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                hoverBackgroundColor: ['#068523', '#ba1323', '#dda005'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        
        options: {
            responsive: true,
            maintainAspectRatio: true,
            tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            },
            legend: {
                position: 'top',
            },
            cutoutPercentage: 75,
            animateScale: true,
            animateRotate: true,
            height: '200px',
           
        },
    });

    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
    }

    // Area Chart Example
    var ctx = document.getElementById("agent-stats-seven-days");
    var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($last7Days),
        datasets: [{
        label: "Lead count last 7 days",
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.05)",
        borderColor: "rgba(78, 115, 223, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(78, 115, 223, 1)",
        pointBorderColor: "rgba(78, 115, 223, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: @json($last7DaysData),
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 10
                }
            }],
            yAxes: [{
                ticks: {
                        maxTicksLimit: 8,
                        padding: 10,
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
        },
       
    }
    });
</script>
@endsection