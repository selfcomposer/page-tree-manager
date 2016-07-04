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

namespace SelfComposer\PageTreeManager\Observers;

use SelfComposer\PageTreeManager\Services\SlugService;
use SelfComposer\PageTreeManager\Slug;

/**
 * Class that observes the events of the Slug model.
 *
 * @package  SelfComposer\PageTreeManager
 */
class SlugObserver
{
	/**
	 * The service that handles the slug generation.
	 *
	 * @var SlugService
	 */
	private $slugService;

	/**
	 * Construct
	 *
	 * @param SlugService $slugService
	 */
	public function __construct(SlugService $slugService)
	{
		$this->slugService = $slugService;
	}

	/**
	 * Handles the model's saving event.
	 *
	 * @param  Slug $slug
	 *
	 * @return boolean
	 */
	public function saving(Slug $slug)
	{
		return $this->slugService->applySlug($slug);
	}
}