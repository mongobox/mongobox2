<?php

namespace App\Videos\Youtube;

use App\Entity\Video\YoutubeVideo;
use App\Videos\VideoFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class ImportFromYoutube
{
    /** @var LoggerInterface */
    private $logger;

    /** @var YoutubeProvider */
    private $youtubeProvider;

    /** @var ObjectManager */
    private $em;

    /** @var ArrayCollection */
    private $videos;

    private $videosIds = [];

    /** @var VideoFactory */
    private $videoFactory;

    private $paginate = 0;
    private $bufferSize = 50;

    private $nbUpdate = 0;
    private $videosWithoutData = 0;
    private $nbVideosLoaded = 0;

    public function __construct(
        LoggerInterface $logger,
        YoutubeProvider $youtubeProvider,
        ObjectManager $entityManager,
        VideoFactory $videoFactory)
    {
        $this->logger = $logger;
        $this->youtubeProvider = $youtubeProvider;
        $this->em = $entityManager;
        $this->videoFactory = $videoFactory;
    }

    /**
     * Load Youtube Videos from database with paginate
     *
     * @return array
     */
    public function getVideosToUpdate(): array
    {
        $videos = [];

        $youtubeVideos = $this->em->getRepository(YoutubeVideo::class)->findAllWithPagination(
            $this->paginate,
            $this->bufferSize
        );

        $this->logger->debug('Load Youtube videos from database', [
            'start'     => $this->paginate,
            'limit'     => $this->bufferSize,
            'nb_videos' => count($youtubeVideos),
        ]);
        foreach ($youtubeVideos as $video) {
            $videos[$video->getProviderId()] = $video;
        }

        return $videos;
    }

    /**
     * Save array mapping id video/id youtube
     */
    public function initVideosIds(): void
    {
        $this->videosIds = [];
        foreach ($this->videos as $video) {
            /** @var YoutubeVideo $video */
            $this->videosIds[$video->getId()] = $video->getProviderId();
        }
    }

    /**
     * Load data from Youtube by ids and update videos in database
     */
    public function createVideosFromYoutube()
    {
        $youtubeData = $this->youtubeProvider->findVideosByIds($this->videosIds);
        foreach ($youtubeData as $key => $data) {

            $video = $this->videos[$key];
            if (is_null($data)) {
                $this->logger->warning('No data for video {key} (id: {id})', [
                    'key' => $key,
                    'id'  => $video->getId(),
                ]);

                $this->videosWithoutData++;

                $video->setFoundOnProvider(false);

                $this->em->persist($video);

                continue;
            }

            $this->logger->debug('Update video {key} (id: {id}) : {data}', [
                'key'  => $key,
                'data' => json_encode($data),
                'id'   => $video->getId(),
            ]);

            /** @var YoutubeVideo $video */
            $video = $this->videoFactory->createVideoFromYoutubeData($video, $data);

            $this->em->persist($video);

            $this->nbUpdate++;
        }

        unset($youtubeData);

        $this->em->flush();
        $this->em->clear();
    }

    public function import()
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->logger->info('Update videos from Youtube: start');

        while ($videos = $this->getVideosToUpdate()) {
            $nbVideos = count($videos);
            $this->videos = $videos;
            $this->nbVideosLoaded += $nbVideos;

            $this->initVideosIds();
            $this->createVideosFromYoutube();

            $this->paginate += $this->bufferSize;

            unset($videos);
        }

        $this->logger->info('Update videos from Youtube: end', [
            'nb_videos_loaded'       => $this->nbVideosLoaded,
            'nb_updated_videos'      => $this->nbUpdate,
            'nb_videos_without_data' => $this->videosWithoutData,
        ]);
    }
}
