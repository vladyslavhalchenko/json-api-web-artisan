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

namespace LaravelJsonApi\Http;

use Illuminate\Contracts\Container\Container as IlluminateContainer;
use InvalidArgumentException;
use LaravelJsonApi\Core\Contracts\Schema\Container as SchemaContainerContract;
use LaravelJsonApi\Core\Contracts\Resources\Factory as ResourceFactoryContract;
use LaravelJsonApi\Core\Contracts\Resources\Container as ResourceContainerContract;
use LaravelJsonApi\Core\Encoder\Encoder;
use LaravelJsonApi\Core\Encoder\Factory as EncoderFactory;
use LaravelJsonApi\Core\Resources\Container as ResourceContainer;
use LaravelJsonApi\Core\Resources\Factory as ResourceFactory;
use LaravelJsonApi\Core\Schema\Container as SchemaContainer;
use LaravelJsonApi\Core\Store\Store;

abstract class Server
{

    /**
     * @var IlluminateContainer
     */
    private IlluminateContainer $container;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var SchemaContainerContract|null
     */
    private ?SchemaContainerContract $schemas = null;

    /**
     * @var ResourceFactoryContract|null
     */
    private ?ResourceFactoryContract $resources = null;

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    abstract public function serving(): void;

    /**
     * Get the server's list of schemas.
     *
     * @return array
     */
    abstract protected function schemas(): array;

    /**
     * Server constructor.
     *
     * @param IlluminateContainer $container
     * @param string $name
     */
    public function __construct(IlluminateContainer $container, string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Expecting a non-empty string.');
        }

        $this->container = $container;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return SchemaContainerContract
     */
    public function container(): SchemaContainerContract
    {
        if ($this->schemas) {
            return $this->schemas;
        }

        return $this->schemas = new SchemaContainer(
            $this->container, $this->schemas()
        );
    }

    /**
     * @return ResourceContainerContract
     */
    public function resources(): ResourceContainerContract
    {
        return new ResourceContainer($this->allResources());
    }

    /**
     * @return Store
     */
    public function store(): Store
    {
        return new Store($this->container());
    }

    /**
     * @return Encoder
     */
    public function encoder(): Encoder
    {
        /** @var EncoderFactory $factory */
        $factory = $this->container->make(EncoderFactory::class);

        return $factory->build($this->allResources());
    }

    /**
     * @return ResourceFactoryContract
     */
    private function allResources(): ResourceFactoryContract
    {
        if ($this->resources) {
            return $this->resources;
        }

        return $this->resources = new ResourceFactory(
            $this->container()->resources()
        );
    }

}
