<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 13 Aug 2014 00:24:32 GMT
 */
if (!defined('NV_IS_MOD_BOOKHOUSE')) die('Stop!!!');

$page_title = $lang_module['search'];
$key_words = $module_info['keywords'];

$array_data = array();
$array_search = array(
    'q' => '',
    'catid' => 0,
    'price_from' => 0,
    'price_to' => 0,
    'area_from' => 0,
    'area_to' => 0,
    'size_v' => 0,
    'size_h' => 0,
    'way' => 0,
    'provinceid' => 0,
    'districtid' => 0,
    'wardid' => 0,
    'typeid' => 0
);
$where = $generate_page = '';
$is_search = 0;

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=search';
$base_url_rewrite = $request_uri = urldecode($_SERVER['REQUEST_URI']);
$array_mod_title[] = array(
    'title' => $lang_module['search'],
    'link' => $base_url
);

if ($nv_Request->isset_request('search', 'get')) {
    $is_search = 1;
    $page = $nv_Request->get_int('page', 'get', 1);
    $num_items = 0;
    
    $array_search = array(
        'q' => $nv_Request->get_title('q', 'get', ''),
        'catid' => $nv_Request->get_int('catid', 'get', 0),
        'price_from' => preg_replace('/[^0-9]/', '', $nv_Request->get_title('price_from', 'get', '')),
        'price_to' => preg_replace('/[^0-9]/', '', $nv_Request->get_title('price_to', 'get', '')),
        'price_spread' => $nv_Request->get_title('price_spread', 'post,get', ''),
        'area_from' => preg_replace('/[^0-9]/', '', $nv_Request->get_title('area_from', 'get', '')),
        'area_to' => preg_replace('/[^0-9]/', '', $nv_Request->get_title('area_to', 'get', '')),
        'size_v' => str_replace(',', '.', $nv_Request->get_title('size_v', 'get', '')),
        'size_h' => str_replace(',', '.', $nv_Request->get_title('size_h', 'get', '')),
        'way' => $nv_Request->get_int('way', 'get', 0),
        'provinceid' => $nv_Request->get_int('provinceid', 'get', 0),
        'districtid' => $nv_Request->get_int('districtid', 'get', 0),
        'wardid' => $nv_Request->get_int('wardid', 'get', 0),
        'typeid' => $nv_Request->get_int('typeid', 'get', 0)
    );
    
    if (!empty($array_search['q'])) {
        $where .= ' AND title like "%' . $array_search['q'] . '%" OR hometext like "%' . $array_search['q'] . '%" OR bodytext like "%' . $array_search['q'] . '%" OR code like "%' . $array_search['q'] . '%"';
        $base_url .= '&q=' . $array_search['q'];
    }
    
    if (!empty($array_search['catid'])) {
        $where .= ' AND catid = ' . $array_search['catid'];
        $base_url .= '&catid=' . $array_search['catid'];
    } else {
        $base_url_rewrite = str_replace('&catid=' . $array_search['catid'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['price_from']) or !empty($array_search['price_to'])) {
        $where .= ' AND showprice = 1';
    }
    
    if (!empty($array_search['price_from'])) {
        if ($array_config['pricetype'] == 1) {
            $array_search['price_from'] = $array_search['price_from'] . '000000';
        }
        $where .= ' AND price >= ' . doubleval($array_search['price_from']);
        $base_url .= '&price_from=' . $array_search['price_from'];
    } else {
        $base_url_rewrite = str_replace('&price_from=' . $array_search['price_from'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['price_to'])) {
        if ($array_config['pricetype'] == 1) {
            $array_search['price_to'] = $array_search['price_to'] . '000000';
        }
        $where .= ' AND price <= ' . doubleval($array_search['price_to']);
        $base_url .= '&price_to=' . $array_search['price_to'];
    } else {
        $base_url_rewrite = str_replace('&price_to=' . $array_search['price_to'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['price_spread'])) {
        $base_url .= 'price_spread=' . $array_search['price_spread'];
        
        $price_spread = explode('-', $array_search['price_spread']);
        if (sizeof($price_spread) == 2) {
            if (!empty($price_spread[0]) and !empty($price_spread[1])) {
                $where .= ' AND (price >= ' . $price_spread[0] . ' AND price <= ' . $price_spread[1] . ')';
            } elseif (!empty($price_spread[0]) and empty($price_spread[1])) {
                $where .= ' AND price >= ' . $price_spread[0];
            }
        }
    } else {
        $base_url_rewrite = str_replace('&price_spread=' . $array_search['price_spread'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['area_from'])) {
        $where .= ' AND area >= ' . doubleval($array_search['area_from']);
        $base_url .= 'area_from=' . $array_search['area_from'];
    } else {
        $base_url_rewrite = str_replace('&area_from=' . $array_search['area_from'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['area_to'])) {
        $where .= ' AND area <= ' . doubleval($array_search['area_to']);
        $base_url .= 'area_to=' . $array_search['area_to'];
    } else {
        $base_url_rewrite = str_replace('&area_to=' . $array_search['area_to'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['size_v'])) {
        $where .= ' AND size_v = ' . doubleval($array_search['size_v']);
        $base_url .= 'size_v=' . $array_search['size_v'];
    } else {
        $base_url_rewrite = str_replace('&size_v=' . $array_search['size_v'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['size_h'])) {
        $where .= ' AND size_h = ' . doubleval($array_search['size_h']);
        $base_url .= 'size_h=' . $array_search['size_h'];
    } else {
        $base_url_rewrite = str_replace('&size_h=' . $array_search['size_h'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['way'])) {
        $where .= ' AND way_id=' . $array_search['way'];
        $base_url .= 'way=' . $array_search['way'];
    } else {
        $base_url_rewrite = str_replace('&way=' . $array_search['way'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['provinceid'])) {
        $where .= ' AND provinceid = ' . $array_search['provinceid'];
        $base_url .= '&provinceid=' . $array_search['provinceid'];
    } else {
        $base_url_rewrite = str_replace('&provinceid=' . $array_search['provinceid'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['districtid'])) {
        $where .= ' AND districtid = ' . $array_search['districtid'];
        $base_url .= '&districtid=' . $array_search['districtid'];
    } else {
        $base_url_rewrite = str_replace('&districtid=' . $array_search['districtid'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['wardid'])) {
        $where .= ' AND wardid = ' . $array_search['wardid'];
        $base_url .= '&wardid=' . $array_search['wardid'];
    } else {
        $base_url_rewrite = str_replace('&wardid=' . $array_search['wardid'], '', $base_url_rewrite);
    }
    
    if (!empty($array_search['typeid'])) {
        $where .= ' AND typeid = ' . $array_search['typeid'];
        $base_url .= '&typeid=' . $array_search['typeid'];
    } else {
        $base_url_rewrite = str_replace('&typeid=' . $array_search['typeid'], '', $base_url_rewrite);
    }
    
    $base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);
    if ($request_uri != $base_url_rewrite and NV_MAIN_DOMAIN . $request_uri != $base_url_rewrite) {
        header('Location: ' . $base_url_rewrite);
        die();
    }
    
    $location = new Location();
    
    if (!empty($where)) {
        $db->sqlreset()
            ->select('COUNT(*) ')
            ->from(NV_PREFIXLANG . '_' . $module_data)
            ->where('status=1 AND status_admin=1 AND is_queue=0 AND inhome=1 AND (exptime > ' . NV_CURRENTTIME . ' OR exptime = 0) ' . $where);
        
        $num_items = $db->query($db->sql())
            ->fetchColumn();
        
        $db->select('id, catid, title, alias, code, area, size_v, size_h, price, addtime, admin_id, provinceid, districtid, wardid, way_id, address, structure, price_time, bodytext, homeimgfile, homeimgthumb, showprice, group_config')
            ->order('ordertime DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);
        
        $result = $db->query($db->sql());
        while ($item = $result->fetch()) {
            $array_data[] = nv_data_show($item);
        }
        
        $generate_page = '';
        if ($num_items > $per_page) {
            $url_link = $_SERVER['REQUEST_URI'];
            if (strpos($url_link, '&page=') > 0) {
                $url_link = substr($url_link, 0, strpos($url_link, '&page='));
            } elseif (strpos($url_link, '?page=') > 0) {
                $url_link = substr($url_link, 0, strpos($url_link, '?page='));
            }
            $_array_url = array(
                'link' => $url_link,
                'amp' => '&page='
            );
            $generate_page = nv_generate_page($_array_url, $num_items, $per_page, $page);
        }
    
    }
    $lang_module['search_result'] = sprintf($lang_module['search_result'], $num_items);
}

$contents = nv_theme_bookhouse_search($array_data, $is_search, $generate_page);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';