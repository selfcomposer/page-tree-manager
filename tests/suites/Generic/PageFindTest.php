<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;
use SelfComposer\PageTreeManager\Slug;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageFindTest extends GenericTestCase
{
	/** @test */
	public function it_should_find_the_root_page_by_an_empty_url_ignoring_the_language()
	{
		$root = Page::create();

		$page = Page::lookup('', $this->lang);

		$this->assertEquals($root->id, $page->id);
	}

	/** @test */
	public function it_should_find_the_root_page_using_a_slash_ignoring_the_language()
	{
		$root = Page::create();

		$page = Page::lookup('/', $this->lang);

		$this->assertEquals($root->id, $page->id);
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_root_page_does_not_exist()
	{
		$this->setExpectedException(ModelNotFoundException::class);

		$page = Page::lookup('/', $this->lang);
	}

	/** @test */
	public function it_should_find_a_child_page_by_its_url()
	{
		$root = Page::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);


		$page = Page::lookup('/child-page', $this->lang);


		$this->assertEquals($child->id, $page->id);
	}

	/** @test */
	public function it_should_find_a_child_page_by_its_url_and_its_lang_code()
	{
		$root = Page::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => 'it', 'name' => 'child page']);

		
		$page  = Page::lookup('/child-page', $this->lang);
		$page2 = Page::lookup('/child-page', 'it');


		$this->assertEquals($child->id, $page->id);
		$this->assertEquals($child2->id, $page2->id);
	}

	/** @test */
	public function it_should_throw_an_exception_if_a_child_page_is_not_found()
	{
		$this->setExpectedException(ModelNotFoundException::class);

		$root = Page::create();

		$page = Page::lookup('/child-page', $this->lang);
	}

	/** @test */
	public function it_should_find_a_deeper_page_by_its_url_even_if_it_exists_a_subtree_with_the_same_name()
	{
		$root = Page::create();
		
		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$grandchild = $child->children()->create([]);
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$grandchild2 = $child2->children()->create([]);
		$grandchild2->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);


		$page  = Page::lookup('/child-page/grandchild-page', $this->lang);
		$page2 = Page::lookup('/grandchild-page/child-page', $this->lang);


		$this->assertEquals($grandchild->id, $page->id);
		$this->assertEquals($grandchild2->id, $page2->id);
	}

	/** @test */
	public function it_should_return_the_correct_page_within_a_complex_nested_tree()
	{
		$root = Page::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$grandchild = $child->children()->create([]);
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'child2 page']);

		$grandchild2 = $child2->children()->create([]);
		$grandchild2->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$child3 = $root->children()->create([]);
		$child3->slugs()->create(['lang_code' => $this->lang, 'name' => 'child3 page']);

		$grandchild3 = $child3->children()->create([]);
		$grandchild3->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

	
		$page  = Page::lookup('/child-page/grandchild-page', $this->lang);
		$page2 = Page::lookup('/child2-page/grandchild-page', $this->lang);
		$page3 = Page::lookup('/child3-page/grandchild-page', $this->lang);

	
		$this->assertEquals($grandchild->id, $page->id);
		$this->assertEquals($grandchild2->id, $page2->id);
		$this->assertEquals($grandchild3->id, $page3->id);
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_page_corresponding_to_the_url_does_not_exists_in_the_requested_language()
	{
		$this->setExpectedException(ModelNotFoundException::class);

		$root = Page::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$page = Page::lookup('/child-page', 'it');
	}

	/** @test */
	public function it_should_throw_an_exception_if_an_intermediate_page_does_not_have_a_slug_for_the_specified_language()
	{
		$this->setExpectedException(ModelNotFoundException::class);

		$root = Page::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$grandchild = $child->children()->create([]);
		$grandchild->slugs()->create(['lang_code' => 'it', 'name' => 'grandchild page']);

		$grandgrandchild = $child->children()->create([]);
		$grandgrandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$page = Page::lookup('/child-page/grandchild-page/grandgrandchild-page', $this->lang);
	}
}