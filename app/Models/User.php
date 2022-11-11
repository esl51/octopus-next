<?php

namespace App\Models;

use Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Roquie\LaravelPerPageResolver\PerPageResolverTrait;

/**
 * User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon $email_verified_at
 *
 * @property Role[] $roles
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use ExposePermissions;
    use PerPageResolverTrait;
    use HasApiTokens;
    use HasColumns;
    use HasFactory;
    use HasFiles;
    use HasRoles;
    use Notifiable;
    use SerializesDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'photo_url',
        'name_placeholder',
    ];

    protected $guard_name = 'web';

    /**
     * Get avatar files.
     *
     * @param string $routePrefix
     * @return \Illuminate\Support\Collection
     */
    public function getAvatarFiles($routePrefix = null)
    {
        return File::collect($this, 'avatar', $routePrefix);
    }

    /**
     * Store avatar.
     *
     * @param Illuminate\Http\UploadedFile $avatar
     * @return void
     */
    public function storeAvatar($avatar)
    {
        $this->deleteAvatar();
        $this->storeFiles($avatar, 'avatar', 256);
    }

    /**
     * Delete avatar.
     *
     * @return void
     */
    public function deleteAvatar()
    {
        $this->deleteDirectory('avatar');
    }

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): ?string
    {
        $avatarFiles = $this->getAvatarFiles();
        if (count($avatarFiles)) {
            $avatarFile = $avatarFiles[0];
            return route('avatar', [
                'user' => $this->id,
                'fileName' => $avatarFile->name,
            ]);
        }
        return null;
    }

    public function getNamePlaceholderAttribute(): string
    {
        $capitals = '';
        $words = preg_split('/[\s-]+/', $this->name);
        $words = [array_shift($words), array_pop($words)];
        foreach ($words as $word) {
            if (ctype_digit($word) && strlen($word) == 1) {
                $capitals .= $word;
            } else {
                $first = grapheme_substr($word, 0, 1);
                $capitals .= ctype_digit($first) ? '' : $first;
            }
        }
        return mb_strtoupper($capitals);
    }

    public function getIsDeletableAttribute(): bool
    {
        // if user is current user and has root role
        if ($this->id == Auth::user()->id && Auth::user()->hasRole('root')) {
            return false;
        }
        // if user has root role and current user is not root
        if ($this->hasRole('root') && !Auth::user()->hasRole('root')) {
            return false;
        }
        // if user has root role and user is last root
        if ($this->hasRole('root') && User::role('root')->count() < 2) {
            return false;
        }
        return true;
    }

    public function getIsEditableAttribute(): bool
    {
        // if user has root role and current user is not root
        if ($this->hasRole('root') && !Auth::user()->hasRole('root')) {
            return false;
        }
        return true;
    }
}
