<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/cache")
 */
class CacheController extends Controller
{
    /**
     * @Route("/", name="zekr_cache_index")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('ZekrBundle:Cache:index.html.twig');
    }

    /**
     * @Route("/remove", name="zekr_cache_remove")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function removeAction(Request $request)
    {
        $this->get('cache.app')->clear();
        $this->get('session')->getFlashBag()->add(
            'success','تم حذف التخزين المؤقت بنجاح'
        );
        return $this->redirectToRoute('zekr_cache_index');
    }

}
