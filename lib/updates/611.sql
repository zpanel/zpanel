UPDATE z_settings SET st_value_tx = '6.1.1' WHERE st_name_vc = 'zpanel_version';
UPDATE z_settings SET st_value_tx = UNIX_TIMESTAMP() WHERE st_name_vc = 'last_update';