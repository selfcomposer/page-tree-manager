<?php

namespace SelfComposer\PageTreeManager\Tests\Scoped;

use SelfComposer\PageTreeManager\Tests\ScopedTestCase;
use SelfComposer\PageTreeManager\Tests\Models\ScopedPage;

class ScopedPageCreateTest extends ScopedTestCase
{
	/** @test */
	public function it_should_create_a_scoped_root_page()
	{
		$root = ScopedPage::create(['type' => 'generic']);
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'generic root page']);

		$this->assertEquals('/generic-root-page', $root->getUrl($this->lang));
	}

	/** @test */
	public function it_should_create_a_scoped_child_page()
	{
		$root = ScopedPage::create(['type' => 'generic']);
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'generic root page']);

		$child = $root->children()->create(['type' => 'generic']);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$this->assertEquals('/generic-root-page/child-page', $child->getUrl($this->lang));
	}

	/** @test */
	public function it_should_throw_an_exception_while_adding_a_non_scoped_page_to_a_scoped_page()
	{
		$this->setExpectedException(\Baum\MoveNotPossibleException::class);

		$root = ScopedPage::create(['type' => 'generic']);
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'generic root page']);

		$child = $root->children()->create([]);
	}

	/** @test */
	public function it_should_throw_an_exception_while_adding_a_scoped_page_to_a_non_scoped_page()
	{
		$this->setExpectedException(\Baum\MoveNotPossibleException::class);

		$root = ScopedPage::create([]);
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'generic root page']);

		$child = $root->children()->create(['type' => 'generic']);
	}
}