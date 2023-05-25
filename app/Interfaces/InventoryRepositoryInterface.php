<?php

namespace App\Interfaces;

interface InventoryRepositoryInterface 
{
    public function inventoryDetails($request);
    public function getProductById($productId);
    public function deleteProduct($productId);
    public function createProduct(array $productDetails);
    public function updateProduct($productId, array $newDetails);
    public function getFulfilledProducts();
}