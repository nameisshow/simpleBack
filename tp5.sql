/*
Navicat MySQL Data Transfer

Source Server         : zhangbo
Source Server Version : 50720
Source Host           : 47.94.251.11:3306
Source Database       : tp5

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2018-04-27 15:58:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tp_button`
-- ----------------------------
DROP TABLE IF EXISTS `tp_button`;
CREATE TABLE `tp_button` (
  `button_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '按钮主键id',
  `button_name` varchar(15) NOT NULL DEFAULT '' COMMENT '按钮名',
  `button_event` varchar(12) NOT NULL DEFAULT '' COMMENT '按钮对应的js事件',
  `button_type` varchar(32) NOT NULL DEFAULT '' COMMENT '按钮类型ajax，href，firame',
  `button_sort` int(11) NOT NULL DEFAULT '0' COMMENT '按钮排序',
  `button_desc` varchar(128) NOT NULL DEFAULT '' COMMENT '按钮的作用描述',
  PRIMARY KEY (`button_id`),
  UNIQUE KEY `tp_button_button_id_uindex` (`button_id`),
  UNIQUE KEY `tp_button_button_name_uindex` (`button_name`),
  UNIQUE KEY `tp_button_button_event_uindex` (`button_event`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='按钮表';

-- ----------------------------
-- Records of tp_button
-- ----------------------------
INSERT INTO `tp_button` VALUES ('1', '添加', 'add', 'iframe', '0', '这是添加按钮');
INSERT INTO `tp_button` VALUES ('2', '修改', 'upd', 'iframe', '1', '这是修改按钮啦啦啦');
INSERT INTO `tp_button` VALUES ('3', '删除', 'del', 'ajax', '2', '这是删除按钮JoJo我不做人啦');
INSERT INTO `tp_button` VALUES ('4', '审核', 'isok', 'ajax', '3', '');
INSERT INTO `tp_button` VALUES ('5', '分配按钮', 'assignButton', 'iframe', '4', '');
INSERT INTO `tp_button` VALUES ('6', '屏蔽', 'isno', 'ajax', '5', '');
INSERT INTO `tp_button` VALUES ('8', '开启', 'open', 'ajax', '6', '');
INSERT INTO `tp_button` VALUES ('10', '关闭', 'close', 'ajax', '7', '');
INSERT INTO `tp_button` VALUES ('12', '模块权限', 'prevm', 'iframe', '8', '');
INSERT INTO `tp_button` VALUES ('14', '按钮权限', 'prevb', 'iframe', '9', '奥赛');

-- ----------------------------
-- Table structure for `tp_module`
-- ----------------------------
DROP TABLE IF EXISTS `tp_module`;
CREATE TABLE `tp_module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名',
  `module_pid` int(11) NOT NULL DEFAULT '0' COMMENT '模块父id',
  `module_code` varchar(1024) NOT NULL DEFAULT '' COMMENT '0-12-23类似这样的层级顺序拼接的模块id',
  `module_sort` int(11) NOT NULL DEFAULT '0' COMMENT '模块排序',
  `button_id` varchar(1024) NOT NULL DEFAULT '' COMMENT '1,2,3,4类似这样以逗号拼接的按钮id',
  `module_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '/idnex/Index/index类似这样的模块链接',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module_module_id_uindex` (`module_id`),
  UNIQUE KEY `module_module_name_uindex` (`module_name`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COMMENT='模块表';

-- ----------------------------
-- Records of tp_module
-- ----------------------------
INSERT INTO `tp_module` VALUES ('1', '系统管理', '0', '0', '0', '', '');
INSERT INTO `tp_module` VALUES ('2', '权限管理', '1', '1-2', '0', '', '');
INSERT INTO `tp_module` VALUES ('3', '按钮管理', '2', '1-2-3', '0', '1,2,3', '/Index/button');
INSERT INTO `tp_module` VALUES ('4', '模块管理', '2', '1-2-4', '1', '1,2,3,5', '/Index/module');
INSERT INTO `tp_module` VALUES ('5', '角色管理', '2', '1-2-5', '2', '1,2,3,12,14', '/Index/role');
INSERT INTO `tp_module` VALUES ('6', '管理员管理', '2', '1-2-6', '3', '1,2,3', '/Index/admins');
INSERT INTO `tp_module` VALUES ('7', '信息管理', '0', '0', '4', '', '');
INSERT INTO `tp_module` VALUES ('8', '文章管理', '7', '7-8', '5', '', '');
INSERT INTO `tp_module` VALUES ('9', '文章列表', '8', '7-8-9', '6', '1,2,3', '/article/articleList');
INSERT INTO `tp_module` VALUES ('116', '原创文章ss', '8', '7-8-116', '122', '1,2,3,4,6', '/article/articlesss');
INSERT INTO `tp_module` VALUES ('117', 'SEO', '1', '1-117', '0', '', '');
INSERT INTO `tp_module` VALUES ('119', 'SEO管理', '117', '1-117-119', '0', '2', '/Index/seo');
INSERT INTO `tp_module` VALUES ('120', '品牌故事', '8', '7-8-120', '0', '', '/article/asd');
INSERT INTO `tp_module` VALUES ('122', '平台文章', '8', '7-8-122', '0', '', '/article/lalala');
INSERT INTO `tp_module` VALUES ('124', '分类管理', '7', '7-124', '0', '', '');
INSERT INTO `tp_module` VALUES ('126', '分类列表', '124', '7-124-126', '0', '', '/category/lalal');

-- ----------------------------
-- Table structure for `tp_role`
-- ----------------------------
DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE `tp_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(32) NOT NULL DEFAULT '' COMMENT '角色名称',
  `role_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `module_id` varchar(1024) NOT NULL DEFAULT '' COMMENT '12,34,55类似这样的模块id拼接的字符串',
  `button_json` varchar(1024) NOT NULL DEFAULT '' COMMENT '由模块约束的json字符串',
  `addtime` int(11) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `tp_role_role_id_uindex` (`role_id`),
  UNIQUE KEY `tp_role_role_name_uindex` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of tp_role
-- ----------------------------
INSERT INTO `tp_role` VALUES ('1', '超级管理员', '', '1,2,3,4,5,6,117,119,7,8,9', '{\"3\":[\"1\",\"2\",\"3\"],\"4\":[\"1\",\"2\",\"3\",\"5\"],\"5\":[\"1\",\"2\",\"3\",\"12\",\"14\"],\"6\":[\"1\",\"2\",\"3\"],\"119\":[\"2\"]}', '1511703079');
INSERT INTO `tp_role` VALUES ('4', '财务ss', '财务是最漂亮的', '7,8,9,116', '{\"116\":[\"3\",\"4\",\"1\",\"2\",\"6\"]}', '1512544821');

-- ----------------------------
-- Table structure for `tp_user`
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '32位md5密码',
  `salt` char(6) NOT NULL DEFAULT '' COMMENT '6位随机字符密码盐值',
  `relaname` varchar(64) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '本次登录id',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '本次登录时间',
  `last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `tp_user_user_id_uindex` (`user_id`),
  UNIQUE KEY `tp_user_username_uindex` (`username`),
  UNIQUE KEY `tp_user_mobile_uindex` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES ('1', '张波', '6f3b8ded65bd7a4db11625ac84e579bb', 'abcdef', 'zhangbo', '13026035350', '1', '41', '116.226.220.4', '1524815833', '119.165.155.25', '1524815803', '1511703079');
INSERT INTO `tp_user` VALUES ('2', '刘栋', '196f09e9bd34168cbbb6481a5db67226', 'wnffto', '刘栋', '13026035351', '4', '6', '36.33.37.214', '1512909127', '36.33.37.214', '1512907210', '1512546110');
