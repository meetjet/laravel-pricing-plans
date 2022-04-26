<?php

namespace Laravel\PricingPlans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Laravel\PricingPlans\Models\Concerns\HasCode;
use Laravel\PricingPlans\Models\Concerns\Resettable;
use Sushi\Sushi;

/**
 * Class Feature
 * @package Laravel\PricingPlans\Models
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $value
 * @property string $description
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Feature extends Model
{
    use Resettable, HasCode;
    use Sushi;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'interval_unit',
        'interval_count',
        'sort_order',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get sushi rows.
     *
     * @return array
     */
    public function getRows(): array
    {
        return array_map(static function ($_item) {
            $_item['interval_unit'] = $_item['interval_unit'] ?? null;
            $_item['interval_count'] = $_item['interval_count'] ?? null;
            return $_item;
        }, Arr::collapse(config('tariff-features')));
    }

    protected function sushiShouldCache(): bool
    {
        return true;
    }

    protected function sushiCacheReferencePath(): string
    {
        return config_path("tariff-features.php");
    }

    /**
     * Plan constructor.
     *
     * @param array $attributes
     * @deprecated
     */
//    public function __construct(array $attributes = [])
//    {
//        parent::__construct($attributes);
//
//        $this->setTable(Config::get('plans.tables.features'));
//    }

    /**
     * @return BelongsToMany
     * @deprecated
     */
//    public function plans(): BelongsToMany
//    {
//        return $this->belongsToMany(
//            Config::get('plans.models.Plan'),
//            Config::get('plans.tables.plan_features'),
//            'feature_id',
//            'plan_id'
//        )->using(Config::get('plans.models.PlanFeature'));
//    }

    /**
     * Get feature usage.
     *
     * This will return all related subscriptions usages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @deprecated
     */
//    public function usage()
//    {
//        return $this->hasMany(
//            Config::get('plans.models.PlanSubscriptionUsage'),
//            'feature_code',
//            'code'
//        );
//    }
}
