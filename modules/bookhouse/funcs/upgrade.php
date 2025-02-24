<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 13 Aug 2014 00:24:32 GMT
 */
if (!defined('NV_IS_MOD_BOOKHOUSE')) die('Stop!!!');

if (empty($array_config['payport'])) {
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'], 404);
}

$array_data = array();

if (empty($array_op)) {
    $mod = 'upgrade_group';
    $page_title = $lang_module['payment_upgrade_group'];
    $array_mod_title[] = array(
        'title' => $page_title
    );
    $array_data['id'] = 0;
} elseif (isset($array_op[1])) {
    $mod = 'group';
    if (preg_match('/^([a-z0-9\-]+)\-([0-9]+)$/i', $array_op[1], $m)) {
        $id = $m[2];
        $array_data = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id)->fetch();
        $array_data['cat'] = $array_cat[$array_data['catid']]['title'];
    }
    $page_title = $lang_module['payment_group'] . ' ' . $array_data['title'];
    $array_mod_title[] = array(
        'title' => $lang_module['payment_group']
    );
}

$content = $db->query('SELECT econtent FROM ' . NV_PREFIXLANG . '_' . $module_data . '_econtent WHERE action="' . $mod . '"')->fetchColumn();

$contents = nv_theme_upgrade($mod, $array_data, $content);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';