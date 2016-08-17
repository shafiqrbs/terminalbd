<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 9/30/15
 * Time: 11:31 PM
 */

namespace Syndicate\Bundle\ComponentBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Syndicate\Bundle\ComponentBundle\Entity\Academic;

class AcademicRepository extends EntityRepository {


    public function insertAcademic($reEntity,$data)
    {

        $em = $this->_em;

        $degree         = $data['degree'];
        $course         = $data['course'];
        $passingYear    = $data['passingYear'];
        $result         = $data['result'];
        $institute      = $data['institute'];


        $i=0;
        foreach($degree as $val ){

            if($data['academicId'][$i] > 0 ){
                $id = $data['academicId'][$i];
                $entity = $em->getRepository('SyndicateComponentBundle:Academic')->find($id);
            }else{
                $entity = New Academic();
            }
            $entity->setTutor($reEntity);
            $entity->setDegree($val);
            $entity->setCourse($course[$i]);
            $entity->setPassingYear($passingYear[$i]);
            $entity->setResult($result[$i]);
            $entity->setInstitute($institute[$i]);
            $em->persist($entity);
            $i++;

        }
        $em->flush();

        if(!empty($data['academicRemoveId'])){

            $this->removeAcademic($data['academicRemoveId']);
        }

    }

    public function removeAcademic($posts)
    {
        $em = $this->_em;
        foreach ($posts as $post ){
            if($post > 0 ){
                $entity = $em->getRepository('SyndicateComponentBundle:Academic')->find($post);
                $em->remove($entity);
            }
        }
        $em->flush();


    }



} 