<?php
/**
 * Copyright 2020 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace LaravelJsonApi\Http\Controllers\Actions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Core\Resources\DataResponse;
use LaravelJsonApi\Core\Store\Store;
use LaravelJsonApi\Http\Requests\ResourceQuery;

trait FetchMany
{

    /**
     * Fetch zero to many JSON API resources.
     *
     * @param Store $store
     * @return Responsable
     */
    public function index(Store $store): Responsable
    {
        $request = ResourceQuery::queryMany(
            $resourceType = Route::current()->parameter('resource_type')
        );

        $data = $store
            ->queryAll($resourceType)
            ->using($request)
            ->firstOrPaginate($request->page());

        return new DataResponse($data);
    }
}