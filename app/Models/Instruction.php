<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Instruction
 *
 * @property int         $id
 * @property string      $header
 * @property string      $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Instruction newModelQuery()
 * @method static Builder|Instruction newQuery()
 * @method static Builder|Instruction query()
 * @method static Builder|Instruction whereContent($value)
 * @method static Builder|Instruction whereCreatedAt($value)
 * @method static Builder|Instruction whereHeader($value)
 * @method static Builder|Instruction whereId($value)
 * @method static Builder|Instruction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Instruction extends Model
{
  use HasFactory;

  protected $fillable = [
    'header',
    'content',
  ];
}
