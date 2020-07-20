<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentRanking extends Model {
    protected $fillable = ['rank_id', 'user_id'];

    /**
     * An agent ranking has a rank
     *
     * @return BelongsTo The attached Rank
     */
    public function rank() : BelongsTo
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }

    /**
     * An AgentRanking has a user.
     *
     * @return BelongsTo The attached User.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
