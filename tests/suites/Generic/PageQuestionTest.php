<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;

class PageQuestionTest extends GenericTestCase
{
	/** @test */
	public function it_should_know_if_two_pages_are_nearby()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second child page']);

		$this->assertTrue($child2->isExactlyBelow($child));
		$this->assertTrue($child->isExactlyAbove($child2));
	}

	/** @test */
	public function it_should_know_if_a_page_is_sibling_of_another_page_in_the_same_subtree()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second child page']);

		$grandchild = $child2->children()->create([]);
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);

		$this->assertTrue($child->isSiblingOf($child2));
		$this->assertFalse($child->isSiblingOf($grandchild));
	}
}