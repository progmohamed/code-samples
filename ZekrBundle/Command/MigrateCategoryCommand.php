<?php

namespace ZekrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use ZekrBundle\Entity\Category;

class MigrateCategoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:migrate-category')
            ->setDescription('migrate Category');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '99999999999999999999999999999999999999999999999999999999999999999999999999M');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $migrationRepository = $em->getRepository('ZekrBundle:MigrationCategory');
        $qb = $migrationRepository->createQueryBuilder('c')
            ->select('c')
            ->where('c.parent IS NOT NULL')
            ->orderBy('c.parent');
        $query = $qb->getQuery();

        $migrationCategories = $query->getResult();

        $categoryRepository = $em->getRepository('ZekrBundle:Category');
        foreach($migrationCategories as $category){

            $parentId = (null != $category->getParent()) ? $category->getParent()->getId() : 0;

            $parent = $categoryRepository->findOneBy(['oldId'=>$parentId]);
            $entity = new Category();

            $entity->setActive(1)
                ->setOldId($category->getId())
                ->setPlainSlug($parent.' - '.$category)
                 ->setParent($parent);

            $output->writeln($category->getId());
            foreach($category->getTranslations() as $translate){
                $entity->translate($translate->getLocale())->setName($translate->getName());
            }
            $entity->mergeNewTranslations();
            $categoryRepository->getBuilder()->add($entity, $parent);


        }

    }

}