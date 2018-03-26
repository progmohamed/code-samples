<?php

namespace ZekrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ZekrBundle\Entity\StaticPage;

class LoadStaticPages implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $em = $this->container->get('doctrine')->getManager();
        $em->createQuery('DELETE ZekrBundle:StaticPage s')->execute();

        $languages =$this->container->get('admin.admin_helper')->getLanguages();


        $entity = new StaticPage();
        $entity->setPlainSlug('about-us');
        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle('About Us');
            $entity->translate($language->getLocale())->setContent('about-us');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);


        $entity = new StaticPage();
        $entity->setPlainSlug('site-goals');
        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle('Site Goals');
            $entity->translate($language->getLocale())->setContent('site-goals');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);


        $entity = new StaticPage();
        $entity->setPlainSlug('faq');
        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle('FAQ');
            $entity->translate($language->getLocale())->setContent('faq');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);


        $entity = new StaticPage();
        $entity->setPlainSlug('terms-of-publication');
        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle('Terms Of Publication');
            $entity->translate($language->getLocale())->setContent('terms-of-publication');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);


        $entity = new StaticPage();
        $entity->setPlainSlug('site-usage-agreement');
        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle('Site usage agreement');
            $entity->translate($language->getLocale())->setContent('site-usage-agreement');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);

        $entity = new StaticPage();
        $entity->setPlainSlug('contact-us');
        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle('Contact us');
            $entity->translate($language->getLocale())->setContent('
                            <p class="mb-15">Donec porta diam eu massa. Quisque diam lorem, interdum vitae,dapibus ac, scelerisque vitae, pede. Donec eget tellus non erat lacinia fermentum. Donec in velit vel ipsum auctor pulvinar. Vestibulum iaculis lacinia est. Proin dictum elementum velit. Fusce euismod consequat ante. Lorem ipsum dolor sit amet, consectetuer adipis. Mauris accumsan nulla vel diam.</p>
                            <address class="mb-15">
                                <span><strong>Adress:</strong> 2425 E. Camelback Rd., Suite 108 Phoenix, AZ 85016</span><br>
                                <span><strong>Office:</strong> 602.252.6655</span><br>
                                <span><strong>Fax:</strong> 866.759.4048</span><br>
                                <span><strong>SMS:</strong> 50500</span><br>
                                <span><strong>Email:</strong> <a href="#">Info@domainname.com</a></span>
                            </address>
                            ');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);





        $manager->flush();

    }
}