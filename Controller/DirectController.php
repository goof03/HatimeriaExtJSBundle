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
        $url = $this->container->get('router')->generate('fos_user_security_login');

        // return the json api description
        //$r = new Response("Ext.Direct.addProvider(".$api.");");
        $response = "
            Ext.Direct.addProvider(".$api.");
            Ext.Direct.on('event', function(response) {
                if (!response.result.success)
                {
                    if (response.result.exception)
                    {
                        switch(response.result.code)
                        {
                            case 404:
                                console.log('404');
                                break;
                            case 403:
                                window.location = '".$url."';
                                break;
                        }
                    }
                }
            });
        ";
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
        $router = new Router($this->container);

        $content = $router->route();
        $hasFiles = count($_FILES) > 0;

        
        if ($router->getRequest()->isFormCallType()) {
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
