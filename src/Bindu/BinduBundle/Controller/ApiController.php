<?php

namespace Bindu\BinduBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends Controller
{

    public function setupAction(Request $request)
    {

        $data = $request->request->all();
        $headers = getallheaders();
        echo $headers['X-API-KEY'];


        exit;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }

    public function apiPingAction(Request $request){

        $data = $request->request->all();
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array('message'=>$data['message'], 'ipAddress'=>$ipAddress)));
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    public function apiResponseAction(Request $request, $data)
    {

//        if (!$request->headers->has('X-ApiKey')) {
//            return new Response('Required API key is missing.', 400);
//        }
//
//        if ($request->headers->get('X-ApiKey') != '123') {
//            return new Response('Unauthorized access.', 401);
//        }

        $response = new JsonResponse($data, 200, [
            'Content-type' => 'application/json'
        ]);

        return $response;
    }

}
