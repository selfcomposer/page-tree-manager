<?php

namespace SelfComposer\PageTreeManager\Tests\Scoped;

use SelfComposer\PageTreeManager\Tests\ScopedTestCase;
use SelfComposer\PageTreeManager\Tests\Models\ScopedPage;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ScopedPageFindTest extends ScopedTestCase
{
	/** @test */
	public function it_should_find_a_root_scoped_page_by_its_url()
	{
		$root = ScopedPage::create();

		$page = ScopedPage::lookup('/', $this->lang);

		$this->assertEquals($root->id, $page->id);
	}

	/** @test */
	public function it_should_find_a_root_scoped_page_by_its_url_and_the_scoped_attributes_even_if_their_urls_are_equals()
	{
		$root = ScopedPage::create();
		$scoped_root = ScopedPage::create(['type' => 'generic']);


		$page = ScopedPage::lookup('/', $this->lang, ['type' => null]);
		$scoped_page = ScopedPage::lookup('/', $this->lang, ['type' => 'generic']);

		
		$this->assertEquals($root->id, $page->id);
		$this->assertEquals($scoped_root->id, $scoped_page->id);
	}

	/** @test */
	public function it_should_find_a_scoped_page_by_its_url_even_if_a_not_scoped_tree_exists_with_the_same_url()
	{
		$root = ScopedPage::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$grandchild = $child->children()->create([]);
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$scoped_root = ScopedPage::create(['type' => 'generic']);

		$scoped_child = $scoped_root->children()->create(['type' => 'generic']);
		$scoped_child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$scoped_grandchild = $scoped_child->children()->create(['type' => 'generic']);
		$scoped_grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);


		$page = ScopedPage::lookup('/child-page/grandchild-page', $this->lang, ['type' => null]);
		$scoped_page = ScopedPage::lookup('/child-page/grandchild-page', $this->lang, ['type' => 'generic']);


		$this->assertEquals($grandchild->id, $page->id);
		$this->assertEquals($scoped_grandchild->id, $scoped_page->id);
	}

	/** @test */
	public function it_should_return_an_exception_if_the_page_corresponding_to_the_url_does_not_exists_for_the_scoped_attribute()
	{
		$this->setExpectedException(ModelNotFoundException::class);

		$root = ScopedPage::create();

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$page = ScopedPage::lookup('/child-page', $this->lang, ['type' => 'generic']);
	}
}