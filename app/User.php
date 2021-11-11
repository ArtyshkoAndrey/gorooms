<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App;

use Eloquent;
use App\Models\Hotel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotificationCollection;

/**
 * User Table
 *
 * @property int                                                        $id
 * @property string                                                     $name
 * @property string                                                     $email
 * @property string|null                                                $phone
 * @property string|null                                                $position
 * @property bool                                                       $is_moderate
 * @property string|null                                                $code
 * @property string|null                                                $hotel_position
 * @property bool                                                       $is_admin
 * @property Carbon|null                                                $email_verified_at
 * @property string                                                     $password
 * @property string|null                                                $remember_token
 * @property Carbon|null                                                $created_at
 * @property Carbon|null                                                $updated_at
 * @property Carbon|null                                                $deleted_at
 * @property-read Hotel                                                 $hotel
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null                                              $notifications_count
 * @property-read mixed                                                 $personal_hotel
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsModerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin Eloquent
 */
class User extends Authenticatable
{

  use Notifiable;
  use SoftDeletes;

  /**
   * General permission, for db
   * @var string
   */
  public const POSITION_GENERAL = 'general';

  /**
   * Staff permission, for db
   * @var string
   */
  public const POSITION_STAFF = 'staff';

  /**
   * Array permission for forEach in blade
   * @var array
   */
  public const POSITIONS = [self::POSITION_GENERAL, self::POSITION_STAFF];

  /**
   * Russian (Translate) permission, for blade
   */
  public const POSITIONS_LANGUAGE = [self::POSITION_STAFF => 'STAFF', self::POSITION_GENERAL => 'GENERAL'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'email', 'password', 'is_admin', 'phone', 'position', 'code', 'position', 'is_moderate'];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = ['password', 'remember_token',];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = ['email_verified_at' => 'datetime', 'is_admin' => 'boolean', 'is_moderate' => 'boolean'];

  private string $info = '';

  /**
   * Get personal hotel.
   *
   * @return HasOne
   */
  public function hotel (): HasOne
  {
    return $this->hasOne(Hotel::class);
  }

  /**
   * return Hotel where staff or general
   *
   * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
   */
  public function getPersonalHotelAttribute ()
  {
    $user = $this;

    return Hotel::withoutGlobalScopes()->whereHas('users', function ($q) use ($user) {
      $q->where('users.id', $user->id);
    })->first();
  }
}
