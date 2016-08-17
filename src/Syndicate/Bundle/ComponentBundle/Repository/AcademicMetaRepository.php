<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 9/30/15
 * Time: 11:32 PM
 */

namespace Syndicate\Bundle\ComponentBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Syndicate\Bundle\ComponentBundle\Entity\AcademicMeta;

class AcademicMetaRepository extends EntityRepository {

    public function insertAcademicMeta($reEntity,$data)
    {

        $em = $this->_em;

        $metaKey        = $data['metaKey'];
        $metaValue      = $data['metaValue'];

        $x=0;

        foreach($metaKey as $val ){

            if($data['metaId'][$x] > 0 ){
                $id = $data['metaId'][$x];
                $entity = $em->getRepository('SyndicateComponentBundle:AcademicMeta')->find($id);
            }else{
                $entity = New AcademicMeta();
            }
            $entity->setTutor($reEntity);
            $entity->setMetaKey($val);
            $entity->setMetaValue($metaValue[$x]);
            $em->persist($entity);
            $x++;

        }
        $em->flush();

        if(!empty($data['metaRemoveId'])){

            $this->removeAcademicMeta($data['metaRemoveId']);
        }

    }


    public function removeAcademicMeta($posts)
    {
        $em = $this->_em;

        foreach ($posts as $post ){
            if($post > 0 ){
                $entity = $em->getRepository('SyndicateComponentBundle:AcademicMeta')->find($post);
                $em->remove($entity);
            }
        }
        $em->flush();


    }


} 