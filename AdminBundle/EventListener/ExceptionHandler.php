<?php

namespace AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionHandler 
{
    private $container;
    private $controller;

    public function __construct($container = null)
    {
        $this->container = $container;
    }
    
    private function render($view, array $parameters = [], Response $response = null)
    {
        return $this->container->get('templating')->renderResponse($view, $parameters, $response);
    }

    private function left($str, $nbr)
    {
        return substr($str,0,$nbr);
    }

    private function isAdmin($request)
    {
        if($request) {
            $path = \strtolower($request->getPathInfo());
            if('/' == \substr($path,0,1)) {
                $path = \substr($path,1);
            }
            if('admin' == $this->left($path,5) ) {
                return true;
            }
            if('/admin' == \substr($path,2,6) ) {
                return true;
            }
        }
        return false;
    }
    
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->getLocale();
        $localeService = $this->container->get('locale.service');
        $language = $localeService->getLanguageByLocale($locale);
        if(!$language) {
            //To DO: Redirect to custom error page (locale not found)
            //throw new \Exception('Locale does not exist ('.$locale.')');
        }
        $this->controller = $event->getController();
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if($this->controller) {
            $request = $event->getRequest();
            if($this->controller[0] instanceof Controller) {
                if($this->isAdmin($request)) {
                    $view = ':admin:exception.html.twig';
                }else{
                    $view = false; //':front:exception.html.twig';
                }
                if($view) {
                    $exception = $event->getException();
                    $response = $this->render($view, [
                        'exception' => $exception,
                        'isException' => $exception instanceof HttpException
                    ]);
                    $event->setResponse($response);
                }
            }
        }
    }
}