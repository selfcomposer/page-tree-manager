<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;
use SelfComposer\PageTreeManager\Tests\Models\ScopedStringPage;
use SelfComposer\PageTreeManager\Tests\Models\ScopedRelationshipPage;

class PageColumnTest extends GenericTestCase
{
	/** @test */
	public function it_should_have_a_scoped_property()
	{
		$page = new Page;

		$this->assertObjectHasAttribute('scoped', $page);
		$this->assertCount(0, $page->getScopedColumns());
	}

	/** @test */
	public function it_should_return_the_name_of_the_page_for_the_specified_language()
	{
		$page = Page::create();
		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'generic page']);

		$this->assertEquals('generic page', $page->getName($this->lang));
	}

	/** @test */
	public function it_should_throw_an_exception_while_trying_to_get_the_page_for_the_specified_language_if_it_does_not_exists()
	{
		$this->setExpectedException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$page = Page::create();
		$page->slugs()->create(['lang_code' => $this->lang, 'name' => 'generic page']);

		$page->getName('it');
	}
}