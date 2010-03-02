<?php

$queries = array();

$queries['query_articles'] = 

"CREATE TABLE " . DB_PREFIX . "articles (
    article_id          INT AUTO_INCREMENT PRIMARY KEY,
    article_title       VARCHAR(512),
    article_slug        VARCHAR(768),
    article_content     TEXT,
    article_onstart     BOOLEAN
)";


$queries['query_menupoints'] = 

"CREATE TABLE " . DB_PREFIX . "menupoints (
    point_id            INT AUTO_INCREMENT PRIMARY KEY,
    point_superroot_id       INT(11),
    point_root_id       INT(11),
    point_name          VARCHAR(256),
    point_slug          VARCHAR(256),
    point_target_type   VARCHAR(32),
    point_target_data   VARCHAR(4096),
    point_sort          INT(5)
    
)";


$queries['query_sidebartexts'] = 

"CREATE TABLE " . DB_PREFIX . "sidebartexts (
    text_id          INT AUTO_INCREMENT PRIMARY KEY,
    text_title       VARCHAR(256),
    text_content     TEXT,
    text_visibility  INT(11),
    text_onarticles  TEXT,
    text_sort        INT(5)
)";


$queries['query_sidebarlinkcategories'] = 

"CREATE TABLE " . DB_PREFIX . "sidebarlinkcategories (
    cat_id          INT AUTO_INCREMENT PRIMARY KEY,
    cat_title       VARCHAR(256),
    cat_visibility  INT(11),
    cat_onarticles  TEXT,
    cat_sort        INT(5)
)";


$queries['query_sidebarlinks'] = 

"CREATE TABLE " . DB_PREFIX . "sidebarlinks (
    link_id          INT AUTO_INCREMENT PRIMARY KEY,
    link_cat_id      VARCHAR(256),
    link_name        VARCHAR(256),
    link_url         VARCHAR(256),
    link_sort        INT(5)
)";


$queries['query_blogposts'] = 

"CREATE TABLE " . DB_PREFIX . "blogposts (
    post_id             INT AUTO_INCREMENT PRIMARY KEY,
    post_timestamp      VARCHAR(32),
    post_year           VARCHAR(4),
    post_month          VARCHAR(2),
    post_day            VARCHAR(2),
    post_author_id      INT,
    post_title          VARCHAR(256),
    post_slug           VARCHAR(256),
    post_content_cut    TEXT,
    post_content        TEXT,
    post_tags           TEXT,
    post_categories     TEXT,
    post_status         BOOLEAN
)";


$queries['query_blogtags'] = 

"CREATE TABLE " . DB_PREFIX . "blogtags (
    tag_id        INT AUTO_INCREMENT PRIMARY KEY,
    tag_name      VARCHAR(64),
    tag_slug      VARCHAR(64)
)";


$queries['query_blogcategories'] = 

"CREATE TABLE " . DB_PREFIX . "blogcategories (
    cat_id        INT AUTO_INCREMENT PRIMARY KEY,
    cat_name      VARCHAR(128),
    cat_slug      VARCHAR(128)
)";


$queries['query_blogcomments'] = 

"CREATE TABLE " . DB_PREFIX . "blogcomments (
    comment_id        INT AUTO_INCREMENT PRIMARY KEY,
    comment_post_id   INT(11),
    comment_title     VARCHAR(128),
    comment_author    VARCHAR(128),
    comment_email     VARCHAR(512),
    comment_timestamp VARCHAR(32),
    comment_content   TEXT
    
)";


$queries['query_users'] = 

"CREATE TABLE " . DB_PREFIX . "users (
    user_id                         INT AUTO_INCREMENT PRIMARY KEY,
    user_name                       VARCHAR(256),
    user_slug                       VARCHAR(256),
    user_forename                   VARCHAR(256),
    user_surname                    VARCHAR(256),
    user_email                      VARCHAR(512),
    user_password                   CHAR(40),
    user_permission_articles        INT(1),
    user_permission_menupoints      INT(1),
    user_permission_texts           INT(1),
    user_permission_links           INT(1),
    user_permission_blogposts       INT(1),
    user_permission_blogcomments    INT(1),
    user_permission_users           INT(1),
    user_permission_plugins         INT(1),
    user_permission_templates       INT(1),
    user_permission_settings        INT(1)    
)";

$queries['query_settings'] = 

"CREATE TABLE " . DB_PREFIX . "settings (
    setting_id              INT AUTO_INCREMENT PRIMARY KEY,        
    setting_sitename_main   VARCHAR(256),
    setting_sitename_sub    VARCHAR(256),
    setting_description     TEXT,
    setting_tags            VARCHAR(512),
    setting_admin_email     VARCHAR(512),
    setting_locale          VARCHAR(4),
    setting_url_type        VARCHAR(32),
    setting_posts_per_page  INT(11),
    setting_blog_onstart    BOOLEAN,
    setting_template_id     VARCHAR(256),
    setting_nospam_key_1    VARCHAR(2048),
    setting_nospam_key_2    VARCHAR(2048),
    setting_nospam_key_3    VARCHAR(2048)
)";

?>