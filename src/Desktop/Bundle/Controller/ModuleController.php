<?php

namespace Desktop\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends Controller
{

    public function moduleAction($subdomain,$module){
        echo $subdomain;
        exit;
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            if($module =='syndicate'){
                $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $slug));
            }else{
                $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $module));
            }

            $page ='';
            $moduleName ='';

            if(!empty($menu)){

                $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));


                $mobileTheme = $siteEntity->getMobileTheme();
                if(!empty($siteEntity) && !empty($mobileTheme) ){
                     $themeName = $siteEntity->getMobileTheme()->getFolderName();
                }else{
                     $themeName ='Default';
                }

                $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));

                $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain,'desktop');

                $moduleTheme = $this->get('setting.menuTreeSettingRepo')->getModuleTheme($menu);
                if($moduleTheme){

                    $moduleName = $this->get('setting.menuTreeSettingRepo')->getModuleTheme($menu);
                    $details = $em->getRepository('SettingContentBundle:'.$moduleName)->findOneBy(array('globalOption'=>$globalOption,'slug' => $slug));
                    $twigName = "module";
                    $fieldName = strtolower($moduleName);
                    if($fieldName == 'blog'){
                        $comment = $em->getRepository('SettingContentBundle:'.$moduleName.'Comment')->findBy(array($fieldName =>$details,'status'=>1));
                        if($comment){
                            $comments = $comment;
                        }else{
                            $comments='';
                        }

                    }else{
                        $comments='';
                    }

                }else{

                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'menuSlug' => $module));
                    $twigName = "content";
                    $details = "";
                    $comments = "";

                }
            }
            $page = ($page) ? $page :'';

            return $this->render('DesktopBundle:'.$themeName.':'.$twigName.'Details.html.twig',
                array(

                    'menu'          => $menusTree,
                    'entity'        => $globalOption,
                    'page'          => $page,
                    'details'       => $details,
                    'comments'       => $comments,
                    'moduleName'    => $moduleName
                )
            );
        }



    }

    public function eventAction($subdomain)
    {


        header('Content-type: text/json');
        echo '[';
        $separator = "";
        $days = 16;
        echo '	{ "date": "1314579600000", "type": "meeting", "title": "Test Last Year", "description": "Lorem Ipsum dolor set", "url": "http://www.event3.com/" },';
        echo '	{ "date": "1377738000000", "type": "meeting", "title": "Test Next Year", "description": "Lorem Ipsum dolor set", "url": "http://www.event3.com/" },';
        for ($i = 1 ; $i < $days; $i= 1 + $i * 2) {
            echo $separator;
            $initTime = (intval(microtime(true))*1000) + (86400000 * ($i-($days/2)));
            echo '	{ "date": "'; echo $initTime; echo '", "type": "meeting", "title": "Project '; echo $i; echo ' meeting", "description": "Lorem Ipsum dolor set", "url": "http://www.event1.com/" },';
            echo '	{ "date": "'; echo $initTime+3600000; echo '", "type": "demo", "title": "Project '; echo $i; echo ' demo", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.", "url": "http://www.event2.com/" },';
            echo '	{ "date": "'; echo $initTime-7200000; echo '", "type": "meeting", "title": "Test Project '; echo $i; echo ' Brainstorming", "description": "Lorem Ipsum dolor set", "url": "http://www.event3.com/" },';
            echo '	{ "date": "'; echo $initTime+10800000; echo '", "type": "test", "title": "A very very long name for a f*cking project '; echo $i; echo ' events", "description": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.", "url": "http://www.event4.com/" },';
            echo '	{ "date": "'; echo $initTime+1800000; echo '", "type": "meeting", "title": "Project '; echo $i; echo ' meeting", "description": "Lorem Ipsum dolor set", "url": "http://www.event5.com/" },';
            echo '	{ "date": "'; echo $initTime+3600000+2592000000; echo '", "type": "demo", "title": "Project '; echo $i; echo ' demo", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.", "url": "http://www.event6.com/" },';
            echo '	{ "date": "'; echo $initTime-7200000+2592000000; echo '", "type": "meeting", "title": "Test Project '; echo $i; echo ' Brainstorming", "description": "Lorem Ipsum dolor set", "url": "http://www.event7.com/" },';
            echo '	{ "date": "'; echo $initTime+10800000+2592000000; echo '", "type": "test", "title": "A very very long name for a f*cking project '; echo $i; echo ' events", "description": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.", "url": "http://www.event8.com/" },';
            echo '	{ "date": "'; echo $initTime+3600000-2592000000; echo '", "type": "demo", "title": "Project '; echo $i; echo ' demo", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.", "url": "http://www.event9.com/" },';
            echo '	{ "date": "'; echo $initTime-7200000-2592000000; echo '", "type": "meeting", "title": "Test Project '; echo $i; echo ' Brainstorming", "description": "Lorem Ipsum dolor set", "url": "http://www.event10.com/" },';
            echo '	{ "date": "'; echo $initTime+10800000-2592000000; echo '", "type": "test", "title": "A very very long name for a f*cking project '; echo $i; echo ' events", "description": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.", "url": "http://www.event11.com/" }';
            $separator = ",";
        }
        echo ']';

       // return new Response($data);
    }

    public function blogsAction($subdomain){

        $data = '';

        $data .='<ul>';
        $data .='<li>';
        $data .='<a>Test blog</a>';
        $data .='<a>Test blog</a>';
        $data .='<a>Test blog</a>';
        $data .='</li>';
        $data .='</ul>';
        echo $data;
        exit;

    }

}
