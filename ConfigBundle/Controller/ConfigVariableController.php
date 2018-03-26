<?php

namespace ConfigBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/config_variable")
 */
class ConfigVariableController extends Controller
{
    /**
     * @Route("/", name="zekr_config_variable_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ConfigBundle:ConfigVariable');
        $variables = $repository->findAll();
        if ('POST' == $request->getMethod()) {

            foreach ($variables as $entity){
                if(null !== $request->request->get($entity->getVariable())){
                    $entity->setValue($request->request->get($entity->getVariable()));
                }else{
                    $entity->setValue(0);
                }
            }
            $em->flush();
        }


        return $this->render('ConfigBundle:ConfigVariable:index.html.twig', array(
            'variables' => $variables,
        ));
    }

}
