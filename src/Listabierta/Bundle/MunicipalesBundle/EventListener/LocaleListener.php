<?php 
namespace Listabierta\Bundle\MunicipalesBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'es_es')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) 
        {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        $locale = $request->query->get('_locale');
        
        if (!empty($locale)) 
        {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } 
        else 
        {
            // if no explicit locale has been set on this request, use one from the session
        	$locale_session = $request->getSession()->get('_locale', $this->defaultLocale);
        	
        	$locale_session = empty($locale_session) ? 'es_es' : $locale_session;
        	
        	$request->getSession()->set('_locale', $locale_session);
        	
            $request->setLocale($locale_session);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}