<?php

namespace App\Repositories\product;

interface productInterface {
    
    public function all();

    public function store(array $data);

    public function getById($id);

    public function update(array $data, $id);

    
}