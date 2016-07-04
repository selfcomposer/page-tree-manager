<?php

namespace SelfComposer\PageTreeManager\Tests\Scoped;

use SelfComposer\PageTreeManager\Tests\ScopedTestCase;
use SelfComposer\PageTreeManager\Tests\Models\ScopedPage;

class ScopedPageColumnTest extends ScopedTestCase
{
	/** @test */
	public function it_should_have_an_element_in_the_scoped_attribute()
	{
		$page = new ScopedPage();

		$this->assertObjectHasAttribute('scoped', $page);
		$this->assertTrue($page->isScoped());
	}
}