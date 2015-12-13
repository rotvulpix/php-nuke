<?php

#####################################################
# File to upgrade from PHP-Nuke 5.5 to PHP-Nuke 5.6
# After you used this file, you can safely delete it.
# Change the parameters to fit your info:
#####################################################

$host 		= "localhost";
$database 	= "nuke";
$username 	= "root";
$password 	= "";
$prefix 	= "nuke";
$user_prefix	= "nuke";

mysqli_connect($host, $username, $password);
@mysqli_select_db($database);

####################### BEGIN THE UPDATE #######################################

// Banners Table Alteration

mysqli_query("ALTER TABLE ".$prefix."_banner ADD dateend datetime AFTER date");
mysqli_query("ALTER TABLE ".$prefix."_banner ADD type tinyint(1) default '0' not null, ADD active tinyint(1) default '1' not null");
$result = mysqli_query("select bid, cid, impressions, clicks, datestart, dateend from ".$prefix."_bannerfinish");
while(list($bid, $cid, $impressions, $clicks, $datestart, $dateend) = mysqli_fetch_row($result)) {
    mysqli_query("insert into ".$prefix."_banner values (NULL, '$cid', '$impressions', '$impressions', '$clicks', '', '', '$datestart', '$dateend', '0', '0')");
}
mysqli_query("DROP TABLE ".$prefix."_bannerfinish");

// Forums Tables alteration to change to Splatt Forums

mysqli_query("alter table ".$prefix."_config drop column config_id");
mysqli_query("alter table ".$prefix."_config drop column selected");
mysqli_query("alter table ".$prefix."_config drop column email_sig");
mysqli_query("alter table ".$prefix."_config drop column email_from");
mysqli_query("alter table ".$prefix."_config add column index_head text null");
mysqli_query("alter table ".$prefix."_config add column index_foot text null");
mysqli_query("alter table ".$prefix."_config add column max_upfile int(6) default 300 not null");
$result = mysqli_query("select topic_id, topic_title, topic_poster, topic_time, topic_views, forum_id, topic_status, topic_notify from ".$prefix."_bbtopics");
while(list($topic_id,$topic_title,$topic_poster,$topic_time,$topic_views,$forum_id,$topic_status,$topic_notify) = mysqli_fetch_row($result)) {
    $topic_title=addslashes($topic_title);
    mysqli_query("insert into ".$prefix."_forumtopics set topic_id='$topic_id',topic_title='$topic_title',topic_poster='$topic_poster',topic_time='$topic_time',topic_views='$topic_views',forum_id='$forum_id',topic_status='$topic_status',topic_notify='$topic_notify'");
}
mysqli_query("alter table ".$prefix."_forums drop column forum_topics");
mysqli_query("alter table ".$prefix."_forums drop column forum_posts");
mysqli_query("alter table ".$prefix."_forums drop column forum_last_post_id");
mysqli_query("alter table ".$prefix."_forums add column forum_pass varchar(60) null");
mysqli_query("alter table ".$prefix."_forums add column forum_notify_email varchar(30) null");
mysqli_query("alter table ".$prefix."_forums add column forum_atch int(2) not null default 0");
$result = mysqli_query("select forum_access from ".$prefix."_forums");
while(list($forum_access) = mysqli_fetch_row($result)) {
    mysqli_query("update ".$prefix."_forums set forum_access='0' where forum_access='2'");
}
$result = mysqli_query("select forum_access from ".$prefix."_forums");
while(list($forum_access) = mysqli_fetch_row($result)) {
    mysqli_query("update ".$prefix."_forums set forum_access='2' where forum_access='3'");
}
$result = mysqli_query("select * from ".$prefix."_posts_text");
while(list($post_id,$post_text) = mysqli_fetch_row($result)){
    $topic_title=addslashes($post_text);
    mysqli_query("update ".$prefix."_posts set post_text='$post_text' where post_id = $post_id");
}

// Statistics Table alteration

mysqli_query("ALTER TABLE ".$prefix."_stats_year DROP PRIMARY KEY, ADD PRIMARY KEY (year)");
mysqli_query("ALTER TABLE ".$prefix."_stats_month DROP PRIMARY KEY, ADD PRIMARY KEY (year, month)");
mysqli_query("ALTER TABLE ".$prefix."_stats_date DROP PRIMARY KEY, ADD PRIMARY KEY (year, month, date)");

echo "PHP-Nuke Update finished!";

?>