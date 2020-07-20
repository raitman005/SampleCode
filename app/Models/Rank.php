<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['rank'];

    /**
     * A Rank is applied to agent rankings
     *
     * @return HasMany The attached Agent Rankings.
     */
    public function agent_rankings() : HasMany
    {
        return $this->hasMany(AgentRanking::class);
    }

    /**
     * A Rank is applied to Followup Emails
     *
     * @return HasMany The attached Followup Emails.
     */
    public function followup_emails() : HasMany
    {
        return $this->hasMany(FollowupEmail::class);
    }

    /**
     * Return rank instance from the given string 
     * 
     * @param <string> $rankName the rankName
     *
     * @return Rank the Rank from the queried $rankName.
     */
    public static function getRank($rankName) : Rank
    {
        return self::where('rank', $rankName)->first();
    }
}
