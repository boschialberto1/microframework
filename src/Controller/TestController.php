<?php

namespace App\Controller;

use App\HTTP\Curl;

class TestController
{

    public function index()
    {
        echo "Hello from TestController\n";
        // test CURL
        $curl = new Curl();
        try {
            $response = $curl->toArray()->get('https://jsonplaceholder.typicode.com/posts/1');
            var_dump($response);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

}