<?php

namespace tests\app\libraries;

use app\libraries\ForumUtils;

class ForumUtilsTester extends \PHPUnit\Framework\TestCase {
    /**
     * @return array<string, mixed>
     */
    private static function getEditState(): array {
        return [
            'title' => 'Original title',
            'status' => 0,
            'lock_date' => null,
            'pinned_expiration' => 1800000000,
            'content' => 'Original content',
            'anonymous' => false,
            'render_markdown' => true,
            'attachments' => ['existing.pdf'],
            'categories' => [1, 2],
        ];
    }

    public static function categoryOnlyEditProvider(): array {
        return [
            'add category' => [[1, 2, 3]],
            'remove category' => [[1]],
            'replace category' => [[1, 3]],
        ];
    }

    /**
     * @dataProvider categoryOnlyEditProvider
     * @param int[] $updated_categories
     */
    public function testCategoryOnlyEdit(array $updated_categories): void {
        $original_state = self::getEditState();
        $updated_state = self::getEditState();
        $updated_state['categories'] = $updated_categories;

        $this->assertTrue(ForumUtils::isCategoryOnlyEdit($original_state, $updated_state));
    }

    public function testNoCategoryEdit(): void {
        $state = self::getEditState();

        $this->assertFalse(ForumUtils::isCategoryOnlyEdit($state, $state));
    }

    public function testCategoryOrderDoesNotCountAsEdit(): void {
        $original_state = self::getEditState();
        $updated_state = self::getEditState();
        $updated_state['categories'] = [2, 1];

        $this->assertFalse(ForumUtils::isCategoryOnlyEdit($original_state, $updated_state));
    }

    public static function otherEditProvider(): array {
        return [
            'title' => ['title', 'Updated title'],
            'status' => ['status', 1],
            'lock date' => ['lock_date', 1800000100],
            'pinned expiration' => ['pinned_expiration', 1800000100],
            'content' => ['content', 'Updated content'],
            'anonymity' => ['anonymous', true],
            'Markdown mode' => ['render_markdown', false],
            'attachments' => ['attachments', ['existing.pdf', 'new.pdf']],
        ];
    }

    /**
     * @dataProvider otherEditProvider
     */
    public function testCategoryEditWithOtherChange(string $field, mixed $value): void {
        $original_state = self::getEditState();
        $updated_state = self::getEditState();
        $updated_state['categories'] = [1, 3];
        $updated_state[$field] = $value;

        $this->assertFalse(ForumUtils::isCategoryOnlyEdit($original_state, $updated_state));
    }
}
