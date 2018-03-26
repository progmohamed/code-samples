<?php

namespace ZekrBundle\Entity\Repository\Category;

use Doctrine\ORM\EntityRepository;

class FrontData
{
    protected $em;
    protected $formData;

    public $tree;

    function __construct($em)
    {
        $this->em = $em;
    }


    public function getCategoryTree($routing, $translator, $locale, $parent = null, $limit = null)
    {
        $em = $this->getEntityManager();
        $params = [];
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.deletedAt is NULL 
        AND ct.locale = :locale
        AND c.active = 1 ";

        $params['locale'] = $locale;

        if (is_null($parent)) {
            $dql .= "AND c.parent IS NULL ";
        } else {
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $parent;
        }
        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);

        if(null != $parent && null != $limit){
            $query->setMaxResults($limit);
        }

        if (count($params)) {
            $query->setParameters($params);
        }
        $result = $query->getResult();

        foreach ($result as $category) {
            if (null == $parent) {
                $this->tree .= '<div class="col-md-4 col-sm-6">
                    <div class="box">
                        <h3 class="tree-list-title main-color">' . $category->translate()->getName() . '</h3>
                        <div class="tree-list">
                            <ul class="">';
            }
            if (null != $parent) {
                $this->tree .= '<li><a href="'.$routing->generate('front_video_list', array('type'=>'category', 'slug'=>$category->getSlug())).'">' . $category->translate()->getName() . '</a></li>';
            }
            $this->getCategoryTree($routing, $translator, $locale, $category->getId(), $limit);

            if (null == $parent) {
                $this->tree .= ' </ul>
                            <div class="padding-15">
                                <a href="'.$routing->generate('front_category_all_childs', array('slug'=>$category->getSlug())).'" class="btn-solid btn-round">'.$translator->trans('front.view_all').'</a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }

        return $this->tree;
    }


    protected function getEntityManager()
    {
        return $this->em;
    }
}