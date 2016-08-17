<?php

namespace Desktop\Bundle\Controller;

use Setting\Bundle\ToolBundle\Event\ReceiveEmailEvent;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SenderController extends Controller
{



    public function emailReceiving(Request $request)
    {
        $data = $request->request->all();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($data);
        $customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data);
        if( $customer->getGlobalOption()->getEmailIntegration() == 1 AND $customer->getGlobalOption()->getEmail() !="" )
        {
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.email_receive', new ReceiveEmailEvent($customer->getGlobalOption(),$customerInbox));

        }
        return new Response('success');
    }

    public function smsReceiving(Request $request)
    {
        $data = $request->request->all();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertSMSCustomer($data);
        $customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data);
        if( $customer->getGlobalOption()->getSmaIntegration() == 1 AND $customer->getGlobalOption()->getMobile() !="" ) {
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.sms_receive', new ReceiveSmsEvent($customer->getGlobalOption(), $customerInbox));
        }
        return new Response('success');
    }



    public function blogSubmitAction(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:BlogComment')->insertMessage($data);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }

    public function admissionSubmitAction(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:AdmissionComment')->insertMessage($data);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }


}
