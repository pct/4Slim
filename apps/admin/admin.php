<?php

require 'lib/admin.php';
require 'lib/upload.php';
require 'admin_settings.php';
require 'admin_sites.php';
require 'admin_items.php';
require 'admin_sidebar.php';

$app->get('/admin', 'admin');
$app->get('/admin/', 'admin');
$app->get('/admin/home', 'admin');
function admin() {
    global $app;

    admin_check_login();

    $app->render('admin/home.html', array (
        'breadcrumb_title' => '管理首頁'
    ));
}

$app->get('/admin/login', 'admin_login');
$app->get('/admin/login/', 'admin_login');
function admin_login() {
    global $app;

    self_logout();

    $app->render('admin/login.html', array (
        'breadcrumb_title' => '登入'
    ));
}

$app->get('/admin/logout', 'admin_logout');
$app->get('/admin/logout/', 'admin_logout');
function admin_logout() {
    global $app;

    self_logout();

    $app->render('admin/logout.html', array (
        'breadcrumb_title' => '登出'
    ));
}

$app->post('/admin/login', 'admin_do_login');
function admin_do_login() {
    global $app;

    $data = array(
        'class' => 'error',
        'msg'   => '登入失敗，請重試！',
    );

    $post = $app->request()->post();

    if ($post['admin_username'] && $post['admin_password']) {
        if ($post['admin_username'] === ADMIN_USERNAME) {
            $ret = ORM::for_table('settings')->where('item_key', 'admin_password')->find_one();
            if (md5($post['admin_password']) === $ret->item_value) {
                $_SESSION['admin_login'] = True;
                $data = array(
                    'class' => 'success',
                    'msg'   => '登入成功！',
                    'link'  => 'admin/home',
                    'link_msg' => '請點此到管理首頁'
                );
            }
        }
    }
    
    $app->render('admin/_notice.html', $data);
}
