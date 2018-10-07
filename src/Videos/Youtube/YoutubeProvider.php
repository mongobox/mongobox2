<?php

namespace App\Videos\Youtube;

use Psr\Log\LoggerInterface;

class YoutubeProvider
{
    const YOUTUBE_STATUS_PUBLIC = 'public';
    const YOUTUBE_STATUS_UNLISTED = 'unlisted';

    const UPDATE_LIMIT = 50;

    /** @var \Google_Client */
    private $googleClient;

    private $googleAppName;
    private $googleDeveloperKey;

    /** @var \Google_Service_YouTube */
    private $youtubeService;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger, string $googleAppName, string $googleDeveloperKey)
    {
        $this->logger = $logger;
        $this->googleAppName = $googleAppName;
        $this->googleDeveloperKey = $googleDeveloperKey;
    }

    public function initGoogleClient()
    {
        $this->googleClient = new \Google_Client();
        $this->googleClient->setApplicationName($this->googleAppName);
        $this->googleClient->setDeveloperKey($this->googleDeveloperKey);

        $this->youtubeService = new \Google_Service_YouTube($this->googleClient);
    }

    public function findVideosByIds(array $ids)
    {
        $this->initGoogleClient();

        $searchedVideosIds = array_values($ids);
        $foundedVideosIds = [];

        $videosYoutube = array_fill_keys($ids, null);
        $videoIds = implode(',', $searchedVideosIds);
        try {
            $response = $this->youtubeService->videos->listVideos("snippet,status,contentDetails", [
                'id' => $videoIds
            ]);
            $items = $response->getItems();
            foreach ($items as $youtubeVideo) {

                $this->logger->debug('Data for Youtube video {key}: {data}', [
                    'key'  => $youtubeVideo->getId(),
                    'data' => json_encode($youtubeVideo),
                ]);

                switch ($youtubeVideo->getStatus()->getPrivacyStatus()) {
                    case self::YOUTUBE_STATUS_PUBLIC:
                    case self::YOUTUBE_STATUS_UNLISTED:

                        $snippet = $youtubeVideo->getSnippet();

                        // Duration
                        $duration = $youtubeVideo->getContentDetails()->getDuration();
                        $interval = new \DateInterval($duration);
                        $duration = $this->toSeconds($interval);

                        // Thumbnails
                        $thumbnails = $snippet->getThumbnails();

                        $videosYoutube[$youtubeVideo->getId()] = [
                            'title'       => $snippet->getTitle(),
                            'duration'    => $duration,
                            'thumbnail'   => $thumbnails->getDefault()->getUrl(),
                            'thumbnailHq' => $thumbnails->getHigh()->getUrl()
                        ];

                        array_push($foundedVideosIds, $youtubeVideo->getId());

                        break;
                    default:
                        $this->logger->warning('Data not available for video {key} : {data}', [
                            'key'  => $youtubeVideo->getId(),
                            'data' => json_encode($youtubeVideo),
                        ]);
                        break;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error during loading data from Youtube: ' . $e->getMessage(), [
                'imploded_ids'       => $videoIds,
                'nb_searched_videos' => count($searchedVideosIds),
                'nb_founded_videos'  => count($foundedVideosIds),
                'diff'               => array_diff($searchedVideosIds, $foundedVideosIds),
            ]);
        }

        unset($searchedVideosIds);
        unset($foundedVideosIds);

        return $videosYoutube;
    }

    /**
     * Convert Date Interval into total seconds
     *
     * @param \DateInterval $delta
     *
     * @return int
     */
    private function toSeconds(\DateInterval $delta)
    {
        $seconds = ($delta->s)
            + ($delta->i * 60)
            + ($delta->h * 60 * 60)
            + ($delta->d * 60 * 60 * 24)
            + ($delta->m * 60 * 60 * 24 * 30)
            + ($delta->y * 60 * 60 * 24 * 365);

        return (int)$seconds;
    }
}
