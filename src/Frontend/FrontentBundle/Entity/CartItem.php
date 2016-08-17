<?php

namespace Frontend\FrontentBundle\Entity;

use Product\Bundle\ProductBundle\Entity\Product;
use Sylius\Component\Cart\Model\CartItem as BaseCartItem;
use Sylius\Component\Order\Model\OrderItemInterface;
use Doctrine\ORM\Mapping as ORM;

class CartItem extends BaseCartItem
{

    private $product;

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function equals(OrderItemInterface $item)
    {
        return $this->product === $item->getProduct();
    }
}