<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BlogAclSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'user',
            ]);
        }
    }

    private function getPermissions(): array
    {
        return [
            // Main Menu
            'blog',

            // BlogPost/PostIndex.vue
            'blog-post-list',
            'blog-post-create',
            'blog-post-edit',
            'blog-post-delete',

            // BlogCategory/CategoryIndex.vue
            'blog-category-list',
            'blog-category-create',
            'blog-category-edit',
            'blog-category-delete',

            // BlogAuthor/AuthorIndex.vue
            'blog-author-list',
            'blog-author-create',
            'blog-author-edit',
            'blog-author-delete',

            // BlogTag/TagIndex.vue
            'blog-tag-list',
            'blog-tag-create',
            'blog-tag-edit',
            'blog-tag-delete',
        ];
    }
}
