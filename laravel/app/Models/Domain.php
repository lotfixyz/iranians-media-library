<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class domain
 *
 * @package App\Models
 * @property int $id
 * @property string|null $name
 * @property string|null $title
 * @property string|null $description
 * @property int|null $inactive
 * @property float|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newQuery()
 * @method static \Illuminate\Database\Query\Builder|Domain onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereInactive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Domain withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Domain withoutTrashed()
 * @mixin \Eloquent
 */
class Domain extends Model
{
    /**
     *
     */
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'domains';
}
