<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use DateTime;
use Exception;
use Generator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use RuntimeException;

/**
 * Class App.
 *
 * @package App\Services
 */
class App
{
    /**
     * Post model.
     *
     * @var Post
     */
    protected Post $postModel;

    public function __construct(
        protected Api $api,
        protected $db,
        protected $cache,
        protected $config,
        protected Statistics $statistics
    )
    {
        $this->cache = new $cache;
        $this->postModel = new Post();
    }

    /**
     * Try to get posts from API and save to the base.
     *
     * @return bool
     * @throws RuntimeException
     */
    public function getPostsFromApiAndSaveToBase(): bool
    {
        try {

            $cachePath = $this->getCachePathToUserPosts();

            // Check cache
            if (!$this->cache::get($cachePath)) {

                $this->putPostsMarkToCache($cachePath);

                $this->db->clearTable(
                    $this->postModel->getTableName()
                );

                // Get posts from API
                $postsGenerator = $this->api->getPostsIterator(
                    $this->getToken(),
                    $this->config->api->posts_pages
                );

                // Save posts to the table
                foreach ($postsGenerator as $postsChunk) {
                    $this->db->addToTable(
                        $this->postModel->getTableName(),
                        $postsChunk
                    );
                }
            }

        } catch (Exception) {

            throw new RuntimeException('Error getting posts from API and saving to the base.');
        }

        return true;
    }

    /**
     * Get each post by one generator.
     *
     * @return Generator
     */
    public function getPostsFromBase(): Generator
    {
        foreach ($this->db->readFromTableByRow('posts') as $dataChunk) {

            yield $this->postModel->create($dataChunk);
        }
    }

    /**
     * Get token from cache or generate it from API.
     *
     * @return string
     * @throws RuntimeException
     */
    protected function getToken(): string
    {
        try {
            $cachePath = $this->getCachePathToUserToken();

            $token = $this->cache::get($cachePath);

            if (!$token) {
                $token = $this->api->getToken();

                $this->cache::put(
                    $cachePath,
                    $token,
                    $this->config->cache->time
                );
            }

        } catch (Exception) {

            throw new RuntimeException('Error getting token.');
        }

        return $token;
    }

    /**
     * Push each Post to the Statistics calculator.
     *
     * @param Post $post
     * @throws RuntimeException
     */
    public function pushPostToStat(Post $post): void
    {
        try {

            $this->statistics->addPostToCalculator($post);

        } catch (Exception) {
            throw new RuntimeException('Error adding post to statistics calculator.');
        }
    }

    /**
     * Get all statistics values.
     *
     * @return array
     */
    #[ArrayShape([
        'totalPostsCount' => "int",
        'avgPostLengthByMonth' => "array",
        'longestPostLengthByMonth' => "array",
        'totalPostCountByWeek' => "array",
        'avgPostCountPerUserByMonth' => "array"
    ])]
    public function getStatisticsByRow(): array
    {
        return [
            'totalPostsCount' => $this->statistics->getTotalPostsCount(),
            'avgPostLengthByMonth' => $this->statistics->getAvgPostLengthByMonth(),
            'longestPostLengthByMonth' => $this->statistics->getLongestPostLengthByMonth(),
            'totalPostCountByWeek' => $this->statistics->getTotalPostCountByWeek(),
            'avgPostCountPerUserByMonth' => $this->statistics->getAvgPostCountPerUserByMonth(),

        ];
    }

    /**
     * Generate cache path to user posts.
     *
     * @return string
     */
    #[Pure] protected function getCachePathToUserPosts(): string
    {
        return $this->config->cache->path . $this->config->user->client_id . '_' . $this->postModel->getTableName();
    }

    /**
     * Generate cache path to the user token.
     *
     * @return string
     */
    #[Pure] protected function getCachePathToUserToken(): string
    {
        return $this->config->cache->path . $this->config->user->client_id . '_sl_token';
    }

    /**
     * Put time to cache for posts.
     * Needs to know when to refresh database from API.
     *
     * @param string $cachePath
     * @return bool
     */
    protected function putPostsMarkToCache(string $cachePath): bool
    {
        return $this->cache::put(
            $cachePath,
            DateTime::createFromFormat('U.u', (string)microtime(true))->format("m-d-Y H:i:s.u"),
            (int)round($this->config->cache->time_posts)
        );
    }
}
