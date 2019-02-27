<?php

namespace App\Helpers\Methods;

trait Accounts {
    public function accountsList() {
        return $this->apiCall('accounts', 'get');
    }

    public function accountGet($id) {
        return $this->apiCall('accounts', 'get', ['id' => $id]);
    }
}