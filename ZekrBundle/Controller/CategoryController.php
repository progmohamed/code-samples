<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\Category;
use ZekrBundle\Form\CategoryType;
use AdminBundle\Classes\AjaxResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="zekr_category_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('ZekrBundle:Category:tree.html.twig', array(
         ));
    }

    /**
     * @Route("/get-data", name="zekr_category_get_data")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function getDataAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $checkboxesAll = (bool) $request->get('checkboxesAll', false);
        $categoryRepository = $em->getRepository('ZekrBundle:Category');
        $return = $categoryRepository->getCategoryTreeWithRoot($checkboxesAll);
        return new JsonResponse($return);
    }


    /**
     * @Route("/new", name="zekr_category_new")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $parentId = $request->get('parent', 0);
        $mode = $request->get('mode', false);
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();

        $categoryRepository = $em->getRepository('ZekrBundle:Category');
        $parent = $categoryRepository->find($parentId);
        $categoryRepository = $em->getRepository('ZekrBundle:Category');
        $entity = new Category();
        $addedCategory = null;
        $form = $this->createForm(CategoryType::class, $entity, [
            'action' => $this->generateUrl('zekr_category_new', ['parent' => $parentId]),
            'languages'=> $languages
        ]);
        $entity->setParent($parent);
        $form->handleRequest($request);
        if('close' == $mode) {
            $addedCategory = $categoryRepository->find($request->get('addedCategoryId'));
        }
        if ($form->isSubmitted() && $form->isValid()) {
            try {

                foreach($languages as $language) {
                    $entity->translate($language->getLocale())->setName($form->get('name_'.$language->getLocale())->getData());
                    $entity->translate($language->getLocale())->setNote($form->get('note_'.$language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();

                $categoryRepository->getBuilder()->add($entity, $parent);

                $this->get('session')->getFlashBag()->add(
                    'success', 'تم حفظ البيانات'
                );
                return $this->redirectToRoute('zekr_category_new', array(
                    'mode' => 'close',
                    'addedCategoryId' => $entity->getId(),
                ));
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add(
                    'danger', $e->getMessage()
                );
            }
        }

        return $this->render('ZekrBundle:Category:new.html.twig', array(
            'entity'            => $entity,
            'addedCategory'     => $addedCategory,
            'form'              => $form->createView(),
            'mode'              => $mode,
        ));

    }


    /**
     * @Route("/edit/", name="zekr_category_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $mode = $request->get('mode', false);
        $categoryRepository = $em->getRepository('ZekrBundle:Category');
        $entity = $categoryRepository->find($request->query->get('id'));
        $editForm = $this->createForm(CategoryType::class, $entity, [
            'action' => $this->generateUrl('zekr_category_edit', ['id' => $entity->getId()]),
            'languages'=>$languages
        ] );
        $editForm->handleRequest($request);

//        $x  = $categoryRepository->getBuilder()->getNodeSubTree($entity);
//        dump($x);exit;

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                try {

                    foreach($languages as $language) {
                        $entity->translate($language->getLocale(), false)->setName($editForm->get('name_'.$language->getLocale())->getData());
                        $entity->translate($language->getLocale(), false)->setNote($editForm->get('note_'.$language->getLocale())->getData());
                    }

                    $entity->mergeNewTranslations();

                    $categoryRepository->getBuilder()->edit($entity);
                    $taskmanager = $this->get('taskmanager.service');
                    $taskmanager->addTaskQueued(
                        'zekr:edit-category',
                        [ 'id' => $entity->getId() ],
                        'Edit Category'
                    );


                    $this->get('session')->getFlashBag()->add(
                        'success', 'تم تعديل البيانات'
                    );
                    return $this->redirectToRoute('zekr_category_edit', array(
                        'mode' => 'close',
                        'id' => $entity->getId(),
                    ));
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add(
                        'danger', $e->getMessage()
                    );
                }
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
                $editForm->get('note_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getNote());
            }
        }
        return $this->render('ZekrBundle:Category:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
            'mode'          => $mode,
        ));
    }


    /**
     * @Route("/delete", name="zekr_category_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request)
    {
        try {
            $ajaxResponse = new AjaxResponse();
            $em = $this->getDoctrine()->getManager();
            $categoryRepository = $em->getRepository('ZekrBundle:Category');
            $entity = $categoryRepository->find($request->request->get('id'));
            if(!$entity) {
                throw new \Exception('Category not found');
            }
            //SoftDelete
            $randomValue = sha1(uniqid(time(), true));
            $entity->setPlainSlug( $randomValue);
            $em->remove($entity);
            $em->flush();

            $taskmanager = $this->get('taskmanager.service');
            $taskmanager->addTaskQueued(
                'zekr:remove-category',
                [ 'id' => $entity->getId() ],
                'Removing Category'
            );

        }catch(\Exception $e) {
            $ajaxResponse->setSuccess(false);
            $ajaxResponse->setMessage($e->getMessage());
        }
        return new JsonResponse($ajaxResponse->getArray());
    }

    /**
     * @Route("/move", name="zekr_category_move")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveAction(Request $request)
    {
        try {
            $ajaxResponse = new AjaxResponse();
            $mode = $request->request->get('mode');
            $em = $this->getDoctrine()->getManager();
            $categoryRepository = $em->getRepository('ZekrBundle:Category');
            $entityMove = $categoryRepository->find($request->request->get('moveId'));
            $entityTo = $categoryRepository->find($request->request->get('toId'));
            if(!$entityMove) {
                throw new \Exception('Category not found');
            }
            $categoryRepository->getBuilder()->move($entityMove, $entityTo, $mode);
        }catch(\Exception $e) {
            $ajaxResponse->setSuccess(false);
            $ajaxResponse->setMessage($e->getMessage());
        }
        return new JsonResponse($ajaxResponse->getArray());
    }

    /**
     * @Route("/move-up/", name="zekr_category_move_up")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveUpAction(Request $request)
    {
        try {
            $ajaxResponse = new AjaxResponse();
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $categoryRepository = $em->getRepository('ZekrBundle:Category');
            $entity = $categoryRepository->find($id);
            if(!$entity) {
                throw new \Exception('Category not found');
            }
            $categoryRepository->getBuilder()->moveUp($entity);
        }catch(\Exception $e) {
            $ajaxResponse->setSuccess(false);
            $ajaxResponse->setMessage($e->getMessage());
        }
        return new JsonResponse($ajaxResponse->getArray());
    }

    /**
     * @Route("/move-down/", name="zekr_category_move_down")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveDownAction(Request $request)
    {
        try {
            $ajaxResponse = new AjaxResponse();
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $categoryRepository = $em->getRepository('ZekrBundle:Category');
            $entity = $categoryRepository->find($id);
            if(!$entity) {
                throw new \Exception('Category not found');
            }
            $categoryRepository->getBuilder()->moveDown($entity);
        }catch(\Exception $e) {
            $ajaxResponse->setSuccess(false);
            $ajaxResponse->setMessage($e->getMessage());
        }
        return new JsonResponse($ajaxResponse->getArray());
    }

}
