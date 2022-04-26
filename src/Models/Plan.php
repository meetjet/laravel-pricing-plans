<?php

namespace Laravel\PricingPlans\Models;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
//use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\Pure;
use Laravel\PricingPlans\Models\Concerns\HasCode;
use Laravel\PricingPlans\Models\Concerns\Resettable;
use Laravel\PricingPlans\Period;
use Sushi\Sushi;

/**
 * Class Plan
 * @package Laravel\PricingPlans\Models
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $features
 * @property string $description
 * @property float $price
 * @property string $interval_unit
 * @property int $interval_count
 * @property int $trial_period_days
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Plan extends Model
{
    use Resettable, HasCode;
    use Sushi;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'interval_unit',
        'interval_count',
        'trial_period_days',
        'is_one_time',
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

    protected $appends = ['features'];

    /**
     * Get sushi rows.
     *
     * @return array
     */
    public function getRows(): array
    {
        $rows = [];
        $plans = config('tariff-plans');

        foreach ($plans as $_planBody) {
            $rows[] = $_planBody['params'];
        }

        return $rows;
    }

    protected function sushiShouldCache(): bool
    {
        return true;
    }

    protected function sushiCacheReferencePath(): string
    {
        return config_path("tariff-plans.php");
    }

    /**
     * Boot function for using with User Events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Default interval is 1 month
        static::saving(function ($model) {
            if (!$model->interval_unit) {
                $model->interval_unit = 'month';
            }

            if (!$model->interval_count) {
                $model->interval_count = 1;
            }
        });
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
//        $this->setTable(Config::get('plans.tables.plans'));
//    }

    /**
     * Get plan features.
     *
     * @return BelongsToMany
     * @deprecated
     */
//    public function features(): BelongsToMany
//    {
//        return $this
//            ->belongsToMany(
//                Config::get('plans.models.Feature'),
//                Config::get('plans.tables.plan_features'),
//                'plan_id',
//                'feature_id'
//            )
//            ->using(Config::get('plans.models.PlanFeature'))
//            ->withPivot(['value', 'note'])
//            ->orderBy('sort_order');
//    }

    /**
     * Get features for the current plan.
     *
     * @return Collection
     */
    public function getFeaturesAttribute(): Collection
    {
        $features = null;
        $plans = config('tariff-plans');

        foreach ($plans as $_planBody) {
            if ($_planBody['params']['code'] === $this->code) {
                foreach ($_planBody['features'] as $_featureCode => $_featureBody) {
                    /** @var \Laravel\PricingPlans\Models\Feature $feature */
                    $feature = Feature::code($_featureCode)->first();

                    if ($feature) {
                        $feature->value = $_featureBody['value'];
                        $features[] = $feature;
                    }
                }
            }
        }

        return collect($features);
    }

    /**
     * Get plan subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @deprecated
     */
//    public function subscriptions()
//    {
//        return $this->hasMany(
//            Config::get('plans.models.PlanSubscription'),
//            'plan_id',
//            'id'
//        );
//    }

    /**
     * Check if plan is free.
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return ((float)$this->price <= 0.00);
    }

    /**
     * Check if plan has trial.
     *
     * @return bool
     */
    public function hasTrial(): bool
    {
        return (is_numeric($this->trial_period_days) and $this->trial_period_days > 0);
    }
}
