<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace SelfComposer\PageTreeManager\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;

use Cocur\Slugify\Slugify;

/**
 * Class that handles the generation of slugs and their uniqueness.
 *
 * @package  SelfComposer\PageTreeManager;
 */
class SlugService
{
    /**
     * The configuration of the package.
     *
     * @var array
     */
	protected $config;

    /**
     * The Slugify Package
     *
     * @var Cocur\Slugify
     */
	protected $slugify;

    /**
     * Constructor
     *
     * @param Slugify $slugify - the Cocur\Slugify package
     */
	public function __construct(Slugify $slugify)
	{
		$this->config = config('page-tree-manager');
		$this->slugify = $slugify;
	}


    /**
     * Apply the slug to the Model
     *
     * @param  Model  $model - the Slug model
     *
     * @return boolean
     */
	public function applySlug(Model $model)
	{
		if($this->needsSlug($model))
		{
			$slug = $this->buildsSlug($model);

			$slug = $this->makesSlugUnique($slug, $model);

			$model->setAttribute($model->getSlugColumnKey(), $slug);
		}

		return $model->isDirty($model->getSlugColumnKey());
	}

	/**
     * Determine whether the model needs slugging.
     *
     * @param Model $model - the Slug model
     *
     * @return bool
     */
    protected function needsSlug(Model $model)
    {
        if($model->isDirty($model->getSlugColumnKey()) || $model->isDirty($model->getNameColumnKey())) return true;

        return (! $model->exists);
    }

    /**
     * The function that builds the slug.
     *
     * The slug field takes precedence over the name.
     *
     * @param  Model  $model - the Slug model
     *
     * @return string
     */
	public function buildsSlug(Model $model)
	{
		if($model->isDirty($model->getSlugColumnKey()))
		{
			$column = $model->getSlugColumnKey();
		}
		else
		{
			$column = $model->getNameColumnKey();
		}

		return $this->slugify->slugify($model->getAttribute($column), array_get($this->config, 'slug_separator'));
	}

	/**
     * Checks if the slug should be unique, and makes it so if needed.
     *
     * @param  string $slug
     * @param  Model  $model
     *
     * @return string
     */
	protected function makesSlugUnique($slug, Model $model)
    {
        $list = $this->getExistingSlugs($slug, $model);

        // if the list is empty or the list doesn't contain the slug
        if ($list->count() === 0 || ! $list->contains($slug))
        {
            return $slug;
        }

        $method = array_get($this->config, 'slug_suffix');
        $separator = array_get($this->config, 'slug_separator');

        // if the method is null, we use an incremental counter
        if ($method === null)
        {
            $suffix = $this->generateSuffix($slug, $model, $list);
        }
        // otherwise we use a closure
        elseif (is_callable($method))
        {
            $suffix = call_user_func($method, $slug, $separator, $list);
        }
        else
        {
            throw new \Exception('Slug suffix for ' . get_class($model) . ' is not null or a closure.');
        }

        return $slug . $separator . $suffix;
    }

	/**
     * Get all existing slugs that are similar to the given slug.
     *
     * @param  string $slug
     * @param  Model  $model
     *
     * @return Illuminate\Support\Collection
     */
    protected function getExistingSlugs($slug, Model $model)
    {
        $separator = array_get($this->config, 'slug_separator');

        $query = $model->newQuery()
            ->join($model->page->getTable(), $model->page->getTable() . '.' . $model->page->getKeyName(), '=', $model->getQualifiedPageColumnKey())
            ->where($model->page->getScopedConditions())
            ->where($model->page->getParentColumnName(), $model->page->parent_id)
            ->where($model->getLangColumnKey(), $model->{$model->getLangColumnKey()})
            ->where(function(Builder $q) use ($slug, $separator, $model) {
                $q->where($model->getSlugColumnKey(), '=', $slug)
                  ->orWhere($model->getSlugColumnKey(), 'LIKE', $slug . $separator . '%');
            });

        $list = $query->select([$model->getSlugColumnKey()])->get();

        return $list->pluck([$model->getSlugColumnKey()]);
    }

    /**
     * Generate a unique suffix for the given slug.
     *
     * @param  string     $slug
     * @param  Model      $model
     * @param  Collection $list
     *
     * @return string
     */
    protected function generateSuffix($slug, Model $model, Collection $list)
    {
        $separator = array_get($this->config, 'slug_separator');

        $len = strlen($slug . $separator);

        // If the slug already exists, but belongs to
        // our model, return the current suffix.
        if ($list->search($slug) === $model->{$model->getSlugColumnKey()})
        {
            $suffix = explode($separator, $slug);

            return end($suffix);
        }

        $list->transform(function ($value, $key) use ($len) {
            return intval(substr($value, $len));
        });

        // find the highest value and return one greater.
        return $list->max() + 1;
    }
}