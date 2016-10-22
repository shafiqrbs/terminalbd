<?php

namespace Bindu\BinduBundle\Controller;
use Desktop\Bundle\Service\MobileDetect;
use Proxies\__CG__\Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\Syndicate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;


class WidgetController extends Controller
{

    public function aboutusAction($slug='')
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $aboutus = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'menu'=>'About us'));
        return $this->render('@Bindu/Widget/aboutus.html.twig', array(
            'about'           => $about,
        ));
    }

   public function businessAction($directory = NULL )
    {

        exit;
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new \Frontend\FrontentBundle\Service\MobileDetect();
        /*if($detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/businessContent.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Desktop/businessContent.html.twig', array(
                'entities'           => $entities,
            ));
        }*/

    }

   public function locationAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->findBy(array('level'=>2),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/locationContent.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Desktop/locationContent.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

    public function moduleAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:Module')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/module.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Desktop/module.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

    public function syndicateModuleAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:SyndicateModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/syndicateModule.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Desktop/syndicateModule.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

    public function appModuleAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/appModule.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Desktop/appModule.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }



    public function searchAction($search='',$sector='',$location='')
    {
        $entity = new GlobalOption();
        $form   = $this->createCreateForm($entity);
        return $this->render('@Bindu/Widget/search.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm(GlobalOption $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new \Setting\Bundle\ToolBundle\Form\SearchType($em), $entity, array(
            'action' => $this->generateUrl('bindu_search', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'commentform',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    public function searchingAction(Request $request)
    {
        $data = $request->request->all();
        var_dump($data);
        exit;
        return $this->redirect($this->generateUrl('bindu_confirm'));
    }



}
