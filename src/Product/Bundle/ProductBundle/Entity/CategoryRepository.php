<?php

namespace Product\Bundle\ProductBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query\Expr\Orx;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends MaterializedPathRepository
{

   public function getCategories($data,$array)
    {


        $em = $this->_em;

        $tree = "";					// Clear the directory tree
        $this->depth = 1;			// Child level depth.
        $top_level_on = 1;			// What top-level category are we on?
        $exclude = array();			// Define the exclusion array
        array_push($exclude, 0);	// Put a starting value in it

        $tree .= '<select class="form-control input-sm select2" name="category[]" id="category" ><option selected="selected" value=0 >---Select one---</option>';

        foreach ($data  as $row ){


            $goOn = 1;
            for($x = 0; $x < count($exclude); $x++ )
            {
                if ( $exclude[$x] == $row->getId() )
                {
                    $goOn = 0;
                    break;
                }
            }
            if ( $goOn == 1 )
            {


                if(in_array($row->getId(), $array)){
                    $tree .= '<option value="'.$row->getId().' >'.$row->getName() . "</option>";

                    array_push($exclude, $row->getId());
                    if ( $row->getId() < 6 )
                    { $top_level_on = $row->getId(); }
                    $tree .=$this->build_child($row->getId(),$array);
                }
            }

        }
        $tree .= '</select>';

        return $tree;
    }



    public function build_child($oldID = null ,$array=array())
    {

        $em = $this->_em;
        $tree = "";					// Clear the directory tree
        $this->depth = 1;			// Child level depth.
        $top_level_on = 1;			// What top-level category are we on?
        $exclude = array();			// Define the exclusion array
        array_push($exclude, 0);	// Put a starting value in it
        $tempTree="";

        $childData = $em->getRepository('ProductProductBundle:Category')->findBy(array('parent' => $oldID ),array('name'=>'asc'));

        foreach ( $childData as $child ){

            if(in_array($child->getId(), $array)){
                $tempTree .= '<option value="'.$child->getId().'" rel="'.$this->depth.'">';
                for ( $c=0;$c<$this->depth;$c++ )
                { $tempTree .= "&nbsp;&nbsp;&nbsp;"; }
                for ( $c=0; $c < $this->depth; $c++ )
                { $tempTree .= ">"; }
                $tempTree .= "" . $child->getName(). "</option>";
            }

            $this->depth++;
            $tempTree .=$this->build_child($child->getId(),$array);
            $this->depth--;
            if(is_array($exclude)){
                array_push($exclude,$child->getId());
            }
        }

        return $tempTree;
    }

    public function printTree( $category , $spacing = '--', $user_tree_array = '' ) {

        $em = $this->_em;
        foreach ($category as $row )
        {
            $user_tree_array[] = array("id" => $row->getId(), "name" => $spacing . $row->getName());
            $user_tree_array = $this->printTree($row->getChildren(), $spacing . '--', $user_tree_array);
        }
        return $user_tree_array;

    }

    public function  getReturnCategoryTree($category,$slected='')
    {
        $categoryTree = $this->printTree($category);

        $tree='';
        $tree .= "<select name='category' id='category' style='width: 278px' class='select2'>";
        $tree .= "<option value=''>Filter by category</option>";
        foreach($categoryTree as $row) {
            $selected = ($slected == $row['id'])? 'selected="selected"':'';
            $tree .= "<option ".$selected." value=".$row["id"].">".$row["name"]."</option>";
        }
        $tree .= "</select>";
        return $tree;
    }


    function getParentId($inventoryCat) {

        $cats = array();
        foreach ( $inventoryCat->getCategories() as $cat ){
           $cats[] = $cat->getId();
        }
        $qb = $this->createQueryBuilder('category');
        $qb->where('category.parent IN(:cats)');
        $qb->setParameter('cats',array_values($cats));
        $qb->orderBy('category.name','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;

    }

    public function productCategorySidebar($category){


       if(empty($category) || count($category) == 0){
           return '';
       }
       $result = "<ul>";
        foreach ($category as $row)
        {
            if (!empty($row->getChildren())) {
                $result.= '<li><a href="/product/category/'.$row->getId().'">'.$row->getName() .'</a>';
                $result.= $this->productCategorySidebar($row->getChildren());
                $result.= "</li>";
            }else {
                $result .= '<li><a href="/product/category/'.$row->getId().'">' . $row->getName() .'</a></li>';
            }
        }

        $result.= "</ul>";
        return $result;

    }



    public function getSelectdDropdownCategories($data,$array,$slected=''){

        $tree = "";					// Clear the directory tree
        $tree .= '<select class="span12 select2" name="category" id="category" >';
        $tree .= '<option value="" >---Select one---</option>';

        foreach ($data  as $row ){

             if(in_array($row->getId(), $array)){
                 $selected = ($slected == $row->getId() )? 'selected="selected"':'';
                 $tree .= '<option  value='.$row->getId().' '.$selected.' >'.$row->getName() . '</option>';
             }
        }
        return $tree .= '</select>';
    }

    public function getGroupCategories($categories,$array = array() ){


        $value ='';
        $value .='<ul>';
        foreach ($categories as $val) {
            $checkd = in_array($val->getId(), $array)? 'checked':'';
            $name = $val->getName();
            if (!empty($name)) {
                $subIcon = (count($val->getChildren()) > 0 ) ? 1 : 2 ;

                if($subIcon == 1){

                    $value .= '<li class="dd-item dd3-item" ><div class="dd4-content"><input type="checkbox" '.$checkd.' name="categories[]" value="'.$val->getId().'" >' . $val->getName().'</div>';
                    //$value .= $this->getGroupCategories($val->getChildren(),$array);
                }else{
                    $value .= '<li class="dd-item dd3-item" ><div class="dd4-content"><input type="checkbox" '.$checkd.' name="categories[]" value="'.$val->getId().'" >' . $val->getName().'</div>';
                }

                $value .= '</li>';
            } else {
                $value .= '<li class="dd-item dd3-item" ><div class="dd4-content"><input type="checkbox" '.$checkd.' name="categories[]" value="'.$val->getId().'" >'.$val->getName(). '</div></li>';
            }
        }
        $value .='</ul>';

        return $value;

    }

    public function getSelectedCategories($categories,$entity){


        $array =array();
        /*$getCategories = $entity -> getCategories();
        if(!empty($getCategories) && $entity ){
            foreach($getCategories as $row ){
                $array[] = $row->getId();
            }

        }*/


        $value ='';
        $value .='<ul>';
        foreach ($categories as $val) {

            $checkd = in_array($val->getId(), $array)? 'checked':'';

            $name = $val->getName();
            if (!empty($name)) {
                if(in_array($val->getId(), $array)){

                    $subIcon = (count($val->getChildren()) > 0 ) ? 1 : 2 ;

                    if($subIcon == 1){
                        $value .= '<li class="dd-item1" ><input type="checkbox" '.$checkd.' name="categories[]" value="'.$val->getId().'" >' . $val->getName();
                        $value .= $this->getSelectedCategories($val->getChildren(),$entity);
                    }else{
                        $value .= '<li class="dd-item1" ><input type="checkbox" '.$checkd.' name="categories[]" value="'.$val->getId().'" >' . $val->getName();
                    }
                    $value .= '</li>';
                }


            }

        }
        $value .='</ul>';
        //\Doctrine\Common\Util\Debug::dump($value);
        //exit;


        return $value;

    }

    public function getProductCategory($categories,$addCalss ='treeview'){

        $value ='';
        $addCalss = ($addCalss == '') ? 'list-group margin-bottom-25 sidebar-menu' : 'dropdown-menu';
        $value .='<ul class="'.$addCalss.'" >';
        foreach ($categories as $val) {

            $name = $val->getName();
            if (!empty($name)) {

                $subIcon = (count($val->getChildren()) > 0 ) ? 1 : 2 ;
                if($subIcon == 1){
                    $value .= '<li class="list-group-item clearfix dropdown" ><a href="shop-product-list.html"><i class="fa fa-angle-right"></i>'.$val->getName().'</a>';
                    $value .= $this->getProductCategory($val->getChildren(),$addCalss ='dropdown-menu');
                }else{
                    $value .= '<li class="list-group-item clearfix" ><a href="shop-product-list.html">' . $val->getName().'</a>';
                }
                $value .= '</li>';
            }

        }
        $value .='</ul>';

        return $value;

    }

    public function getProductCategoryMenu($categories,$addCalss =''){

        $value ='';
        $addCalss = ($addCalss == '') ? 'list-group margin-bottom-25 sidebar-menu' : 'dropdown-menu';
        $value .='<ul class="'.$addCalss.'" >';
        foreach ($categories as $val) {

            $name = $val->getName();
            if (!empty($name)) {

                $subIcon = (count($val->getChildren()) > 0 ) ? 1 : 2 ;

                if($subIcon == 1){
                    $value .= '<li class="list-group-item clearfix dropdown" ><a href="shop-product-list.html"><i class="fa fa-angle-right"></i>'.$val->getName().'</a>';
                    $value .= $this->getProductCategory($val->getChildren(),$addCalss ='dropdown-menu');
                }else{
                    $value .= '<li class="list-group-item clearfix" ><a href="shop-product-list.html">' . $val->getName().'</a>';
                }
                $value .= '</li>';
            }

        }
        $value .='</ul>';

        return $value;

    }


    public function setFeatureOrdering($data)
    {
        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();

        foreach ($data as $key => $value){
            $val = ($value) ? $value: 0 ;
            $q = $qb->update('ProductProductBundle:Category', 'mg')
                ->set('mg.sorting', $i)
                ->where('mg.id = :id')
                ->setParameter('id', $key)
                ->getQuery()
                ->execute();
            $i++;

        }
    }

    public function setCategoryFeature($data)
    {

        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $isFeatures = $data['feature'];
        $catIDs = $data['catId'];
        foreach ($catIDs as $value){

            $val = in_array($value , $isFeatures ) ? 1 : 0 ;
            $q = $qb->update('ProductProductBundle:Category', 'mg')
                ->set('mg.feature', $val)
                ->where('mg.id = :id')
                ->setParameter('id', $value)
                ->getQuery()
                ->execute();
            $i++;

        }


    }

    public function getRootCategoriesQB() {

        $qb = $this->createQueryBuilder('c');

        return $qb
            ->where('c.status = :status')
            ->andWhere($qb->expr()->isNull('c.parent'))
            ->setParameter('status', 1)
            ->orderBy('c.name', 'ASC');
    }

    public function getRootCategories() {
        return $this->getRootCategoriesQB()->getQuery()->getResult();
    }

    public function getFeaturedRootCategories() {
        $qb = $this->getRootCategoriesQB();

        return $qb
            ->andWhere($qb->expr()->eq('c.feature', true))
            ->getQuery()
            ->getResult();
    }

    public function getCategoryFeature()
    {
        $categories = $this->getFeaturedRootCategories();

        $value ='';
        $addCalss =  'list-group margin-bottom-25 sidebar-menu' ;
        $value .='<ul class="'.$addCalss.'" >';
        foreach ($categories as $val) {
                $value .= '<li class="list-group-item clearfix" ><a href="/category/'.$val->getSlug().'">' . $val->getName().'</a></li>';
        }
        $value .='</ul>';

        return $value;
    }

    public function getFlatTree()
    {

        $categories = $this->childrenHierarchy();

        $this->buildFlatTree($categories, $array);

        return $array;
    }

    public function getFlatCategoryTree()
    {

        $categories = $this->childrenHierarchy();

        $this->buildFlatCategoryTree($categories, $array);

        return $array;
    }

    private function buildFlatTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $category) {
            $array[$category['id']] = $this->formatLabel($category['level'], $category['name']);
            if(isset($category['__children'])) {
                $this->buildFlatTree($category['__children'], $array);
            }
        }
    }

    private function buildFlatCategoryTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $category) {
            $array[] = $this->find($category['id']);
            if(isset($category['__children'])) {
                $this->buildFlatCategoryTree($category['__children'], $array);
            }
        }
    }

    private function formatLabel($level, $value) {
        $level = $level - 1;
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . "$value";
    }


    public function getCategoryOptions(){

        $ret = array();
        $em = $this->_em;
        $categories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1),array('name'=>'asc'));

        foreach( $categories as $cat ){
            if( !$cat->getParent() ){
                continue;
            }
            $key = $cat->getParent()->getName();
            if(!array_key_exists($key, $ret) ){
                $ret[ $cat->getParent()->getName() ] = array();
            }
            $ret[ $cat->getParent()->getName() ][ $cat->getId() ] = $cat;
        }
        return $ret;
    }

    /**
     * @param $categories Category[]
     * @return array
     */
    public function buildCategoryGroup($categories)
    {
        $result = array();

        foreach($categories as $category) {

            $parentCategory = $this->getParentCategoryByLevel($category, 2);


            if(empty($parentCategory)) {
                continue;
            }

            $parentId = $parentCategory->getId();

            if(!isset($result[$parentId])) {
                $result[$parentId] = array(
                    'name' =>  $parentCategory->getName(),
                    'id' =>  $parentCategory->getId(),
                    '__children' =>  array(),
                );
            }

            $result[$parentId]['__children'][] = array(
                'name' => $category->getName(),
                'id' => $category->getId()
            );
        }

        return $result;
    }

    public function getCategoryOptionGroup()
    {
        $results = $this->createQueryBuilder('node')
            ->orderBy('node.level, node.name', 'ASC')
            ->where('node.level > 1')
            ->getQuery()
            ->getResult();

        $categories = $this->getCategoriesIndexedById($results);
        $grouped = array();
        foreach ($categories as $category) {
            switch($category->getLevel()) {
                case 2: break;
                default:
                    $grouped[$categories[$category->getParentIdByLevel(2)]->getName()][$category->getId()] = $category;
            }
        }
        return $grouped;
    }

    /**
     * @param Category $category
     * @param int $level
     * @return Category
     */
    public function getParentCategoryByLevel(Category $category, $level = 1)
    {
        return $this->find($category->getParentIdByLevel($level));
    }

    /**
     * @param $results
     * @return Category[]
     */
    protected function getCategoriesIndexedById($results)
    {
        $categories = array();

        foreach ($results as $category) {
            $categories[$category->getId()] = $category;
        }
        return $categories;
    }

    public function getUserCategoryOptionGroup(InventoryConfig $inventroy)
    {
        $grouped = array();

        if(!empty($inventroy->getItemTypeGrouping())){

            $qb = $this->createQueryBuilder('node');
            $orX = $qb->expr()->orX();

            $categories = $inventroy->getItemTypeGrouping()->getCategories();
            foreach($categories as $category){
                $orX->add("node.path like '" . $category->getId() . "/%'");
            }

            $results = $qb
                ->orderBy('node.level, node.name', 'ASC')
                ->where('node.level > 1')
                ->andWhere($orX)
                ->getQuery()
                ->getResult();

            $categories = $this->getCategoriesIndexedById($results);

            foreach ($categories as $category) {
                switch($category->getLevel()) {
                    case 2: break;
                    default:
                        $grouped[$categories[$category->getParentIdByLevel(2)]->getName()][$category->getId()] = $category;
                }
            }
            return $grouped;
        }

        return $grouped == null ? array() : $grouped;

    }

    public function getUseInventoryItemCategory(InventoryConfig $inventroy)
    {
        $arr =array();
        $array =array();
        if(!empty($inventroy->getItemTypeGrouping())){

            $categories = $inventroy->getItemTypeGrouping()->getCategories();
            foreach($categories as $category){
                $arr[] = array(
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'level' => $category->getLevel(),
                    '__children' => $this->childrenHierarchy($category)
                );
            }
            $this->buildFlatCategoryTree($arr , $array);
        }
        return $array == null ? array() : $array;

    }

    public function searchAutoComplete($inventory,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.masterProducts','m');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("m.inventoryConfig = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }
}
