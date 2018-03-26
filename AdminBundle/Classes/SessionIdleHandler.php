<?php

namespace AdminBundle\Classes;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ConfigBundle\Service\ConfigService;

class SessionIdleHandler
{

    protected $session;
    protected $securityToken;
    protected $router;
    protected $maxIdleTime;

    public function __construct(SessionInterface $session, TokenStorageInterface $securityToken, RouterInterface $router, ConfigService $configService)
    {
        $this->session = $session;
        $this->securityToken = $securityToken;
        $this->router = $router;
        $this->maxIdleTime = $configService->getGlobalConfigValue('sessionMaxIdleTime', 900);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType() || $this->isAdmin($event->getRequest()) === false) {

            return;
        }

        if ($this->maxIdleTime > 0) {

            $this->session->start();
            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();

            if ($lapse > $this->maxIdleTime) {

                $this->securityToken->setToken(null);
                $this->session->getFlashBag()->set('info', 'You have been logged out due to inactivity.');

                // Change the route if you are not using FOSUserBundle.
                $event->setResponse(new RedirectResponse($this->router->generate('admin_logout')));
            }
        }
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

    private function left($str, $nbr)
    {
        return substr($str,0,$nbr);
    }

}