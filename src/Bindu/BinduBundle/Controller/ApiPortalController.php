<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiPortalController extends Controller
{


    public function portalInformationAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data = array(
                'name' => 'Right Brain Solution Ltd.',
                'mobile' => '01828148148',
                'email' => 'info@rightbrainsolution.com',
                'website' => 'www.rightbrainsolution.com',
                'address' => "Pul Tower, Plot no 29, Gausul Azam Avenue, Sector-14, Uttara, Dhaka-1230",
                'lat' => "23.869141",
                'lon' => "90.389381",

            );
        }else{
            return new Response('Unauthorized access.', 401);

        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function domainListAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->apiDomains();
        }else{
            return new Response('Unauthorized access.', 401);

        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function appsAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->getAppModules();
        }else{
            return new Response('Unauthorized access.', 401);

        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function appDetailsAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $id = $_REQUEST['id'];
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->getAppModuleDetails($id);
        }else{
            return new Response('Unauthorized access.', 401);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }
    public function appWiseCustomerAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $id = $_REQUEST['id'];
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->apiAppDomain($id);
        }else{
            return new Response('Unauthorized access.', 401);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }


}
