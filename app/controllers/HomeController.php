<?php

class HomeController extends Controller
{
    public function index()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect($this->config['app']['base_url'] . '?controller=dashboard&action=index');
        }
        
        $this->view->render('home/index');
    }
}
