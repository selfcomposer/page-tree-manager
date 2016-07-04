<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageFindTest extends GenericTestCase
{
	/** @test */
	public function it_should_find_a_root_page_by_its_url()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$page = Page::match('/root-page', $this->lang);

		$this->assertEquals($root->toArray(), $page->toArray());
	}

	/** @test */
	public function it_should_find_the_root_page_by_its_url_and_language_code()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => 'it', 'name' => 'root page']);

		$page = Page::match('/root-page', $this->lang);
		$page2 = Page::match('/root-page', 'it');

		$this->assertEquals($root->toArray(), $page->toArray());
		$this->assertEquals($root2->toArray(), $page2->toArray());
	}

	/** @test */
	public function it_should_find_a_child_page_by_its_url()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$page = Page::match('/root-page/child-page', $this->lang);

		$this->assertEquals($child->toArray(), $page->toArray());
	}

	/** @test */
	public function it_should_find_a_deeper_page_by_its_url_even_if_it_exists_a_subtree_with_the_same_name()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$grandchild = $child->children()->create([]);
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root2->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$page = Page::match('/root-page/child-page/grandchild-page', $this->lang);

		$this->assertEquals($grandchild->toArray(), $page->toArray());
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_page_corresponding_to_the_url_does_not_exists()
	{
		$this->expectException(ModelNotFoundException::class);

		$page = Page::match('/root-page/child-page', $this->lang);
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_page_corresponding_to_the_url_does_not_exists_in_the_requested_language()
	{
		$this->expectException(ModelNotFoundException::class);

		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$page = Page::match('/root-page/child-page', 'it');
	}
}