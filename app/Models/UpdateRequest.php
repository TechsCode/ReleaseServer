<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

/**
 * Class UpdateRequest
 * @package App\Models
 *
 * @property int $id
 * @property string $update_token
 * @property UpdateRequestStatus $status
 * @property string $plugin_name
 * @property string $current_version
 * @property Carbon $current_version_date
 * @property string $update_to
 * @property array $allowed_plugins
 * @property bool $has_beta_access
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UpdateRequest extends Model
{

    protected $fillable = [
        'update_token',
        'status',
        'plugin_name',
        'current_version',
        'update_to',
        'allowed_plugins',
        'has_beta_access',
    ];

    protected $casts = [
        'has_beta_access' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'current_version_date'
    ];

    public function getStatusAttribute($value): UpdateRequestStatus
    {
        return UpdateRequestStatus::from($value);
    }

    public function getAllowedPluginsAttribute($value): array
    {
        if (isset($value) && is_string($value) && strlen($value) > 0) {
            return explode(',', $value);
        }
        return [];
    }

    public function getCurrentVersionDateAttribute($value){
        $plugin_release = ReleaseVersion::where('plugin_name', $this->plugin_name)
            ->where('plugin_version', $this->current_version)
            ->first();
        if($plugin_release){
            return $plugin_release->created_at;
        }
        return null;
    }

}
