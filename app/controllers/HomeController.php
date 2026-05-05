<?php

/**
 * Controller: Home
 * Public landing page.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $pageTitle = 'Mental Health & Wellness Portal';
        $this->view('home.index', compact('pageTitle'));
    }
}
