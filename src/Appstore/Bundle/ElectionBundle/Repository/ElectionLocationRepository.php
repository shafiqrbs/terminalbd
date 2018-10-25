<?php

namespace Appstore\Bundle\ElectionBundle\Repository;
use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Appstore\Bundle\ElectionBundle\Entity\ElectionMember;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Appstore\Bundle\ElectionBundle\Entity\ElectionLocation;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ElectionLocationRepository extends MaterializedPathRepository{


	public function initialDistrict(ElectionConfig $config){

		/* @var $exist ElectionLocation */

		$exist = $this->findOneBy(array('electionConfig'=>$config));
		if(empty($exist)){
			$entity = new ElectionLocation();
			$entity->setElectionConfig($config);
			$entity->setDistrict($config->getDistrict());
			$entity->setName($config->getDistrict()->getName());
			$this->_em->persist($entity);
		}else {
			$exist->setDistrict($config->getDistrict());
			$exist->setName($config->getDistrict()->getName());
		}
			$this->_em->flush();
	}


	public function getFlatTree()
	{

		$locations = $this->childrenHierarchy();
		$this->buildFlatTree($locations, $array);
		return $array;
	}

	public function getFlatLocationTree()
	{

		$locations = $this->childrenHierarchy();

		$this->buildFlatLocationTree($locations, $array);

		return $array;
	}

	private function buildFlatTree($locations, &$array = array())
	{
		usort($locations, function($a, $b){
			return strcmp($a["name"], $b["name"]);
		});

		foreach($locations as $location) {
			$array[$location['id']] = $this->formatLabel($location['level'], $location['name']);
			if(isset($location['__children'])) {
				$this->buildFlatTree($location['__children'], $array);
			}
		}
	}

	private function buildFlatLocationTree($locations, &$array = array())
	{
		usort($locations, function($a, $b){
			return strcmp($a["name"], $b["name"]);
		});

		foreach($locations as $location) {
			$array[] = $this->find($location['id']);
			if(isset($location['__children'])) {
				$this->buildFlatLocationTree($location['__children'], $array);
			}
		}
	}

	private function formatLabel($level, $value) {
		$level = $level - 1;
		return str_repeat("-", $level * 3) . str_repeat(">", $level) . "$value";
	}


	public function getLocationOptions($config = ''){

		$ret = array();
		$em = $this->_em;
		$locations = $em->getRepository('ElectionBundle:ElectionLocation')->findBy(array(),array('name'=>'asc'));

		foreach( $locations as $cat ){
			if( !$cat->getParent() ){
				continue;
			}
			if(!array_key_exists($cat->getParent()->getName(), $ret) ){
				$ret[ $cat->getParent()->getName() ] = array();
			}
			$ret[ $cat->getParent()->getName() ][ $cat->getId() ] = $cat;
		}
		return $ret;
	}

	/**
	 * @param $locations location[]
	 * @return array
	 */
	public function buildLocationGroup($locations)
	{
		$result = array();

		foreach($locations as $location) {
			$parentLocation = $this->getParentLocationByLevel($location, 2);


			if(empty($parentLocation)) {
				continue;
			}

			$parentId = $parentLocation->getId();

			if(!isset($result[$parentId])) {
				$result[$parentId] = array(
					'name' =>  $parentLocation->getName(),
					'__children' =>  array(),
				);
			}

			$result[$parentId]['__children'][] = array(
				'name' => $location->getName()
			);
		}

		return $result;
	}

	public function getLocationOptionGroup()
	{
		$results = $this->createQueryBuilder('node')
		                ->orderBy('node.parent', 'ASC')
		                ->where('node.level < 1')
		                ->getQuery()
		                ->getResult();

		$locations = $this->getLocationsIndexedById($results);

		$grouped = array();

		foreach ($locations as $location) {
			switch($location->getLevel()) {

				case 3:
					$grouped[$locations[$location->getParentIdByLevel(3)]->getName()][$location->getId()] = $location;
			}
		}

		return $grouped;
	}

	public function getDistrictOptionGroup()
	{
		$results = $this->createQueryBuilder('node')
		                ->orderBy('node.level, node.name', 'ASC')
		                ->where('node.level < 3')
		                ->getQuery()
		                ->getResult();

		$locations = $this->getLocationsIndexedById($results);

		$grouped = array();

		foreach ($locations as $location) {
			switch($location->getLevel()) {
				case 2:
					$grouped[$locations[$location->getParentIdByLevel(2)]->getName()][$location->getId()] = $location;
			}
		}

		return $grouped;
	}

	/**
	 * @param $results
	 * @return ElectionLocation[]
	 */
	protected function getCategoriesIndexedById($results)
	{
		$categories = array();

		foreach ($results as $category) {
			$categories[$category->getId()] = $category;
		}
		return $categories;
	}

	public function getLocationGroup($config)
	{
		$grouped = array();

		$qb = $this->createQueryBuilder('node');
		$results = $qb
			->orderBy('node.level, node.name', 'ASC')
			->where('node.electionConfig = '.$config)
			->getQuery()
			->getResult();

		$categories = $this->getCategoriesIndexedById($results);

		foreach ($categories as $category) {
			switch($category->getLevel()) {
				case 1: break;
				default:
					$grouped[$categories[$category->getParentIdByLevel(1)]->getName()][$category->getId()] = $category;
			}
		}
		return $grouped == null ? array() : $grouped;

	}

	/**
	 * @param Location $location
	 * @param int $level
	 * @return Location
	 */
	public function getParentLocationByLevel(ElectionLocation $location, $level = 2)
	{
		return $this->find($location->getParentIdByLevel($level));
	}

	/**
	 * @param $results
	 * @return Location[]
	 */
	protected function getLocationsIndexedById($results)
	{
		$locations = array();

		foreach ($results as $location) {
			$locations[$location->getId()] = $location;
		}
		return $locations;
	}


	public function getUnderChild($parent,$thana)
	{

		$em = $this->_em;
		$entities = $em->getRepository('SettingLocationBundle:Location')->findBy(array('parent'=>$parent),array('name'=>'asc'));
		$data = '';
		$data .= '<label>Thana/Upazilla</label>';
		$data .= '<p><select id="thana" name="thana" class="select2"  >';
		foreach( $entities as $entity){
			if ($thana === $entity->getId()){ $selected = 'selected' ; }else{ $selected=''; }
			$data .='<option  '.$selected.'  value="'. $entity->getId() .'">'.$entity->getName().'</option>';
		}
		$data .='</select></p>';
		return $data;

	}

	public function searchAutoComplete(ElectionConfig $config , $type , $q)
	{
		$query = $this->createQueryBuilder('e');
		$query->join('e.parent','parent');
		$query->join('e.locationType','t');
		$query->select('e.id as id');
		$query->addSelect('CONCAT(e.name, \',\', parent.name) AS text');
		$query->where("e.electionConfig = ".$config->getId());
		$query->andWhere("t.slug = '{$type}'");
		$query->andWhere($query->expr()->like("e.name", "'$q%'"  ));
		$query->groupBy('e.id');
		$query->orderBy('e.name', 'ASC');
		$query->setMaxResults( '10' );
		return $query->getQuery()->getResult();


	}

	public function getVillageMemberName(ElectionConfig $config,$q = ''){

		if(!empty($q)) {
			$qb = $this->createQueryBuilder( 'e' );
			$qb->join( 'e.locationType', 'p' );
			$qb->where( 'e.electionConfig =' . $config->getId() );
			$qb->andWhere( "p.defineSlug = :slug" )->setParameter( 'slug', 'village' );
			$qb->andWhere( $qb->expr()->like( "e.name", "'$q%'" ) );
			$result = $qb->getQuery()->getOneOrNullResult();

			return $result;
		}
		return false;

	}

	public function getMemberVoteCenter(ElectionConfig $config,$q = ''){

		if(!empty($q)){
			$qb = $this->createQueryBuilder('e');
			$qb->join('e.locationType','p');
			$qb->where('e.electionConfig ='.$config->getId());
			$qb->andWhere("p.defineSlug = :slug")->setParameter('slug','vote-center');
			$qb->andWhere($qb->expr()->like("e.name", "'$q%'"  ));
			$result  = $qb->getQuery()->getOneOrNullResult();
			return $result;
		}
		return false;


	}



}
