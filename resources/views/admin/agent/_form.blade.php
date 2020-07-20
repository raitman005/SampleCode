<div class="form-group">
    <input type="email" name="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="txtbox-email" required placeholder="Email" value="{{ $user->email ?? ''}}" />
    @if ($errors->has('email'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif
</div>

@if(!isset($user->id))
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
@endif

<div class="form-group row">
    <div class="col-md-6">
        <input type="text" name="firstname" class="form-control {{ $errors->has('firstname') ? ' is-invalid' : '' }}" id="txtbox-firstname" required placeholder="First Name" value="{{ $user->firstname ?? ''}}"/>
        @if ($errors->has('firstname'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('firstname') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-md-6">
        <input type="text" name="lastname" class="form-control {{ $errors->has('lastname') ? ' is-invalid' : '' }}" id="txtbox-lastname" required placeholder="Last Name" value="{{ $user->lastname ?? ''}}"/>
        @if ($errors->has('lastname'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('lastname') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <input type="email" name="gmail" class="form-control {{ $errors->has('gmail') ? ' is-invalid' : '' }}" id="txtbox-gmail" required placeholder="Gmail" value="{{ $user->gmail ?? ''}}"/>
        @if ($errors->has('gmail'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('gmail') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-md-6">
        <input type="text" name="phone" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" id="txtbox-phone" required placeholder="Phone" value="{{ $user->phone ?? ''}}"/>
        @if ($errors->has('phone'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('phone') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <select  name="rank_id" class="form-control {{ $errors->has('rank_id') ? ' is-invalid' : '' }}" id="dropdown-rank" required>
            <option value="">Select a rank</option>
            @foreach($ranks as $rank)
                <option value="{{ $rank->id }}" {{ isset($user->id) && $user->agent_ranking->rank_id == $rank->id ? 'selected' : ''}}>{{ $rank->rank  }}</option>
            @endforeach
        </select>
        @if ($errors->has('rank_id'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('rank_id') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-md-6">
        <input type="text" name="followup_lead_limit" class="form-control {{ $errors->has('followup_lead_limit') ? ' is-invalid' : '' }}" id="txtbox-followup_lead_limit" required placeholder="Daily Followup Lead Limit" value="{{ $user->followup_lead_limit ?? '' }}"/>
        @if ($errors->has('followup_lead_limit'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('followup_lead_limit') }}</strong>
            </span>
        @endif
    </div>
</div>