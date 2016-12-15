<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{

    const student_role   = 1;
    const assistant_role = 2;
    const teacher_role   = 4;
    const admin_role     = 8;

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

    public function getIsAdminAttribute()
    {
        return $this->role & self::admin_role;
    }

    public function getIsAssistantAttribute()
    {
        return $this->role & self::assistant_role;
    }

    public function getIsTeacherAttribute()
    {
        return $this->role & self::teacher_role;
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
        return $query->where('role','&', $this->assistant_role);
    }
}
