<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use HasFactory, Notifiable;
    use LogsActivity;

    const AVATAR_STORAGE = 'public/images/avatars';
    const AVATAR_THUMB_STORAGE = 'public/images/avatars/thumb';
    const AVATAR_DEFAULT = '/default/default-user.png';

    /**
     * All fillable attributes will be logged
     *
     * @var boolean
     */
    static $logFillable = true;

    /**
     * to log every attribute in your $logAttributes variable,
     * but only those that has actually changed after the update
     *
     * @var boolean
     */
    protected static $logOnlyDirty = true;

    /**
     * prevents the package from storing empty logs
     *
     * @var boolean
     */
    protected static $submitEmptyLogs = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'avatar',
        'module',
        'provider', // social login
        'provider_id', // social login
        'provider_response', // social login
        'last_login',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider', // social login
        'provider_id', // social login
        'provider_response' // social login

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'last_login' => 'datetime',
    ];

    public function getLastLogin($format = 'YYYY-MM-DD HH:mm:ss', $default = '')
    {
        if ($this->last_login === null) {
            return $default;
        }

        return Carbon::createFromTimeString($this->last_login)->isoFormat($format);
    }


    //avatar
    /**
     * Return true if user has an avatar image.
     *
     * @return bool
     */
    public function hasAvatar()
    {
        if ($this->avatar) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if current user has an avatar.
     *
     * @return string|false
     */
    public function getAvatarPathAttribute()
    {
        return Storage::url(self::AVATAR_STORAGE) .  DIRECTORY_SEPARATOR . $this->avatar;
        //  return public_path( . DIRECTORY_SEPARATOR . $this->avatar);
    }

    /**
     * Check if current user has an avatar.
     *
     * @return string|false
     */
    public function getAvatarThumbPathAttribute()
    {
        return Storage::url(self::AVATAR_THUMB_STORAGE) .  DIRECTORY_SEPARATOR . $this->avatar;
        //  return public_path( . DIRECTORY_SEPARATOR . $this->avatar);
    }

    /**
     * Delete avatar image.
     *
     * @return bool
     */
    public function deleteAvatar()
    {
        if ($this->hasAvatar()) {
            return unlink($this->getAvatarPathAttribute());
        }

        return false;
    }

    /**
     * Return current user avatar uri.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {


            return Storage::url(self::AVATAR_STORAGE) .  DIRECTORY_SEPARATOR . $this->avatar;
        }

        return $this->getAvatarDefault();
    }

    /**
     * Return current user avatar uri.
     *
     * @return string
     */
    public function getAvatarThumbUrlAttribute()
    {
        if ($this->avatar) {


            return Storage::url(self::AVATAR_THUMB_STORAGE) .  DIRECTORY_SEPARATOR . $this->avatar;
        }

        return $this->getAvatarDefault();
    }

    private function getAvatarDefault()
    {
        return asset(self::AVATAR_DEFAULT);
    }

    // JWT
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
