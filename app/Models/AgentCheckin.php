<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\State;
use App\Models\User;

class AgentCheckin extends Model {
    protected $fillable = ['state_id', 'user_id'];

    /**
     * An AgentCheckin has a state.
     *
     * @return BelongsTo The attached User.
     */
    public function state() : BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * An AgentCheckin has a user.
     *
     * @return BelongsTo The attached User.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * An AgentCheckin has a user.
     *
     * @param User $user The user that is being checking
     *
     * @return AgentCheckin $checkInRecord if has result boolean false otherwise.
     */
    public static function checkInStatus(User $user) 
    {
        $stateId = State::getstate("checked-in")->id;
        $checkInRecord = self::where('user_id', $user->id)->where('state_id', $stateId)->first();

        return $checkInRecord ? $checkInRecord : false;
    }
}
