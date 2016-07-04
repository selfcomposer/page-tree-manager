<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;

class PageCreateTest extends GenericTestCase
{
	/** @test */
	public function it_should_return_the_slug_of_the_page_if_it_is_defined_during_the_creation()
	{
		$page = Page::create();

		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page', 'slug' => 'different slug']);

		$this->assertEquals('root page', $page->getName($this->lang));
		$this->assertEquals('/different-slug', $page->getUrl($this->lang));
	}

	/** @test */
	public function it_should_return_the_url_of_the_page_without_the_lang_code_for_a_new_root_page()
	{
		$page = Page::create();

		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$this->assertEquals('/root-page', $page->getUrl($this->lang));
	}

	/** @test */
	public function it_should_return_the_url_of_the_page_with_the_lang_code_for_a_new_root_page()
	{
		$page = Page::create();

		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$this->assertEquals('/en/root-page', $page->getUrl($this->lang, true));
	}

	/** @test */
	public function it_should_return_the_url_of_a_child_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

	 	$this->assertEquals('/root-page/child-page', $child->getUrl($this->lang));
	 	$this->assertEquals('/en/root-page/child-page', $child->getUrl($this->lang, true));
	}

	/** @test */
	public function it_should_throw_an_exception_if_we_try_to_add_a_slug_with_the_same_language_for_the_same_page_()
	{
		$this->setExpectedException(\Illuminate\Database\QueryException::class);

		$page = Page::create();

		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);
		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page modified']);
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_root_page_does_not_have_a_slug_for_the_same_language_of_the_child_page()
	{
		$this->expectException(\Exception::class);

		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => 'it', 'name' => 'pagina figlio']);

		$child->getUrl('it');
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_child_page_does_not_have_a_slug_for_the_language()
	{
		$this->expectException(\Exception::class);

		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => 'it', 'name' => 'pagina figlio']);

		$child->getUrl($this->lang);
	}

	/** @test */
	public function it_should_create_a_new_child_page_with_the_same_slug_of_the_root_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$this->assertEquals('/root-page/root-page', $child->getUrl($this->lang));
	}

	/** @test */
	public function it_should_allow_to_create_a_root_page_with_two_equal_slugs_for_different_languages()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);
		$root->slugs()->create(['lang_code' => 'it', 'name' => 'root page']);

		$this->assertEquals('/root-page', $root->getUrl($this->lang));
		$this->assertEquals('/root-page', $root->getUrl('it'));
		$this->assertEquals('/en/root-page', $root->getUrl($this->lang, true));
		$this->assertEquals('/it/root-page', $root->getUrl('it', true));
	}

	/** @test */
	public function it_should_add_an_incremental_counter_to_the_slug_of_the_root_page_if_it_already_exists()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$this->assertEquals('/root-page-1', $root2->getUrl($this->lang));
	}

	/** @test */
	public function it_should_add_one_to_the_incremental_counter_to_the_slug_of_the_root_page_if_it_already_exists()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root3 = Page::create();
		$root3->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$this->assertEquals('/root-page-2', $root3->getUrl($this->lang));
	}

	/** @test */
	public function it_should_add_an_incremental_counter_to_the_slug_of_the_child_page_if_it_already_exists()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$this->assertEquals('/root-page/child-page', $child->getUrl($this->lang));
		$this->assertEquals('/root-page/child-page-1', $child2->getUrl($this->lang));
	}

	/** @test */
	public function it_should_use_a_user_function_to_generate_the_suffix_for_an_already_existing_slug()
	{
		app('config')->set('page-tree-manager.slug_suffix', function() {
			return 'random';
		});

		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$this->assertEquals('/root-page/child-page', $child->getUrl($this->lang));
		$this->assertEquals('/root-page/child-page-random', $child2->getUrl($this->lang));
	}
}