<?php
require_once 'controllers/BaseController.php';

class HomeController extends BaseController {
    public function index() {
        $this->render('home/index', [
            'title' => 'Home - ' . SITE_NAME,
            'user' => $this->getCurrentUser()
        ]);
    }
} 