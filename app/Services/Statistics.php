<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use DateTime;
use Exception;
use JetBrains\PhpStorm\Pure;
use RuntimeException;

/**
 * Class Statistics
 *
 * @package App\Services
 */
class Statistics
{
    protected array $postsStat = [];
    protected int $postsCount = 0;
    protected array $statSumPostLengthByMonth = [];
    protected array $statLongestPostByMonth = [];
    protected array $statTotalPostCountByWeek = [];
    protected array $statAvgPostCountPerUserByMonth = [];


    /**
     * Add each Post to the calculator.
     *
     * @param Post $post
     */
    public function addPostToCalculator(Post $post): void
    {
        try {

            $postInf = (object)[
                'id' => $post->id,
                'from_id' => $post->from_id,
                'message_length' => mb_strlen($post->message),
                'month' => (new DateTime($post->created_time))->format('y-m'),
                'week' => (new DateTime($post->created_time))->format('y-W'),
            ];

        } catch (Exception) {

            throw new RuntimeException('Error format post fields.');
        }

        $this->postsCount++;
        $this->calcSumPostsLengthByMonth($postInf);
        $this->calcLongestPostsByMonth($postInf);
        $this->calcTotalPostCountByWeek($postInf);
        $this->calcAvgPostCountPerUserByMonth($postInf);
    }

    /**
     * @return int
     */
    public function getTotalPostsCount(): int
    {
        return $this->postsCount;
    }


    /**
     * Prepare for Average character length of posts per month.
     *
     * @param object $post
     */
    private function calcSumPostsLengthByMonth(object $post): void
    {
        if (!array_key_exists($post->month, $this->statSumPostLengthByMonth)) {
            $this->statSumPostLengthByMonth[$post->month] = [
                'count' => 0,
                'length' => 0,
            ];
        }
        $this->statSumPostLengthByMonth[$post->month]['count']++;
        $this->statSumPostLengthByMonth[$post->month]['length'] += $post->message_length;
    }

    /**
     * Average character length of posts per month.
     *
     * @return array
     */
    public function getAvgPostLengthByMonth(): array
    {
        $res = [];
        foreach ($this->statSumPostLengthByMonth as $month => $statMonth) {
            $res[$month] = round($statMonth['length'] / $statMonth['count']);
        }

        return $res;
    }


    /**
     * Prepare for Longest post by character length per month.
     *
     * @param object $post
     */
    private function calcLongestPostsByMonth(object $post): void
    {
        if (!array_key_exists($post->month, $this->statLongestPostByMonth)) {
            $this->statLongestPostByMonth[$post->month] = [
                'post' => '',
                'length' => 0,
            ];
        }

        if ($this->statLongestPostByMonth[$post->month]['length'] < $post->message_length) {
            $this->statLongestPostByMonth[$post->month]['post'] = $post->id;
            $this->statLongestPostByMonth[$post->month]['length'] = $post->message_length;
        }
    }

    /**
     * Longest post by character length per month.
     *
     * @return array
     */
    public function getLongestPostLengthByMonth(): array
    {
        $res = [];
        foreach ($this->statLongestPostByMonth as $month => $statMonth) {
            $res[$month] = $statMonth['post'];
        }

        return $res;
    }


    /**
     * Prepare for Total posts split by week number
     *
     * @param object $post
     */
    private function calcTotalPostCountByWeek(object $post): void
    {
        if (!array_key_exists($post->week, $this->statTotalPostCountByWeek)) {
            $this->statTotalPostCountByWeek[$post->week] = 0;
        }

        $this->statTotalPostCountByWeek[$post->week]++;
    }

    /**
     * Total posts split by week number
     *
     * @return array
     */
    public function getTotalPostCountByWeek(): array
    {
        return $this->statTotalPostCountByWeek;
    }


    /**
     * Prepare for Average number of posts per user per month.
     *
     * @param object $post
     */
    private function calcAvgPostCountPerUserByMonth(object $post): void
    {
        if (!array_key_exists($post->from_id, $this->statAvgPostCountPerUserByMonth)) {
            $this->statAvgPostCountPerUserByMonth[$post->from_id] = [];
        }
        if (!array_key_exists($post->month, $this->statAvgPostCountPerUserByMonth[$post->from_id])) {
            $this->statAvgPostCountPerUserByMonth[$post->from_id][$post->month] = 0;
        }

        $this->statAvgPostCountPerUserByMonth[$post->from_id][$post->month]++;
    }

    /**
     * Average number of posts per user per month.
     *
     * @return array
     */
    #[Pure] public function getAvgPostCountPerUserByMonth(): array
    {
        $res = [];
        foreach ($this->statAvgPostCountPerUserByMonth as $user => $userStat) {
            $userPostCount = 0;
            $userMonthCount = 0;
            foreach ($userStat as $month => $count) {
                $userMonthCount++;
                $userPostCount += $count;
            }
            $res[$user] = round($userPostCount / $userMonthCount);
        }

        return $res;
    }
}
