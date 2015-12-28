<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{

    public function getAuthIdentifierName()
    {
        return $this->uvanetid;
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
    }

    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }

    public function is_student()
    {
        return $this->student != NULL;
    }

    public function student()
    {
        return $this->hasOne('App\Models\Student');
    }

    public function getNameAttribute()
    {
        return $this->firstname .' '.trim($this->infix.' '.$this->lastname);
    }

    public function scopeIsAssistant($query)
    {
        return $query->where('assistant',1);
    }
}