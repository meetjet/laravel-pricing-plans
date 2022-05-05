<?php

namespace Laravel\PricingPlans\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

trait BelongsToPlanModel
{
    /**
     * Get plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(
            Config::get('plans.models.Plan'),
            'plan_code',
            'code'
        );
    }

    /**
     * Scope by plan id.
     *
     * @param Builder $query
     * @param string  $planCode
     *
     * @return Builder
     */
    public function scopeByPlan(Builder $query, string $planCode): Builder
    {
        return $query->where('plan_code', $planCode);
    }
}
