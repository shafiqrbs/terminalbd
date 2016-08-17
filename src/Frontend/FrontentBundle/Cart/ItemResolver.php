<?php
namespace Frontend\FrontentBundle\Cart;

use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolverInterface;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Cart\Resolver\ItemResolvingException;

class ItemResolver implements ItemResolverInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function resolve(CartItemInterface $item, $request)
    {
        $productId = $request->query->get('productId');

        // If no product id given, or product not found, we throw exception with nice message.
        if (!$productId || !$product = $this->getProductRepository()->find($productId)) {
            throw new ItemResolvingException('Requested product was not found');
        }

        // Assign the product to the item and define the unit price.
        $item->setProduct($product);
        $item->setUnitPrice((int)($product->getPrice() * 100));

        // Everything went fine, return the item.
        return $item;
    }

    private function getProductRepository()
    {
        return $this->entityManager->getRepository('ProductProductBundle:Product');
    }
}