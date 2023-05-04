<?php

namespace App\Providers;

use Symfony\Component\HttpFoundation\Response as HttpCode;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use App\Core\Commands\MakeRepository;
use App\Core\Commands\MakeCollection;
use App\Core\Commands\MakeException;
use App\Core\Commands\MakeContract;
use App\Core\Commands\MakeCriteria;
use App\Core\Commands\MakeConcern;
use App\Core\Commands\MakeRequest;
use App\Core\Commands\MakeService;
use App\Core\Commands\MakeHelper;
use App\Core\Commands\MakeFilter;
use App\Core\Commands\MakeModel;
use App\Core\Commands\MakeTrait;
use App\Core\Commands\MakeEnum;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCollection::class,
                MakeRepository::class,
                MakeException::class,
                MakeContract::class,
                MakeCriteria::class,
                MakeRequest::class,
                MakeConcern::class,
                MakeService::class,
                MakeHelper::class,
                MakeFilter::class,
                MakeModel::class,
                MakeTrait::class,
                MakeEnum::class,
            ]);
        }

        $this->registerResponseMacros();
    }

    /**
     * Register Response Macros
     *
     * @return void
     */
    private function registerResponseMacros(): void
    {
        Response::macro('success', function ($data = null) {
            if (is_null($data)) {
                $data = [ 'success' => true ];
            }
            return response()->json($data, 200);
        });

        Response::macro('successWithoutData', function () {
            return response()->json([ 'success' => true ], HttpCode::HTTP_OK);
        });

        Response::macro('error', function ($data, $statusCode = HttpCode::HTTP_BAD_REQUEST) {
            return response()->json($data, $statusCode);
        });
    }
}
