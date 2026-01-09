<?php

namespace Modules\Blog\Services;

use Carbon\Carbon;
use Modules\Blog\Models\Post;

class PostService
{
    /**
     * Get latest published posts for homepage display.
     */
    public static function getLatestPosts(int $limit = 3)
    {
        return Post::query()
            ->with(['author', 'tags'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}
