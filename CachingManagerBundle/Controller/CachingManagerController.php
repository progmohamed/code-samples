<?php

namespace CachingManagerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/caching-manager")
 */
class CachingManagerController extends Controller
{

    /**
     * @Route("", name="cachingmanager_cachingmanagement_list")
     * @Security("has_role('ROLE_CACHINGMANAGEMENT_CACHINGMANAGEMENT_LIST')")
     */
    public function indexAction()
    {
        $client = $this->get('cachingmanager.service')->getClient();
        $allKeys = [];
        $cursor = null;
        do {
            $keys = $client->scan($cursor);
            if (isset($keys[1]) && is_array($keys[1])) {
                $cursor = $keys[0];
                $keys = $keys[1];
            }
            if ($keys) {
                $allKeys = array_merge($allKeys, $keys);
            }
        } while ($cursor = (int)$cursor);
        return $this->render('CachingManagerBundle:CachingManager:index.html.twig', [
            'allKeys' => $allKeys
        ]);
    }


    /**
     * @Route("/delete", name="cachingmanager_cachingmanagement_delete")
     * @Security("has_role('ROLE_CACHINGMANAGEMENT_CACHINGMANAGEMENT_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $single = $request->query->get('single', false);
        $id = $request->query->get('id', false);
        $flushAll = $request->query->get('flush_all', false);

        if ($id) {
            if ($single) {
                $id = base64_encode(serialize([$id]));
            }
            $ids = unserialize(base64_decode($id));
        }

        $client = $this->get('cachingmanager.service')->getClient();
        try {
            if ($flushAll) {
                $client->flushall();
            } else {
                $client->del($ids);
            }
            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_deleted')
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'danger', $e->getMessage()
            );
        }

        return $this->redirectToRoute('cachingmanager_cachingmanagement_list');
    }


    /**
     * @Route("/batch", name="cachingmanager_cachingmanagement_batch")
     * @Security("has_role('ROLE_CACHINGMANAGEMENT_CACHINGMANAGEMENT_DELETE')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $idx = explode(',', $ids);
        $action = $request->query->get('action');
        if ('delete' == $action) {
            return $this->redirectToRoute('cachingmanager_cachingmanagement_delete', [
                'id' => base64_encode(serialize($idx))
            ]);
        }
    }
}
