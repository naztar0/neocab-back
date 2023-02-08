<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Microsoft\Graph\Graph;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Graph::class, function () {
            $graph = new Graph();
            $graph->setAccessToken(config('services.azure.client_secret'));
            return $graph;
        });
        $this->app->bind(OAuth2Client::class, function () {
            return new OAuth2Client([
                'clientId'                => config('services.azure.client_id'),
                'clientSecret'            => config('services.azure.client_secret'),
                'redirectUri'             => config('services.azure.redirect_uri'),
                'urlAuthorize'            => config('services.azure.authority').config('services.azure.authorize_endpoint'),
                'urlAccessToken'          => config('services.azure.authority').config('services.azure.token_endpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes'                  => config('services.azure.scopes'),
                ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (!Collection::hasMacro('paginate')) {
            Collection::macro('paginate',
                function ($perPage = 10, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                    return (
                        new LengthAwarePaginator(
                            array_values($this->forPage($page, $perPage)->toArray()), $this->count(), $perPage, $page, $options
                        )
                    )->withPath('');
                });
        }
    }
}
