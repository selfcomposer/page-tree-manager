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

namespace SelfComposer\PageTreeManager;

use SelfComposer\PageTreeManager\Observers\SlugObserver;
use SelfComposer\PageTreeManager\PageNode;

use Illuminate\Database\Eloquent\Model;

/**
 * The class that stores the slugs of the pages.
 *
 * @package  SelfComposer\PageTreeManager;
 */
class Slug extends Model
{
	/**
	 * Column name to store the reference to the page.
	 *
	 * @var string
	 */
	protected $pageColumn = 'page_id';

	/**
	 * Column name for the page name.
	 *
	 * @var string
	 */
	protected $nameColumn = 'name';

	/**
	 * Column name for the slug page.
	 *
	 * @var string
	 */
	protected $slugColumn = 'slug';

	/**
	 * Column name for the page language.
	 *
	 * @var string
	 */
	protected $langColumn = 'lang_code';

	/**
	 * Guard fields for mass-assignment.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * The booting method of the model.
	 *
	 * It registers event listeners on the Slug instance,
	 * to create the correct slug on saving.
	 *
	 * @return void
	 */
	protected static function boot()
	{
     	parent::boot();

     	static::observe(SlugObserver::class);
    }

	/*
    |--------------------------------------------------------------------------
    | GETTERS
    |--------------------------------------------------------------------------
    */

    /**
	 * Get the page column's key.
     *
     * @return string
     */
    public function getPageColumnKey()
    {
    	return $this->pageColumn;
    }

    /**
     * Get the name column's key.
     *
     * @return string
     */
    public function getNameColumnKey()
	{
		return $this->nameColumn;
	}

	/**
	 * Get the slug column's key.
	 *
	 * @return string
	 */
	public function getSlugColumnKey()
	{
		return $this->slugColumn;
	}

	/**
	 * Get the language column's key.
	 *
	 * @return string
	 */
	public function getLangColumnKey()
	{
		return $this->langColumn;
	}

	/**
	 * Get the qualified page column's key.
	 *
	 * @return string
	 */
	public function getQualifiedPageColumnKey()
	{
		return $this->getTable() . '.' . $this->getPageColumnKey();
	}

	/*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

	/**
	 * Slug belongs to Page.
	 *
	 * @link https://laravel.com/docs/eloquent-relationships#one-to-one
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function page()
	{
		return $this->belongsTo(config('page-tree-manager.page_model'), $this->getPageColumnKey());
	}
}