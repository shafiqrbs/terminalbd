<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;


/**
 * ApplicationPricing controller.
 *
 */
class DomainController extends Controller
{


    public function generateDomainPathAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findByDomain();

        $domains = array(
            array(
                'resource' => '@FrontendBundle/Resources/config/routing/ecommercesubdomain.yml',
                'domain' => 'www.tlsbd.org',
                'subdomain' => 'tlsbd'
            )
        );


        $resource = '@FrontendBundle/Resources/config/routing/ecommercesubdomain.yml';
        $routes = array();

        foreach ($entities as $data){

            $routes['_www_domain_app_' . strtolower(str_replace('.', '_', $data->getDomain()))] = array(
                'resource' => $resource ,
                'host' => '{domain_name}',
                'name_prefix' => $data->getSubDomain() . "_",
                'defaults' => array(
                    'subdomain' => $data->getSubDomain(),
                    'domain_name' => 'www.' . $data->getDomain()
                ),
                'requirements' => array(
                    'domain_name' => sprintf('www.%s|%s', $data->getDomain(), $data->getDomain())
                )
            );
           /* $routes['_domain_app_' . strtolower(str_replace('.', '_', $data->getDomain()))] = array(
                'resource' => $resource ,
                'host' => $data->getDomain(),
                'name_prefix' => $data->getSubDomain() . "_",
                'defaults' => array(
                    'subdomain' => $data->getSubDomain()
                )
            );*/

        }

        $routesString = Yaml::dump($routes);

        file_put_contents(realpath(WEB_PATH . "/../app/config/dynamic/sites.yml"), $routesString);

        return $this->redirect($this->generateUrl('tools_domain'));

    }


    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all GlobalOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->getList();
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:Domain:index.html.twig', array(
            'entities' => $entities,
        ));
    }

     /**
     * Lists all GlobalOption entities.
     *
     */
    public function clientAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findBy(array('agent' => $user));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:Domain:client.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function optionStatusAction()
    {
        $items = array();
        $items[]=array('value' =>1,'text'=> 'Active');
        $items[]=array('value' =>2,'text'=> 'Hold');
        $items[]=array('value' =>3,'text'=> 'Suspended');
        return new JsonResponse($items);


    }

    public function optionStatusUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $entity->setStatus($data['value']);
        $em->flush();

        if($entity->getStatus() != 1){
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.domain_notification', new \Setting\Bundle\ToolBundle\Event\DomainNotification($entity));
        }
        exit;
    }

    public function resetDomainPasswordAction(GlobalOption $option)
    {
        $entity = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('globalOption'=> $option,'domainOwner' => 1));
        if(!empty($entity)){
            $a = mt_rand(1000,9999);
            $entity->setPlainPassword($a);
            $this->get('fos_user.user_manager')->updateUser($entity);
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.change_domain_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeDomainSmsEvent($option,$entity->getUsername(),$a));
            $this->get('session')->getFlashBag()->add(
                'success',"Change password successfully"
            );
        }
        return $this->redirect($this->generateUrl('tools_domain'));

    }

    public function resetManualDomainPasswordAction(Request $request, GlobalOption $option)
    {
        $entity = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('globalOption'=> $option,'domainOwner'=>1));
        if(!empty($entity)){
            $a = $request->request->get('password');
            $entity->setPlainPassword($a);
            $this->get('fos_user.user_manager')->updateUser($entity);
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.change_domain_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeDomainSmsEvent($option,$entity->getUsername(),$a));
        }
        exit;
    }

    public function resetSystemDataAction(GlobalOption $option)
    {


        set_time_limit(0);
        if($option->getAccountingConfig()){
            $this->getDoctrine()->getRepository('AccountingBundle:AccountingConfig')->accountingReset($option);
        }
        if($option->getEcommerceConfig()) {
            $this->getDoctrine()->getRepository('EcommerceBundle:EcommerceConfig')->ecommerceReset($option);
        }
        if($option->getInventoryConfig()) {
            $this->getDoctrine()->getRepository('InventoryBundle:InventoryConfig')->inventoryReset($option);
        }
        if($option->getHospitalConfig()) {
            $this->getDoctrine()->getRepository('HospitalBundle:HospitalConfig')->hospitalReset($option);
        }
        $dir = WEB_PATH . "/uploads/domain/" . $option->getId() . "/inventory";
        $a = new Filesystem();
        $a->remove($dir);
        $a->mkdir($dir);
        $this->get('session')->getFlashBag()->add(
            'success',"Successfully reset data"
        );
        return $this->redirect($this->generateUrl('tools_domain'));

    }



}
