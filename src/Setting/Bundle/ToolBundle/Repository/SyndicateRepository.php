<?php

namespace Setting\Bundle\ToolBundle\Repository;

use Doctrine\Common\Util\Debug;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Setting\Bundle\ToolBundle\Entity\Syndicate;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SyndicateRepository extends MaterializedPathRepository{


    public function findGroupEntity(){

        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id as id, e.entityName as name');
        $qb->where("e.entityName != :entityName");
        $qb->setParameter('entityName', 'null');
        return $qb->getQuery()->getResult();
    }

    public function getFlatTree()
    {

        $Syndicates = $this->childrenHierarchy();

        $this->buildFlatTree($Syndicates, $array);

        return $array;
    }

    public function getFlatSyndicateTree()
    {

        $Syndicates = $this->childrenHierarchy();

        $this->buildFlatSyndicateTree($Syndicates, $array);

        return $array;
    }

    private function buildFlatTree($Syndicates, &$array = array())
    {
        usort($Syndicates, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($Syndicates as $Syndicate) {
            $array[$Syndicate['id']] = $this->formatLabel($Syndicate['level'], $Syndicate['name']);
            if(isset($Syndicate['__children'])) {
                $this->buildFlatTree($Syndicate['__children'], $array);
            }
        }
    }

    private function buildFlatSyndicateTree($Syndicates, &$array = array())
    {
        usort($Syndicates, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($Syndicates as $Syndicate) {
            $array[] = $this->find($Syndicate['id']);
            if(isset($Syndicate['__children'])) {
                $this->buildFlatSyndicateTree($Syndicate['__children'], $array);
            }
        }
    }

    private function formatLabel($level, $value) {
        $level = $level - 1;
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . "$value";
    }


    public function getSyndicateOptions(){

        $ret = array();
        $em = $this->_em;
        $Syndicates = $em->getRepository('SettingToolBundle:Syndicate')->findBy(array(),array('name'=>'asc'));

        foreach( $Syndicates as $cat ){
            if( !$cat->getParent() ){
                continue;
            }
            if(!array_key_exists($cat->getName(), $ret) ){
                $ret[ $cat->getName() ] = array();
            }
            $ret[ $cat->getParent()->getName() ][ $cat->getId() ] = $cat;
        }
        return $ret;
    }

    /**
     * @param $Syndicates Syndicate[]
     * @return array
     */
    public function buildSyndicateGroup($Syndicates)
    {
        $result = array();

        foreach($Syndicates as $Syndicate) {
            $parentSyndicate = $this->getParentSyndicateByLevel($Syndicate, 2);


            if(empty($parentSyndicate)) {
                continue;
            }

            $parentId = $parentSyndicate->getId();

            if(!isset($result[$parentId])) {
                $result[$parentId] = array(
                    'name' =>  $parentSyndicate->getName(),
                    'slug' =>  $parentSyndicate->getSlug(),
                    '__children' =>  array(),
                );
            }

            $result[$parentId]['__children'][] = array(
                'name' => $Syndicate->getName(),
                'slug' => $Syndicate->getSlug()
            );
        }

        return $result;
    }

    public function getSyndicateOptionGroup()
    {
        $results = $this->createQueryBuilder('node')
            ->orderBy('node.level, node.name', 'ASC')
            ->where('node.level < 3')
            ->getQuery()
            ->getResult()
        ;

        $Syndicates = $this->getSyndicatesIndexedById($results);

        $grouped = array();

      //  Debug::dump($Syndicates);

        foreach ($Syndicates as $Syndicate) {
            switch($Syndicate->getLevel()) {
                case 2:
                    $grouped[$Syndicates[$Syndicate->getParentIdByLevel()]->getName()]["" . $Syndicate->getId()] = $Syndicate;
            }
        }

        return $grouped;
    }

    /**
     * @param Syndicate $Syndicate
     * @param int $level
     * @return Syndicate
     */
    public function getParentSyndicateByLevel(Syndicate $Syndicate, $level = 1)
    {
        return $this->find($Syndicate->getParentIdByLevel($level));
    }

    /**
     * @param $results
     * @return Syndicate[]
     */
    protected function getSyndicatesIndexedById($results)
    {
        $Syndicates = array();

        foreach ($results as $Syndicate) {
            $Syndicates[$Syndicate->getId()] = $Syndicate;
        }
        return $Syndicates;
    }

    public function getSyndicateBaseVendor()
    {
        return false;
    }

    public function getSelectedSubSyndicates($categories,$entity)
    {

        $array =array();
        if(!empty($entity->getSiteSetting())){

            $selectSyndicate = $entity->getSiteSetting()->getSyndicates();
            foreach($selectSyndicate as $row ){
                $array[] = $row->getId();
            }

        }

        $value ='';
        $value .='<ul>';
        foreach ($categories as $val) {

            $checkd = in_array($val->getId(), $array)? 'checked':'';

            if (!empty($val->getName())) {

                    $subIcon = (count($val->getChildren()) > 0 ) ? 1 : 2 ;
                    if($subIcon == 1){
                        $value .= '<li class="dd-item1" ><input type="checkbox" '.$checkd.' name="subSyndicates[]" value="'.$val->getId().'" >' . $val->getName();
                        $value .= $this->getSelectedSubSyndicates($val->getChildren(),$entity);
                    }else{
                        $value .= '<li class="dd-item1" ><input type="checkbox" '.$checkd.' name="subSyndicates[]" value="'.$val->getId().'" >' . $val->getName();
                    }
                    $value .= '</li>';

            }

        }
        $value .='</ul>';

        return $value;



    }
    public function getSyndicateUnderScholars($categories,$entity)
    {

        $array =array();
        if(!empty($entity->getSyndicates())){

            $selectSyndicate = $entity->getSyndicates();
            foreach($selectSyndicate as $row ){
                $array[] = $row->getId();
            }
        }

        $value ='';
        $value .='<ul>';
        foreach ($categories as $val) {

            $checkd = in_array($val->getId(), $array)? 'checked':'';

            if (!empty($val->getName())) {

                    $subIcon = (count($val->getChildren()) > 0 ) ? 1 : 2 ;
                    if($subIcon == 1){
                        $value .= '<li class="dd-item1" ><input type="checkbox" '.$checkd.' name="subSyndicates[]" value="'.$val->getId().'" >' . $val->getName();
                        $value .= $this->getSyndicateUnderScholars($val->getChildren(),$entity);
                    }else{
                        $value .= '<li class="dd-item1" ><input type="checkbox" '.$checkd.' name="subSyndicates[]" value="'.$val->getId().'" >' . $val->getName();
                    }
                    $value .= '</li>';

            }

        }
        $value .='</ul>';

        return $value;



    }

    public function getSyndicateTree(){

        $data= '';
        $data .=' <select>
      <optgroup label="Swedish Cars">
        <option value="volvo">Volvo</option>
        <option value="saab">Saab</option>
      </optgroup>
      <optgroup label="German Cars">
        <option value="mercedes">Mercedes</option>
        <option value="audi">Audi</option>
      </optgroup>
    </select> ';
        return $data;

    }




}
