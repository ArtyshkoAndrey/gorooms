<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Categories for Attributes
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Attribute[] $attributes
 * @property-read int|null $attributes_count
 * @method static Builder|AttributeCategory newModelQuery()
 * @method static Builder|AttributeCategory newQuery()
 * @method static Builder|AttributeCategory query()
 * @method static Builder|AttributeCategory whereCreatedAt($value)
 * @method static Builder|AttributeCategory whereId($value)
 * @method static Builder|AttributeCategory whereName($value)
 * @method static Builder|AttributeCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AttributeCategory extends Model
{
  public const TYPES = [
    Hotel::class => 'Отели',
    Room::class => 'Номера',
  ];

  /**
   * @var string[]
   */
  protected $fillable = [
    'name',
    'model_type',
  ];

  /**
   * Attributed
   *
   * @return HasMany
   */
  public function attributes(): HasMany
  {
    return $this->hasMany(Attribute::class);
  }
}
