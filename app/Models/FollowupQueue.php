<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowupQueue extends Model {
    protected $fillable = ['order', 'user_id', 'state_id', 'followup_email_id'];

    /**
     * An FollowupEmail has a rank.
     *
     * @return BelongsTo The attached Rank.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * An FollowupEmail has a rank.
     *
     * @return BelongsTo The attached Rank.
     */
    public function state() : BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * An FollowupEmail has a rank.
     *
     * @return BelongsTo The attached Rank.
     */
    public function followup_email() : BelongsTo
    {
        return $this->belongsTo(FollowupEmail::class, 'followup_email_id');
    }
}
