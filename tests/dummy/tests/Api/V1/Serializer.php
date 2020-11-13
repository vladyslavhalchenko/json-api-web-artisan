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

namespace App\Tests\Api\V1;

use App\Models\Post;
use App\Models\User;
use LaravelJsonApi\Core\Document\ResourceObject;
use function url;

class Serializer
{

    /**
     * Get the expected post resource.
     *
     * @param Post $post
     * @return ResourceObject
     */
    public function post(Post $post): ResourceObject
    {
        $self = url('/api/v1/posts', $post);

        return ResourceObject::fromArray([
            'type' => 'posts',
            'id' => (string) $post->getRouteKey(),
            'attributes' => [
                'content' => $post->content,
                'createdAt' => $post->created_at->toJSON(),
                'slug' => $post->slug,
                'synopsis' => $post->synopsis,
                'title' => $post->title,
                'updatedAt' => $post->updated_at->toJSON(),
            ],
            'relationships' => [
                'author' => [
                    'links' => [
                        'self' => "{$self}/relationships/author",
                        'related' => "{$self}/author",
                    ],
                ],
                'comments' => [
                    'links' => [
                        'self' => "{$self}/relationships/comments",
                        'related' => "{$self}/comments",
                    ],
                ],
            ],
            'links' => [
                'self' => $self,
            ],
        ]);
    }

    /**
     * Get the expected user resource.
     *
     * @param User $user
     * @return ResourceObject
     */
    public function user(User $user): ResourceObject
    {
        $self = url('/api/v1/users', $user);

        return ResourceObject::fromArray([
            'type' => 'users',
            'id' => (string) $user->getRouteKey(),
            'attributes' => [
                'createdAt' => $user->created_at->toJSON(),
                'name' => $user->name,
                'updatedAt' => $user->updated_at->toJSON(),
            ],
            'links' => [
                'self' => $self,
            ],
        ]);
    }
}
