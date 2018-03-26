<?php

namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use ZekrBundle\Entity\VideoReport;

class VideoController extends FOSRestController
{

    /**
     * @Route("{projectSlug}/videos/{videoId}",
     * requirements={
     *         "videoId": "\d+"
     *     }
     * )
     */
    public function getVideosAction(Request $request, $projectSlug, $videoId)
    {
        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Video')->getVideo($request->getLocale(), $projectSlug, $videoId);
        $data['count'] = count($results);
        $data['results'] = $results;
        return $data;
    }


    /**
     * @Route("{projectSlug}/videos/selected_videos")
     */
    public function getSelectedVideosAction(Request $request, $projectSlug)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);

        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Video')->getSelectedVideoList($request->getLocale(), $this->get('knp_paginator'), $projectSlug, $page, $limit);

        $context = new SerializationContext();
        $context->setGroups(['default', 'videoList']);
        $view = $this->view($results, 200);
        $view->setSerializationContext($context);

        return $this->handleView($view);
    }


    /**
     * @Route("{projectSlug}/videos/report_videos/{videoId}")
     */
    public function postVideoReportAction(Request $request, $projectSlug, $videoId)
    {
        $em = $this->getDoctrine()->getManager();
        $reason = $request->request->get('reason');
        $message = $request->request->get('message');
        $video = $em->getRepository('ZekrBundle:Video')->getVideo($request->getLocale(), $projectSlug, $videoId);

        $reasonArr = [VideoReport::CONTRARY_CONTENT, VideoReport::PRIVACY_INFRINGEMENT, VideoReport::COPYRIGHT_INFRINGEMENT, VideoReport::OTHER];
        if (!in_array($reason, $reasonArr)) {
            $data['status'] = false;
            $data['message'] = 'please make sure report reason number';
        } elseif (null == $video) {
            $data['status'] = false;
            $data['message'] = 'please make sure the video number';
        } else {
            try {
                $entity = new VideoReport();
                $entity->setVideo($video)
                    ->setReason($reason)
                    ->setMessage($message);
                $em->persist($entity);
                $em->flush();
                $data['status'] = true;
                $data['message'] = 'success';
            } catch (\Exception $e) {
                $data['status'] = false;
                $data['message'] = $e->getMessage();
            }
        }

        return $data;
    }

    /**
     * @Route("{projectSlug}/videos", name="get_videos_list")
     * @Route("{projectSlug}/videos/{type}/{typeId}", name="get_videos_list_in_type",
     *     requirements={
     *         "type": "category|collection|video_type|rewaya|person|juz|hizb|surah"
     *     })
     */
    public function getVideosListAction(Request $request, $projectSlug, $type = null, $typeId = null)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);
        $sortField = $request->query->get('sort_field', 'inserted_at');
        $sortDirection = $request->query->get('sort_direction', 'DESC');

        $context = new SerializationContext();
        if ($type == 'collection') {
            $items = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Video')->getVideoListForCollection($request->getLocale(), $projectSlug, $typeId);
            $data['count'] = count($items);
            $data['results'] = $items;
            $results = $data;
            $context->setGroups(['default', 'videoList']);
        } else {
            $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Video')->getVideoList($request->getLocale(), $this->get('knp_paginator'), $projectSlug, $page, $limit, $sortField, $sortDirection, $type, $typeId);
            $results->sortField = $sortField;
            $results->sortDirection = $sortDirection;
            $context->setGroups(['default', 'videoList', 'sortable']);
        }
        $view = $this->view($results, 200);
        $view->setSerializationContext($context);

        return $this->handleView($view);
    }

}
