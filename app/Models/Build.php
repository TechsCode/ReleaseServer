<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Build
 * @package App\Models
 *
 * @property int $id
 * @property string $plugin_name
 * @property string $plugin_version
 * @property int $build_number
 * @property string $description
 * @property ReleaseVersion[] $releaseVersions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Build extends Model
{

    protected $fillable = [
        'plugin_name',
        'plugin_version',
        'build_number',
        'description',
    ];

    public function releaseVersions(): HasMany
    {
        return $this->hasMany(ReleaseVersion::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
