<?php

# settings  show
$app->get('/admin/settings', 'admin_settings');
function admin_settings() {
    global $app;

    admin_check_login();

    $settings = admin_get_setting_values();
    $app->render('admin/settings.html', array (
        'breadcrumb_title' => '網站設定',
        'site_name'        => $settings['site_name'],
        'site_footer'      => $settings['site_footer'],
    ));
}

# settings update
$app->put('/admin/settings', 'admin_setting_do_update');
function admin_setting_do_update() {
    global $app;

    admin_check_login();

    $put = $app->request()->put();
    admin_update_values($put);
    
    $app->render('admin/_notice.html', array (
        'class' => 'success',
        'msg'   => '更新成功',
    ));
}

# password show
$app->get('/admin/password', 'admin_password');
function admin_password() {
    global $app;

    admin_check_login();

    $settings = admin_get_setting_values();
    $app->render('admin/password.html', array (
        'breadcrumb_title' => '管理者密碼設定',
    ));
}

# phone show
$app->get('/admin/phone', 'admin_phone');
function admin_phone() {
    global $app;

    admin_check_login();

    $settings = admin_get_setting_values();
    $app->render('admin/phone.html', array (
        'breadcrumb_title' => '網站電話設定',
    ));
}

# email show
$app->get('/admin/email', 'admin_email');
function admin_email() {
    global $app;

    admin_check_login();

    $settings = admin_get_setting_values();
    $app->render('admin/email.html', array (
        'breadcrumb_title' => '網站電話設定',
    ));
}

