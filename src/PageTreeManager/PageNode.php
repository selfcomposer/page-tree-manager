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

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use SelfComposer\PageTreeManager\PageObserver;
use SelfComposer\PageTreeManager\Slug;

/**
 * Abstract class used to wrap the Baum Node.
 *
 * @package  SelfComposer\PageTreeManager;
 */
abstract class PageNode extends \Baum\Node
{
	/**
	 * Get the name of the page.
	 *
	 * @param  string $lang_code - the language code
	 *
	 * @return string
	 */
	public function getName($lang_code)
	{
		return $this->slug($lang_code)->getAttribute($this->slug($lang_code)->getNameColumnKey());
	}

	/**
	 * Get the slug of the page.
	 *
	 * @param  string $lang_code - the language code
	 *
	 * @return string
	 */
	public function getSlug($lang_code)
	{
		return $this->slug($lang_code)->getAttribute($this->slug($lang_code)->getSlugColumnKey());
	}

	/**
	 * Get the URL of the page.
	 *
	 * @param  string  $lang_code - the language code
	 * @param  boolean $lang_prefix - if the language prefix should be appended
	 *
	 * @return string
	 */
	public function getUrl($lang_code, $lang_prefix = false)
	{
		$slug = new Slug;

		$pages = $this->ancestorsAndSelf()->with(['slugs' => function($query) use ($slug, $lang_code)
		{
			$query->where($slug->getLangColumnKey(), $lang_code);
		}])->get();

		$url = $pages->reduce(function($carry, $item) {
			if($item->slugs->count() == 0) throw new \Exception("The page {$item->id} does not have a slug for this language");

			return $carry . '/' . $item->slugs->first()->slug;
		});

		return $lang_prefix ? '/' . $lang_code . $url : $url;
	}

	/**
	 * Search a page by its given url, lang code and optional scoped attributes.
	 * 
	 * @param  string $url
	 * @param  string $lang_code
	 * @param  array  $scoped
	 * @return PageNode
	 */
	public static function lookup($url, $lang_code, $scoped = [])
	{
		$slug = new Slug;

		if(starts_with($url, '/')) {
			$url = substr($url, 1, strlen($url));
		}

		$tokens = explode('/', $url);

		$root = self::where($scoped)->where('depth', 0)->firstOrFail();

		if(! $root) throw new ModelNotFoundException();

		if(head($tokens) === "") return $root;

		$hierarchy = $root->descendantsAndSelf()->with('slugs')->limitDepth(count($tokens))->get()->toHierarchy();

		if($hierarchy->isEmpty()) throw new ModelNotFoundException();

		return self::reduce($hierarchy, null, $tokens, $lang_code);
	}

	/**
	 * Reduces a hiearachy tree to a single page.
	 * 
	 * @param  Collection $elements
	 * @param  integer    $id
	 * @param  array      $tokens
	 * @param  string     $lang_code
	 * @return self
	 */
	private static function reduce($elements, $id, $tokens, $lang_code)
	{
		// skip if it is the root page
		if(! is_null($id)) {
			$token = array_shift($tokens);

			$elements = $elements->filter(function($element, $key) use ($id, $token, $lang_code) {
				foreach($element->slugs as $slug) {
					return (
						($slug->{$slug->getSlugColumnKey()} == $token) &&
						($slug->{$slug->getLangColumnKey()} == $lang_code) &&
						($element->{$element->getParentColumnName()} == $id)
					);
				}
			});

			if($elements->isEmpty()) throw new ModelNotFoundException();

			if($elements->first()->children->isEmpty() && empty($tokens)) return $elements->first();
		}
		
		return self::reduce($elements->first()->children, $elements->first()->id, $tokens, $lang_code);
	}
	
	/**
	 * Build the nested collection which represents the tree.
	 *
	 * @return Collection
	 */
	public function getTree()
	{
		return $this->getDescendantsAndSelf()->toHierarchy();
	}

	/**
	 * Return the conditions to build scoped queries.
	 *
	 * @return array
	 */
	public function getScopedConditions()
	{
		return array_map(function($column) {
   			return [$column, '=', $this->$column];
   		}, $this->getScopedColumns());
	}

	/*
    |--------------------------------------------------------------------------
    | QUESTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Returns true if a page is exactly above another page.
     *
     * @param  PageNode  $page
     *
     * @return boolean
     */
	public function isExactlyAbove($page)
	{
		return $this->getRight() == $page->getLeft() - 1;
	}

	/**
	 * Returns true if a page is exactly below another page.
	 *
	 * @param  PageNode  $page
	 *
	 * @return boolean
	 */
	public function isExactlyBelow($page)
	{
		return $this->getLeft() == $page->getRight() + 1;
	}

	/**
	 * Returns true if a page is sibling of another page.
	 *
	 * @param  PageNode  $page
	 *
	 * @return boolean
	 */
	public function isSiblingOf($page)
	{
		return ($this->getDepth() == $page->getDepth() && $this->getParentId() == $page->getParentId());
	}

	/*
    |--------------------------------------------------------------------------
    | MOVEMENTS
    |--------------------------------------------------------------------------
    */

    /**
     * Make current page a root page, provided that a page
     * with the same scope does not exist.
     *
     * @return PageNode
     */
   	public function makeRoot()
   	{
   		// guard agains root with the same scope
   		$present = $this->newQuery()
   			->where($this->getScopedConditions())
   			->whereNull($this->getParentColumnName())
   			->count() > 0;

   		if($present) throw new \Baum\MoveNotPossibleException();

   		return parent::makeRoot();
   	}

   	/**
   	 * Move the page up to another page.
   	 * Is an alias for moveToLeftOf.
   	 *
   	 * @param  PageNode $page
   	 *
   	 * @return PageNode
   	 */
	public function moveUpTo($page)
	{
		return $this->moveToLeftOf($page);
	}

	/**
	 * Move the page down to another page.
	 * Is an alias for moveToRightOf.
	 *
	 * @param  PageNode $page
	 *
	 * @return PageNode
	 */
	public function moveDownTo($page)
	{
		return $this->moveToRightOf($page);
	}

	/*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

	/**
	 * PageNode has many Slugs
	 *
	 * @link https://laravel.com/docs/eloquent-relationships#one-to-many
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function slugs()
	{
		return $this->hasMany(Slug::class, config('page-tree-manager.page_foreign_key'));
	}

	/**
	 * Filter the Slugs relationship with the provided language.
	 *
	 * @param  string $lang_code
	 *
	 * @return Slug
	 */
	public function slug($lang_code)
	{
		return $this->slugs()->where('lang_code', $lang_code)->firstOrFail();
	}
}
