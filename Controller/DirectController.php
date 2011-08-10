<?php

namespace Hatimeria\ExtJSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Hatimeria\ExtJSBundle\Api\Api;
use Hatimeria\ExtJSBundle\Router\Router;

class DirectController extends Controller
{
    /**
     * Generate the ExtDirect API.
     * 
     * @return Response 
     */
    public function getApiAction()
    {
        // instantiate the api object
        $api = new Api($this->container);
        
        $response = sprintf("Ext.Direct.addProvider(%s);", $api);
        $response .= sprintf("
            Ext.ns('App.Direct'); 
            App.Direct.environment = '%s';
            ", $this->container->getParameter("kernel.environment"));

        $signinRoute = $this->container->getParameter('hatimeria_ext_js.signin_route');
        
        if($signinRoute) {
            $signinUrl = $this->container->get('router')->generate($signinRoute);
            $response.= sprintf("App.Direct.signinUrl = '%s'", $signinUrl);
        }
        $r = new Response($response);
        $r->headers->set("Content-Type","text/javascript");
        
        return $r;
    }

    /**
     * Route the ExtDirect calls.
     *
     * @return Response
     */
    public function routeAction()
    {
        // instantiate the router object
        $router  = new Router($this->container);
        $request = $router->getRequest();
        $content = $router->route();
        
        if ($request->isFormCallType() && !$request->isXmlHttpRequest()) {
           $content = sprintf("<html><body><textarea>%s</textarea></body></html>", $content);
           $contentType = "text/html";
        } else {
           $contentType = "application/json";
        }
        
        $r = new Response($content);
        $r->headers->set("Content-Type", $contentType);
        
        return $r;
    }
}
