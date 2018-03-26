<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\Surah;
use ZekrBundle\Form\SurahType;

/**
 * @Route("/surah")
 */
class SurahController extends Controller
{
    /**
     * @Route("/", name="zekr_surah_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:Surah');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_surah_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_surah_list', array(
                'filter' => $dataGrid->getEncodedFilterArray($form)
            ));
        }

        $filter = $request->query->get('filter');
        if ($filter) {
            $formData = $dataGrid->decodeFilterArray($filter);
            $form = $dataGrid->setFormFilterData($form, $formData);
        }

        $entities = $dataGrid->getGrid();

        return $this->render('ZekrBundle:Surah:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ));
    }

    /**
     * @ParamConverter("entity", class="ZekrBundle:Surah")
     * @Route("/show/{id}", name="zekr_surah_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(Surah $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:Surah:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Surah")
     * @Route("/edit/{id}", name="zekr_surah_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Surah $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $editForm = $this->createForm(SurahType::class, $entity, ['languages'=>$languages] );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                foreach($languages as $language) {
                    $entity->translate($language->getLocale(), false)->setName($editForm->get('name_'.$language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', 'Data has been updated'
                );
                return $this->redirectToRoute('zekr_surah_show', array('id' => $entity->getId()));
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
            }
        }


        return $this->render('ZekrBundle:Surah:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ));
    }


}
