<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;

class PageMoveTest extends GenericTestCase
{
	/** @test */
	public function it_should_throw_an_exception_while_moving_a_child_page_to_root_if_another_root_is_present()
	{
		$this->setExpectedException(\Baum\MoveNotPossibleException::class);

		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child->makeRoot();
	}

	/** @test */
	public function it_should_make_a_page_child_of_another_page()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$root2 = Page::create();
		$root2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second root page']);

		$root2->makeChildOf($root);

		$this->assertEquals('/root-page/second-root-page', $root2->getUrl($this->lang));
	}

	/** @test */
	public function it_should_move_a_child_page_above_another_child_page_in_the_same_subtree()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = $root->children()->create([]);
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);

		$child2 = $root->children()->create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second child page']);

		$this->assertTrue($child2->isExactlyBelow($child));

		$child2->moveUpTo($child);

		$this->assertTrue($child2->isExactlyAbove($child));
	}

	/** @test */
	public function it_should_move_a_deep_nested_page_to_first_child_of_root()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = Page::create();
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);
		$child->makeChildOf($root);

		$grandchild = Page::create();
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);
		$grandchild->makeFirstChildOf($root);

		$root->reload();
		$child->reload();

		$this->assertTrue($grandchild->isExactlyAbove($child));
		$this->assertEquals('/root-page/child-page', $child->getUrl($this->lang));
		$this->assertEquals('/root-page/grandchild-page', $grandchild->getUrl($this->lang));
	}

	/** @test */
	public function it_should_move_a_deep_nested_page_to_last_child_of_root()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = Page::create();
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);
		$child->makeChildOf($root);

		$child2 = Page::create([]);
		$child2->slugs()->create(['lang_code' => $this->lang, 'name' => 'second child page']);
		$child2->makeChildOf($root);

		$child3 = Page::create();
		$child3->slugs()->create(['lang_code' => $this->lang, 'name' => 'third child page']);
		$child3->makeChildOf($root);

		$this->assertTrue($child3->isExactlyBelow($child2));


		$child2 = Page::find($child2->id);
		$child2->makeLastChildOf($root);

		$child3->reload();

		$this->assertTrue($child2->isExactlyBelow($child3));
		$this->assertEquals('/root-page/third-child-page', $child3->getUrl($this->lang));
	}
}
