<?php

namespace App\Models;

use App\Traits\UseImages;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Traits\CreatedAtOrdered;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Room
 *
 * @property int                         $id
 * @property string|null                 $name
 * @property int|null                    $number
 * @property int|null                    $order
 * @property int|null                    $category_id
 * @property bool                        $moderate
 * @property string|null                 $description
 * @property int                         $hotel_id
 * @property string|null                 $deleted_at
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 * @property bool                        $is_hot
 * @property-read Collection|Attribute[] $attrs
 * @property-read int|null               $attrs_count
 * @property-read Category|null          $category
 * @property-read Collection|Cost[]      $costs
 * @property-read int|null               $costs_count
 * @property-read mixed                  $all_costs
 * @property-read string|null            $meta_description
 * @property-read string|null            $meta_keywords
 * @property-read string|null            $meta_title
 * @property-read Hotel                  $hotel
 * @property-read Image                  $image
 * @property-read Collection|Image[]     $images
 * @property-read int|null               $images_count
 * @property-read PageDescription|null   $meta
 * @method static Builder|Room hot()
 * @method static Builder|Room newModelQuery()
 * @method static Builder|Room newQuery()
 * @method static Builder|Room query()
 * @method static Builder|Room whereCategoryId($value)
 * @method static Builder|Room whereCreatedAt($value)
 * @method static Builder|Room whereDeletedAt($value)
 * @method static Builder|Room whereDescription($value)
 * @method static Builder|Room whereHotelId($value)
 * @method static Builder|Room whereId($value)
 * @method static Builder|Room whereIsHot($value)
 * @method static Builder|Room whereModerate($value)
 * @method static Builder|Room whereName($value)
 * @method static Builder|Room whereNumber($value)
 * @method static Builder|Room whereOrder($value)
 * @method static Builder|Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{
  use UseImages;
  use CreatedAtOrdered;

  public const PER_PAGE = 6;

  public string $no_image = 'img/img-room-sm-1.jpg';

  protected $fillable = [
    'name',
    'number',
    'order',
    'moderate',
    'description',
    'is_hot',
  ];

  protected $casts = [
    'moderate' => 'boolean',
    'is_hot' => 'boolean',
  ];

  protected $with = [
    'attrs',
    'images',
    'costs',
  ];

  ### SCOPES

  protected static function boot()
  {
    parent::boot();

    static::addGlobalScope('moderation', function (Builder $builder) {
      if (auth()->check()) {
        if ((!auth()->user()->is_admin && !auth()->user()->is_moderate) &&
          !Route::currentRouteNamed('lk.*') &&
          !Route::currentRouteNamed('moderator.*') &&
          !Route::currentRouteNamed('api.*') &&
          !Route::currentRouteNamed('admin.*')
        ) {
          $builder->whereHas('hotel', function ($q) {
            $q->where('moderate', false)->where('show', true);
          })->where('moderate', false);
        }
      } else {
        $builder->whereHas('hotel', function ($q) {
          $q->where('moderate', false)->where('show', true);
        })->where('moderate', false);
      }
    });
  }

  public static function orderByCost(string $orderBy): Builder
  {
    return Room::leftJoin('min_rooms_costs', (new self())->getTable() . '.id', '=', 'min_rooms_costs.room_id')->orderBy('min_rooms_costs.min_cost', $orderBy);
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  public function hotel(): BelongsTo
  {
    return $this->belongsTo(Hotel::class);
  }

  public function attrs(): BelongsToMany
  {
    return $this->belongsToMany(Attribute::class, 'attribute_room', 'room_id', 'attribute_id');
  }

  public function scopeHot(Builder $query): Builder
  {
    return $query->where('is_hot', true);
  }

  public function getAllCostsAttribute(): Collection
  {
    $costs = $this->costs()->with('period.type')->get()->sortBy('type.sort');

    $types = Cache::remember('types', 60 * 60 * 24 * 12, function () {
      return CostType::orderBy('sort')->get();
    });

    $collection = new Collection();

    foreach ($types as $type) {
      $check = $costs->contains('period.type.id', $type->id);
      if (!$check) {
        $cost = (object)[
          'type' => $type,
          'value' => 'Не предоставляется',
        ];
        $collection->add($cost);
      } else {
        $collection->add($costs->firstWhere('period.type.id', '=', $type->id));
      }

    }

    return $collection;
  }

  public function costs(): HasMany
  {
    return $this->hasMany(Cost::class, 'room_id', 'id');
  }

  public function attachMeta(Request $request): Room
  {
    if (!$request->get('meta_title', false) && !$request->get('meta_description', false) && !$request->get('meta_keywords', false))
      return $this;

    $key = $this->getRouteKeyName();
    $url = '/rooms/' . $this->$key;

    $data = [];
    $data['title'] = $request->get('meta_title');
    $data['meta_description'] = $request->get('meta_description');
    $data['meta_keywords'] = $request->get('meta_keywords');
    $data['url'] = $url;
    $data['model_type'] = self::class;

    $meta = PageDescription::updateOrCreate(['url' => $url], $data);
    $meta->model_type = self::class;
    $meta->save();

    $this->meta()->save($meta);

    return $this;
  }

  public function meta(): HasOne
  {
    return $this->hasOne(PageDescription::class, 'model_id')->where('model_type', self::class);
  }

  public function getMetaDescriptionAttribute(): ?string
  {
    return @$this->meta->meta_description ?? null;
  }

  public function getMetaKeywordsAttribute(): ?string
  {
    return @$this->meta->meta_keywords ?? null;
  }

  public function getMetaTitleAttribute(): ?string
  {
    return @$this->meta->title ?? null;
  }
}
