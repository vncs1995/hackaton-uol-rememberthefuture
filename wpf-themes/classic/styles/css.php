<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

/**
* 
* @file: CSS Matrix
* @version: 1.0.0
* @theme: Classic
* 
*/
$css = '/* wpForo Dynamic CSS Document */

#wpforo-wrap { color: '. $WPFCOLOR_2 .'; background: '. $WPFCOLOR_1 .' } 
#wpforo-wrap .wpforo-subforum i{ color:'. $WPFCOLOR_6 .'; }
#wpforo-wrap #footer { background:'. $WPFCOLOR_8 .';} 
#wpforo-wrap .wpf-p-error { background-color: '. $WPFCOLOR_8 .'; color: '. $WPFCOLOR_2 .'; }
#wpforo-wrap .wpf-res-menu { color: '. $WPFCOLOR_1 .'; }
#wpforo-wrap #wpforo-menu { background-color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap #wpforo-menu li { border-right:'. $WPFCOLOR_5 .' 1px solid; }
#wpforo-wrap #wpforo-menu li a { color: '. $WPFCOLOR_1 .'; }

#wpforo-wrap .wpforo-active,
#wpforo-wrap #wpforo-menu li:hover,  
#wpforo-wrap #wpforo-menu .current-menu-item, 
#wpforo-wrap #wpforo-menu .current-menu-ancestor,
#wpforo-wrap #wpforo-menu .current-menu-parent,
#wpforo-wrap #wpforo-menu .current_page_item { background-color: '. $WPFCOLOR_12 .'!important; }
#wpforo-load { color: '. $WPFCOLOR_12 .'; }
#wpforo-load i{ color: '. $WPFCOLOR_12 .'!important; }

#wpforo-wrap .wpf-search input[type="text"]{ color: '. $WPFCOLOR_3 .'; background: transparent; }
#wpforo-wrap .wpf-search input[type="text"]:focus{ background: '. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpf-search i{ color: '. $WPFCOLOR_12 .'; }

#wpforo-wrap #wpforo-poweredby{ background-color:'. $WPFCOLOR_3 .'; color: '. $WPFCOLOR_1 .'; }
#wpforo-wrap #wpforo-title{ color:'. $WPFCOLOR_2 .';}

#wpforo-wrap a:link { color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap a:visited { color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap a:hover{ color: '. $WPFCOLOR_11 .';}
#wpforo-wrap a:active { color: '. $WPFCOLOR_11 .'; }

#wpforo-wrap pre { border:'. $WPFCOLOR_8 .' 1px solid; background:'. $WPFCOLOR_17 .'; }

.wpfcl-a{ color:'. $WPFCOLOR_11 .'!important; }
.wpfbg-a{ background-color:'. $WPFCOLOR_11 .'!important; }
.wpfcl-b{ color:'. $WPFCOLOR_12 .'!important; }
.wpfbg-b{ background-color:'. $WPFCOLOR_12 .'!important; }
.wpfcl-0{ color:'. $WPFCOLOR_6 .'!important; } 
.wpfbg-0{ background-color:'. $WPFCOLOR_6 .'!important; }
.wpfcl-1{ color:'. $WPFCOLOR_3 .'!important; } 
.wpfbg-1{ background-color:'. $WPFCOLOR_3 .'!important; }
.wpfcl-2{ color:'. $WPFCOLOR_5 .'!important; }
.wpfbg-2{ background-color:'. $WPFCOLOR_5 .'!important; }
.wpfcl-3{ color:'. $WPFCOLOR_1 .'!important; } 
.wpfbg-3{ background-color:'. $WPFCOLOR_1 .'!important; }
.wpfcl-4{ color:'. $WPFCOLOR_13 .'!important;} 
.wpfbg-4{ background-color:'. $WPFCOLOR_13 .'!important; }
.wpfcl-5{ color:'. $WPFCOLOR_20 .'!important; }
.wpfbg-5{ background-color:'. $WPFCOLOR_20 .'!important; }
.wpfcl-6{ color:'. $WPFCOLOR_14 .'!important; }
.wpfbg-6{ background-color:'. $WPFCOLOR_14 .'!important; }
.wpfcl-7{ color:'. $WPFCOLOR_8 .'!important; } 
.wpfbg-7{ background-color:'. $WPFCOLOR_8 .'!important; }
.wpfcl-8{ color:'. $WPFCOLOR_30 .'!important; }
.wpfbg-8{ background-color:'. $WPFCOLOR_30 .'!important; }
.wpfcl-9{ color:'. $WPFCOLOR_9 .'!important; }
.wpfbg-9{ background-color:'. $WPFCOLOR_9 .'!important; }

#wpforo-wrap .author-rating {  border: 1px solid '. $WPFCOLOR_8 .'; background: '. $WPFCOLOR_9 .'; }

#wpforo-wrap .wpf-breadcrumb .wpf-root{ border-left:1px solid '. $WPFCOLOR_4 .'; }
#wpforo-wrap .wpf-breadcrumb a.wpf-end { background: transparent!important; }
#wpforo-wrap .wpf-breadcrumb a.wpf-end:hover{ background: transparent!important; }
#wpforo-wrap .wpf-breadcrumb a { color:'. $WPFCOLOR_4 .'; background: '. $WPFCOLOR_1 .';}
#wpforo-wrap .wpf-breadcrumb a:hover{ background:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpf-breadcrumb a:hover:after { background:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpf-breadcrumb a.active{ background:'. $WPFCOLOR_9 .'; color:'. $WPFCOLOR_16 .'; }
#wpforo-wrap .wpf-breadcrumb a.active:after { background:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpf-breadcrumb a:after { background: '. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpf-breadcrumb a.active:after { background: '. $WPFCOLOR_9 .'; }

#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-content img{ border:'. $WPFCOLOR_8 .' 1px solid; background:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-post .wpf-right blockquote{ border:'. $WPFCOLOR_6 .' 1px dotted; background:'. $WPFCOLOR_8 .'; }
#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-code{ border:'. $WPFCOLOR_8 .' 1px solid; background:'. $WPFCOLOR_8 .'; }
#wpforo-wrap .wpforo-post .wpf-right code{ border:'. $WPFCOLOR_8 .' 1px solid; background:'. $WPFCOLOR_8 .'; }
#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-code-title{ border-bottom:'. $WPFCOLOR_8 .' 1px solid;}
#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-attachments{ border:'. $WPFCOLOR_1 .' 1px dotted; }
#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-attachments img{border:'. $WPFCOLOR_8 .' 2px solid; background:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-signature{ border-top:'. $WPFCOLOR_11 .' 1px dotted; }

#wpforo-wrap .wpfl-1 .wpforo-category{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
#wpforo-wrap .wpfl-1 .cat-title{ color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-1 .cat-stat-posts { color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-1 .cat-stat-topics {color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-1 .forum-wrap{ border-top:'. $WPFCOLOR_7 .' 1px solid; }
#wpforo-wrap .wpfl-1 .wpforo-forum{ background-color:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpfl-1 .wpforo-forum-description{color:'. $WPFCOLOR_5 .'; }
#wpforo-wrap .wpfl-1 .wpforo-subforum{ border-top:'. $WPFCOLOR_10 .' 1px dotted; }
#wpforo-wrap .wpfl-1 .wpforo-forum-footer{ color:'. $WPFCOLOR_6 .'; }
#wpforo-wrap .wpfl-1 .wpforo-last-topics{ background-color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-1 .wpforo-last-topics-tab{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-1 .wpforo-last-topics-list{ color:'. $WPFCOLOR_3 .'; border-bottom:'. $WPFCOLOR_7 .' 0px solid; }

	#wpforo-wrap .wpfl-1 .wpforo-topic-head{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
	#wpforo-wrap .wpfl-1 .head-title{ color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-1 .head-stat-posts { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-1 .head-stat-views { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-1 .topic-wrap{ border-top:'. $WPFCOLOR_7 .' 1px solid; }
	#wpforo-wrap .wpfl-1 .wpforo-topic{ background-color:'. $WPFCOLOR_9 .'; }
	#wpforo-wrap .wpfl-1 .wpforo-topic-info{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-1 .wpforo-topic-stat-posts{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-1 .wpforo-topic-stat-views{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-1 .wpforo-last-posts{ background-color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-1 .wpforo-last-posts-tab{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-1 .wpforo-last-posts-list{ color:'. $WPFCOLOR_3 .'; border-bottom:'. $WPFCOLOR_7 .' 0px solid; }

		#wpforo-wrap .wpfl-1 .post-wrap{ border-top:none; border-bottom:'. $WPFCOLOR_8 .' 3px solid; }
		#wpforo-wrap .wpfl-1 .wpforo-post-head{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
		#wpforo-wrap .wpfl-1 .wpforo-post{ background-color:'. $WPFCOLOR_9 .'; }
		#wpforo-wrap .wpfl-1 .wpforo-post .wpf-right .wpforo-post-content-top{ border-bottom:1px solid '. $WPFCOLOR_8 .'; }
		#wpforo-wrap .wpfl-1 .wpforo-post .wpf-left .avatar{ background:'. $WPFCOLOR_1 .'; border:'. $WPFCOLOR_8 .' 2px solid; }
		#wpforo-wrap .wpfl-1 .wpforo-post .wpf-left .author-rating{ border:1px solid '. $WPFCOLOR_8 .'; background:'. $WPFCOLOR_9 .'; }
		#wpforo-wrap .wpfl-1 .wpforo-post .bottom { border-top:'. $WPFCOLOR_8 .' 1px solid; }
		#wpforo-wrap .wpfl-1 .wpforo-post .bottom .bleft a{ color:'. $WPFCOLOR_20 .'; }

#wpforo-wrap .wpfl-2 .wpforo-category{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
#wpforo-wrap .wpfl-2 .cat-title{ color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-2 .cat-lastpostinfo{ color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-2 .forum-wrap{ border-top:'. $WPFCOLOR_7 .' 1px solid; }
#wpforo-wrap .wpfl-2 .wpforo-forum{ background-color:'. $WPFCOLOR_9 .';}
#wpforo-wrap .wpfl-2 .wpforo-forum-icon{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-2 .wpforo-forum-info{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-2 .wpforo-subforum{  border-top:'. $WPFCOLOR_10 .' 1px dotted;}
#wpforo-wrap .wpfl-2 .wpforo-forum-stat{ color:'. $WPFCOLOR_20 .'; }
#wpforo-wrap .wpfl-2 .wpforo-last-post{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-2 .wpforo-last-post-avatar{ color:'. $WPFCOLOR_3 .' }
#wpforo-wrap .wpfl-2 .wpforo-last-post-avatar img{ background:'. $WPFCOLOR_1 .'; border:1px solid '. $WPFCOLOR_7 .'; }

	#wpforo-wrap .wpfl-2 .wpforo-topic-head{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
	#wpforo-wrap .wpfl-2 .head-title{ color:'. $WPFCOLOR_1 .';}
	#wpforo-wrap .wpfl-2 .head-stat-posts { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-2 .head-stat-views { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-2 .head-stat-lastpost { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-2 .topic-wrap{ border-top:'. $WPFCOLOR_7 .' 1px solid; }
	#wpforo-wrap .wpfl-2 .wpforo-topic{ background-color:'. $WPFCOLOR_9 .'; }
	#wpforo-wrap .wpfl-2 .wpforo-topic-avatar{color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-2 .wpforo-topic-avatar img{ background:'. $WPFCOLOR_1 .'; border:1px solid '. $WPFCOLOR_7 .'; }
	#wpforo-wrap .wpfl-2 .wpforo-topic-info{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-2 .wpforo-topic-stat-posts{ color:'. $WPFCOLOR_3 .';}
	#wpforo-wrap .wpfl-2 .wpforo-topic-stat-views{ color:'. $WPFCOLOR_3 .';}
	#wpforo-wrap .wpfl-2 .wpforo-topic-stat-lastpost{ color:'. $WPFCOLOR_3 .';}

		#wpforo-wrap .wpfl-2 .post-wrap{ border-top:'. $WPFCOLOR_8 .' 1px solid;  }
		#wpforo-wrap .wpfl-2 .wpforo-post-head{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
		#wpforo-wrap .wpfl-2 .wpforo-post{ background-color:'. $WPFCOLOR_1 .'; }
		#wpforo-wrap .wpfl-2 .wpforo-post .wpf-right{ background:'. $WPFCOLOR_9 .'; }
		#wpforo-wrap .wpfl-2 .wpforo-post .wpf-right .wpforo-post-content-bottom{ border-top:'. $WPFCOLOR_1 .' 1px solid; background:'. $WPFCOLOR_1 .'; }
		#wpforo-wrap .wpfl-2 .wpforo-post .wpf-left .avatar{ background:'. $WPFCOLOR_1 .'; border:'. $WPFCOLOR_8 .' 2px solid; }
		#wpforo-wrap .wpfl-2 .wpforo-post .wpf-right .wpforo-post-content-bottom .cbleft a{color:'. $WPFCOLOR_20 .';}

#wpforo-wrap .wpfl-3 .wpforo-category{ background-color: '. $WPFCOLOR_12 .';border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
#wpforo-wrap .wpfl-3 .cat-title{ color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-3 .cat-stat-posts { color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-3 .cat-stat-answers { color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-3 .cat-stat-questions { color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-3 .forum-wrap{ border-top:'. $WPFCOLOR_7 .' 1px solid; }
#wpforo-wrap .wpfl-3 .wpforo-forum{ background-color:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpfl-3 .wpforo-forum-icon{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-3 .wpforo-forum-info{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-3 .wpforo-subforum{ border-top:'. $WPFCOLOR_10 .' 1px dotted; }
#wpforo-wrap .wpfl-3 .wpforo-forum-footer{ color:'. $WPFCOLOR_6 .'; }
#wpforo-wrap .wpfl-3 .wpforo-forum-stat-posts{color:'. $WPFCOLOR_3 .';}
#wpforo-wrap .wpfl-3 .wpforo-forum-stat-answers{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-3 .wpforo-forum-stat-questions{color:'. $WPFCOLOR_3 .';}
#wpforo-wrap .wpfl-3 .wpforo-last-topics{ background-color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpfl-3 .wpforo-last-topics-tab{ color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpfl-3 .wpforo-last-topics-list{ color:'. $WPFCOLOR_3 .'; border-bottom:'. $WPFCOLOR_7 .' 0px solid; }
#wpforo-wrap .wpfl-3 .wpforo-last-topic-posts{ background:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpfl-3 .wpforo-last-topic .votes{ background:'. $WPFCOLOR_9 .'; }
#wpforo-wrap .wpfl-3 .wpforo-last-topic .answers{ background:'. $WPFCOLOR_9 .';  }
#wpforo-wrap .wpfl-3 .wpforo-last-topic .views{background:'. $WPFCOLOR_9 .'; }

	#wpforo-wrap .wpfl-3 .wpforo-topic-head{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 2px solid; }
	#wpforo-wrap .wpfl-3 .head-title{ color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-3 .head-stat-posts { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-3 .head-stat-lastpost { color:'. $WPFCOLOR_1 .'; }
	#wpforo-wrap .wpfl-3 .topic-wrap{ border-top:'. $WPFCOLOR_1 .' 1px solid; border-bottom:'. $WPFCOLOR_8 .' 1px solid; }
	#wpforo-wrap .wpfl-3 .wpforo-topic{ background-color:'. $WPFCOLOR_9 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic-avatar{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic-avatar img{ background:'. $WPFCOLOR_1 .'; border:1px solid '. $WPFCOLOR_7 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic-info{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic-stat-lastpost{ color:'. $WPFCOLOR_3 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic-status .votes{ background:'. $WPFCOLOR_9 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic-status .answers{ background:'. $WPFCOLOR_9 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic .views{ background:'. $WPFCOLOR_9 .'; }
	#wpforo-wrap .wpfl-3 .wpforo-topic .count{border-right:1px solid '. $WPFCOLOR_8 .'; border-top:1px solid '. $WPFCOLOR_8 .';border-left:1px solid '. $WPFCOLOR_8 .';}
	#wpforo-wrap .wpfl-3 .wpforo-topic .wpforo-label{border-right:1px solid '. $WPFCOLOR_8 .'; border-bottom:1px solid '. $WPFCOLOR_8 .';border-left:1px solid '. $WPFCOLOR_8 .';}

		#wpforo-wrap .wpfl-3 .post-wrap{ border-top:'. $WPFCOLOR_8 .' 1px solid;  }
		#wpforo-wrap .wpfl-3 .wpforo-post-head{ background-color: '. $WPFCOLOR_12 .'; border-bottom:'. $WPFCOLOR_7 .' 1px solid; }
		#wpforo-wrap .wpfl-3 .wpforo-post{ background-color:'. $WPFCOLOR_1 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-left .wpf-toggle-answer{ color:'. $WPFCOLOR_31 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-left .wpf-toggle-not-answer{ color:'. $WPFCOLOR_6 .'!important; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-right{ background:'. $WPFCOLOR_9 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-right .wpforo-post-content-top{ border-bottom:1px dotted '. $WPFCOLOR_8 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-right .wpforo-post-tool-bar{ border-top:1px dotted '. $WPFCOLOR_8 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-right .wpforo-post-signature-content{ border-top:'. $WPFCOLOR_11 .' 1px dotted; }
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-right .wpforo-post-author-data-content{ border:'. $WPFCOLOR_1 .' 1px solid; background:'. $WPFCOLOR_17 .';}
		#wpforo-wrap .wpfl-3 .wpforo-post .wpf-right .wpforo-post-author-data .avatar{ background:'. $WPFCOLOR_1 .';  border:1px solid '. $WPFCOLOR_7 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-comment{ background-color:'. $WPFCOLOR_1 .'; }
		#wpforo-wrap .wpfl-3 .wpforo-comment .wpf-right{  background:'. $WPFCOLOR_9 .'; }

#wpforo-wrap .wpforo-profile-wrap .wpf-profile-plugin-menu .wpf-pp-menu .wpf-pp-menu-item{color: '. $WPFCOLOR_5 .'; border-right:1px solid '. $WPFCOLOR_5 .'; border-right:1px solid '. $WPFCOLOR_5 .';}
#wpforo-wrap .wpforo-profile-wrap .wpf-profile-plugin-menu .wpf-pp-menu .wpf-pp-menu-item:hover{color: '. $WPFCOLOR_12 .'; border-right:1px solid '. $WPFCOLOR_12 .'; border-right:1px solid '. $WPFCOLOR_12 .';}
#wpforo-wrap .wpforo-profile-wrap .wpf-profile-plugin-menu #wpf-pp-forum-menu .wpf-pp-menu-item{color:'. $WPFCOLOR_12 .'; border-right:1px solid '. $WPFCOLOR_12 .';}
#wpforo-wrap .wpf-profile-section{color: '. $WPFCOLOR_4 .';}
#wpforo-wrap .wpf-profile-section .wpf-profile-section-head{border-bottom:1px solid '. $WPFCOLOR_10 .';}
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-label{ border-bottom:'. $WPFCOLOR_1 .' 1px solid; }
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-field{ border-bottom:'. $WPFCOLOR_1 .' 1px solid; }
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-field input[type="file"],
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-field input[type="password"],
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-field input[type="text"],
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-field select,
#wpforo-wrap .wpforo-profile-wrap .wpforo-profile-field textarea { color:'. $WPFCOLOR_3 .'; }
#wpforo-wrap .wpforo-profile-wrap .h-left{  }
#wpforo-wrap .wpforo-profile-wrap .profile-display-name{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap .wpforo-profile-wrap .profile-stat-data{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap .wpforo-profile-wrap .profile-stat-data-item{ color:'. $WPFCOLOR_15 .';}
#wpforo-wrap .wpforo-profile-wrap .profile-rating-bar-wrap{background:'. $WPFCOLOR_1 .';}
#wpforo-wrap .wpforo-profile-wrap .profile-rating-bar-wrap .rating-bar-cell{color:'. $WPFCOLOR_1 .';}
#wpforo-wrap .wpforo-profile-wrap .h-bottom .wpf-profile-menu{border-right:'. $WPFCOLOR_1 .' 1px solid; background:'. $WPFCOLOR_5 .'; color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-profile-wrap .h-bottom .wpf-profile-menu:hover{ background: '. $WPFCOLOR_12 .';}
#wpforo-wrap .wpforo-profile-wrap .h-left .avatar{ background-color:'. $WPFCOLOR_1 .'; border:2px solid '. $WPFCOLOR_7 .'; }
#wpforo-wrap .wpforo-profile-wrap .wpf-username{ color:'. $WPFCOLOR_14 .'; }

#wpforo-wrap .wpforo-activity-content{ border:'. $WPFCOLOR_8 .' 1px solid;  }
#wpforo-wrap .wpforo-activity-content .activity-icon{ border-right:2px solid '. $WPFCOLOR_1 .'; }

#wpforo-wrap .wpforo-sbn-content{ border:'. $WPFCOLOR_8 .' 1px solid;  }
#wpforo-wrap .wpforo-sbn-content .sbn-icon{ border-right:2px solid '. $WPFCOLOR_1 .'; }

#wpforo-wrap .wpforo-messages-content .uml{ background: '. $WPFCOLOR_1 .'; border-bottom: 1px solid '. $WPFCOLOR_14 .'; }
#wpforo-wrap .wpforo-messages-content .uml:hover, .uml a:hover{ color: '. $WPFCOLOR_1 .' !important; }
#wpforo-wrap .wpforo-messages-content .umlactive{ background: '. $WPFCOLOR_14 .' !important; }
#wpforo-wrap .wpforo-messages-content .umlactive a{ color: '. $WPFCOLOR_1 .' !important; }
#wpforo-wrap .wpforo-messages-content .umlactive .uml_lastmsg{ color: '. $WPFCOLOR_1 .' !important; }
#wpforo-wrap .wpforo-messages-content .uml .uml_lastmsg{ color: '. $WPFCOLOR_6 .'; }
#wpforo-wrap .wpforo-messages-content .uml-has-message{ background: rgba(255, 163, 0, 0.22); }
#wpforo-wrap .wpforo-messages-content .whr{ border-top: 1px solid '. $WPFCOLOR_8 .' !important; color: '. $WPFCOLOR_7 .'; }
#wpforo-wrap .wpforo-messages-content .whr abbr{ background-color: '. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-messages-content .pm_submit{ color: '. $WPFCOLOR_1 .'; }

#wpforo-wrap input[type="text"], #wpforo-wrap input[type="password"], #wpforo-wrap input[type="email"], #wpforo-wrap textarea, #wpforo-wrap select { border: 1px solid '. $WPFCOLOR_10 .'; color: '. $WPFCOLOR_5 .'; box-shadow: inset 0px 1px 4px '. $WPFCOLOR_8 .'; -moz-box-shadow: inset 0px 1px 4px '. $WPFCOLOR_8 .'; -webkit-box-shadow: inset 0px 1px 4px '. $WPFCOLOR_8 .'; }
#wpforo-wrap input[type="submit"], #wpforo-wrap input[type="button"]{ background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; border: 1px solid '. $WPFCOLOR_14 .'; }
#wpforo-wrap input[type="submit"]:hover{ background:'. $WPFCOLOR_14 .'; }
#wpforo-wrap input[type="button"]:hover{ background:'. $WPFCOLOR_14 .'; }
#wpforo-wrap .wpf-button{ background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'!important; border: 1px solid '. $WPFCOLOR_14 .'; }
#wpforo-wrap .wpf-button:hover{ background:'. $WPFCOLOR_14 .'; }

#wpforo-wrap #wpf-topic-create{ border: solid '. $WPFCOLOR_10 .' 1px; }
#wpforo-wrap .wpf-topic-create .wp-editor-tools{ border-bottom:1px '. $WPFCOLOR_10 .' solid; }
#wpforo-wrap .wpf-topic-create .wp-editor-tabs a.switch-tmce{ border: 1px '. $WPFCOLOR_10 .' solid; }
#wpforo-wrap .wpf-topic-create .wp-editor-tabs a.switch-html{ border: 1px '. $WPFCOLOR_10 .' solid; }
#wpforo-wrap .wpf-topic-create .quicktags-toolbar input[type="button"] { background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpf-topic-create .quicktags-toolbar input[type="button"]:hover{ background:'. $WPFCOLOR_14 .'!important; }

#wpforo-wrap #wpf-post-create{ border: solid '. $WPFCOLOR_10 .' 1px; }
#wpforo-wrap .wpf-post-create .wp-editor-tools{ border-bottom:1px '. $WPFCOLOR_10 .' solid; }
#wpforo-wrap .wpf-post-create .wp-editor-tabs a.switch-tmce{ border: 1px '. $WPFCOLOR_10 .' solid; }
#wpforo-wrap .wpf-post-create .wp-editor-tabs a.switch-html{ border: 1px '. $WPFCOLOR_10 .' solid; }
#wpforo-wrap .wpf-post-create .quicktags-toolbar input[type="button"] { background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpf-post-create .quicktags-toolbar input[type="button"]:hover{ background:'. $WPFCOLOR_14 .'!important; }

#wpforo-wrap .wpforo-members-wrap .wpforo-members-content { border:'. $WPFCOLOR_8 .' 1px solid; }
#wpforo-wrap .wpforo-members-wrap td.wpf-members-search { border-bottom:1px solid '. $WPFCOLOR_8 .'; }
#wpforo-wrap .wpforo-members-wrap td.wpf-members-avatar { border-right:2px solid '. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-members-wrap td.wpf-members-regdate-head{ border-bottom:1px solid '. $WPFCOLOR_8 .'; }
#wpforo-wrap .wpforo-members-wrap td.wpf-members-foot { border-top:1px solid '. $WPFCOLOR_8 .'; }

#wpforo-wrap .wpforo-register-wrap .wpforo-register-content { border-top:'. $WPFCOLOR_8 .' 1px solid; border-bottom:'. $WPFCOLOR_8 .' 1px solid; }
#wpforo-wrap .wpforo-register-wrap td.wpf-register-label{ border-right:'. $WPFCOLOR_1 .' 1px solid; border-bottom:'. $WPFCOLOR_1 .' 1px solid; }
#wpforo-wrap .wpforo-register-wrap td.wpf-register-field{ border-bottom:'. $WPFCOLOR_1 .' 1px solid; }

#wpforo-wrap .wpforo-login-wrap .wpforo-login-content { border-top:'. $WPFCOLOR_8 .' 1px solid;  border-bottom:'. $WPFCOLOR_8 .' 1px solid; }
#wpforo-wrap .wpforo-login-wrap td.wpf-login-label{ border-right:'. $WPFCOLOR_1 .' 1px solid; border-bottom:'. $WPFCOLOR_1 .' 1px solid; }
#wpforo-wrap .wpforo-login-wrap td.wpf-login-field{ border-bottom:'. $WPFCOLOR_1 .' 1px solid; }

#wpforo-wrap .wpforo-404-wrap .wpforo-404-content { border-top:'. $WPFCOLOR_8 .' 1px solid; border-bottom:'. $WPFCOLOR_8 .' 1px solid; }
#wpforo-wrap .wpforo-404-wrap .wpf-search-box { border:'. $WPFCOLOR_7 .' 1px dashed; }

#wpforo-wrap #wpforo-search-title { color:'. $WPFCOLOR_2 .'; }
#wpforo-wrap .wpforo-search-wrap .wpf-search-bar{ background:'. $WPFCOLOR_9 .'; border:'. $WPFCOLOR_8 .' 1px solid; }
#wpforo-wrap .wpforo-search-wrap .wpf-search-bar .wpfltd{ border-bottom:1px dotted '. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-search-wrap .wpf-search-bar .wpfrtd{ border-bottom:1px dotted '. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-search-wrap .wpforo-search-content { border-bottom:'. $WPFCOLOR_8 .' 1px solid; }
#wpforo-wrap .wpforo-search-wrap .wpforo-search-content .wpf-htr{ background-color:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; }
#wpforo-wrap .wpforo-search-wrap .wpforo-search-content .wpf-ttr{ background:'. $WPFCOLOR_9 .' }
#wpforo-wrap .wpforo-search-wrap .wpforo-search-content td.wpf-shead-icon{ border-right:1px solid '. $WPFCOLOR_1 .';}
#wpforo-wrap .wpforo-search-wrap .wpforo-search-content td.wpf-spost-icon { border-right:1px solid '. $WPFCOLOR_1 .';}

#wpforo-wrap .wpf-action{ color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap .wpf-action:hover{ cursor: pointer; color: '. $WPFCOLOR_11 .'; }

#wpforo-wrap .wpf-navi .wpf-navi-wrap .wpf-page-info{ color:'. $WPFCOLOR_4 .'; }
#wpforo-wrap .wpf-navi .wpf-navi-wrap .wpf-prev-button{ background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; border: 1px solid '. $WPFCOLOR_14 .'; }
#wpforo-wrap .wpf-navi .wpf-navi-wrap .wpf-next-button{ background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; border: 1px solid '. $WPFCOLOR_14 .'; }

#wpforo-wrap  #wpforo-stat-header{ background-color:'. $WPFCOLOR_3 .'; color: '. $WPFCOLOR_1 .'; border-bottom: 2px solid '. $WPFCOLOR_7 .';}
#wpforo-wrap  #wpforo-stat-body{ border:'. $WPFCOLOR_8 .' 1px solid; background:'. $WPFCOLOR_9 .'; }
#wpforo-wrap  #wpforo-stat-body .wpf-stat-data{ }
#wpforo-wrap  #wpforo-stat-body .wpf-stat-item{border-right:1px solid '. $WPFCOLOR_8 .';}
#wpforo-wrap  #wpforo-stat-body .wpf-stat-item i{color:'. $WPFCOLOR_5 .';}
#wpforo-wrap  #wpforo-stat-body .wpf-stat-item .wpf-stat-value{color:'. $WPFCOLOR_5 .';}
#wpforo-wrap  #wpforo-stat-body .wpf-stat-item .wpf-stat-label{border-top:1px dotted '. $WPFCOLOR_7 .'; color:'. $WPFCOLOR_5 .';}
#wpforo-wrap  #wpforo-stat-body .wpf-last-info i{color:'. $WPFCOLOR_5 .';}

#wpforo-wrap .widget-title { border-bottom:2px solid '. $WPFCOLOR_8 .';}
#wpforo-wrap .wpforo-widget-wrap .wpforo-widget-content li{ border-bottom:1px dotted '. $WPFCOLOR_7 .'; }
#wpforo-wrap .pm_content{ background-color: '. $WPFCOLOR_1 .' !important; }
div.pm_content::-webkit-scrollbar-track { -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); }
div.pm_content::-webkit-scrollbar-thumb { background: '. $WPFCOLOR_14 .'; -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); }
div.pm_content::-webkit-scrollbar-thumb:window-inactive { background: '. $WPFCOLOR_14 .'; }
div.pm_content::-webkit-scrollbar-button { background-color: '. $WPFCOLOR_16 .'; }

.ui-dialog { z-index: 999998 !important; }
.wpf-video{	margin: 10px !important; }

.ui-dialog .ui-widget-header{  background-color: '. $WPFCOLOR_12 .'!important; border-bottom:'. $WPFCOLOR_7 .' 1px solid!important; }
.ui-dialog .ui-widget-header span.ui-dialog-title{ color: '. $WPFCOLOR_1 .' !important; }
.ui-widget input[type="submit"]:hover{ background:'. $WPFCOLOR_14 .'; }
.ui-widget input[type="submit"]{ background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; border: 1px solid '. $WPFCOLOR_14 .'; }

#wpf_attach_dialog .wpforo-button{ background:'. $WPFCOLOR_12 .'; color:'. $WPFCOLOR_1 .'; border: 1px solid '. $WPFCOLOR_14 .'; }
#wpf_attach_dialog .wpforo-button:hover{ background:'. $WPFCOLOR_14 .'; }
#wpf_attach_dialog .wpforo-button-prime{ background-color:'. $WPFCOLOR_12 .'; border:1px solid '. $WPFCOLOR_14 .'; }
#wpf_attach_dialog .wpforo-button-prime:hover{ background-color:'. $WPFCOLOR_14 .'; }
#wpf_attach_dialog button.delete{ background-color:'. $WPFCOLOR_41 .'; border:1px solid '. $WPFCOLOR_42 .'; }
#wpf_attach_dialog button.delete:hover{ background-color:'. $WPFCOLOR_42 .'; }
#wpf_attach_dialog button.start{ background-color:'. $WPFCOLOR_32 .'; border:1px solid '. $WPFCOLOR_33 .'; }
#wpf_attach_dialog button.start:hover{ background-color:'. $WPFCOLOR_33 .'; }
#wpf_attach_dialog button.cancel{ background-color:'. $WPFCOLOR_5 .'; border:1px solid '. $WPFCOLOR_6 .'; }
#wpf_attach_dialog button.cancel:hover{ background-color:'. $WPFCOLOR_6 .'; }
#wpf_attach_dialog tr.template-download span.preview img{ border:'. $WPFCOLOR_8 .' 1px solid; background-color:'. $WPFCOLOR_1 .'; }
#wpf_attach_dialog tr.template-upload span.preview img{ border:'. $WPFCOLOR_8 .' 1px solid; background-color:'. $WPFCOLOR_1 .'; }
#wpf_attach_dialog tr.template-download td .error{ font-weight:bold; color:'. $WPFCOLOR_42 .'; }

#wpf-msg-box a{color:'. $WPFCOLOR_1 .';}
#wpf-msg-box a:hover{color:'. $WPFCOLOR_8 .';}
.wpf-msg-box-triangle-right.top{background-color:'. $WPFCOLOR_12 .';background:-moz-linear-gradient('. $WPFCOLOR_12 .');background:-o-linear-gradient('. $WPFCOLOR_12 .');background:linear-gradient('. $WPFCOLOR_12 .');}
.wpf-msg-box-triangle-right{color:'. $WPFCOLOR_1 .'!important;}
.wpf-msg-box-triangle-right.top:after{border-color:transparent '. $WPFCOLOR_12 .';}
.wpf-msg-box-triangle-right:after{border-color:'. $WPFCOLOR_12 .' transparent;}
.wpf-msg-box-triangle-right.error{background-color:'. $WPFCOLOR_42 .';background:-moz-linear-gradient('. $WPFCOLOR_42 .');background:-o-linear-gradient('. $WPFCOLOR_42 .');background:linear-gradient('. $WPFCOLOR_42 .');}
.wpf-msg-box-triangle-right.error:after{border-color:transparent '. $WPFCOLOR_42 .';}
.wpf-msg-box-triangle-right.success{background-color:'. $WPFCOLOR_31 .';background:-moz-linear-gradient('. $WPFCOLOR_31 .');background:-o-linear-gradient('. $WPFCOLOR_31 .');background:linear-gradient('. $WPFCOLOR_31 .');}
.wpf-msg-box-triangle-right.success:after{border-color:transparent '. $WPFCOLOR_31 .';}
@media screen and (max-width:600px) {
	#wpforo-wrap #wpforo-menu .wpf-menu{background-color:'. $WPFCOLOR_3 .';}
	#wpforo-wrap .wpfl-1 .wpforo-post .wpf-right .wpforo-post-content-top{border-top: 1px solid '. $WPFCOLOR_8 .';}
}

#wpforo-wrap.wpf-dark { color: '. $WPFCOLOR_15 .'; } 
#wpforo-wrap.wpf-dark .wpfcl-3{ color:'. $WPFCOLOR_15 .'!important; } 
#wpforo-wrap.wpf-dark .wpfcl-1{ color:'. $WPFCOLOR_15 .'!important; } 
#wpforo-wrap.wpf-dark .wpf-action{ color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpforo-post .wpf-right .wpforo-post-content p{ color:'. $WPFCOLOR_15 .'!important; }
#wpforo-wrap.wpf-dark #wpforo-menu li a { color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-2 .head-stat-lastpost, #wpforo-wrap.wpf-dark .wpfl-3 .head-stat-lastpost{ color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-2 .wpforo-last-post{ color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-2 .wpforo-topic-stat-lastpost, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-topic-stat-lastpost{ color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-2 .wpforo-forum-description, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-forum-description{ color: '. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .cat-title, #wpforo-wrap.wpf-dark .wpfl-2 .cat-title, #wpforo-wrap.wpf-dark .wpfl-3 .cat-title{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .wpforo-category div, #wpforo-wrap.wpf-dark .wpfl-2 .wpforo-category div, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-category div{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .wpforo-last-topic-date, #wpforo-wrap.wpf-dark .wpfl-2 .wpforo-last-topic-date, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-last-topic-date{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .wpforo-topic-stat-posts, #wpforo-wrap.wpf-dark .wpfl-1 .wpforo-topic-stat-views{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-2 .wpforo-topic-stat-posts, #wpforo-wrap.wpf-dark .wpfl-2 .wpforo-topic-stat-views{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-3 .wpforo-topic-stat-posts, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-topic-stat-views{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .wpforo-last-post-date, #wpforo-wrap.wpf-dark .wpfl-2 .wpforo-last-post-date, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-last-post-date{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .head-title, #wpforo-wrap.wpf-dark .wpfl-2 .head-title, #wpforo-wrap.wpf-dark .wpfl-3 .head-title{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .head-stat-posts, #wpforo-wrap.wpf-dark .wpfl-2 .head-stat-posts, #wpforo-wrap.wpf-dark .wpfl-3 .head-stat-posts{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .head-stat-views, #wpforo-wrap.wpf-dark .wpfl-2 .head-stat-views, #wpforo-wrap.wpf-dark .wpfl-3 .head-stat-views{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark .wpfl-1 .wpforo-post .wpf-left, #wpforo-wrap.wpf-dark .wpfl-2 .wpforo-post .wpf-left, #wpforo-wrap.wpf-dark .wpfl-3 .wpforo-post .wpf-left{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap .wpfl-1 .wpforo-post .wpf-right .wpforo-post-content-top, #wpforo-wrap .wpfl-2 .wpforo-post .wpf-right .wpforo-post-content-top, #wpforo-wrap .wpfl-3 .wpforo-post .wpf-right .wpforo-post-content-top{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap .wpfl-3 .wpforo-forum-stat-questions, #wpforo-wrap .wpfl-3 .wpforo-forum-stat-answers, #wpforo-wrap .wpfl-3 .wpforo-forum-stat-posts{ color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark #wpforo-stat-header{ color: '. $WPFCOLOR_15 .'; border-bottom: 1px solid '. $WPFCOLOR_7 .';}
#wpforo-wrap.wpf-dark #wpforo-stat-body .wpf-last-info p.wpf-topic-icons{border-top: 1px dotted '. $WPFCOLOR_7 .';}
#wpforo-wrap.wpf-dark .wpf-button{ color:'. $WPFCOLOR_15 .'!important; }
#wpforo-wrap.wpf-dark .wpf-topic-create .wpf-subject{background-color:'. $WPFCOLOR_2 .'}
#wpforo-wrap.wpf-dark #wpf-topic-create{background-color:'. $WPFCOLOR_9 .'}
#wpforo-wrap.wpf-dark #wpf-topic-create div.mce-toolbar-grp{background-color:'. $WPFCOLOR_9 .'}
#wpforo-wrap.wpf-dark .wpf-post-create .wpf-subject{background-color:'. $WPFCOLOR_2 .'}
#wpforo-wrap.wpf-dark #wpf-post-create{background-color:'. $WPFCOLOR_9 .'}
#wpforo-wrap.wpf-dark #wpf-post-create div.mce-toolbar-grp{background-color:'. $WPFCOLOR_9 .'}
#wpforo-wrap.wpf-dark input[type="submit"], #wpforo-wrap.wpf-dark input[type="button"]{color:'. $WPFCOLOR_15 .'!important;}
#wpforo-wrap.wpf-dark input[type="text"], #wpforo-wrap.wpf-dark input[type="password"], #wpforo-wrap.wpf-dark input[type="email"], #wpforo-wrap.wpf-dark textarea, #wpforo-wrap.wpf-dark select{color:'. $WPFCOLOR_3 .';background:'. $WPFCOLOR_2 .'}
#wpforo-wrap.wpf-dark input[type="text"].wpf-search-field{background-color:transparent; color:'. $WPFCOLOR_15 .'; }
#wpforo-wrap.wpf-dark #wpforo-stat-body{border:none;}
#wpforo-wrap.wpf-dark .wpforo-members-content table tr{ background-color:'. $WPFCOLOR_9 .'!important; }
';


