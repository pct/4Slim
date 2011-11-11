<?php

#show
$app->get('/admin/sidebar', 'admin_sidebar');
function admin_sidebar() {
    global $app;

    admin_check_login();

    $settings = admin_get_setting_values();
    $app->render('admin/sidebar.html', array (
        'breadcrumb_title' => '側邊欄資料',
        'sidebar_block1'   => $settings['sidebar_block1'],
        'sidebar_block2'   => $settings['sidebar_block2'],
        'sidebar_block3'   => $settings['sidebar_block3'],
    ));
}

