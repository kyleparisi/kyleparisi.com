<?php
require __DIR__ . "/vendor/autoload.php";

use Melbahja\Seo\Factory;
use RedBeanPHP\R as R;

echo "Generating SEO site maps:" . PHP_EOL;

R::setup('sqlite:' . __DIR__ . '/application.db');
R::setAutoResolve(true);

$sitemap = Factory::sitemap('https://kyleparisi.com', [
    'save_path' => __DIR__ . "/public"
]);
$sitemap->links('blog.xml', function ($map) {
    /** @var Post $posts */
    $posts = R::findAll("post");
    $map->loc('/blog')->freq('monthly');
    foreach ($posts as $post) {
        echo '/blog/' . $post->slug . PHP_EOL;
        $map->loc('/blog/' . $post->slug);
    }
});

// return bool
// throws SitemapException if save_path options not exists
$sitemap->save();
