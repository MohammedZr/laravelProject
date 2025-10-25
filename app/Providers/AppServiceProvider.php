<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
           // Mini Cart للمستخدم الصيدلية فقط
    View::composer('*', function ($view) {
        try {
            $mini = [
                'count' => 0,
                'total' => 0,
                'byCompany' => collect(),
            ];

            if (auth()->check() && auth()->user()->role === 'pharmacy') {
                $cart = Cart::forUser(auth()->id())->load(['items.drug','items.company']);
                $mini['count'] = $cart->items->sum('quantity');
                $mini['total'] = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
                $mini['byCompany'] = $cart->items->groupBy('company_id');
            }

            $view->with('miniCart', $mini);
        } catch (\Throwable $e) {
            // لا نفشل العرض بسبب السلة
            $view->with('miniCart', ['count'=>0,'total'=>0,'byCompany'=>collect()]);
        }
    });
    }
}
