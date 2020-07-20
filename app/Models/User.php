<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'lastname', 'firstname', 'role_id', 'gmail', 'phone', 'followup_lead_limit', 'bank_lead_limit', 'under_the_lead_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * A User has Role
     *
     * @return BelongsTo The attached Role.
     */
    public function role() : BelongsTo 
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * A User has Rank
     *
     * @return HasOne The attached agent ranking.
     */
    public function agent_ranking() : HasOne 
    {
        return $this->hasOne(AgentRanking::class);
    }

    /**
     * A User has many check-ins
     *
     * @return HasMany The attached agent checkins.
     */
    public function agent_checkins() : HasMany
    {
        return $this->hasMany(AgentCheckin::class);
    }

    /**
     * A User has many followup queues
     *
     * @return HasMany The attached followup queues.
     */
    public function followup_queues() : HasMany
    {
        return $this->hasMany(FollowupQueues::class);
    }

    /**
     * A user has bank queue record.
     *
     * @return HasMany The attached BankQueue.
     */
    public function bankQueues() : HasMany{
        return $this->hasMany(BankQueue::class);
    }

    /**
    * Check if the user has contains a role
    * 
    * @param <string> $role - The role name 
    *
    * @return boolean true has role, false otherwise
    */

    public function hasRole($roleName) 
    {
        $role = $this->role->where('role', $roleName)->first();
        return $role ? true : false;
    }
}
