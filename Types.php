<?php

use RedBeanPHP\OODBBean;

class Remember extends OODBBean {
    public $user;
    public $expires;
    public $key;
}

class AppBlade extends Jenssegers\Blade\Blade {
    public function make(string $view, array $data = [], array $mergeData = []) {}
}

class User extends OODBBean {
    public $id;
    public $name;
    public $email;
    public $forgot_token;
    public $verified_email;
    public $created;
    public $role;
    public $password;
}

class Post extends OODBBean {
    public $title;
    public $slug;
    public $author;
    public $content;
    public $deleted;
    public $draft;
    public $date;
}

class Subscriber extends OODBBean {
    public $subscribed;
    public $hash_id;
}
