<?php

$sql = rex_sql::factory();
$sql->setQuery('SET FOREIGN_KEY_CHECKS = 0');

try {
    rex_sql_table::get('rex_user_passkey')
        ->ensureColumn(new rex_sql_column('id', 'varchar(255)', false, null, null))
        ->ensureColumn(new rex_sql_column('user_id', 'int(10) unsigned', false, null, null))
        ->ensureColumn(new rex_sql_column('public_key', 'text', false, null, null))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime', false, null, null))
        ->setPrimaryKey(['id'])
        ->ensureIndex(new rex_sql_index('rex_user_passkey_user_id', ['user_id']))
        ->ensureForeignKey(new rex_sql_foreign_key('rex_user_passkey_user_id', 'rex_user', ['user_id' => 'id'], rex_sql_foreign_key::CASCADE, rex_sql_foreign_key::CASCADE))
        ->ensure();

    rex_sql_table::get('rex_user_session')
        ->ensureColumn(new rex_sql_column('session_id', 'varchar(255)', false, null, null))
        ->ensureColumn(new rex_sql_column('user_id', 'int(10) unsigned', false, null, null))
        ->ensureColumn(new rex_sql_column('cookie_key', 'varchar(255)', true, null, null))
        ->ensureColumn(new rex_sql_column('passkey_id', 'varchar(255)', true, null, null))
        ->ensureColumn(new rex_sql_column('ip', 'varchar(39)', false, null, null))
        ->ensureColumn(new rex_sql_column('useragent', 'varchar(255)', false, null, null))
        ->ensureColumn(new rex_sql_column('starttime', 'datetime', false, null, null))
        ->ensureColumn(new rex_sql_column('last_activity', 'datetime', false, null, null))
        ->setPrimaryKey(['session_id'])
        ->ensureIndex(new rex_sql_index('cookie_key', ['cookie_key'], rex_sql_index::UNIQUE))
        ->ensureIndex(new rex_sql_index('rex_user_session_user_id', ['user_id']))
        ->ensureIndex(new rex_sql_index('rex_user_session_passkey_id', ['passkey_id']))
        ->ensureForeignKey(new rex_sql_foreign_key('rex_user_session_passkey_id', 'rex_user_passkey', ['passkey_id' => 'id'], rex_sql_foreign_key::CASCADE, rex_sql_foreign_key::CASCADE))
        ->ensureForeignKey(new rex_sql_foreign_key('rex_user_session_user_id', 'rex_user', ['user_id' => 'id'], rex_sql_foreign_key::CASCADE, rex_sql_foreign_key::CASCADE))
        ->ensure();

    rex_sql_table::get('rex_article')
        ->removeIndex('id')
        ->alter();

    rex_sql_table::get('rex_article_slice')
        ->removeIndex('clang_id')
        ->removeIndex('article_id')
        ->alter();

    rex_sql_table::get('rex_media_manager_type')
        ->ensureColumn(new rex_sql_column('status', 'tinyint(1) unsigned', false, '0', null))
        ->alter();

    rex_sql_table::get('rex_user')
        ->ensureColumn(new rex_sql_column('password', 'varchar(255)', true, null, null))
        ->removeColumn('cookiekey')
        ->alter();

    $sql->setQuery(<<<'SQL'
        INSERT INTO `rex_config` (`namespace`, `key`, `value`)
        VALUES
            ('core', 'package-config', '{\"adminer\":{\"install\":true,\"status\":true},\"backup\":{\"install\":true,\"status\":true},\"be_style\":{\"install\":true,\"status\":true,\"plugins\":{\"customizer\":{\"install\":false,\"status\":false},\"redaxo\":{\"install\":true,\"status\":true}}},\"cronjob\":{\"install\":false,\"status\":false,\"plugins\":{\"article_status\":{\"install\":false,\"status\":false},\"optimize_tables\":{\"install\":false,\"status\":false}}},\"debug\":{\"install\":false,\"status\":false},\"developer\":{\"install\":true,\"status\":true},\"install\":{\"install\":true,\"status\":true},\"media_manager\":{\"install\":true,\"status\":true},\"mediapool\":{\"install\":true,\"status\":true},\"metainfo\":{\"install\":true,\"status\":true},\"phpmailer\":{\"install\":false,\"status\":false},\"project\":{\"install\":true,\"status\":true},\"structure\":{\"install\":true,\"status\":true,\"plugins\":{\"content\":{\"install\":true,\"status\":true},\"history\":{\"install\":false,\"status\":false},\"version\":{\"install\":false,\"status\":false}}},\"users\":{\"install\":true,\"status\":true},\"ydeploy\":{\"install\":true,\"status\":true},\"yform\":{\"install\":true,\"status\":true,\"plugins\":{\"email\":{\"install\":true,\"status\":true},\"manager\":{\"install\":true,\"status\":true},\"rest\":{\"install\":false,\"status\":false},\"tools\":{\"install\":false,\"status\":false}}}}'),
            ('core', 'package-order', '[\"be_style\",\"be_style\\/redaxo\",\"users\",\"adminer\",\"backup\",\"developer\",\"install\",\"media_manager\",\"mediapool\",\"structure\",\"metainfo\",\"structure\\/content\",\"ydeploy\",\"yform\",\"yform\\/email\",\"yform\\/manager\",\"project\"]'),
            ('core', 'version', '\"5.18.3\"')
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
        SQL);

    $sql->setQuery(<<<'SQL'
        INSERT INTO `rex_media_manager_type` (`id`, `status`, `name`, `description`, `createdate`, `createuser`, `updatedate`, `updateuser`)
        VALUES
            (1, 1, 'rex_media_small', '200 × 200 px', '2025-03-28 10:09:51', 'admin', '2025-03-28 10:09:51', 'admin'),
            (2, 1, 'rex_media_medium', '600 × 600 px', '2025-03-28 10:09:51', 'admin', '2025-03-28 10:09:51', 'admin'),
            (3, 1, 'rex_media_large', '1200 × 1200 px', '2025-03-28 10:09:51', 'admin', '2025-03-28 10:09:51', 'admin')
        ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `name` = VALUES(`name`), `description` = VALUES(`description`), `createdate` = VALUES(`createdate`), `createuser` = VALUES(`createuser`), `updatedate` = VALUES(`updatedate`), `updateuser` = VALUES(`updateuser`)
        SQL);

    $sql->setQuery(<<<'SQL'
        INSERT INTO `rex_media_manager_type_effect` (`id`, `type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`, `updatedate`, `updateuser`)
        VALUES
            (1, 1, 'resize', '{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"200\",\"rex_effect_resize_height\":\"200\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, '2025-03-28 10:09:51', 'admin', '2025-03-28 10:09:51', 'admin'),
            (2, 2, 'resize', '{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"600\",\"rex_effect_resize_height\":\"600\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, '2025-03-28 10:09:51', 'admin', '2025-03-28 10:09:51', 'admin'),
            (3, 3, 'resize', '{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"1200\",\"rex_effect_resize_height\":\"1200\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, '2025-03-28 10:09:51', 'admin', '2025-03-28 10:09:51', 'admin')
        ON DUPLICATE KEY UPDATE `type_id` = VALUES(`type_id`), `effect` = VALUES(`effect`), `parameters` = VALUES(`parameters`), `priority` = VALUES(`priority`), `createdate` = VALUES(`createdate`), `createuser` = VALUES(`createuser`), `updatedate` = VALUES(`updatedate`), `updateuser` = VALUES(`updateuser`)
        SQL);
} finally {
    $sql = rex_sql::factory();
    $sql->setQuery('SET FOREIGN_KEY_CHECKS = 1');
}
