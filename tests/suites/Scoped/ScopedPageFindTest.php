<?php

namespace SelfComposer\PageTreeManager\Tests\Scoped;

use SelfComposer\PageTreeManager\Tests\ScopedTestCase;
use SelfComposer\PageTreeManager\Tests\Models\ScopedPage;

class ScopedPageFindTest extends ScopedTestCase
{
	/** @test */
	public function it_should_find_a_scoped_page_by_its_url()
	{
		$root = ScopedPage::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$page = ScopedPage::match('/root-page', $this->lang);

		$this->assertEquals($root->toArray(), $page->toArray());
	}

	/** @test */
	public function it_should_find_a_scoped_page_by_its_url_and_the_scoped_attributes_even_if_their_url_are_equals()
	{
		$root = ScopedPage::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$scoped_root = ScopedPage::create(['type' => 'generic']);
		$scoped_root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);


		$page = ScopedPage::match('/root-page', $this->lang);
		$scoped_page = ScopedPage::match('/root-page', $this->lang, ['type' => 'generic']);


		$this->assertEquals($page->getUrl($this->lang), $scoped_page->getUrl($this->lang));
		$this->assertEquals($root->toArray(), $page->toArray());
		$this->assertEquals($scoped_root->toArray(), $scoped_page->toArray());
	}

	/** @test */
	public function it_should_throw_an_exception_if_the_page_corresponding_to_the_url_does_not_exists_for_the_scoped_attribute()
	{
		$this->setExpectedException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$root = ScopedPage::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$page = ScopedPage::match('/root-page/child-page', $this->lang, ['type' => 'generic']);
	}
}