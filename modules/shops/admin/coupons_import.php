<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Datdevsx <datdevsx@gmail.com>
 * @Copyright (C) 2021 Vuji. All rights reserved
 * @Createdate 05/19/2021 19:00
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

if ($nv_Request->isset_request('import', 'post, get')) {
    $data = $nv_Request->get_title('data', 'post', '');

    $replace = ['&', '\'', '"', '<', '>', '\\', '/', '(', ')', '*', '[', ']', '!', '=', '%', '^', ':', '{', '}', '`', '~'];
    $search = ['&amp;', '&#039;', '&quot;', '&lt;', '&gt;', '&#x005C;', '&#x002F;', '&#40;', '&#41;', '&#42;', '&#91;', '&#93;', '&#33;', '&#x3D;', '&#x25;', '&#x5E;', '&#x3A;', '&#x7B;', '&#x7D;', '&#x60;', '&#x7E;'];

    $data = str_replace($search, $replace, $data);

    foreach (json_decode($data) as $d) {
        $row = [];
        $error = [];
        $row['title'] = $d->title;
        $row['code'] = $d->code;
        $row['type'] = $d->type;
        $row['discount'] = $d->discount;
        $row['total_amount'] = $d->total_amount;
        $row['product'] = $d->product;
        if (!empty($row['product'])) {
            $get_arr_products = explode(',', $row['product']);
            $get_products_id = [];
            foreach ($get_arr_products as $get_product) {
                $id = $db->query('SELECT id FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE ' . NV_LANG_DATA . '_title="' . $get_product . '"')->fetchColumn();
                array_push($get_products_id, $id);
            }
            $row['product'] = $get_products_id;
        }
        if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $d->date_start, $m)) {
            $_hour = 0;
            $_min = 0;
            $row['date_start'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
        } else {
            $row['date_start'] = 0;
        }
        if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $d->date_end, $m)) {
            $_hour = 23;
            $_min = 59;
            $_sec = 59;
            $row['date_end'] = mktime($_hour, $_min, $_sec, $m[2], $m[1], $m[3]);
        } else {
            $row['date_end'] = 0;
        }
        $row['uses_per_coupon'] = $d->uses_per_coupon;

        try {
            unset($exc, $insert_id);
            if (empty($row['id'])) {
                $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_coupons (
                    title, code, type, discount, total_amount, date_start, date_end, uses_per_coupon, date_added, status
                ) VALUES (
                    :title, :code, :type, :discount, :total_amount, :date_start, :date_end, :uses_per_coupon, ' . NV_CURRENTTIME . ', 1
                )';
                $data_insert = [];
                $data_insert['title'] = $row['title'];
                $data_insert['code'] = $row['code'];
                $data_insert['type'] = $row['type'];
                $data_insert['discount'] = $row['discount'];
                $data_insert['total_amount'] = $row['total_amount'];
                $data_insert['date_start'] = $row['date_start'];
                $data_insert['date_end'] = $row['date_end'];
                $data_insert['uses_per_coupon'] = $row['uses_per_coupon'];
                $insert_id = $db->insert_id($sql, 'id', $data_insert);
            }
            if (!empty($exc) or !empty($insert_id)) {
                // Them san pham vao bang product
                if (empty($row['id']) and !empty($row['product'])) {
                    foreach ($row['product'] as $pid) {
                        $db->query('INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_coupons_product VALUES( ' . $insert_id . ', ' . $pid . ')');
                    }
                } else {
                    if ($product_old != $row['product']) {
                        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons_product WHERE cid=' . $row['id']);
                        foreach ($row['product'] as $pid) {
                            if (!empty($pid)) {
                                $db->query('INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_coupons_product VALUES(' . $row['id'] . ', ' . $pid . ')');
                            }
                        }
                    }
                }
                $nv_Cache->delMod($module_name);
            }
        } catch (PDOException $e) {
            trigger_error(print_r($e, true));
        }
    }
} else {
    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    $page_title = 'Thêm danh sách mã giảm giá';

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}
