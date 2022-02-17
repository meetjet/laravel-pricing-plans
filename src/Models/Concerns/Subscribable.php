<?php

namespace Laravel\PricingPlans\Models\Concerns;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Laravel\PricingPlans\Models\Plan;
use Laravel\PricingPlans\SubscriptionBuilder;
use Laravel\PricingPlans\SubscriptionUsageManager;

trait Subscribable
{
    /**
     * Get user plan subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subscriptions()
    {
        return $this->morphMany(
            Config::get('plans.models.PlanSubscription'),
            'subscriber'
        );
    }

    /**
     * Get a subscription by name.
     *
     * @param  string $name Subscription name
     * @return \Laravel\PricingPlans\Models\PlanSubscription|null
     */
    public function subscription(string $name = 'default')
    {
        if ($this->relationLoaded('subscriptions')) {
            return $this->subscriptions
                ->orderByDesc(function ($subscription) {
                    return $subscription->created_at->getTimestamp();
                })
                ->first(function ($subscription) use ($name) {
                    return $subscription->name === $name;
                });
        }

        return $this->subscriptions()
            ->where('name', $name)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Check if the user has a given subscription.
     *
     * @param string      $name
     * @param string|null $planCode
     *
     * @return bool
     */
    public function subscribed(string $name = 'default', string $planCode = null): bool
    {
        $planSubscription = $this->subscription($name);

        if (is_null($planSubscription)) {
            return false;
        }

        if (is_null($planCode) || $planCode === $planSubscription->plan->code) {
            return $planSubscription->isActive();
        }

        return false;
    }

    /**
     * Subscribe user to a new plan.
     *
     * @param Plan   $plan
     * @param string $name
     *
     * @return SubscriptionBuilder
     */
    public function newSubscription(Plan $plan, string $name = 'default'): SubscriptionBuilder
    {
        return new SubscriptionBuilder($this, $name, $plan);
    }

    /**
     * Get subscription usage manager instance.
     *
     * @param  string $name Subscription name
     * @return SubscriptionUsageManager
     */
    public function subscriptionUsage(string $name = 'default'): SubscriptionUsageManager
    {
        return new SubscriptionUsageManager($this->subscription($name));
    }
}
