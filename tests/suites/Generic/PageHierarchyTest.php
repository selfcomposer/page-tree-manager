<?php

namespace SelfComposer\PageTreeManager\Tests\Generic;

use SelfComposer\PageTreeManager\Tests\GenericTestCase;
use SelfComposer\PageTreeManager\Tests\Models\Page;

class PageHierarchyTest extends GenericTestCase
{
	/** @test */
	public function it_should_return_a_nested_collection_representing_the_tree()
	{
		$root = Page::create();
		$root->slugs()->create(['lang_code' => $this->lang, 'name' => 'root page']);

		$child = Page::create();
		$child->slugs()->create(['lang_code' => $this->lang, 'name' => 'child page']);
		$child->makeChildOf($root);

		$grandchild = Page::create();
		$grandchild->slugs()->create(['lang_code' => $this->lang, 'name' => 'grandchild page']);
		$grandchild->makeChildOf($child);

		$tree = $root->getTree();

		$this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $tree);
		$this->assertEquals(1, $tree->first()->children()->count());
		$this->assertEquals(1, $tree->first()->children()->first()->children()->count());
		$this->assertEquals('/root-page/child-page/grandchild-page', $tree->first()->children()->first()->children()->first()->getUrl($this->lang));
	}
}