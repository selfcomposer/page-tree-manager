<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;

class PageUpdateTest extends GenericTestCase
{
	/** @test */
	public function it_should_update_the_name_and_the_slug_of_an_existing_root_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root->slug($this->lang)->setAttribute('name', 'modified root page')->save();

		$this->assertEquals('/modified-root-page', $root->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_only_the_slug_of_an_existing_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root->slug($this->lang)->setAttribute('slug', 'modified-root-page')->save();

		$this->assertEquals('root page', $root->getName($this->lang));
		$this->assertEquals('/modified-root-page', $root->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_the_name_and_the_slug_of_an_existing_child_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child->slug($this->lang)->setAttribute('name', 'modified child page')->save();

		$this->assertEquals('/root-page/modified-child-page', $child->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_only_the_slug_of_an_existing_child_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child->slug($this->lang)->setAttribute('slug', 'modified-child-page')->save();

		$this->assertEquals('child page', $child->getName($this->lang));
		$this->assertEquals('/root-page/modified-child-page', $child->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_the_name_and_the_slug_of_an_existing_root_page_using_a_unique_slug()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second root page']);


		$root2->slug($this->lang)->setAttribute('name', 'root page')->save();


		$this->assertEquals('/root-page-1', $root2->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_only_the_slug_of_an_existing_root_page_using_a_unique_slug()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second root page']);


		$root2->slug($this->lang)->setAttribute('slug', 'root-page')->save();


		$this->assertEquals('second root page', $root2->getName($this->lang));
		$this->assertEquals('/root-page-1', $root2->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_the_name_and_the_slug_of_an_existing_child_page_using_a_unique_slug()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second child page']);


		$child2->slug($this->lang)->setAttribute('name', 'child page')->save();


		$this->assertEquals('/root-page/child-page-1', $child2->getUrl($this->lang));
	}

	/** @test */
	public function it_should_update_only_the_slug_of_an_existing_child_page_using_a_unique_slug()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second child page']);


		$child2->slug($this->lang)->setAttribute('slug', 'child page')->save();


		$this->assertEquals('second child page', $child2->getName($this->lang));
		$this->assertEquals('/root-page/child-page-1', $child2->getUrl($this->lang));
	}
}