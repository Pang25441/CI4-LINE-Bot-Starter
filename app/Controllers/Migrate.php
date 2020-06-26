<?php

namespace App\Controllers;

class Migrate extends BaseController
{
    public function index()
    {
        $migrate = \Config\Services::migrations();

        try {
            $migrate->latest();
            // $migrate->regress(2);
        } catch (\Exception $e) {
            // Do something with the error here...
            echo $e->getMessage();
        }
    }
}
