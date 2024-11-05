<?php

namespace App\Models\Access;

use App\Traits\Access\ExposePermissions;
use App\Traits\Files\HasFiles;
use App\Traits\HasColumns;
use App\Traits\HasService;
use App\Traits\SerializesDates;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
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
 * @property \Illuminate\Support\Carbon $disabled_at
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
    use HasService;
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
        'disabled_at',
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
        'disabled_at' => 'datetime',
    ];

    /**
     * File types.
     *
     * @var array<string, array|null>
     */
    protected $fileTypes = [
        'avatar' => [
            'multiple' => false,
            'replace' => true,
            'cropSize' => 256,
        ],
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'name_placeholder',
        'filable_title',
    ];

    protected $guard_name = 'web';

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
        if ($this->id == auth()->id() && auth()->user()->hasRole('root')) {
            return false;
        }
        // if user has root role and current user is not root
        if ($this->hasRole('root') && !auth()->user()->hasRole('root')) {
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
        if ($this->hasRole('root') && !auth()->user()->hasRole('root')) {
            return false;
        }
        return true;
    }

    public function getIsDisablableAttribute(): bool
    {
        // is user is disabled
        if ($this->disabled_at) {
            return false;
        }
        // if user is current user and has root role
        if ($this->id == auth()->id()) {
            return false;
        }
        // if user has root role and current user is not root
        if ($this->hasRole('root') && !auth()->user()->hasRole('root')) {
            return false;
        }
        return true;
    }

    public function getIsEnablableAttribute(): bool
    {
        // is user is not disabled
        if ($this->disabled_at == null) {
            return false;
        }
        // if user has root role and current user is not root
        if ($this->hasRole('root') && !auth()->user()->hasRole('root')) {
            return false;
        }
        return true;
    }

    public function getFilableTitleAttribute()
    {
        return $this->name;
    }

    public static function ownedFilesScope(Builder $query): void
    {
        $query->where('filable_id', auth()->id());
    }

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();

        self::bootHasFiles();
    }
}
