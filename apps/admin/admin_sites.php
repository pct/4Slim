<?php

#list
$app->get('/admin/sites', 'admin_sites');
function admin_sites() {
    global $app;

    admin_check_login();

    $sites = ORM::for_table('sites')->find_many();

    $app->render('admin/sites.html', array (
        'breadcrumb_title' => '系列網站列表',
        'sites' => $sites,
    ));
}

#create
$app->get('/admin/site/new', 'admin_site_new');
function admin_site_new() {
    global $app;

    admin_check_login();

    $app->render('admin/site_new.html', array (
        'breadcrumb_title' => '新增系列網站'
    ));
}

#edit
$app->get('/admin/site/:id/edit', 'admin_site_edit');
function admin_site_edit($id) {
    global $app;

    admin_check_login();

    $site = ORM::for_table('sites')->find_one($id);

    $app->render('admin/site_edit.html', array (
        'breadcrumb_title' => '編輯系列網站',
        'site' => $site
    ));
}

#delete
$app->get('/admin/site/:id/delete', 'admin_site_delete');
function admin_site_delete($id) {
    global $app;

    admin_check_login();

    $site = ORM::for_table('sites')->find_one($id);

    $app->render('admin/site_delete.html', array (
        'breadcrumb_title' => '刪除系列網站',
        'site' => $site
    ));
}

# do create
$app->post('/admin/site', 'admin_site_do_create');
function admin_site_do_create() {
    global $app;
    
    admin_check_login();

    $post = $app->request()->post();
    $site = ORM::for_table('sites')->create();
    $site->site_name = $post['site_name'];
    $site->site_url  = $post['site_url'];

    $ret = $site->save();
    
    if ($ret) {
        $data = array(
            'class' => 'success',
            'msg'   => '建立成功',
            'link'  => 'admin/sites',
            'link_msg' => '到列表查看',
        );
    } else {
        $data = array(
            'class' => 'error',
            'msg'   => '建立失敗，請重試！',
        );
    }

    $app->render('admin/_notice.html', $data);
}

# do update
$app->put('/admin/site/:id', 'admin_site_do_update');
function admin_site_do_update($id) {
    global $app;

    admin_check_login();

    $put = $app->request()->put();
    $site = ORM::for_table('sites')->find_one($id);
    $site->site_name = $put['site_name'];
    $site->site_url  = $put['site_url'];
    
    $ret = $site->save();
    
    if ($ret) {
        $data = array(
            'class'    => 'success',
            'msg'      => '更新成功',
            'link'     => 'admin/sites',
            'link_msg' => '到列表查看',
        );
    } else {
        $data = array(
            'class' => 'error',
            'msg'   => '更新失敗，請重試！',
        );
    }

    $app->render('admin/_notice.html', $data);
}

# do delete
$app->delete('/admin/site/:id', 'admin_site_do_delete');
function admin_site_do_delete($id) {
    global $app;

    admin_check_login();

    $site = ORM::for_table('sites')->find_one($id);
    $ret = $site->delete();
    
    if ($ret) {
        $data = array(
            'class' => 'success',
            'msg'   => '刪除成功',
            'link'  => 'admin/sites',
            'link_msg' => '到列表查看',
        );
    } else {
        $data = array(
            'class' => 'error',
            'msg'   => '刪除失敗，請重試！',
        );
    }

    $app->render('admin/_notice.html', $data);
}

