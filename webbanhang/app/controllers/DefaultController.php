<?php

class DefaultController
{
    public function index()
    {
        header('Location: /webbanhang/Product');
        exit;
    }
}