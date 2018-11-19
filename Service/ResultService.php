<?php

namespace Emmedy\H5PBundle\Service;

use AppBundle\Entity\User;
use Emmedy\H5PBundle\Entity\ContentResult;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ResultService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ResultService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param $userId
     * @return ContentResult
     */
    public function handleRequest(Request $request, $userId)
    {
        $contentId = $request->get('contentId', false);

        if (!$contentId) {
            \H5PCore::ajaxError('Invalid content');
        }

        // TODO: Fire 'h5p_alter_user_result' event here.

        $contentRepo = $this->container->get('doctrine')->getRepository('EmmedyH5PBundle:Content');
        $contentResultRepo = $this->container->get('doctrine')->getRepository('EmmedyH5PBundle:ContentResult');

        $result = $contentResultRepo->findOneBy(['userId' => $userId, 'content' => $contentId]);

        if (!$result) {
            $result = new ContentResult($userId);
            $result->setContent($contentRepo->find($contentId));
        }

        dump($request);

        $result->setMaxScore($request->get('maxScore') ?? $result->getMaxScore());
        $result->setFinished($request->get('finished') ?? $result->getFinished());
        $result->setOpened($request->get('opened') ?? $result->getOpened());
        $result->setScore($request->get('score') ?? $result->getScore());
        $result->setTime($request->get('time') ?? $result->getTime());

        return $result;
    }
}
