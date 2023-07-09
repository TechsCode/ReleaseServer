<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ReleaseVersion
 * @package App\Models
 *
 * @property int $id
 * @property int $build_id
 * @property string $plugin_name
 * @property string $plugin_version
 * @property Build $build
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ReleaseVersion extends Model
{

    protected $fillable = [
        'build_id',
        'plugin_name',
        'plugin_version',
    ];

    public function build(): BelongsTo
    {
        return $this->belongsTo(Build::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
