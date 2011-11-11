<?php

#list
$app->get('/admin/items', 'admin_items');
function admin_items() {
    global $app;

    admin_check_login();

    $items = ORM::for_table('items')->find_many();

    $app->render('admin/items.html', array (
        'breadcrumb_title' => '項目列表',
        'items' => $items,
    ));
}

#create
$app->get('/admin/item/new', 'admin_item_new');
function admin_item_new() {
    global $app;

    admin_check_login();

    $app->render('admin/item_new.html', array (
        'breadcrumb_title' => '新增項目'
    ));
}

#edit
$app->get('/admin/item/:id/edit', 'admin_item_edit');
function admin_item_edit($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);

    $app->render('admin/item_edit.html', array (
        'breadcrumb_title' => '編輯項目',
        'item' => $item
    ));
}

#delete
$app->get('/admin/item/:id/delete', 'admin_item_delete');
function admin_item_delete($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);

    $app->render('admin/item_delete.html', array (
        'breadcrumb_title' => '刪除項目',
        'item' => $item
    ));
}

# do create
$app->post('/admin/item', 'admin_item_do_create');
function admin_item_do_create() {
    global $app;
    
    admin_check_login();

    $post = $app->request()->post();
    $item = ORM::for_table('items')->create();
    $item->item_name        = $post['item_name'];
    $item->home_long_desc   = $post['home_long_desc'];
    $item->home_short_desc  = $post['home_short_desc'];
    $item->detail_intro     = $post['detail_intro'];
    $item->detail_desc      = $post['detail_desc'];
    $item->item_type        = $post['item_type'];
    $item->at_home          = $post['at_home'];

    if($post['home_big_image']) {
        $item->home_big_image = $post['home_big_image'];
    }

    if($post['home_small_image']) {
        $item->home_small_image = $post['home_small_image'];
    }

    $ret = $item->save();
    
    if ($ret) {
        $data = array(
            'class' => 'success',
            'msg'   => '建立成功',
            'link'  => 'admin/items',
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
$app->put('/admin/item/:id', 'admin_item_do_update');
function admin_item_do_update($id) {
    global $app;

    admin_check_login();

    $put = $app->request()->put();
    $item = ORM::for_table('items')->find_one($id);
    $item->item_name        = $put['item_name'];
    $item->home_long_desc   = $put['home_long_desc'];
    $item->home_short_desc  = $put['home_short_desc'];
    $item->detail_intro     = $put['detail_intro'];
    $item->detail_desc      = $put['detail_desc'];
    $item->item_type        = $put['item_type'];
    $item->at_home          = $put['at_home'];

    if($put['home_big_image']) {
        $item->home_big_image = $put['home_big_image'];
    }

    if($put['home_small_image']) {
        $item->home_small_image = $put['home_small_image'];
    }

    $ret = $item->save();
    
    if ($ret) {
        $data = array(
            'class'    => 'success',
            'msg'      => '更新成功',
            'link'     => 'admin/items',
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
$app->delete('/admin/item/:id', 'admin_item_do_delete');
function admin_item_do_delete($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);
    $ret = $item->delete();
    
    if ($ret) {
        $data = array(
            'class' => 'success',
            'msg'   => '刪除成功',
            'link'  => 'admin/items',
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

# do upload
$app->post('/admin/item/upload', 'admin_item_do_upload');
function admin_item_do_upload() {
    global $app;

    admin_check_login();

    $allowed_extensions = array('jpg', 'png', 'jpeg', 'gif');
    $size_limit = 2 * 1024 * 1024;

    $uploader = new qqFileUploader($allowed_extensions, $size_limit);
    $result = $uploader->handleUpload(PROJECT_PATH . '/uploads/');
    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
}



# item hotels
$app->get('/admin/item/:id/hotels', 'admin_item_hotels');
function admin_item_hotels($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);
    $items = ORM::for_table('items')
        ->where_not_equal('item_id', $id)
        ->where('item_type', 'hotel')
        ->find_many();

    $app->render('admin/item_hotels.html', array (
        'breadcrumb_title' => '相關飯店',
        'orig_item'        => $item,
        'items'            => $items,
    ));
}

$app->put('/admin/item/:id/hotels', 'admin_item_do_update_rel_hotels');
function admin_item_do_update_rel_hotels($id) {
    global $app;

    admin_check_login();

    $put = $app->request()->put();
    $item = ORM::for_table('items')->find_one($id);
    if ($item) {
        ORM::for_table('item_hotel_rels')->where('item_id', $id)->delete_many();
        if (isset($put['rel_hotels']) && $put['rel_hotels']) {
            foreach ($put['rel_hotels'] as $hotel_id) {
                $item_rel = ORM::for_table('item_hotel_rels')->create();
                $item_rel->item_id = $id;
                $item_rel->item_hotel_id = $hotel_id;
                $item_rel->save();
            }
        }
    }
    
    $data = array(
        'class'    => 'success',
        'msg'      => '更新成功',
        'link'     => 'admin/items',
        'link_msg' => '到列表查看',
    );

    $app->render('admin/_notice.html', $data);
}

# item sights
$app->get('/admin/item/:id/sights', 'admin_item_sights');
function admin_item_sights($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);
    $items = ORM::for_table('items')
        ->where_not_equal('item_id', $id)
        ->where('item_type', 'sight')
        ->find_many();

    $app->render('admin/item_sights.html', array (
        'breadcrumb_title' => '相關景點',
        'orig_item'        => $item,
        'items'            => $items,
    ));
}

$app->put('/admin/item/:id/sights', 'admin_item_do_update_rel_sights');
function admin_item_do_update_rel_sights($id) {
    global $app;

    admin_check_login();

    $put = $app->request()->put();
    $item = ORM::for_table('items')->find_one($id);
    if ($item) {
        ORM::for_table('item_sight_rels')->where('item_id', $id)->delete_many();
        if (isset($put['rel_sights']) && $put['rel_sights']) {
            foreach ($put['rel_sights'] as $sight_id) {
                $item_rel = ORM::for_table('item_sight_rels')->create();
                $item_rel->item_id = $id;
                $item_rel->item_sight_id = $sight_id;
                $item_rel->save();
            }
        }
    }
    
    $data = array(
        'class'    => 'success',
        'msg'      => '更新成功',
        'link'     => 'admin/items',
        'link_msg' => '到列表查看',
    );

    $app->render('admin/_notice.html', $data);
}





# item slides 
$app->get('/admin/item/:id/slides', 'admin_item_slides');
function admin_item_slides($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);
    $item_slides = ORM::for_table('item_metas')
        ->where('item_id', $id)
        ->where('item_meta_type', 'image')
        ->find_many();

    $app->render('admin/item_slides.html', array (
        'breadcrumb_title' => '內頁輪播圖',
        'item'             => $item,
        'item_slides'      => $item_slides,
    ));
}

$app->get('/admin/item/:id/slide/new', 'admin_item_slide_new');
function admin_item_slide_new($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);

    $app->render('admin/item_slide_new.html', array (
        'breadcrumb_title' => '新增輪播圖',
        'item'             => $item,
    ));
}

$app->get('/admin/item/:id/slide/:meta_id/delete', 'admin_item_slide_delete');
function admin_item_slide_delete($id, $meta_id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);
    $item_meta = ORM::for_table('item_metas')->find_one($meta_id);

    $app->render('admin/item_slide_delete.html', array (
        'breadcrumb_title' => '刪除輪播圖',
        'item'             => $item,
        'item_meta'        => $item_meta,
    ));
}

# do delete
$app->delete('/admin/item/:id/slide/:meta_id', 'admin_item_slide_do_delete');
function admin_item_slide_do_delete($id, $meta_id) {
    global $app;

    admin_check_login();

    $item_meta = ORM::for_table('item_metas')->find_one($meta_id);

    if ($item_meta) {
        $item_meta->delete();
        $data = array(
            'class' => 'success',
            'msg'   => '刪除成功',
            'link'  => 'admin/item/'.$id.'/slides',
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

$app->post('/admin/item/:id/slide/upload', 'admin_item_slide_do_upload');
function admin_item_slide_do_upload($id) {
    global $app;

    admin_check_login();

    $allowed_extensions = array('jpg', 'png', 'jpeg', 'gif');
    $size_limit = 2 * 1024 * 1024;

    $uploader = new qqFileUploader($allowed_extensions, $size_limit);
    $result = $uploader->handleUpload(PROJECT_PATH . '/uploads/');

    if (isset($result['filename'])) {
        $ret = ORM::for_table('item_metas')->create();
        $ret->item_id = $id;
        $ret->item_meta_path = $result['filename'];
        $ret->item_meta_type = 'image';
        $ret->save();
    }

    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
}


# item doc
$app->get('/admin/item/:id/doc', 'admin_item_doc');
function admin_item_doc($id) {
    global $app;

    admin_check_login();

    $item = ORM::for_table('items')->find_one($id);

    $app->render('admin/item_doc.html', array (
        'breadcrumb_title' => '上傳文件',
        'item'             => $item,
    ));
}

$app->post('/admin/item/:id/doc/upload', 'admin_item_doc_do_upload');
function admin_item_doc_do_upload($id) {
    global $app;

    admin_check_login();

    $item_meta_doc = ORM::for_table('item_metas')
        ->where('item_id', $id)
        ->where('item_meta_type', 'doc')
        ->find_one();

    $allowed_extensions = array('doc', 'docx', 'pdf');
    $size_limit = 2 * 1024 * 1024;

    $uploader = new qqFileUploader($allowed_extensions, $size_limit);
    $result = $uploader->handleUpload(PROJECT_PATH . '/upload_docs/');

    if (isset($result['filename'])) {
        if ($item_meta_doc) {
            $ret = $item_meta_doc;
        } else {
            $ret = ORM::for_table('item_metas')->create();
        }
        $ret->item_id = $id;
        $ret->item_meta_path = $result['filename'];
        $ret->item_meta_type = 'doc';
        $ret->save();
    }

    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
}

