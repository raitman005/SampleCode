@extends('admin.layouts.app')
@section('title', 'Change Agent Password')
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
        <div class="col-md-10">
            <h1 class="h3 mb-2 text-white">Change Agent Password - {{ $user->firstname . " " . $user->lastname }}</h1>        
        </div>
        <div class="col-md-2 text-right">
            <a  href="{{ route('admin.agents') }}" class="btn btn-light">Back</a>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4 text-gray-800">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Email: {{ $user->email }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.agent.passwordupdate') }}">
                @csrf
            
                <div class="form-group row">
                    <div class="col-md-12">
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="txtbox-password" required placeholder="Password" />
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" id="txtbox-password-confirmation" required placeholder="Confirm Password" />
                        @if ($errors->has('password_confirmation'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <hr/>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
