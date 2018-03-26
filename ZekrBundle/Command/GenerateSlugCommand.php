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
use Knp\DoctrineBehaviors\Model\Sluggable\Transliterator;
class GenerateSlugCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:generate-slug')
            ->setDescription('generate slug from exists value in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $rows = $em->getRepository("ZekrBundle:Person")->findAll();

        foreach ($rows as $row){
            $sluggableText = $row->getPlainSlug();

            $transliterator = new Transliterator;
            $sluggableText = $transliterator->transliterate($sluggableText, $this->getSlugDelimiter());

            $urlized = strtolower( trim( preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $sluggableText ), $this->getSlugDelimiter() ) );
            $urlized = preg_replace("/[\/_|+ -]+/", $this->getSlugDelimiter(), $urlized);

            $row->setSlug($urlized);

//            $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
//            foreach($languages as $language) {
//                $row->translate($language->getLocale(), false)->setName($sluggableText);
//            }
//            $row->mergeNewTranslations();

            $em->flush();

        }

    }

    private function getSlugDelimiter()
    {
        return '-';
    }

}