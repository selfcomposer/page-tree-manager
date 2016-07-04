<?php

namespace SelfComposer\PageTreeManager\Tests\Scoped;

use SelfComposer\PageTreeManager\Tests\ScopedTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;
use SelfComposer\PageTreeManager\Tests\Models\ScopedPage;

class ScopedPageMoveTest extends ScopedTestCase
{
	/** @test */
	public function it_should_allow_multiple_root_pages_if_their_scoped_attributes_are_different()
	{
		$root = ScopedPage::create(['type' => 'product']);
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$scoped_root = ScopedPage::create(['type' => 'generic']);
		$scoped_root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$this->assertEquals('/root-page', $root->getUrl($this->lang));
		$this->assertEquals('/root-page', $scoped_root->getUrl($this->lang));
	}

	/** @test */
	public function it_should_throw_an_exception_while_making_a_page_root_if_another_root_is_present()
	{
		$this->setExpectedException(\Baum\MoveNotPossibleException::class);

		$scoped_root = ScopedPage::create(['type' => 'generic']);
		$scoped_root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $scoped_root->children()->create(['type' => 'generic']);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child->makeRoot();
	}
}