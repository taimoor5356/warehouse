<?php

namespace App\Repositories\customer;

use Illuminate\Http\Request;

Interface CustomerInterface
{
    //
    public function index(Request $request);
    public function create();
    public function store(Request $request);
    public function edit($id);
    public function update(Request $request, $id);
    public function getCustBrandProd(Request $request);
    public function brandProducts(Request $request);
    public function getCustBrandProds(Request $request);
    public function getCustBrandRemainingProds(Request $request);
    public function customerProducts(Request $request, $id);
    public function showAll(Request $request, $id);
    public function showAllProducts(Request $request, $id);
    public function editCustomerBrand(Request $request);
    public function show($id);
    public function editCustomerDetails($id);
    public function saveCustomerProduct(Request $request, $id);
    public function saveCustomerBrandProduct(Request $request);
    public function customerBrands(Request $request, $id);
    public function getCustomerBrand($id);
    public function getAllBrands();
    public function getServiceCharges($id);
    public function trash(Request $request);
    public function restore($id);
    public function permanentlyDeleteCustomer($id);
    public function createCustomerBrand($id);
    public function storeCustomerBrand($id, Request $request);
    public function editCustomerSingleBrand(Request $request, $customerId, $brandId);
    public function updateCustomerBrand($customerId, $brandId, Request $request);
    public function customerBrandLabelsHistory($customerId, $brandId, Request $request);
    public function CustomerBrandTrash($customerId, Request $request);
    public function getCustomerCharges($id);
    public function setLabelStatus(Request $request);
    public function setSellerCostStatus(Request $request);
    public function updateCustomerProdSellingPrice(Request $request);
    public function deleteCustomerProd($id, $prod_id);
    public function deleteCustomerBrandProduct($c_id, $b_id, $p_id);
    public function customerTrashedProducts(Request $request, $id);
    public function restoreCustomerTrashedProduct(Request $request, $c_id, $b_id, $p_id);
    public function add_labels(Request $request);
    public function addCustomerLabel(Request $request, $id);
    public function addBrandProducts(Request $request, $id);
    public function getProductsCustomers(Request $request, $id);
}
