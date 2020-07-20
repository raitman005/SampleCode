<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    //
    /**
     * A Role has many Users
     *
     * @return HasMany The attached Users.
     */
    public function users() : HasMany 
    {
        return $this->hasMany(User::class);
    }

    /**
     * Return role instance from the given string 
     * 
     * @param <string> $roleName the rolename
     *
     * @return Role the Role from the queried $roleName.
     */
    public static function getRole($roleName) : Role
    {
        return self::where('role', $roleName)->first();
    }


}
