@extends('admin.layouts.app')
@section('title', 'New Agent')
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
        <div class="col-md-6">
            <h1 class="h3 mb-2 text-white">New Agent</h1>        
        </div>
        <div class="col-md-6 text-right">
            <a  href="{{ route('admin.agents') }}" class="btn btn-light">Back</a>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4 text-gray-800">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create Agent</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.agent.store') }}">
                @csrf
                
                @include('admin.agent._form')

                <hr/>

                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
