/*
SQLyog Professional
MySQL - 5.7.30-log : Database - prettybag
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `admin_member` */

DROP TABLE IF EXISTS `admin_member`;

CREATE TABLE `admin_member` (
  `mem_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(60) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `password` varchar(65) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(4) NOT NULL DEFAULT '' COMMENT '随机安全码',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `login_time` timestamp NULL DEFAULT NULL COMMENT '最后一次登陆时间',
  PRIMARY KEY (`mem_id`),
  KEY `IDX_NAME` (`name`),
  KEY `IDX_MOBLIE` (`mobile`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=50002 DEFAULT CHARSET=utf8mb4;

/*Data for the table `admin_member` */

insert  into `admin_member`(`mem_id`,`name`,`nickname`,`mobile`,`email`,`avatar`,`sex`,`status`,`password`,`salt`,`add_time`,`update_time`,`login_time`) values 
(50001,'Victoria','Victoria','18825071640','849376723@qq.com','',0,1,'$2y$10$QKCFbwVstP/SBYnvwZZYk.AyU0oKy0CBJ6JtnLgAvBu4BAXhRikOy','vict','2021-03-31 09:36:54',NULL,'2021-09-09 10:55:45');

/*Table structure for table `attachment` */

DROP TABLE IF EXISTS `attachment`;

CREATE TABLE `attachment` (
  `attach_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '文件名称md5',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '文件类型',
  `cate` varchar(10) NOT NULL DEFAULT '' COMMENT '站点',
  `size` mediumint(9) NOT NULL DEFAULT '0' COMMENT '大小',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`attach_id`),
  UNIQUE KEY `IDX_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4;

/*Data for the table `attachment` */

insert  into `attachment`(`attach_id`,`name`,`type`,`cate`,`size`,`add_time`) values 
(1,'b7b4130680d0769bf3092e91d2ed90e5','jpg','product',102298,NULL),
(2,'57908c1154d44285e17978b33fbfaaeb','jpg','product',55786,NULL),
(3,'8d61f2e2f0b4f9b75cfd1f8bfc443c67','jpg','product',125518,NULL),
(4,'dec8eed0602af33ca4b91186531362bb','jpg','product',86560,NULL),
(5,'48f72052141279846b99a9a23ab5839f','jpg','product',125500,NULL),
(6,'31eda5c9cc675adf54ff3ad14c88a4ff','jpg','product',78456,NULL),
(7,'26729bfd83dd3df960065b6fdccc401b','jpg','product',132009,NULL),
(8,'5016e69f4a4b165b97ca0c648851d1db','jpg','product',68980,NULL),
(9,'3299528ca81b1aac91724b05cc6dfbf7','jpg','product',110031,NULL),
(10,'ed280a0ea3cc38f3cbbc747acfbef47d','png','introduce',35,NULL),
(11,'a4ae218a384ca0e5030ae1556f7141f0','jpg','introduce',9735,NULL),
(12,'d6661741ef0b8590f5cae6cc2b31070a','jpg','introduce',7922,NULL),
(13,'ca866ca0eaa9b7230a6a37ea67ad80e4','jpg','introduce',8279,NULL),
(14,'e31f44e0261ea409251a029acdabfc54','jpg','introduce',7930,NULL),
(15,'1b0330bba21b6dd3d67a0bdfa17b232c','jpg','introduce',10771,NULL),
(16,'fb024bb3b37cb3ade9ce4cda46d6593d','jpg','introduce',36396,NULL),
(17,'0e1d9334b9299154404fa00fb8a5dfb9','jpg','introduce',17516,NULL),
(18,'c39483382dfc4fcce64d25b1fba16077','jpg','introduce',17851,NULL),
(19,'a577c83814f5723c3b4ff7d733e1cbcf','jpg','introduce',19132,NULL),
(20,'59de090225c7bae6be8bcd7aa825f669','jpg','introduce',46874,NULL),
(21,'38fb254eee55bed0dd09778abbd9d671','jpg','introduce',17808,NULL),
(22,'478cac3114a0ad5f893f1487afd94518','jpg','introduce',22322,NULL),
(23,'aecb3a3de6b602dd1ad0fa390c5b848f','jpg','introduce',19333,NULL),
(24,'c3c4c88c3a58ccc7d019ba5a3d3a2437','jpg','introduce',17303,NULL),
(25,'d47a43ae402c103c3c810ef17fa6fa2a','jpg','introduce',15834,NULL),
(26,'f2e2cf3d44a8d0a823ce7bb0d351bc8f','jpg','introduce',17862,NULL),
(27,'a83d73e4578dbaf7e1a246b235f4bc72','jpg','introduce',20428,NULL),
(28,'53f1e6b8570fd969a28aec5fe28513ea','jpg','introduce',17527,NULL),
(29,'911b67973e765b3202dcf2947255a747','jpg','introduce',17413,NULL),
(30,'0278b26e82ad985abca93c821f14c29e','jpg','introduce',17967,NULL),
(31,'9c628e2261240878f3de9ab3e7cb033b','jpg','introduce',17284,NULL),
(32,'93aa08cf1f4411d690ccb09c3040648a','jpg','introduce',19329,NULL),
(33,'d50bc3abe5bce66a36422ad6183b7ded','jpg','introduce',17737,NULL),
(34,'1ae44d69b13c14d5e6e9a10361622832','jpg','introduce',19790,NULL),
(35,'60c5079d14c1dda58c8b4530d3aeefcf','jpg','introduce',16076,NULL),
(36,'7d504f8dbd9411ad6bb2e105dd95722e','jpg','introduce',17464,NULL),
(37,'b11c863efaa5af2502ceecc040e50587','jpg','introduce',16391,NULL),
(38,'1adffb279393958aec4ac648e57a13cc','jpg','introduce',19085,NULL),
(39,'49439c241ef546eb3bb604871a4d0c54','jpg','introduce',18484,NULL),
(40,'defff36d60f9ba9be0d6c723e8e35789','jpg','introduce',20357,NULL),
(41,'15f5d559a6c119b9fe6abf27b0451117','jpg','introduce',17667,NULL),
(42,'e37ed36c469680a3c02398cb0372a571','jpg','introduce',17107,NULL),
(43,'eafcd13a380a5a6247fd7a01cca041e4','jpg','introduce',23418,NULL),
(44,'18726ee5a5047cdc5785698727ee38dc','jpg','introduce',17462,NULL),
(45,'ed93f7868fd94fe54871a55edad9be78','jpg','introduce',17262,NULL),
(46,'4976c33b9f7f8f6904637ab1075dcdcf','jpg','introduce',18392,NULL),
(47,'4df2c20770ffcb7804003116d8f7dffb','jpg','introduce',16497,NULL),
(48,'785470014522514be8d8ca5c42cf2e02','jpg','introduce',15186,NULL),
(49,'68d8deba17065989c6fa737a9d2942ba','jpg','introduce',15633,NULL),
(50,'182d9eb88842307e9acefc5445729172','jpg','introduce',17894,NULL),
(51,'8ed1f799985cc4a56fa601f21860b63f','jpg','introduce',18737,NULL),
(52,'851734412b4a3d86fd4b91318fb0aff8','jpg','introduce',22224,NULL),
(53,'ac3f179f979c4e6a01da078fd5832e43','jpg','introduce',19067,NULL),
(54,'08ef680a52cb503f9151960bfc385b7a','jpg','introduce',17963,NULL),
(55,'47cf7efcb5cbef8a50adce072bee0614','jpg','introduce',19098,NULL),
(56,'34a6b8412c786a39598c7e3ced86a41a','jpg','introduce',16453,NULL),
(57,'70956f7732d2a8496ba52279b59f7590','jpg','introduce',16421,NULL),
(58,'8fc32beaa2aa6227af7acb504fff2fb5','jpg','introduce',16307,NULL),
(59,'ed17ac456038874d2d291d5ca98b294b','jpg','introduce',17098,NULL),
(60,'8d0d9af77f081f520c0223ccb0d8fa65','jpg','introduce',54921,NULL),
(61,'7824791bc325e4cbfe4a6ab1e9788021','jpg','introduce',98685,NULL),
(62,'3dda146cc9a3bf18131b99beb0f713d5','jpg','introduce',128097,NULL),
(63,'f01b4780cf00cbbad14884a6ab5e6f73','jpg','introduce',37194,NULL),
(64,'4763c114cc4e6679dab405bb6c91ca16','jpg','introduce',83873,NULL),
(65,'5bfa550c3dbe5fd008320cd78b358f65','jpg','introduce',64179,NULL),
(66,'acbbdfaa5962e46ee24976e332a3fb7b','jpg','introduce',92220,NULL),
(67,'42ba1c63620b782fa06014ad2e9a42c0','jpg','introduce',52726,NULL),
(68,'cd90f492646e0995c2195fb63943fd98','jpg','introduce',41266,NULL),
(69,'ab6dfda2af6b82b8ecb4a3348879aef9','jpg','introduce',101319,NULL),
(70,'031cec3e5e10549b022aceb2529baba3','jpg','introduce',104891,NULL),
(71,'cf1cf30580f9bc627b50a23e1e366150','jpg','introduce',11656,NULL),
(72,'3e27088b395e5f42162177f645d716ec','jpg','introduce',14935,NULL),
(73,'aa367a9ff6cd32fa26719782c2c9aaf9','jpg','introduce',9250,NULL),
(74,'974008799ef4eba065421159022b4db5','jpg','introduce',95735,NULL),
(75,'bab1a948a914ed61ee9c9c6b14eb157b','jpg','introduce',69249,NULL),
(76,'89e8740f1fbf73eb125930aaa442dac1','jpg','introduce',155829,NULL),
(77,'330fde833dd5afc0b9903e74d3e97f77','jpg','introduce',117567,NULL),
(78,'0b50017887fbf98202de7009facdac46','jpg','introduce',72781,NULL),
(79,'3b53da8b0762ec1b3cc0cf59a71d02a1','jpg','introduce',61414,NULL),
(80,'a2dce8b006259e39fbaaefdb016641a3','jpg','introduce',56052,NULL),
(81,'867f8ef2f688c6390d5b6f2e1f52cafb','jpg','introduce',151644,NULL),
(82,'319aabc2dbb8d91e46d64b0881d47736','jpg','introduce',86239,NULL),
(83,'1c2ec24faa8919a25f15f27e729d341d','jpg','introduce',61758,NULL),
(84,'48aa11179fd29fdeebb5f25365aa3a7d','jpg','introduce',70362,NULL),
(85,'341833e9dbdce023cc304a6f12933b1b','jpg','introduce',63921,NULL),
(86,'a4acd9a77d70c4f46e098e955245d5db','jpg','product',74629,NULL),
(87,'5f69a6a5ba02c283824071249432115b','jpg','product',96266,NULL),
(88,'55772d55fbbaf3ea92ce443abba90bc4','jpg','product',61063,NULL),
(89,'3823670de55f3ee7ebcbcaf14bc338eb','jpg','product',122037,NULL),
(90,'0f16cfbd12fb1563becda24a824f5690','jpg','product',128207,NULL),
(91,'1c6941fcadf4238eb001c97c45da682e','jpg','product',70053,NULL),
(92,'df894c519728128945f7ceea0ab057b5','jpg','product',102844,NULL),
(93,'2c33ae5f7c52a0b2a6d9a6da7c9bbda1','jpg','product',73677,NULL),
(94,'387f3cd1ab496f06bd49d2427691782b','jpg','introduce',110638,NULL),
(95,'486227de1d16b32c1894ffe9d088c87a','jpg','introduce',39256,NULL),
(96,'68c5db29218216fc4eee52fc6057cde3','jpg','introduce',101367,NULL),
(97,'5a513f5a4b5732fdd324f067fc4486ef','jpg','introduce',67177,NULL),
(98,'97b2d58055111659b49d7ca6abff8846','jpg','introduce',91662,NULL),
(99,'31aac2b6c7c7e119401111b1c3ccf473','jpg','introduce',51794,NULL),
(100,'121a83cea0f95635476452d4ea5378a8','jpg','introduce',51211,NULL),
(101,'cefb71f15a1463055c17c12ef0a41eec','jpg','introduce',99632,NULL),
(102,'4b452fcc358709bea757db6034a85b6a','jpg','introduce',110487,NULL),
(103,'8d696660ea4abc9db2c1a04197d5fa3e','jpg','introduce',12850,NULL),
(104,'a6c0965e5f6b815016e55a933076127d','jpg','introduce',12913,NULL),
(105,'f5241b9d5f037307d6cb3b4f53476d9f','jpg','introduce',13005,NULL),
(106,'71ef92963c5794bf2693f509c7a2ba6f','jpg','introduce',98586,NULL),
(107,'346b053bd87560240fcd83141bfc81a4','jpg','introduce',77957,NULL),
(108,'0a1d6f8b46724c076a27daafb0294032','jpg','introduce',68487,NULL),
(109,'54464476d1c60aefffe7e9fe45cda1f8','jpg','introduce',31041,NULL),
(110,'9d20b739c062468f5595888ac12faaf1','jpg','introduce',140431,NULL),
(111,'62c0136e7383fae462ae3b3acdc28bda','jpg','introduce',56088,NULL),
(112,'32594066bf595d8efb1cb1afc6cead3e','jpg','introduce',54042,NULL),
(113,'be130847b64ecae58550ab92fdc51290','jpg','introduce',32012,NULL),
(114,'3a196885f49e801a12ee02037607f753','jpg','introduce',124550,NULL),
(115,'426fcb4ad77faba9cb6216a881ace906','jpg','introduce',72812,NULL),
(116,'8da5d34cf6cfb1b02ca36892bb34806e','jpg','introduce',60326,NULL),
(117,'eaebb03a91f992724473140ca60d6f31','jpg','introduce',34721,NULL),
(118,'7f9a4ef0096584459ea0e0e0b220b8cc','jpg','introduce',133574,NULL),
(119,'a53ba3066557040c6f9f88957f7752ac','jpg','product',85027,NULL),
(120,'b6879c28ab5dcaa23512dcf5941a91a9','jpg','product',81604,NULL),
(121,'bf2cfcae80d838f616133a2fc35f656f','jpg','product',87861,NULL),
(122,'dff06b62a68887734ac3bd6c5f3148fe','jpg','product',92502,NULL),
(123,'47db35bf3d8025222f5cfd1f9e8a52bf','jpg','product',74948,NULL),
(124,'ad2f4f4113aa661bec33482b0c349cef','jpg','introduce',129810,NULL),
(125,'515207610b800df2ea75a80b0b3e5891','jpg','introduce',91749,NULL),
(126,'749896df985c7be023d61cc9a6eed6ee','jpg','introduce',71054,NULL),
(127,'14c1b36568942248323388621b44d872','jpg','introduce',78908,NULL),
(128,'a78c6c0a925b6bac92cebabb6da88dce','jpg','introduce',82389,NULL),
(129,'06ac8d2af40a119ec83591db2e84d6c0','jpg','introduce',61189,NULL),
(130,'9c2927c14a4f0ee23cb8830d32a5389e','jpg','introduce',80864,NULL),
(131,'42016a08448b6021f7cdf119d1468a9a','jpg','product',34753,NULL),
(132,'95b8e1eba5ecf68a73f997917511f4cd','jpg','product',106836,NULL),
(133,'06b14b9d1a2a2b198427c98632dda747','jpg','product',115907,NULL),
(134,'a15f09655c87e969f87bab3f76ce621f','jpg','product',105298,NULL),
(135,'0caf5678d97a9e95810fb96c55f07957','jpg','product',107619,NULL),
(136,'eef83674bc687f422a0ae5769ed09c59','png','product',472034,NULL),
(137,'b04d6e109bc6cf191363028b23d6d563','jpg','product',82025,NULL),
(138,'1a27e9053669c926e76aef73d33c62db','jpg','introduce',118615,NULL),
(139,'435bca101b3d02ca6a150007e91a6085','jpg','introduce',128992,NULL),
(140,'36f3548b5779ed6c35c36b62c4a0f007','jpg','introduce',117311,NULL),
(141,'fadae45da49c6484fd2aecba09fd83b9','jpg','introduce',107705,NULL),
(142,'997374f272988fa1843c8c368a593c49','jpg','introduce',80376,NULL),
(143,'2e6d3ee5df8570c712179d295a618d8e','jpg','introduce',53719,NULL),
(144,'defc827a86f57c02b6237c90ee680689','jpg','introduce',70672,NULL),
(145,'89f53cd4e65766e8b0e8c3f6feb6a566','jpg','introduce',40300,NULL),
(146,'fdcb67e216adb6b1043a3088a0e69be7','jpg','introduce',36900,NULL),
(147,'1ffdaeb3e94512ff849098145b59b340','jpg','introduce',108246,NULL),
(148,'fcae771653668c3f98ab2b897d96fcb8','jpg','introduce',126384,NULL),
(149,'b0e9d103617f036384d321b3c123b728','jpeg','avatar',28207,'2021-08-12 14:20:31'),
(150,'e2252e9d35915ad0a2cebf5a153edded','jpeg','avatar',87076,'2021-08-12 14:36:17');

/*Table structure for table `attribute` */

DROP TABLE IF EXISTS `attribute`;

CREATE TABLE `attribute` (
  `attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(120) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`attr_id`),
  UNIQUE KEY `UNI_NAME` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Data for the table `attribute` */

insert  into `attribute`(`attr_id`,`name`,`status`) values 
(1,'尺码',0),
(2,'颜色分类',0),
(3,'包装规格',0);

/*Table structure for table `attribute_language` */

DROP TABLE IF EXISTS `attribute_language`;

CREATE TABLE `attribute_language` (
  `attr_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性值ID',
  `lan_id` varchar(4) NOT NULL DEFAULT '' COMMENT '语言ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '翻译值',
  PRIMARY KEY (`attr_id`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `attribute_language` */

/*Table structure for table `attrvalue` */

DROP TABLE IF EXISTS `attrvalue`;

CREATE TABLE `attrvalue` (
  `attv_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '属性值名称',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`attv_id`),
  UNIQUE KEY `UNI_NAME` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

/*Data for the table `attrvalue` */

insert  into `attrvalue`(`attv_id`,`name`,`status`) values 
(1,'可调节',0),
(2,'炭灰',0),
(3,'紫绒原色',0),
(4,'枣红',0),
(5,'青绒原色',0),
(6,'深咖+青绒原色',0),
(7,'紫绒+青绒原色（少量现货）',0),
(8,'深灰+浅灰色',0),
(9,'红色（预售1月23日发货）',0),
(10,'浅灰色',0),
(11,'50支',0),
(12,'1000支',0),
(13,'500支',0),
(14,'100支',0),
(15,'2000支',0),
(16,'200支',2),
(17,'3000支',2);

/*Table structure for table `attrvalue_language` */

DROP TABLE IF EXISTS `attrvalue_language`;

CREATE TABLE `attrvalue_language` (
  `attv_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性值ID',
  `lan_id` varchar(4) NOT NULL DEFAULT '0' COMMENT '语言码',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '翻译值',
  PRIMARY KEY (`attv_id`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `attrvalue_language` */

insert  into `attrvalue_language`(`attv_id`,`lan_id`,`name`) values 
(16,'de','200 Stück'),
(16,'en','200 pieces'),
(16,'es','200'),
(16,'fr','200'),
(16,'jp','200本'),
(16,'zht','200支'),
(17,'de','3000 Stück'),
(17,'en','3000 pieces'),
(17,'es','3000'),
(17,'fr','3 000'),
(17,'jp','3000本です'),
(17,'zht','3000支');

/*Table structure for table `cart` */

DROP TABLE IF EXISTS `cart`;

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `sku_id` int(11) NOT NULL DEFAULT '0' COMMENT 'SKUID',
  `quantity` smallint(6) NOT NULL DEFAULT '0' COMMENT '数量',
  `checked` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否选中',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '记录时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`cart_id`),
  UNIQUE KEY `IDX_UNI` (`mem_id`,`sku_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

/*Data for the table `cart` */

insert  into `cart`(`cart_id`,`mem_id`,`sku_id`,`quantity`,`checked`,`add_time`,`update_time`) values 
(1,5,2,1,1,NULL,NULL),
(4,10002,7,1,1,'2021-08-09 15:24:12',NULL),
(10,10002,2,5,1,'2021-08-10 15:02:20','2021-08-10 15:20:05'),
(11,10001,2,1,0,'2021-08-12 15:21:09',NULL);

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `cate_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parent_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8mb4;

/*Data for the table `category` */

insert  into `category`(`cate_id`,`parent_id`,`name`,`status`) values 
(22,0,'女装',0),
(33,0,'男装',0),
(46,37,'天花板灯',0),
(47,37,'壁灯',0),
(48,37,'台灯&落地灯',0),
(49,37,'吊扇灯&配件',0),
(50,37,'户外灯',0),
(51,37,'新型小灯',0),
(52,37,'商业照明',0),
(53,37,'灯具配件',0),
(54,37,'灯泡',0),
(55,46,'吸顶灯',0),
(56,46,'枝形吊灯',0),
(57,46,'吊灯',0),
(58,46,'轨道灯',0),
(59,46,'嵌入式灯',0),
(60,46,'岛灯',0),
(61,46,'桌球灯',0),
(62,47,'壁灯',0),
(63,47,'浴室镜前灯',0),
(64,47,'图片灯',0),
(65,47,'橱柜灯',0),
(66,47,'小夜灯',0),
(67,47,'阶梯灯',0),
(68,47,'聚光灯',0),
(69,48,'台灯',0),
(70,48,'书桌灯',0),
(71,48,'地灯',0),
(72,48,'台灯套装',0),
(73,48,'灯罩',0),
(74,49,'吊扇灯',0),
(77,50,'装饰投影灯',0),
(78,50,'防洪灯',0),
(79,50,'地灯',0),
(80,50,'景观照明',0),
(81,50,'庭院灯',0),
(82,50,'柱灯',0),
(83,50,'绳索灯',0),
(84,50,'桌面照明',0),
(85,50,'火把灯',0),
(86,50,'雨伞灯',0),
(87,50,'灯具配件',0),
(88,51,'黑光灯',0),
(89,51,'夹子书灯',0),
(90,51,'迪斯科舞灯',0),
(91,51,'光纤灯',0),
(92,51,'模型灯',0),
(93,51,'熔岩灯',0),
(94,51,'led绳索灯',0),
(95,51,'霓虹灯',0),
(96,51,'灯笼纸灯',0),
(97,51,'纽扣灯',0),
(98,52,'海湾照明',0),
(99,52,'紧急照明',0),
(100,52,'紧急出口照明',0),
(101,52,'街道路灯',0),
(102,52,'频闪灯',0),
(103,53,'天花板底盘装饰',0),
(104,53,'灯罩补件',0),
(105,53,'灯饰扣配件',0),
(106,53,'灯具吊链',0),
(107,53,'灯具吊杆',0),
(108,53,'低压转换器',0),
(109,53,'橱柜吸顶配件',0),
(110,54,'荧光灯泡',0),
(111,54,'荧光灯管',0),
(112,54,'卤素灯泡',0),
(113,54,'高强度放电灯泡',0),
(114,54,'白炽灯泡',0),
(115,54,'氪氙灯泡',0),
(116,54,'LED灯泡',0),
(117,54,'黑光灯泡',0),
(118,58,'成套工具',0),
(119,58,'灯头',0),
(120,58,'吊灯',0),
(121,58,'轨道',0),
(122,58,'配件',0),
(123,58,'连接器',0),
(124,59,'外壳',0),
(125,59,'凹槽',0),
(126,59,'灯具及装饰套件',0),
(135,80,'夹板灯',0),
(136,80,'模型灯',0),
(137,80,'插地灯',0),
(138,80,'阶梯灯',0),
(139,80,'路径灯',0),
(140,80,'聚光灯',0),
(141,81,'吸顶灯',0),
(142,81,'单吊',0),
(143,81,'壁灯',0),
(144,84,'灯笼式',0),
(145,84,'台灯式',0),
(146,87,'灯油',0),
(147,87,'低压转换器',0),
(148,87,'柱灯配件',0),
(149,33,'外套夹克',0),
(150,22,'连衣裙',0),
(151,150,'日常连衣裙',0),
(152,150,'派对连衣裙',0),
(153,150,'酒会礼服连衣裙',0),
(154,150,'正式连衣裙',0),
(155,150,'职场连衣裙',0),
(156,150,'婚礼连衣裙',0),
(157,22,'上衣，T恤&衬衫',0),
(158,157,'衬衫&排扣衬衫',0),
(159,157,'紧身连体衣',0),
(160,157,'POLO衫',0),
(161,157,'吊带背心',0),
(162,157,'T恤',0),
(163,157,'长款上衣',0),
(164,157,'马甲上衣',0),
(165,22,'毛衣',0),
(166,165,'开衫',0),
(167,165,'套头毛衣',0),
(168,165,'披肩',0),
(169,165,'马甲针织衫',0),
(170,22,'卫衣',0),
(171,22,'牛仔裤',0),
(172,22,'长裤',0),
(173,172,'日常长裤',0),
(174,172,'职场长裤',0),
(175,172,'晚装长裤',0),
(176,22,'半身裙',0),
(177,176,'日常半身裙',0),
(178,176,'职场半身裙',0),
(179,176,'晚装半身裙',0),
(180,22,'短裤',0),
(181,180,'日常短裤',0),
(182,180,'牛仔短裤',0),
(183,22,'打底裤',0),
(184,22,'运动装',0),
(185,184,'运动带帽卫衣',0),
(186,184,'运动套头卫衣',0),
(187,184,'运动夹克',0),
(188,184,'运动套装',0),
(189,184,'运动衬衫&T恤',0),
(190,184,'运动长裤',0),
(191,184,'运动打底裤',0),
(192,184,'运动短裤',0),
(193,184,'运动半身裙',0),
(194,184,'运动裤裙',0),
(195,184,'运动打底类',0),
(196,184,'运动内衣裤',0),
(197,184,'运动袜子',0),
(198,22,'泳装&罩衫',0),
(199,198,'比基尼',0),
(200,199,'比基尼上装',0),
(201,199,'比基尼下装',0),
(202,199,'比基尼套装',0),
(203,198,'背心款泳装',0),
(204,198,'一件式泳装',0),
(205,198,'罩衫',0),
(206,198,'沙滩裤',0),
(207,198,'防晒泳衣',0),
(208,22,'内衣，睡衣&家居服',0),
(209,208,'内衣裤',0),
(210,209,'文胸',0),
(211,210,'胸贴',0),
(212,210,'日常文胸',0),
(213,210,'乳房切除文胸',0),
(214,210,'聚拢迷你文胸',0),
(215,210,'运动文胸',0),
(216,209,'内裤',0),
(217,216,'丁字裤',0),
(218,216,'比基尼式内裤',0),
(219,216,'三角内裤',0),
(220,216,'平角内裤',0),
(221,216,'低腰内裤',0),
(222,209,'情趣内衣',0),
(223,209,'吊带&背心内衣',0),
(224,209,'塑身衣',0),
(225,224,'提臀收腹内裤',0),
(226,224,'平角塑腿裤',0),
(227,224,'塑身上装',0),
(228,224,'束腰带',0),
(229,224,'塑身连体衣',0),
(230,224,'塑身裙',0),
(231,209,'打底内衬',0),
(232,231,'打底内衬连衣裙',0),
(233,231,'打底内衬下装',0),
(234,209,'紧身胸衣',0),
(235,209,'吊袜带&吊袜腰带',0),
(236,209,'内衣配件',0),
(237,236,'乳贴',0),
(238,236,'胸垫',0),
(239,236,'肩带',0),
(240,236,'肩带扣',0),
(241,208,'睡衣&家居服',0),
(242,241,'睡衣家居服上装',0),
(243,241,'睡衣家居服下装',0),
(244,241,'睡衣家居服套装',0),
(245,241,'睡袍',0),
(246,241,'睡裙',0),
(247,208,'保暖内衣',0),
(248,247,'保暖内衣上装',0),
(249,247,'保暖内衣下装',0),
(250,247,'保暖内衣套装',0),
(251,22,'连体长裤，连体短裤&背带裤',0),
(252,251,'连体长裤',0),
(253,251,'背带裤',0),
(254,251,'连体短裤',0),
(255,22,'外套，夹克&马甲',0),
(256,255,'羽绒服&派克大衣',0),
(257,255,'羊毛大衣&双排扣大衣',0),
(258,255,'风衣，雨衣&冲锋衣',0),
(259,258,'冲锋衣',0),
(260,258,'雨衣',0),
(261,258,'风衣',0),
(262,255,'轻便夹克',0),
(263,255,'休闲夹克',0),
(264,255,'牛仔夹克',0),
(265,255,'皮&仿皮外套夹克',0),
(266,255,'皮毛&仿皮毛外套夹克',0),
(267,255,'马甲',0),
(268,255,'运动类外套夹克',0),
(269,268,'羊绒外套夹克',0),
(270,268,'户外风衣冲锋衣',0),
(271,268,'防风&防雨外套夹克',0),
(272,22,'西装&西装套装',0),
(273,272,'西装',0),
(274,272,'西装套装',0),
(275,22,'短袜&丝袜',0),
(276,275,'休闲短袜',0),
(277,275,'裙装袜&长裤袜',0),
(278,275,'保暖护腿袜',0),
(279,275,'连裤袜',0),
(280,275,'丝袜',0),
(281,275,'家居保暖袜',0),
(282,275,'浅口隐形袜',0),
(283,33,'衬衫上衣',0),
(284,283,'T恤',0),
(285,283,'背心',0),
(286,283,'POLO衫',0),
(287,283,'亨利衫',0),
(288,283,'排扣衬衫',0),
(289,283,'正式衬衫',0),
(290,283,'礼服衬衫',0),
(291,33,'卫衣',0),
(292,33,'毛衣',0),
(293,292,'套头毛衣',0),
(294,292,'开衫',0),
(295,292,'Polo针织衫',0),
(296,292,'马甲针织衫',0),
(297,33,'牛仔裤',0),
(298,33,'长裤',0),
(299,298,'正装长裤',0),
(300,298,'休闲长裤',0),
(301,33,'短裤',0),
(302,301,'工装短裤',0),
(303,301,'牛仔短裤',0),
(304,301,'无褶短裤',0),
(305,301,'单褶短裤',0),
(306,33,'运动装',0),
(307,306,'运动带帽卫衣',0),
(308,306,'运动套头卫衣',0),
(309,306,'运动夹克',0),
(310,306,'运动T恤衬衫',0),
(311,306,'运动马甲',0),
(312,306,'运动长裤',0),
(313,306,'运动短裤',0),
(314,306,'运动田径服',0),
(315,306,'运动打底类',0),
(316,306,'运动内衣裤',0),
(317,306,'运动袜子',0),
(318,306,'运动丁字裤',0),
(319,33,'泳装',0),
(320,319,'短款泳裤',0),
(321,319,'中长款泳裤',0),
(322,319,'三角泳裤',0),
(323,319,'水母服',0),
(324,33,'西装&西装套装',0),
(325,324,'西装套装',0),
(326,324,'西装单件',0),
(327,326,'西装夹克',0),
(328,326,'西裤',0),
(329,324,'西装',0),
(330,324,'礼服西装',0),
(331,324,'西装马甲',0),
(332,33,'内衣',0),
(333,332,'三点式内裤',0),
(334,332,'拳击紧身内裤',0),
(335,332,'拳击宽松内裤',0),
(336,332,'三角内裤',0),
(337,332,'丁字裤',0),
(338,332,'塑身衣',0),
(339,332,'保暖内衣',0),
(340,339,'保暖内衣上装',0),
(341,339,'保暖内衣下装',0),
(342,339,'保暖内衣套装',0),
(343,332,'平角内裤',0),
(344,332,'打底衫',0),
(345,33,'袜子',0),
(346,345,'休闲袜',0),
(347,345,'裤装袜',0),
(348,33,'睡衣&家居服',0),
(349,348,'睡衣上衣',0),
(350,348,'睡衣下装',0),
(351,348,'睡衣套装',0),
(352,348,'睡袍',0),
(353,149,'运动类外套夹克',0),
(354,353,'户外风衣冲锋衣',0),
(355,353,'运动夹克',0),
(356,149,'羽绒服',0),
(357,149,'羊绒外套夹克',0),
(358,149,'皮&仿皮外套夹克',0),
(359,149,'轻便夹克',0),
(360,359,'棉质夹克',0),
(361,359,'牛仔夹克',0),
(362,359,'校队棒球服夹克',0),
(363,359,'冲锋衣',0),
(364,149,'风衣&雨衣',0),
(365,149,'马甲',0),
(366,149,'羊毛外套夹克',0),
(370,0,'窗户装饰',0),
(371,370,'窗帘',0),
(372,22,'套装',0);

/*Table structure for table `category_language` */

DROP TABLE IF EXISTS `category_language`;

CREATE TABLE `category_language` (
  `cate_id` smallint(6) NOT NULL,
  `lan_id` varchar(4) NOT NULL DEFAULT '',
  `name` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`cate_id`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `category_language` */

insert  into `category_language`(`cate_id`,`lan_id`,`name`) values 
(22,'de','Frauen tragen'),
(22,'en','Women\'s wear'),
(22,'es','Ropa de mujer'),
(22,'fr','Vêtements pour femmes'),
(22,'jp','婦人服'),
(22,'zht','女裝'),
(151,'de','Jeden Tag Kleid'),
(151,'en','Everyday dress'),
(151,'es','Vestido diario'),
(151,'fr','Robe de tous les jours'),
(151,'jp','日常のワンピース'),
(151,'zht','日常連衣裙'),
(370,'de','Dekoration des Fensters'),
(370,'en','Window decoration'),
(370,'es','Decoración de ventanas'),
(370,'jp','窓の飾り付け'),
(370,'zht','窗戶裝潢');

/*Table structure for table `country` */

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `code2` varchar(2) NOT NULL COMMENT '2字母代码',
  `code3` varchar(3) NOT NULL COMMENT '3字母代码',
  `dialing_code` smallint(6) NOT NULL COMMENT '电话区号',
  `name_cn` varchar(32) NOT NULL COMMENT '中文名称',
  `name_en` varchar(64) NOT NULL COMMENT '英文名称',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`code2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

/*Data for the table `country` */

insert  into `country`(`code2`,`code3`,`dialing_code`,`name_cn`,`name_en`,`sort`,`status`) values 
('AD','AND',376,'安道尔','Andorra',7,1),
('AE','ARE',971,'阿拉伯联合酋长国','United Arab Emirates',228,1),
('AF','AFG',93,'阿富汗','Afghanistan',2,1),
('AG','ATG',1268,'安提瓜和巴布达','Antigua and Barbuda',10,1),
('AI','AIA',1264,'安圭拉','Anguilla',9,1),
('AL','ALB',355,'阿尔巴尼亚','Albania',4,1),
('AM','ARM',374,'亚美尼亚','Armenia',12,1),
('AN','ANT',599,'安提瓜和巴布达','Netherlands Antilles',153,1),
('AO','AGO',244,'安哥拉','Angola',8,1),
('AR','ARG',54,'阿根廷','Argentina',11,1),
('AS','ASM',1684,'美属萨摩亚','American Samoa',6,1),
('AT','AUT',43,'奥地利','Austria',15,1),
('AU','AUS',61,'澳大利亚','Australia',14,1),
('AW','ABW',297,'阿鲁巴','Aruba',13,1),
('AX','ALA',358,'奥兰群岛','Aland Islands',3,1),
('AZ','AZE',994,'阿塞拜疆','Azerbaijan',16,1),
('BA','BIH',387,'波黑','Bosnia and Herzegovina',28,1),
('BB','BRB',1246,'巴巴多斯','Barbados',20,1),
('BD','BGD',880,'孟加拉国','Bangladesh',19,1),
('BE','BEL',32,'比利时','Belgium',22,1),
('BF','BFA',226,'布基纳国','Burkina Faso',35,1),
('BG','BGR',359,'保加利亚','Bulgaria',34,1),
('BH','BHR',973,'巴林','Bahrain',18,1),
('BI','BDI',257,'布隆迪','Burundi',36,1),
('BJ','BEN',229,'贝宁','Benin',24,1),
('BM','BMU',1441,'百慕大群岛','Bermuda',25,1),
('BN','BRN',673,'巴林','Brunei Darussalam',33,1),
('BO','BOL',591,'玻利维亚','Bolivia',27,1),
('BR','BRA',55,'巴西','Brazil',30,1),
('BS','BHS',1242,'巴哈马','Bahamas',17,1),
('BT','BTN',975,'不丹','Bhutan',26,1),
('BW','BWA',267,'博茨瓦纳','Botswana',29,1),
('BY','BLR',375,'白俄罗斯','Belarus',21,1),
('BZ','BLZ',501,'伯利兹','Belize',23,1),
('CA','CAN',1,'加拿大','Canada',39,1),
('CC','CCK',618,'科科斯(基林)群岛','Cocos (Keeling) Islands',47,1),
('CD','COD',243,'刚果(金)','Congo (Kinshasa)',51,1),
('CF','CAF',236,'中非','Central African Republic',42,1),
('CG','COG',242,'刚果','Congo (Brazzaville)',50,1),
('CH','CHE',41,'瑞士','Switzerland',210,1),
('CI','CIV',225,'科特迪瓦','Côte d\'Ivoire',54,1),
('CK','COK',682,'库克群岛','Cook Islands',52,1),
('CL','CHL',56,'智利','Chile',44,1),
('CM','CMR',237,'喀麦隆','Cameroon',38,1),
('CN','CHN',86,'中国','China',45,1),
('CO','COL',57,'哥伦比亚','Colombia',48,1),
('CR','CRI',506,'哥斯达黎加','Costa Rica',53,1),
('CU','CUB',53,'古巴','Cuba',56,1),
('CV','CPV',238,'佛得角','Cape Verde',40,1),
('CX','CXR',618,'圣诞岛','Christmas Island',46,1),
('CY','CYP',357,'塞浦路斯','Cyprus',57,1),
('CZ','CZE',420,'捷克','Czech Republic',58,1),
('DE','DEU',49,'德国','Germany',80,1),
('DJ','DJI',253,'吉布提','Djibouti',60,1),
('DK','DNK',45,'丹麦','Denmark',59,1),
('DM','DMA',1767,'多米尼克','Dominica',61,1),
('DO','DOM',1809,'多米尼加','Dominican Republic',62,1),
('DZ','DZA',213,'阿尔及利亚','Algeria',5,1),
('EC','ECU',593,'厄瓜多尔','Ecuador',63,1),
('EE','EST',372,'爱沙尼亚','Estonia',68,1),
('EG','EGY',20,'埃及','Egypt',64,1),
('EH','ESH',212,'西撒哈拉','Western Sahara',238,1),
('ER','ERI',291,'厄立特里亚','Eritrea',67,1),
('ES','ESP',34,'西班牙','Spain',203,1),
('ET','ETH',251,'埃塞俄比亚','Ethiopia',69,1),
('FI','FIN',358,'芬兰','Finland',73,1),
('FJ','FJI',679,'斐济','Fiji',72,1),
('FK','FLK',500,'福克兰群岛（马尔维纳斯群岛）','Falkland Islands (Malvinas)',70,1),
('FM','FSM',691,'密克罗尼西亚','Micronesia, Federated States of',140,1),
('FO','FRO',298,'法罗群岛','Faroe Islands',71,1),
('FR','FRA',33,'法国','France',74,1),
('GA','GAB',241,'加蓬','Gabon',77,1),
('GB','GBR',44,'英国','United Kingdom',229,1),
('GD','GRD',1473,'格林纳达','Grenada',85,1),
('GE','GEO',995,'格鲁吉亚','Georgia',79,1),
('GF','GUF',594,'法属圭亚那','French Guiana',75,1),
('GG','GGY',44,'根西岛','Guernsey',89,1),
('GH','GHA',233,'加纳','Ghana',81,1),
('GI','GIB',350,'直布罗陀','Gibraltar',82,1),
('GL','GRL',299,'格陵兰岛','Greenland',84,1),
('GM','GMB',220,'冈比亚','Gambia',78,1),
('GN','GIN',224,'几内亚','Guinea',90,1),
('GP','GLP',590,'瓜德罗普岛','Guadeloupe',86,1),
('GQ','GNQ',240,'赤几','Equatorial Guinea',66,1),
('GR','GRC',30,'希腊','Greece',83,1),
('GS','SGS',500,'南乔治亚与南三明治群岛','South Georgia and the South Sandwich Islands',201,1),
('GT','GTM',502,'危地马拉','Guatemala',88,1),
('GU','GUM',1671,'关岛','Guam',87,1),
('GW','GNB',245,'几比','Guinea-Bissau',91,1),
('GY','GUY',592,'圭亚那','Guyana',92,1),
('HK','HKG',852,'中国香港','Hong Kong, SAR China',96,1),
('HN','HND',504,'洪都拉斯','Honduras',95,1),
('HR','HRV',685,'克罗地亚','Croatia',55,1),
('HT','HTI',509,'海地','Haiti',93,1),
('HU','HUN',36,'匈牙利','Hungary',97,1),
('ID','IDN',62,'印尼','Indonesia',100,1),
('IE','IRL',353,'爱尔兰','Ireland',103,1),
('IL','ISR',972,'以色列','Israel',105,1),
('IM','IMN',44,'马恩岛','Isle of Man',104,1),
('IN','IND',91,'印度','India',99,1),
('IO','IOT',246,'英属印度洋领土','British Indian Ocean Territory',31,1),
('IQ','IRQ',964,'伊拉克','Iraq',102,1),
('IR','IRN',98,'伊朗伊斯兰共和国','Iran, Islamic Republic of',101,1),
('IS','ISL',354,'冰岛','Iceland',98,1),
('IT','ITA',39,'意大利','Italy',106,1),
('JE','JEY',44,'泽西','Jersey',109,1),
('JM','JAM',1876,'牙买加','Jamaica',107,1),
('JO','JOR',962,'约旦','Jordan',110,1),
('JP','JPN',81,'日本','Japan',108,1),
('KE','KEN',254,'肯尼亚','Kenya',112,1),
('KG','KGZ',996,'吉尔吉斯斯坦','Kyrgyzstan',117,1),
('KH','KHM',855,'柬埔寨','Cambodia',37,1),
('KI','KIR',686,'基里巴斯','Kiribati',113,1),
('KM','COM',269,'科摩罗','Comoros',49,1),
('KN','KNA',1869,'圣基茨岛和尼维斯','Saint Kitts and Nevis',182,1),
('KP','PRK',850,'朝鲜','Korea, Democratic People\'s Republic of',114,1),
('KR','KOR',82,'韩国','Korea, Republic of',115,1),
('KW','KWT',965,'科威特','Kuwait',116,1),
('KY','CYM',1345,'开曼群岛','Cayman Islands',41,1),
('KZ','KAZ',76,'哈萨克斯坦','Kazakhstan',111,1),
('LA','LAO',856,'老挝','Laos (Lao PDR)',1,1),
('LB','LBN',961,'黎巴嫩','Lebanon',119,1),
('LC','LCA',1758,'圣卢西亚','Saint Lucia',183,1),
('LI','LIE',423,'列支敦士登','Liechtenstein',123,1),
('LK','LKA',94,'斯里兰卡','Sri Lanka',204,1),
('LR','LBR',231,'利比里亚','Liberia',121,1),
('LS','LSO',266,'莱索托','Lesotho',120,1),
('LT','LTU',370,'立陶宛','Lithuania',124,1),
('LU','LUX',352,'卢森堡','Luxembourg',125,1),
('LV','LVA',371,'拉脱维亚','Latvia',118,1),
('LY','LBY',218,'利比亚','Libya',122,1),
('MA','MAR',212,'摩洛哥','Morocco',146,1),
('MC','MCO',377,'摩纳哥','Monaco',142,1),
('MD','MDA',373,'摩尔多瓦','Moldova',141,1),
('ME','MNE',382,'黑山','Montenegro',144,1),
('MF','MAF',590,'法属圣马丁','Saint-Martin (French part)',186,1),
('MG','MDG',261,'马达加斯加','Madagascar',128,1),
('MH','MHL',692,'马绍尔群岛','Marshall Islands',134,1),
('MK','MKD',389,'马其顿','Macedonia, Republic of',127,1),
('ML','MLI',223,'马里','Mali',132,1),
('MM','MMR',95,'缅甸','Myanmar',148,1),
('MN','MNG',976,'蒙古','Mongolia',143,1),
('MO','MAC',853,'中国澳门','Macao, SAR China',126,1),
('MP','MNP',1670,'北马里亚纳群岛','Northern Mariana Islands',161,1),
('MQ','MTQ',596,'马提尼克','Martinique',135,1),
('MR','MRT',222,'毛里塔尼亚','Mauritania',136,1),
('MS','MSR',1664,'蒙特塞拉特岛','Montserrat',145,1),
('MT','MLT',356,'马耳他','Malta',133,1),
('MU','MUS',230,'毛里求斯','Mauritius',137,1),
('MV','MDV',960,'马尔代夫','Maldives',131,1),
('MW','MWI',265,'马拉维','Malawi',129,1),
('MX','MEX',52,'墨西哥','Mexico',139,1),
('MY','MYS',60,'马来西亚','Malaysia',130,1),
('MZ','MOZ',258,'莫桑比克','Mozambique',147,1),
('NA','NAM',264,'纳米比亚','Namibia',149,1),
('NC','NCL',687,'新喀里多尼亚','New Caledonia',154,1),
('NE','NER',227,'尼日尔','Niger',157,1),
('NF','NFK',672,'诺福克岛','Norfolk Island',160,1),
('NG','NGA',234,'尼日利亚','Nigeria',158,1),
('NI','NIC',505,'尼加拉瓜','Nicaragua',156,1),
('NL','NLD',31,'荷兰','Netherlands',152,1),
('NO','NOR',47,'挪威','Norway',162,1),
('NP','NPL',977,'尼泊尔','Nepal',151,1),
('NR','NRU',674,'瑙鲁','Nauru',150,1),
('NU','NIU',683,'纽埃','Niue',159,1),
('NZ','NZL',64,'新西兰','New Zealand',155,1),
('OM','OMN',968,'阿曼','Oman',163,1),
('PA','PAN',507,'巴拿马','Panama',167,1),
('PE','PER',51,'秘鲁','Peru',170,1),
('PF','PYF',689,'法属波利尼西亚','French Polynesia',76,1),
('PG','PNG',675,'巴新','Papua New Guinea',168,1),
('PH','PHL',63,'菲律宾','Philippines',171,1),
('PK','PAK',92,'巴基斯坦','Pakistan',164,1),
('PL','POL',48,'波兰','Poland',173,1),
('PM','SPM',508,'圣圣皮埃尔和密克隆','Saint Pierre and Miquelon',184,1),
('PN','PCN',870,'皮特凯恩','Pitcairn',172,1),
('PR','PRI',1787,'波多黎各','Puerto Rico',175,1),
('PS','PSE',970,'巴勒斯坦领土','Palestinian Territory',166,1),
('PT','PRT',351,'葡萄牙','Portugal',174,1),
('PW','PLW',680,'帕劳','Palau',165,1),
('PY','PRY',595,'巴拉圭','Paraguay',169,1),
('QA','QAT',974,'卡塔尔','Qatar',176,1),
('RE','REU',262,'留尼汪岛','Réunion',177,1),
('RO','ROU',40,'罗马尼亚','Romania',178,1),
('RS','SRB',381,'塞尔维亚','Serbia',192,1),
('RU','RUS',7,'俄罗斯','Russian Federation',179,1),
('RW','RWA',250,'卢旺达','Rwanda',180,1),
('SA','SAU',966,'沙特阿拉伯','Saudi Arabia',190,1),
('SB','SLB',677,'所罗门群岛','Solomon Islands',198,1),
('SC','SYC',248,'塞舌尔','Seychelles',193,1),
('SD','SDN',249,'苏丹','Sudan',205,1),
('SE','SWE',46,'瑞典','Sweden',209,1),
('SG','SGP',65,'新加坡','Singapore',195,1),
('SH','SHN',290,'圣海伦娜','Saint Helena',181,1),
('SI','SVN',386,'斯洛文尼亚','Slovenia',197,1),
('SJ','SJM',47,'斯瓦尔巴群岛和扬马延岛','Svalbard and Jan Mayen Islands',207,1),
('SK','SVK',421,'斯洛伐克','Slovakia',196,1),
('SL','SLE',232,'塞拉利昂','Sierra Leone',194,1),
('SM','SMR',378,'圣马力诺','San Marino',188,1),
('SN','SEN',221,'塞内加尔','Senegal',191,1),
('SO','SOM',252,'索马里','Somalia',199,1),
('SR','SUR',597,'苏里南','Suriname',206,1),
('SS','SSD',249,'南苏丹','South Sudan',202,1),
('ST','STP',239,'圣多美和普林西比','Sao Tome and Principe',189,1),
('SV','SLV',503,'萨尔瓦多','El Salvador',65,1),
('SY','SYR',963,'叙利亚','Syrian Arab Republic(Syria)',211,1),
('SZ','SWZ',268,'斯威士兰','Swaziland',208,1),
('TC','TCA',1649,'特克斯和凯科斯群岛','Turks and Caicos Islands',224,1),
('TD','TCD',235,'乍得','Chad',43,1),
('TG','TGO',228,'多哥','Togo',217,1),
('TH','THA',66,'泰国','Thailand',215,1),
('TJ','TJK',992,'塔吉克斯坦','Tajikistan',213,1),
('TK','TKL',690,'托克劳群岛','Tokelau',218,1),
('TL','TLS',670,'东帝汶','Timor-Leste',216,1),
('TM','TKM',993,'土库曼斯坦','Turkmenistan',223,1),
('TN','TUN',216,'突尼斯','Tunisia',221,1),
('TO','TON',676,'汤加','Tonga',219,1),
('TR','TUR',90,'土耳其','Turkey',222,1),
('TT','TTO',1868,'特立尼达和多巴哥','Trinidad and Tobago',220,1),
('TV','TUV',688,'图瓦卢','Tuvalu',225,1),
('TW','TWN',886,'中国台湾','Taiwan, China',212,1),
('TZ','TZA',255,'坦桑尼亚','Tanzania, United Republic of',214,1),
('UA','UKR',380,'乌克兰','Ukraine',227,1),
('UG','UGA',256,'乌干达','Uganda',226,1),
('US','USA',1,'美国','United States',230,1),
('UY','URY',598,'乌拉圭','Uruguay',231,1),
('UZ','UZB',998,'乌兹别克斯坦','Uzbekistan',232,1),
('VA','VAT',379,'梵蒂冈','Holy See (Vatican City State)',94,1),
('VC','VCT',1784,'圣文森特和格林纳丁斯','Saint Vincent and Grenadines',185,1),
('VE','VEN',58,'委内瑞拉','Venezuela',234,1),
('VG','VGB',1284,'英属维尔京群岛','British Virgin Islands',32,1),
('VI','VIR',1340,'美属维尔京群岛','Virgin Islands, US',236,1),
('VN','VNM',84,'越南','Viet Nam',235,1),
('VU','VUT',678,'瓦努阿图','Vanuatu',233,1),
('WF','WLF',681,'沃利斯和富图纳群岛','Wallis and Futuna Islands',237,1),
('WS','WSM',685,'萨摩亚','Samoa',187,1),
('YE','YEM',967,'也门','Yemen',239,1),
('YT','MYT',262,'马约特岛','Mayotte',138,1),
('ZA','ZAF',27,'南非','South Africa',200,1),
('ZM','ZMB',260,'赞比亚','Zambia',240,1),
('ZW','ZWE',263,'津巴布韦','Zimbabwe',241,1);

/*Table structure for table `country_language` */

DROP TABLE IF EXISTS `country_language`;

CREATE TABLE `country_language` (
  `country_code2` varchar(2) NOT NULL COMMENT '国家二字码',
  `lan_id` tinyint(1) NOT NULL COMMENT '语言ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '文本',
  PRIMARY KEY (`country_code2`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `country_language` */

/*Table structure for table `currency` */

DROP TABLE IF EXISTS `currency`;

CREATE TABLE `currency` (
  `code` varchar(3) NOT NULL COMMENT '货币符号',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '货币名称',
  `symbol` varchar(4) NOT NULL DEFAULT '' COMMENT '符号',
  `rate` decimal(10,6) NOT NULL DEFAULT '0.000000' COMMENT '汇率',
  PRIMARY KEY (`code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

/*Data for the table `currency` */

insert  into `currency`(`code`,`name`,`symbol`,`rate`) values 
('AUD','澳币','A$',0.228000),
('CAD','加元','C$',0.215000),
('CNY','人民币','¥',1.000000),
('EUR','欧元','€',0.151080),
('GBP','英镑','£',0.132480),
('HKD','港币','HK$',1.220600),
('JPY','日元','¥',16.958000),
('MXN','墨西哥元','M$',3.157520),
('SGD','新加坡元','S$',0.228625),
('USD','美元','$',0.174240);

/*Table structure for table `description` */

DROP TABLE IF EXISTS `description`;

CREATE TABLE `description` (
  `desc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '描述值',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`desc_id`),
  UNIQUE KEY `IDX_UNI` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4;

/*Data for the table `description` */

insert  into `description`(`desc_id`,`name`,`status`) values 
(1,'100支 200支 500支 1000支 2000支 3000支 50支',0),
(2,'111V~240V（含）',0),
(3,'11W(含)-15W(含)',0),
(4,'15-60周岁',0),
(5,'150W',0),
(8,'20-24周岁 25-29周岁 30-34周岁 35-39周岁',0),
(10,'2年',0),
(11,'33.5cm',0),
(12,'3C规格型号：见附件。220-240V～ 50/60Hz',0),
(13,'50W',0),
(14,'5年',0),
(15,'6W(含)-10W(含)',0),
(16,'DGX-88-11',0),
(17,'Falconeyes/锐鹰',0),
(18,'Goiden eagie',0),
(19,'LDD-XYD020',0),
(20,'LED',0),
(21,'Pure Cashmere/全绒时代',0),
(22,'tyd',0),
(24,'≤36V(含)',0),
(25,'东莞鹰科影视器材厂',0),
(26,'中国',0),
(27,'中年 情侣 青年',0),
(28,'中年 老年 青年',0),
(29,'主要材质',0),
(31,'产地',0),
(32,'人群',0),
(33,'休闲',0),
(34,'优雅',0),
(35,'余姚金鹰摄影器材有限公司',0),
(36,'光源类型',0),
(37,'光身',0),
(38,'其他',0),
(39,'凡胜（家装主材）',0),
(40,'出游',0),
(41,'功率',0),
(42,'包装',0),
(43,'包装方式',0),
(44,'包装规格',0),
(45,'卷边',0),
(47,'可调节',0),
(48,'可调节色温 彩色 其他/other',0),
(49,'否',0),
(50,'品牌',0),
(51,'圆顶',0),
(52,'型号',0),
(53,'套餐类型',0),
(54,'女',0),
(55,'安装方式',0),
(56,'客厅 餐厅 厨房 书房 卧室 卫生间 其他/other',0),
(57,'尺码',0),
(58,'带光源',0),
(59,'帽檐款式',0),
(60,'帽顶款式',0),
(61,'控制类型',0),
(62,'摄影灯品牌',0),
(63,'无檐',0),
(64,'明轨',0),
(65,'春季 秋季 冬季',0),
(66,'是否带木柄',0),
(67,'是否智能操控',0),
(68,'材质',0),
(69,'标准套餐',0),
(70,'标准白光（5500k±200k或5600K±200K）',2),
(71,'檐形',0),
(72,'欣影',0),
(73,'款式',0),
(74,'款式细节',0),
(75,'毛线帽/针织帽',0),
(76,'浅灰色',0),
(77,'澳名',0),
(78,'灯具是否带光源',0),
(79,'生产企业',0),
(80,'电压',0),
(81,'秋季 冬季',0),
(82,'紫绒+青绒原色（少量现货） 深咖+青绒原色 深灰+浅灰色',0),
(83,'红色（预售1月23日发货）',0),
(84,'绍兴上虞星影器材有限公司',0),
(85,'绒线',0),
(86,'色温',0),
(87,'证书状态：有效',0),
(88,'证书编号：2020181001018043',0),
(89,'质保年限',0),
(90,'适用场景',0),
(91,'适用季节',0),
(92,'适用对象',0),
(93,'适用年龄',0),
(94,'适用空间',0),
(95,'通用',0),
(96,'通用 影棚摄影 户外',0),
(97,'野猪林',0),
(98,'金刀',0),
(99,'铝',0),
(100,'长度',0),
(101,'青绒原色 炭灰 紫绒原色 枣红',0),
(102,'非智能控制',0),
(103,'颜色分类',0),
(104,'风格',0),
(128,'200W单灯标配',2);

/*Table structure for table `description_language` */

DROP TABLE IF EXISTS `description_language`;

CREATE TABLE `description_language` (
  `desc_id` int(11) NOT NULL DEFAULT '0' COMMENT '描述ID',
  `lan_id` varchar(4) NOT NULL DEFAULT '' COMMENT '语言ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '翻译文本',
  PRIMARY KEY (`desc_id`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `description_language` */

insert  into `description_language`(`desc_id`,`lan_id`,`name`) values 
(70,'de','Standard-weißes Licht (5500k (Kombi-Nr. 177k; 200K oder 5600k (Kombileuchte)'),
(70,'en','Standard white light (5500k ± 200K or 5600k ± 200K)'),
(70,'es','Luz blanca estándar (5500k ± 200k o 5600k ± 200k)'),
(70,'fr','Lumière blanche standard (5500k ± 200K ou 5600k ± 200K)'),
(70,'jp','標準白色光（5500 k±200 kまたは5600 K±200 K）'),
(70,'zht','標準白光（5500k±200k或5600K±200K）'),
(128,'de','200W Standard-Einrichtung für Einzelleuchten'),
(128,'en','200W single lamp standard configuration'),
(128,'es','200w estándar de una sola luz'),
(128,'fr','200 W lampe simple standard'),
(128,'jp','200 W単灯標準配合'),
(128,'zht','200W單燈標配');

/*Table structure for table `email` */

DROP TABLE IF EXISTS `email`;

CREATE TABLE `email` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `site_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '站点ID',
  `account_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'email账户ID',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '接收人ID',
  `lan_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '语言ID',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '邮件类型',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送状态',
  `content` varchar(32) NOT NULL DEFAULT '' COMMENT '发送内容',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '记录时间',
  `send_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

/*Data for the table `email` */

insert  into `email`(`email_id`,`site_id`,`account_id`,`mem_id`,`lan_id`,`type`,`status`,`content`,`add_time`,`send_time`) values 
(1,80,1,1,0,1,1,'048632','2021-08-01 22:45:43','2021-08-01 22:45:43'),
(2,80,1,1,0,1,1,'974017','2021-08-01 22:46:50','2021-08-01 22:46:50'),
(3,80,1,1,0,1,1,'266944','2021-08-01 22:49:26','2021-08-01 22:49:26'),
(4,80,1,1,0,1,1,'588319','2021-08-01 22:51:18','2021-08-01 22:51:18'),
(5,80,1,1,0,1,1,'540140','2021-08-01 23:01:19','2021-08-01 23:01:19'),
(6,80,1,1,0,1,1,'066597','2021-08-04 21:19:46','2021-08-04 21:19:46'),
(7,80,1,1,0,1,1,'330784','2021-08-04 21:19:48','2021-08-04 21:19:48'),
(8,80,1,1,0,1,1,'513390','2021-08-04 21:19:48','2021-08-04 21:19:48'),
(9,80,1,1,0,1,1,'283416','2021-08-04 21:19:49','2021-08-04 21:19:49'),
(10,80,1,1,0,1,1,'450219','2021-08-04 21:19:51','2021-08-04 21:19:51'),
(11,80,1,1,0,1,1,'687607','2021-08-04 21:19:52','2021-08-04 21:19:52'),
(12,80,1,1,0,1,1,'025324','2021-08-04 21:19:53','2021-08-04 21:19:53'),
(13,80,1,1,0,1,1,'211994','2021-08-04 21:19:54','2021-08-04 21:19:54'),
(14,80,1,1,0,1,1,'559278','2021-08-04 21:19:54','2021-08-04 21:19:54'),
(15,80,1,1,0,1,1,'050820','2021-08-04 21:19:55','2021-08-04 21:19:55'),
(16,80,1,1,0,1,1,'454507','2021-08-04 21:32:33','2021-08-04 21:32:33');

/*Table structure for table `email_account` */

DROP TABLE IF EXISTS `email_account`;

CREATE TABLE `email_account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '发件人签名',
  `address` varchar(64) NOT NULL DEFAULT '' COMMENT '邮件地址,全部小写',
  `smtp` varchar(64) NOT NULL DEFAULT '' COMMENT '格式:domain:port',
  `smtp_ssl` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用ssl,0停用,1启用',
  `email_user` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱登录用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '邮箱登录密码',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态:0停用,1启用',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注说明',
  `create_at` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `email_account` */

insert  into `email_account`(`account_id`,`name`,`address`,`smtp`,`smtp_ssl`,`email_user`,`password`,`status`,`remark`,`create_at`) values 
(1,'PrettyBag','849376723@qq.com','smtp.qq.com:465',1,'849376723@qq.com','wqszejuchvelbcad',1,'','2021-08-01 22:22:59');

/*Table structure for table `email_account_used` */

DROP TABLE IF EXISTS `email_account_used`;

CREATE TABLE `email_account_used` (
  `site_id` tinyint(4) NOT NULL COMMENT '站点ID',
  `account_id` tinyint(4) NOT NULL COMMENT 'email账户ID',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '记录时间',
  PRIMARY KEY (`site_id`,`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `email_account_used` */

insert  into `email_account_used`(`site_id`,`account_id`,`add_time`) values 
(80,1,'2021-07-30 14:14:52');

/*Table structure for table `language` */

DROP TABLE IF EXISTS `language`;

CREATE TABLE `language` (
  `code` varchar(4) NOT NULL DEFAULT '' COMMENT '主键码',
  `tr_code` varchar(4) NOT NULL DEFAULT '' COMMENT '翻译码',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '名称',
  `name2` varchar(20) NOT NULL DEFAULT '' COMMENT '内部使用名称',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

/*Data for the table `language` */

insert  into `language`(`code`,`tr_code`,`name`,`name2`) values 
('de','de','Deutsch','德语'),
('en','en','English','英语'),
('es','spa','Espanol','西班牙语'),
('fr','fra','Français','法语'),
('jp','jp','日本語','日本语'),
('zh','zh','中文简体','中文简体'),
('zht','cht','中文繁體','中文繁体');

/*Table structure for table `member` */

DROP TABLE IF EXISTS `member`;

CREATE TABLE `member` (
  `mem_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `site_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '站点ID',
  `first_name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓',
  `last_name` varchar(32) NOT NULL DEFAULT '' COMMENT '名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(60) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `verify` tinyint(4) NOT NULL DEFAULT '0' COMMENT '认证',
  `password` varchar(65) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(4) NOT NULL DEFAULT '' COMMENT '随机安全码',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `login_time` timestamp NULL DEFAULT NULL COMMENT '最后一次登陆时间',
  PRIMARY KEY (`mem_id`),
  KEY `IDX_NAME` (`first_name`),
  KEY `IDX_MOBLIE` (`mobile`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10003 DEFAULT CHARSET=utf8mb4;

/*Data for the table `member` */

insert  into `member`(`mem_id`,`site_id`,`first_name`,`last_name`,`mobile`,`email`,`avatar`,`sex`,`status`,`verify`,`password`,`salt`,`add_time`,`update_time`,`login_time`) values 
(10000,80,'Victory','Bluker','','849376723@qq.com','',0,1,0,'','','2021-07-29 13:32:26',NULL,'2021-08-06 15:04:05'),
(10001,80,'Victoria','Blueker','+1 18825071642','849376724@qq.com','avatar/b0e9d103617f036384d321b3c123b728.jpeg',0,1,0,'$2y$10$U4wL32GKBd6pDPhPTWqvautldjZAKROgaEYReWKHkwJSoz1OqzUZa','VmIb','2021-08-09 10:59:55','2021-09-09 10:57:07','2021-09-09 10:57:07'),
(10002,80,'Victory','Bluker','','849376725@qq.com','',0,1,0,'$2y$10$9S3d2tdQhlMPtktPdBZS.eFehMcaLKC4o7qt9JiWnRESK6G.KTQli','KOI5','2021-08-09 14:43:25','2021-08-27 10:26:11','2021-08-27 10:26:11');

/*Table structure for table `member_address` */

DROP TABLE IF EXISTS `member_address`;

CREATE TABLE `member_address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mem_id` int(11) NOT NULL COMMENT '用户ID',
  `first_name` varchar(32) NOT NULL COMMENT '姓',
  `last_name` varchar(32) NOT NULL COMMENT '名',
  `country_code2` varchar(2) NOT NULL COMMENT '国家2子代码',
  `country` varchar(64) NOT NULL DEFAULT '' COMMENT '国家',
  `zone_id` int(11) NOT NULL DEFAULT '0' COMMENT '省份或者州ID',
  `state` varchar(32) NOT NULL COMMENT '省份，州',
  `city` varchar(32) NOT NULL COMMENT '城市',
  `address1` varchar(64) NOT NULL COMMENT '地址1',
  `address2` varchar(64) NOT NULL DEFAULT '' COMMENT '地址2，可选',
  `postcode` varchar(10) NOT NULL COMMENT '邮编',
  `phone` varchar(20) NOT NULL COMMENT '电话',
  `tax_number` varchar(32) NOT NULL DEFAULT '' COMMENT '税号',
  `lan_id` tinyint(1) NOT NULL DEFAULT '2' COMMENT '语言ID',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认地址',
  `is_bill` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否账单地址',
  PRIMARY KEY (`address_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4;

/*Data for the table `member_address` */

insert  into `member_address`(`address_id`,`mem_id`,`first_name`,`last_name`,`country_code2`,`country`,`zone_id`,`state`,`city`,`address1`,`address2`,`postcode`,`phone`,`tax_number`,`lan_id`,`is_default`,`is_bill`) values 
(5,10001,'yao','yuanji','US','',322046,'Arkansas','dgssf','fdsafsd','fdsafdsa','4234324','+1 23432443242','',0,0,0),
(6,10001,'dfdsaf','fdsafd','US','',322047,'Arizona','fdsafdsaf','fdsafdsa','fdsafdsaf','5455435','+1 34252353245','',0,0,0),
(8,10001,'sdad','asdasda','AX','',0,'dasdad','dasdadad','dasda','asdasd','dasda','+358 18819347034','',0,0,0),
(9,10001,'fdafd','fdsafdsafsda','US','',322039,'Armed Forces Canada','fdsafdsa','fdsafds','dfsafsad','3445243','+1 35353246245','',0,0,0),
(10,10001,'1','1','AL','',0,'asdasd','3','1','121212','7789852522','+355 6669999','',0,0,0),
(11,10001,'1','1','AL','',0,'asdasd','3','1','121212','7789852522','+355 6669999','',0,0,0),
(12,10001,'First Name','Last Name','AF','',0,'note','man','1 st','','20001','+93 1380001','',0,0,0),
(25,10001,'First Name','Name','AU','',322002,'New South Wales','man','1 st','test1','20001','+61 1380001','',0,0,0),
(29,10001,'First Name','Name','AF','',0,'note','man','new billing address','test1','20001','+93 1380010','',0,0,0),
(33,10001,'First Name','Name','AU','',322003,'Northern Territory','man','1 st','test1','20001','+61 1380001','',0,0,0),
(34,10001,'First Name','Name','AU','',322002,'New South Wales','man','1 st','test1','20001','+61 1380001','',0,0,0),
(35,10001,'First Name','Name','AU','',322003,'Northern Territory','man','1 st','test1','20001','+61 1380001','',0,0,0),
(36,10001,'First Name','Name','AU','',322002,'New South Wales','man','1 st','test1','20001','+61 1380001','',0,0,0),
(37,10001,'First Name','Name','US','',322039,'Armed Forces Canada','man','1 st','test1','20001','+1 1380001','',0,0,0),
(38,10001,'First Name','Name','US','',322038,'Armed Forces Americas','man','1 st','test1','20001','+1 1380001','',0,0,0),
(39,10001,'Danny','1','US','',322038,'Armed Forces Americas','汕尾','陆丰市碣石镇','角清','516545','+1 13172873412','',0,0,0),
(40,10001,'First Name2222','Name','US','',322038,'Armed Forces Americas','man','1 st','test1','20001','+1 1380001','',0,0,0),
(41,10001,'dada','dadas','US','',322038,'Armed Forces Americas','guangzhou','天河东路','天河北路','23232','+1 02131231231','',0,0,0),
(42,10001,'homelam','lam','US','',322040,'Armed Forces Europe','guangzhou','1','','q','+1 1234567898','',0,0,0),
(43,10001,'homelam','lam','US','',322038,'Armed Forces Americas','guangzhou','guangzhoushi haizhu luntou11','','52534412','+1 13512732333','',0,0,0),
(44,10001,'zzzzz','lam','AF','',0,'guangdong','guangzhou','guangzhoushi haizhu luntou11','fds232','52534412','+93 13512732430','',0,0,0),
(45,10001,'xiaohongd','tan','AX','',0,'Armed Forces Americas','guangzhou','天河1223w1212138','guangzhou tianhe cocah teminold','5253001','+358 045644646565','',0,0,0),
(48,10001,'homelam','lam','US','',322038,'Armed Forces Americas','guangzhou','guangzhoushi haizhu luntou11','fds232','52534412','+1 13512732430','',0,0,0),
(55,10001,'First Name','Name','US','',322038,'Armed Forces Americas','man','1 st','test1','20001','+1 1380001','',0,0,0),
(56,10001,'asda','dasdasd','US','',322039,'Armed Forces Canada','dasdas','dasdasd','asdadf','123132','+1 0188193456456','',0,0,0),
(57,10001,'Hehhs','Hehsj','AF','',0,'Hdjdj','Hdhsjj','Hshsjsj','Shjsjsj','10922','+93 188191917282','',0,0,0),
(58,10001,'asda','dasdasd','US','',322040,'Armed Forces Europe','dasdas','dasdasd','asdadf','123132','+1 0188193456456','',0,0,0),
(59,10001,'210','54051','AF','',0,'ajhsok','skjoikj','1193 Penn Street','角清','45465','+93 140980','',0,0,0),
(61,10001,'V','I','IN','',0,'Guangdong','guangzhou','toria','','0000','+91 18825071640','',0,0,0),
(62,10001,'V','I','MX','',0,'Guangdong','guangzhou','toria','','0000','+52 18825071640','44800085440',0,0,0),
(63,10001,'s','s','US','',322038,'Armed Forces Americas','d','f','jk','s','+1 18825071640','',0,0,0),
(64,10001,'hello','kitty','US','',322038,'Armed Forces Americas','Washington D.C.','Washington D.C.','','20001','+1 13602888686','',0,0,0),
(65,10001,'V','I','MX','',0,'ssss','guangzhou','toria','','0000','+52 18825071642','42243309114',0,0,0),
(66,10001,'hduhd','jdjdhd','MX','',0,'hshdhdd','hdbdbdhhd','ndhdhxhdbd','','4645497664','+52 645484649464','44800085592',0,0,0),
(67,10001,'12','12','MX','',0,'5535','5555','5555','','458456','+52 123459856','',0,0,0),
(68,10001,'sss','222','MX','',0,'Guangdong','guangzhou','toria','','0000','+52 18825071640','44800085592',0,0,0),
(69,10001,'哦','QQ','MX','',0,'hbsksk','694976','护体自我','','66646','+52 18525636694','12345678900',0,0,0),
(71,10001,'sss','222','MX','',0,'Guangdong','guangzhou','toria','','0000','+52 18825071640','44800085592',0,0,0),
(75,10001,'V','I','AF','',0,'Guangdong','guangzhou','toria','','0000','+93 18825071640','',0,0,0),
(76,10001,'V','I','US','',322038,'Armed Forces Americas','guangzhou','toria','','0000','+1 18825071640','',0,0,0),
(78,10001,'V','I','GB','',0,'London','London','toria','','EC1A','+44 18825071642','',0,0,0),
(79,10001,'V','I','HK','',0,'ssss','guangzhou','toria','','0000','+852 18825071642','',0,0,0),
(80,10001,'V','I','US','',322038,'Armed Forces Americas','guangzhou','toria','','0000','+1 18825071642','',0,0,0),
(81,10001,'V','I','US','',322038,'Armed Forces Americas','guangzhou','toria','','0000','+1 18825071642','',0,0,0),
(82,10001,'kkkkk','I','US','',322038,'Armed Forces Americas','m','toria','','0000','+1 18825071642','',0,0,0),
(83,10001,'1','1','US','',322038,'Armed Forces Americas','1','1','角清1','1','+1 10317287341','',0,0,0),
(84,10001,'V','I','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 188250716421111','44800085592',0,0,0),
(85,10001,'V2222','I333333','AF','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','11111111111111111111',0,0,0),
(86,10001,'V','I','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','42243309114',0,0,0),
(87,10001,'V3333','I222222','AF','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','11111111111111111111',0,0,0),
(88,10001,'V8888','I8888','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','8888',0,1,0),
(89,10001,'V','I','AS','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','42243309114',0,0,0),
(90,10001,'V','I','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','8888',2,0,0),
(91,10001,'V','I','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','8888',2,0,0),
(92,10001,'V','I','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','8888',2,0,0),
(93,10001,'V','I','LA','',0,'ssss','guangzhou','toria','123123123','0000','+376 18825071642','8888',2,0,0);

/*Table structure for table `member_collect` */

DROP TABLE IF EXISTS `member_collect`;

CREATE TABLE `member_collect` (
  `coll_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `spu_id` int(11) NOT NULL DEFAULT '0' COMMENT 'spuID',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`coll_id`),
  UNIQUE KEY `IDX_UNI` (`mem_id`,`spu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

/*Data for the table `member_collect` */

insert  into `member_collect`(`coll_id`,`mem_id`,`spu_id`,`add_time`) values 
(38,10002,1,'2021-08-12 09:27:29'),
(39,10002,2,'2021-08-12 09:27:30'),
(49,10001,3,'2021-08-12 17:44:57'),
(50,10001,4,'2021-08-12 17:44:57'),
(51,10001,1,'2021-08-12 17:46:42'),
(52,10001,2,'2021-08-12 17:46:44');

/*Table structure for table `member_history` */

DROP TABLE IF EXISTS `member_history`;

CREATE TABLE `member_history` (
  `his_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mem_id` int(11) NOT NULL COMMENT '用户ID',
  `spu_id` int(11) NOT NULL COMMENT '产品ID',
  `add_date` date NOT NULL COMMENT '添加日期',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`his_id`),
  UNIQUE KEY `IDX_UNI` (`mem_id`,`spu_id`,`add_date`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

/*Data for the table `member_history` */

insert  into `member_history`(`his_id`,`mem_id`,`spu_id`,`add_date`,`add_time`) values 
(1,10001,1,'2021-08-09','2021-08-09 18:01:53'),
(2,10001,1,'2021-08-10','2021-08-10 09:19:18'),
(3,10001,2,'2021-08-10','2021-08-10 14:42:53'),
(4,10001,1,'2021-08-11','2021-08-11 11:28:22'),
(5,10001,1,'2021-08-12','2021-08-12 15:21:06'),
(6,10001,2,'2021-08-12','2021-08-12 17:04:16'),
(7,10001,1,'2021-08-13','2021-08-13 15:37:06'),
(8,10001,2,'2021-08-17','2021-08-17 16:34:04'),
(9,10001,2,'2021-08-19','2021-08-19 09:51:37'),
(10,10001,2,'2021-08-23','2021-08-23 11:03:04'),
(11,10001,1,'2021-08-23','2021-08-23 17:54:19'),
(12,10001,1,'2021-08-24','2021-08-24 09:37:15');

/*Table structure for table `member_uuid` */

DROP TABLE IF EXISTS `member_uuid`;

CREATE TABLE `member_uuid` (
  `uuid` varchar(32) NOT NULL COMMENT 'uuid',
  `site_id` tinyint(2) NOT NULL DEFAULT '0' COMMENT '站点ID',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID, 登录时绑定',
  `lan_id` varchar(4) NOT NULL DEFAULT '' COMMENT '语言',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`uuid`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `member_uuid` */

insert  into `member_uuid`(`uuid`,`site_id`,`mem_id`,`lan_id`,`add_time`) values 
('r95E2V6IJWI0gUOiXEMTpaJKquVXfwDz',0,50001,'en','2021-09-09 10:55:45');

/*Table structure for table `message` */

DROP TABLE IF EXISTS `message`;

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `group_key` varchar(32) NOT NULL DEFAULT '0' COMMENT '聊天组key',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '发消息人ID',
  `lan_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '语言ID',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '消息实体',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`message_id`),
  KEY `IDX_UNI` (`group_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `message` */

insert  into `message`(`message_id`,`group_key`,`mem_id`,`lan_id`,`content`,`add_time`) values 
(1,'055cf0b2d6a87fc5db7651914df5d64c',50001,2,'Hi, Welcome to prettybag, what can I do for you?','2021-08-02 17:43:38');

/*Table structure for table `message_member` */

DROP TABLE IF EXISTS `message_member`;

CREATE TABLE `message_member` (
  `item_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `group_key` varchar(32) NOT NULL DEFAULT '' COMMENT '聊天组key',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '人员ID',
  `unread` int(11) NOT NULL DEFAULT '0' COMMENT '未读数量',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `IDX_UNI` (`group_key`,`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `message_member` */

/*Table structure for table `order` */

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_no` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
  `site_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '站点ID',
  `mem_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '订单状态',
  `coupon_id` int(11) NOT NULL DEFAULT '0' COMMENT '优惠券使用ID',
  `payment_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '支付方式ID',
  `lan_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '下单语言',
  `currency` varchar(3) NOT NULL DEFAULT '' COMMENT '下单货币',
  `insurance_free` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '保险费用',
  `coupon_free` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券使用折扣',
  `shipping_fee` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `product_total` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价',
  `order_total` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '订单总价',
  `pay_time` timestamp NULL DEFAULT NULL COMMENT '支付时间',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '下单时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `is_review` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否评论',
  `is_delete` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10019 DEFAULT CHARSET=utf8mb4;

/*Data for the table `order` */

insert  into `order`(`order_id`,`order_no`,`site_id`,`mem_id`,`status`,`coupon_id`,`payment_id`,`lan_id`,`currency`,`insurance_free`,`coupon_free`,`shipping_fee`,`product_total`,`order_total`,`pay_time`,`add_time`,`update_time`,`is_review`,`is_delete`) values 
(10003,'210813150642080846',80,10001,1,0,0,2,'USD',0.00,0.00,0.00,69.44,69.44,NULL,NULL,NULL,0,0),
(10004,'210813152259516219',80,10001,2,0,0,2,'USD',0.00,0.00,0.00,69.44,69.44,NULL,'2021-08-13 15:22:59','2021-08-13 15:22:59',0,0),
(10005,'210813152623065266',80,10001,2,0,0,2,'USD',0.00,0.00,0.00,6944.00,6944.00,NULL,'2021-08-13 15:26:23','2021-08-13 15:26:23',0,0),
(10006,'210813152649077323',80,10001,3,0,0,2,'USD',0.00,0.00,0.00,6944.00,6944.00,NULL,'2021-08-13 15:26:49','2021-08-13 15:26:49',0,0),
(10007,'210813152649808388',80,10001,4,0,0,2,'USD',0.00,0.00,0.00,6944.00,6944.00,NULL,'2021-08-13 15:26:49','2021-08-13 15:26:49',0,0),
(10010,'210813153153213085',80,10001,4,0,0,2,'USD',0.00,0.00,0.00,6944.00,6944.00,NULL,'2021-08-13 15:31:53','2021-08-13 15:31:53',1,0),
(10011,'210813153225012747',80,10001,1,0,0,2,'USD',0.00,0.00,0.00,347.20,347.20,NULL,'2021-08-13 15:32:25','2021-08-13 15:32:25',0,0),
(10012,'210813153453206898',80,10001,1,0,0,2,'USD',0.00,0.00,0.00,69.44,69.44,NULL,'2021-08-13 15:34:53','2021-08-13 15:34:53',0,0),
(10013,'210813153644031125',80,10001,1,0,0,2,'USD',0.00,0.00,0.00,7152.32,7152.32,NULL,'2021-08-13 15:36:44','2021-08-13 15:36:44',0,0),
(10014,'210813160217282918',80,10001,1,0,0,2,'USD',0.00,0.00,0.00,318.40,318.40,NULL,'2021-08-13 16:02:17','2021-08-13 16:02:17',0,0),
(10015,'210820113856843886',80,10001,1,0,0,2,'USD',0.00,0.00,3.99,80.16,84.15,NULL,'2021-08-20 11:38:56','2021-08-20 11:38:56',0,0),
(10017,'210823110423589034',80,10001,1,1,0,2,'USD',0.00,0.00,3.99,80.16,84.15,NULL,'2021-08-23 11:04:23','2021-08-23 11:04:23',0,0),
(10018,'210823175732966930',80,10001,1,1,0,2,'USD',0.00,0.00,4.99,278.40,283.39,NULL,'2021-08-23 17:57:32','2021-08-23 17:57:32',0,0);

/*Table structure for table `order_address` */

DROP TABLE IF EXISTS `order_address`;

CREATE TABLE `order_address` (
  `order_address_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0递送地址 1账单地址',
  `first_name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓',
  `last_name` varchar(32) NOT NULL DEFAULT '' COMMENT '名',
  `country_code2` varchar(2) NOT NULL DEFAULT '' COMMENT '国家二字码',
  `zone_id` int(11) NOT NULL DEFAULT '0' COMMENT '省洲ID',
  `state` varchar(32) NOT NULL DEFAULT '' COMMENT '省洲名称',
  `city` varchar(32) NOT NULL DEFAULT '' COMMENT '城市',
  `address1` varchar(64) NOT NULL DEFAULT '' COMMENT '地址1',
  `address2` varchar(64) NOT NULL DEFAULT '' COMMENT '地址2',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `postcode` varchar(10) NOT NULL DEFAULT '' COMMENT '邮编',
  `tax_number` varchar(32) NOT NULL DEFAULT '' COMMENT '税号',
  PRIMARY KEY (`order_address_id`),
  UNIQUE KEY `IDX_UNI` (`order_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `order_address` */

insert  into `order_address`(`order_address_id`,`order_id`,`type`,`first_name`,`last_name`,`country_code2`,`zone_id`,`state`,`city`,`address1`,`address2`,`phone`,`postcode`,`tax_number`) values 
(1,10017,0,'V8888','I8888','LA',0,'ssss','guangzhou','toria','123123123','','0000','8888'),
(2,10017,1,'V8888','I8888','LA',0,'ssss','guangzhou','toria','123123123','','0000','8888'),
(3,10018,0,'V8888','I8888','LA',0,'ssss','guangzhou','toria','123123123','','0000','8888'),
(4,10018,1,'V','I','LA',0,'ssss','guangzhou','toria','123123123','+856 18825071642','0000','8888');

/*Table structure for table `order_product` */

DROP TABLE IF EXISTS `order_product`;

CREATE TABLE `order_product` (
  `order_product_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sku_id` int(11) NOT NULL DEFAULT '0' COMMENT 'skuID',
  `attach_id` int(11) NOT NULL DEFAULT '0' COMMENT 'sku图片',
  `name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_estonian_ci NOT NULL DEFAULT '' COMMENT '产品名称',
  `quantity` smallint(5) NOT NULL DEFAULT '0' COMMENT '数量',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '产品成交价',
  `original_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '产品原价',
  PRIMARY KEY (`order_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

/*Data for the table `order_product` */

insert  into `order_product`(`order_product_id`,`order_id`,`sku_id`,`attach_id`,`name`,`quantity`,`price`,`original_price`) values 
(3,10003,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',1,69.44,89.58),
(4,10004,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',1,69.44,89.58),
(5,10005,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',100,69.44,89.58),
(6,10006,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',100,69.44,89.58),
(7,10007,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',100,69.44,89.58),
(10,10010,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',100,69.44,89.58),
(11,10011,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',5,69.44,89.58),
(12,10012,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',1,69.44,89.58),
(13,10013,1,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 炭灰',103,69.44,89.58),
(14,10014,2,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 紫绒原色',5,63.68,89.15),
(15,10015,5,10,'Pure Cashmere 纯羊绒绞花针织毛线帽子男女士双面双色两用秋冬潮 - 可调节 深咖+青绒原色',1,80.16,104.21),
(17,10017,5,10,'Pure Cashmere 纯羊绒绞花针织毛线帽子男女士双面双色两用秋冬潮 - 可调节 深咖+青绒原色',1,80.16,104.21),
(18,10018,3,10,'Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting - 可调节 枣红',4,69.60,105.10);

/*Table structure for table `order_product_attribute` */

DROP TABLE IF EXISTS `order_product_attribute`;

CREATE TABLE `order_product_attribute` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_product_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单产品ID',
  `attr_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_name` varchar(120) NOT NULL DEFAULT '' COMMENT '属性名称',
  `attv_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性值id',
  `attv_name` varchar(120) NOT NULL DEFAULT '0' COMMENT '属性值名称',
  `attach_id` int(11) NOT NULL DEFAULT '0' COMMENT '图片ID',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;

/*Data for the table `order_product_attribute` */

insert  into `order_product_attribute`(`item_id`,`order_product_id`,`attr_id`,`attr_name`,`attv_id`,`attv_name`,`attach_id`) values 
(1,3,1,'尺码',1,'可调节',12),
(2,3,2,'颜色分类',2,'炭灰',0),
(3,4,1,'尺码',1,'可调节',0),
(4,4,2,'颜色分类',2,'炭灰',0),
(5,5,1,'尺码',1,'可调节',0),
(6,5,2,'颜色分类',2,'炭灰',0),
(7,6,1,'尺码',1,'可调节',0),
(8,6,2,'颜色分类',2,'炭灰',0),
(9,7,1,'尺码',1,'可调节',0),
(10,7,2,'颜色分类',2,'炭灰',0),
(11,10,1,'尺码',1,'可调节',0),
(12,10,2,'颜色分类',2,'炭灰',0),
(13,11,1,'尺码',1,'可调节',0),
(14,11,2,'颜色分类',2,'炭灰',0),
(15,12,1,'尺码',1,'可调节',0),
(16,12,2,'颜色分类',2,'炭灰',0),
(17,13,1,'尺码',1,'可调节',0),
(18,13,2,'颜色分类',2,'炭灰',0),
(19,14,1,'尺码',1,'可调节',0),
(20,14,2,'颜色分类',3,'紫绒原色',0),
(21,15,1,'尺码',1,'可调节',0),
(22,15,2,'颜色分类',6,'深咖+青绒原色',0),
(25,17,1,'尺码',1,'可调节',0),
(26,17,2,'颜色分类',6,'深咖+青绒原色',0),
(27,18,1,'尺码',1,'可调节',0),
(28,18,2,'颜色分类',4,'枣红',12);

/*Table structure for table `payment` */

DROP TABLE IF EXISTS `payment`;

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type` tinyint(4) NOT NULL COMMENT '类型',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `is_sandbox` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否沙盒',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `app_key` varchar(150) NOT NULL DEFAULT '' COMMENT '公钥',
  `secret_key` varchar(150) NOT NULL DEFAULT '' COMMENT '私钥',
  `webhook_key` varchar(150) NOT NULL DEFAULT '' COMMENT '通知密钥',
  `remark` varchar(64) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `payment` */

insert  into `payment`(`payment_id`,`type`,`status`,`is_sandbox`,`name`,`app_key`,`secret_key`,`webhook_key`,`remark`,`add_time`) values 
(1,2,1,1,'769554050@qq.com','pk_test_51J0LqWBTZdJdvW63sFA3icT1gRtncgVCO1lADbVX566P3PrMpYOOThgY4FE5Ccm8hrlwpNsfL3vhQQN7vHCs9pSx00qJoOBYMi','sk_test_51J0LqWBTZdJdvW63CxpzDjOtZiztn3WfvjUEugzcKtgM2DXTVGWY55vBhUx0lNVXp7duysnbE6WaNNxcmZYy9Thg00wA5rTJ0Z','whsec_zBhphlruQlb2KQqYFIdPTMEik8hInhsk','Stripe 测试账号','2021-09-06 12:00:35');

/*Table structure for table `payment_used` */

DROP TABLE IF EXISTS `payment_used`;

CREATE TABLE `payment_used` (
  `site_id` tinyint(1) NOT NULL COMMENT '站点ID',
  `type` tinyint(1) NOT NULL COMMENT '支付类型',
  `payment_id` int(11) NOT NULL COMMENT '支付账户ID',
  `sort` tinyint(1) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '增加时间',
  PRIMARY KEY (`site_id`,`type`,`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `payment_used` */

/*Table structure for table `product_attribute_used` */

DROP TABLE IF EXISTS `product_attribute_used`;

CREATE TABLE `product_attribute_used` (
  `sku_id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'skuID',
  `attr_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性名ID',
  `attv_id` int(11) NOT NULL DEFAULT '0' COMMENT '属性值ID',
  `attach_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '图片ID 无则为0',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`sku_id`,`attr_id`,`attv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_attribute_used` */

insert  into `product_attribute_used`(`sku_id`,`attr_id`,`attv_id`,`attach_id`,`sort`) values 
(1,1,1,0,1),
(1,2,2,6,2),
(2,1,1,0,1),
(2,2,3,7,2),
(3,1,1,0,1),
(3,2,4,8,2),
(4,1,1,0,1),
(4,2,5,9,2),
(5,1,1,0,1),
(5,2,6,91,2),
(6,1,1,0,1),
(6,2,7,92,2),
(7,1,1,0,1),
(7,2,8,93,2),
(8,1,1,0,1),
(8,2,9,123,2),
(9,2,10,137,1),
(9,3,11,137,2),
(10,2,10,137,1),
(10,3,12,137,2),
(11,2,10,137,1),
(11,3,13,137,2),
(12,2,10,137,1),
(12,3,14,137,2),
(13,2,10,137,1),
(13,3,15,137,2),
(14,2,10,137,1),
(14,3,16,137,2),
(15,2,10,137,1),
(15,3,17,137,2);

/*Table structure for table `product_description` */

DROP TABLE IF EXISTS `product_description`;

CREATE TABLE `product_description` (
  `desc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '描述值',
  PRIMARY KEY (`desc_id`),
  UNIQUE KEY `IDX_UNI` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_description` */

insert  into `product_description`(`desc_id`,`name`) values 
(105,'100支 200支 500支 1000支 2000支 3000支 50支'),
(46,'111V~240V（含）'),
(37,'11W(含)-15W(含)'),
(64,'15-19周岁 20-24周岁 25-29周岁 30-34周岁 35-39周岁 7-14周岁 40-59周岁 60周岁以上'),
(22,'150W'),
(58,'16CM-日落红-9瓦 16CM-日落红-12瓦-赠四色膜纸 27CM-日落红-9瓦-赠四色膜纸 27CM-日落红-12瓦-赠四色膜纸 40CM-日落红-15瓦-赠四色膜纸 台式伸缩款28CM-40CM-日落红--15瓦-赠四色膜纸 16C'),
(24,'1尺(13W 带电池) 2尺(25W 不带电池) 2尺(25W 带电池) 2尺(25W 带双电池) 2尺(25W 带电池+2.6米灯架) 2尺双灯套装(25W 带电池) 4尺(50W 不带电池) 4尺(50W 带电池) 4尺(50W 带电池'),
(91,'20-24周岁 25-29周岁 30-34周岁 35-39周岁'),
(13,'200W单灯标配 —【萌新性价比力荐】—【美颜嫩肤明肌】— 单灯套餐A【适用'),
(52,'2年'),
(103,'33.5cm'),
(33,'3C规格型号：见附件。220-240V～ 50/60Hz'),
(28,'50W'),
(61,'5年'),
(57,'6W(含)-10W(含)'),
(35,'DGX-88-11'),
(23,'Falconeyes/锐鹰'),
(5,'Goiden eagie'),
(56,'LDD-XYD020'),
(48,'LED'),
(62,'Pure Cashmere/全绒时代'),
(12,'tyd'),
(30,'undefined'),
(59,'≤36V(含)'),
(25,'东莞鹰科影视器材厂'),
(97,'中国'),
(90,'中年 情侣 青年'),
(80,'中年 老年 青年'),
(65,'主要材质'),
(32,'产品名称：固定式LED灯具（轨道安装式，LED模块用交流电子控制装置，Ⅱ类，IP20，适宜直接安装在普通可燃材料表面）'),
(96,'产地'),
(71,'人群'),
(70,'休闲'),
(92,'优雅'),
(9,'余姚金鹰摄影器材有限公司'),
(47,'光源类型'),
(73,'光身'),
(60,'其他'),
(55,'凡胜（家装主材）'),
(78,'出游'),
(21,'功率'),
(100,'包装'),
(99,'包装方式'),
(104,'包装规格'),
(94,'卷边'),
(7,'双灯套装：金鹰LED1500*2+280灯架*2+65cm柔光球*1+60*90cm柔光箱*1 三灯套装：金鹰LED1500*3+280灯架*3+65cm柔光球*1+60*90cm柔光箱*2'),
(68,'可调节'),
(26,'可调节色温 彩色 其他/other'),
(54,'否'),
(1,'品牌'),
(82,'圆顶'),
(11,'型号'),
(14,'套餐类型'),
(87,'女'),
(38,'安装方式'),
(41,'客厅 餐厅 厨房 书房 卧室 卫生间 其他/other'),
(67,'尺码'),
(44,'带光源'),
(85,'帽檐款式'),
(81,'帽顶款式'),
(49,'控制类型'),
(4,'摄影灯品牌'),
(86,'无檐'),
(39,'明轨'),
(77,'春季 秋季 冬季'),
(101,'是否带木柄'),
(53,'是否智能操控'),
(3,'材质'),
(15,'标准套餐'),
(18,'标准白光（5500k±200k或5600K±200K）'),
(75,'檐形'),
(10,'欣影'),
(83,'款式'),
(72,'款式细节'),
(84,'毛线帽/针织帽'),
(98,'浅灰色'),
(34,'澳名'),
(43,'灯具是否带光源'),
(8,'生产企业'),
(45,'电压'),
(89,'秋季 冬季'),
(88,'紫绒+青绒原色（少量现货） 深咖+青绒原色 深灰+浅灰色'),
(93,'红色（预售1月23日发货）'),
(16,'绍兴上虞星影器材有限公司'),
(66,'绒线'),
(17,'色温'),
(31,'证书状态：有效'),
(29,'证书编号：2020181001018043'),
(51,'质保年限'),
(19,'适用场景'),
(76,'适用季节'),
(79,'适用对象'),
(63,'适用年龄'),
(40,'适用空间'),
(20,'通用'),
(27,'通用 影棚摄影 户外'),
(95,'野猪林'),
(2,'金刀'),
(36,'铝'),
(102,'长度'),
(74,'青绒原色 炭灰 紫绒原色 枣红'),
(50,'非智能控制'),
(6,'颜色分类'),
(69,'风格'),
(42,'黑色/5头-5W-白光/欧司朗芯片+东菱驱动 黑色/5头-5W-暖光/欧司朗芯片+东菱驱动 黑色/5头-5W-中性光/欧司朗芯片+东菱驱动 白色/5头-5W-白光/欧司朗芯片+东菱驱动 白色/5头-5W-暖光/欧司朗芯片+东菱驱动 白色/5');

/*Table structure for table `product_description_language` */

DROP TABLE IF EXISTS `product_description_language`;

CREATE TABLE `product_description_language` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `desc_id` int(11) NOT NULL DEFAULT '0' COMMENT '描述ID',
  `lan_id` varchar(4) NOT NULL DEFAULT '' COMMENT '语言ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '翻译文本',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `IDX_UNI` (`desc_id`,`lan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_description_language` */

insert  into `product_description_language`(`item_id`,`desc_id`,`lan_id`,`name`) values 
(1,1,'1','品牌'),
(2,2,'1','金刀'),
(3,3,'1','材质');

/*Table structure for table `product_description_used` */

DROP TABLE IF EXISTS `product_description_used`;

CREATE TABLE `product_description_used` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `spu_id` int(11) NOT NULL DEFAULT '0' COMMENT 'spuID',
  `name_id` int(11) NOT NULL DEFAULT '0' COMMENT '名称ID',
  `value_id` int(11) NOT NULL DEFAULT '0' COMMENT '值ID',
  `sort` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `IND_UNI` (`spu_id`,`name_id`,`value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_description_used` */

insert  into `product_description_used`(`item_id`,`spu_id`,`name_id`,`value_id`,`sort`) values 
(1,1,1,62,0),
(2,1,63,64,0),
(3,1,65,66,0),
(4,1,67,68,0),
(5,1,69,70,0),
(6,1,71,20,0),
(7,1,72,73,0),
(9,1,75,60,0),
(10,1,76,77,0),
(11,1,19,78,0),
(12,1,79,80,0),
(13,1,81,82,0),
(14,1,83,84,0),
(15,1,85,86,0),
(16,2,1,62,0),
(17,2,63,64,0),
(18,2,65,66,0),
(19,2,67,68,0),
(20,2,69,70,0),
(21,2,71,87,0),
(22,2,72,73,0),
(24,2,75,60,0),
(25,2,76,89,0),
(26,2,19,78,0),
(27,2,79,90,0),
(28,2,81,82,0),
(29,2,83,84,0),
(30,2,85,60,0),
(31,3,1,62,0),
(32,3,63,91,0),
(33,3,65,66,0),
(34,3,67,68,0),
(35,3,69,92,0),
(36,3,71,87,0),
(37,3,72,73,0),
(39,3,75,94,0),
(40,3,76,89,0),
(41,3,19,78,0),
(42,3,79,90,0),
(43,3,81,82,0),
(44,3,83,84,0),
(45,3,85,86,0),
(46,4,1,95,0),
(47,4,96,97,0),
(49,4,99,100,0),
(50,4,101,54,0),
(51,4,102,103,0);

/*Table structure for table `product_introduce` */

DROP TABLE IF EXISTS `product_introduce`;

CREATE TABLE `product_introduce` (
  `spu_id` bigint(20) NOT NULL COMMENT 'spuID',
  `attach_id` bigint(20) NOT NULL COMMENT '文件ID',
  `sort` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`spu_id`,`attach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_introduce` */

insert  into `product_introduce`(`spu_id`,`attach_id`,`sort`) values 
(1,10,1),
(1,11,2),
(1,12,3),
(1,13,4),
(1,14,5),
(1,15,6),
(1,16,7),
(1,17,8),
(1,18,9),
(1,19,10),
(1,20,11),
(1,21,12),
(1,22,13),
(1,23,14),
(1,24,15),
(1,25,16),
(1,26,17),
(1,27,18),
(1,28,19),
(1,29,20),
(1,30,21),
(1,31,22),
(1,32,23),
(1,33,24),
(1,34,25),
(1,35,26),
(1,36,27),
(1,37,28),
(1,38,29),
(1,39,30),
(1,40,31),
(1,41,32),
(1,42,33),
(1,43,34),
(1,44,35),
(1,45,36),
(1,46,37),
(1,47,38),
(1,48,39),
(1,49,40),
(1,50,41),
(1,51,42),
(1,52,43),
(1,53,44),
(1,54,45),
(1,55,46),
(1,56,47),
(1,57,48),
(1,58,49),
(1,59,50),
(1,60,51),
(1,61,52),
(1,62,53),
(1,63,54),
(1,64,55),
(1,65,56),
(1,66,57),
(1,67,58),
(1,68,59),
(1,69,60),
(1,70,61),
(1,71,62),
(1,72,63),
(1,73,64),
(1,74,65),
(1,75,66),
(1,76,67),
(1,77,68),
(1,78,69),
(1,79,70),
(1,80,71),
(1,81,72),
(1,82,73),
(1,83,74),
(1,84,75),
(1,85,76),
(2,10,1),
(2,11,2),
(2,12,3),
(2,13,4),
(2,14,5),
(2,15,6),
(2,16,7),
(2,17,8),
(2,18,9),
(2,19,10),
(2,20,11),
(2,21,12),
(2,22,13),
(2,23,14),
(2,24,15),
(2,25,16),
(2,26,17),
(2,27,18),
(2,28,19),
(2,29,20),
(2,30,21),
(2,31,22),
(2,32,23),
(2,33,24),
(2,34,25),
(2,35,26),
(2,36,27),
(2,37,28),
(2,38,29),
(2,39,30),
(2,40,31),
(2,41,32),
(2,42,33),
(2,43,34),
(2,44,35),
(2,45,36),
(2,46,37),
(2,47,38),
(2,48,39),
(2,49,40),
(2,50,41),
(2,51,42),
(2,52,43),
(2,53,44),
(2,54,45),
(2,55,46),
(2,56,47),
(2,57,48),
(2,58,49),
(2,59,50),
(2,60,51),
(2,61,52),
(2,84,78),
(2,85,79),
(2,94,53),
(2,95,54),
(2,96,55),
(2,97,56),
(2,98,57),
(2,99,58),
(2,100,59),
(2,101,60),
(2,102,61),
(2,103,62),
(2,104,63),
(2,105,64),
(2,106,65),
(2,107,66),
(2,108,67),
(2,109,68),
(2,110,69),
(2,111,70),
(2,112,71),
(2,113,72),
(2,114,73),
(2,115,74),
(2,116,75),
(2,117,76),
(2,118,77),
(3,10,1),
(3,11,2),
(3,12,3),
(3,13,4),
(3,14,5),
(3,15,6),
(3,16,7),
(3,17,8),
(3,18,9),
(3,19,10),
(3,20,11),
(3,21,12),
(3,22,13),
(3,23,14),
(3,24,15),
(3,25,16),
(3,26,17),
(3,27,18),
(3,28,19),
(3,29,20),
(3,30,21),
(3,31,22),
(3,32,23),
(3,33,24),
(3,34,25),
(3,35,26),
(3,36,27),
(3,37,28),
(3,38,29),
(3,39,30),
(3,40,31),
(3,41,32),
(3,42,33),
(3,43,34),
(3,44,35),
(3,45,36),
(3,46,37),
(3,47,38),
(3,48,39),
(3,49,40),
(3,50,41),
(3,51,42),
(3,52,43),
(3,53,44),
(3,54,45),
(3,55,46),
(3,56,47),
(3,57,48),
(3,58,49),
(3,59,50),
(3,124,51),
(3,125,52),
(3,126,53),
(3,127,54),
(3,128,55),
(3,129,56),
(3,130,57),
(4,138,1),
(4,139,2),
(4,140,3),
(4,141,4),
(4,142,5),
(4,143,6),
(4,144,7),
(4,145,8),
(4,146,9),
(4,147,10),
(4,148,11);

/*Table structure for table `product_language` */

DROP TABLE IF EXISTS `product_language`;

CREATE TABLE `product_language` (
  `spu_id` int(11) NOT NULL DEFAULT '0' COMMENT 'spuID',
  `lan_id` varchar(4) NOT NULL DEFAULT '' COMMENT '语言ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  PRIMARY KEY (`spu_id`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_language` */

insert  into `product_language`(`spu_id`,`lan_id`,`name`) values 
(1,'en','Pure cashmere British single guest cashmere hat female two-color double-sided men\'s tide autumn and winter warm knitting'),
(1,'zh','Pure cashmere英单客供 羊绒帽子女双色双层边男潮秋冬季保暖针织'),
(2,'zh','Pure Cashmere 纯羊绒绞花针织毛线帽子男女士双面双色两用秋冬潮'),
(3,'zh','Pure cashmere羊绒针织帽子红色 女士秋冬季英伦风抽条翻边百搭潮'),
(4,'zh','烧烤签子不锈钢羊肉串烤肉烤串家用商用钢签用品工具铁签子字钎子');

/*Table structure for table `product_sku` */

DROP TABLE IF EXISTS `product_sku`;

CREATE TABLE `product_sku` (
  `sku_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `spu_id` int(11) NOT NULL COMMENT '所属skuID',
  `site_id` tinyint(2) NOT NULL DEFAULT '0' COMMENT '站点ID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `attach_id` int(11) NOT NULL DEFAULT '0' COMMENT '主图ID',
  `stock` smallint(6) NOT NULL DEFAULT '0' COMMENT '库存',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `original_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `cost_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `sale_total` mediumint(9) NOT NULL DEFAULT '0' COMMENT '销量',
  `item_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '源skuID',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`sku_id`),
  KEY `IDX_SPU` (`spu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_sku` */

insert  into `product_sku`(`sku_id`,`spu_id`,`site_id`,`status`,`attach_id`,`stock`,`price`,`original_price`,`cost_price`,`sale_total`,`item_id`,`add_time`) values 
(1,1,80,0,6,99,434.00,559.86,198.00,102,4181591372126,'2021-08-06 14:22:10'),
(2,1,80,0,7,99,398.00,557.20,198.00,102,4252834324370,'2021-08-06 14:22:10'),
(3,1,80,1,8,95,435.00,656.85,198.00,102,4252834324369,'2021-08-06 14:22:10'),
(4,1,80,1,9,99,421.00,538.88,198.00,102,4181591372125,'2021-08-06 14:22:11'),
(5,2,80,1,91,98,501.00,651.30,256.00,102,3915555104014,'2021-08-06 14:22:37'),
(6,2,80,1,92,99,553.00,763.14,256.00,102,3915555104013,'2021-08-06 14:22:38'),
(7,2,80,1,93,99,538.00,785.48,256.00,102,3915555104015,'2021-08-06 14:22:38'),
(8,3,80,1,123,99,479.00,661.02,225.00,102,4721222454096,'2021-08-06 14:23:04'),
(9,4,80,1,137,9021,223.50,353.13,7.50,102,4769368927595,'2021-08-06 14:25:30'),
(10,4,80,1,137,9898,335.80,423.11,147.80,102,4769368927599,'2021-08-06 14:25:30'),
(11,4,80,1,137,9785,313.80,429.91,74.80,102,4769368927598,'2021-08-06 14:25:30'),
(12,4,80,1,137,1385,213.60,271.27,14.60,102,4769368927596,'2021-08-06 14:25:30'),
(13,4,80,1,137,9989,565.80,752.51,289.80,102,4643062193197,'2021-08-06 14:25:30'),
(14,4,80,1,137,8855,246.90,375.29,29.90,102,4769368927597,'2021-08-06 14:25:30'),
(15,4,80,1,137,9986,752.40,1158.70,438.40,102,4643062193196,'2021-08-06 14:25:30');

/*Table structure for table `product_spu` */

DROP TABLE IF EXISTS `product_spu`;

CREATE TABLE `product_spu` (
  `spu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `site_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '站点ID',
  `cate_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `rank` mediumint(9) NOT NULL DEFAULT '0' COMMENT '综合排名',
  `attach_id` int(11) NOT NULL DEFAULT '0' COMMENT '主图ID',
  `min_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '最小价格',
  `max_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '最大价格',
  `original_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `sale_total` mediumint(9) NOT NULL DEFAULT '0' COMMENT '销量总数',
  `visit_total` int(11) NOT NULL DEFAULT '0' COMMENT '点击统计',
  `free_ship` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否免运费',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`spu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_spu` */

insert  into `product_spu`(`spu_id`,`site_id`,`cate_id`,`status`,`rank`,`attach_id`,`min_price`,`max_price`,`original_price`,`sale_total`,`visit_total`,`free_ship`,`add_time`) values 
(1,80,24,1,0,119,398.00,435.00,674.25,0,0,0,'2021-08-06 14:22:10'),
(2,80,24,1,0,131,501.00,553.00,624.89,0,0,0,'2021-08-06 14:22:37'),
(3,80,25,1,0,119,479.00,479.00,574.80,0,0,0,'2021-08-06 14:23:04'),
(4,80,4,1,0,131,213.60,752.40,857.74,0,0,0,'2021-08-06 14:25:27');

/*Table structure for table `product_spu_data` */

DROP TABLE IF EXISTS `product_spu_data`;

CREATE TABLE `product_spu_data` (
  `spu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'spuID',
  `site_id` tinyint(4) NOT NULL COMMENT '站点ID',
  `supplier_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `item_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '源产品ID',
  `item_url` varchar(250) NOT NULL DEFAULT '' COMMENT '源产品URL',
  `shop_name` varchar(50) NOT NULL DEFAULT '' COMMENT '源商家名称',
  `shop_url` varchar(250) NOT NULL DEFAULT '' COMMENT '源商家URL',
  `check_result` tinyint(1) NOT NULL DEFAULT '0' COMMENT '检查状态',
  `check_time` timestamp NULL DEFAULT NULL COMMENT '检查时间',
  PRIMARY KEY (`spu_id`),
  UNIQUE KEY `UNI_ID` (`site_id`,`supplier_id`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_spu_data` */

insert  into `product_spu_data`(`spu_id`,`site_id`,`supplier_id`,`item_id`,`item_url`,`shop_name`,`shop_url`,`check_result`,`check_time`) values 
(1,80,2,599739071372,'https://item.taobao.com/item.htm?id=599739071372','Pure cashmere','//shop67319432.taobao.com/',0,NULL),
(2,80,2,582658200822,'https://item.taobao.com/item.htm?id=582658200822','Pure cashmere','//shop67319432.taobao.com/',0,NULL),
(3,80,2,577688567287,'https://item.taobao.com/item.htm?id=577688567287','Pure cashmere','//shop67319432.taobao.com/',0,NULL),
(4,80,2,639481984749,'https://item.taobao.com/item.htm?id=639481984749','米尚户外烧烤用品','//shop145619525.taobao.com/',0,NULL);

/*Table structure for table `product_spu_image` */

DROP TABLE IF EXISTS `product_spu_image`;

CREATE TABLE `product_spu_image` (
  `spu_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'skuID',
  `attach_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '图片文件ID',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`spu_id`,`attach_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `product_spu_image` */

insert  into `product_spu_image`(`spu_id`,`attach_id`,`sort`) values 
(1,1,1),
(1,2,2),
(1,3,3),
(1,4,4),
(1,5,5),
(2,86,1),
(2,87,2),
(2,88,3),
(2,89,4),
(2,90,5),
(3,119,1),
(3,120,2),
(3,121,3),
(3,122,4),
(3,123,5),
(4,131,1),
(4,132,2),
(4,133,3),
(4,134,4),
(4,135,5),
(4,136,6);

/*Table structure for table `site` */

DROP TABLE IF EXISTS `site`;

CREATE TABLE `site` (
  `site_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `domain` varchar(64) NOT NULL DEFAULT '' COMMENT '域名',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT 'title',
  `keyword` varchar(255) NOT NULL DEFAULT '' COMMENT 'keyword',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'desc',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '内部使用备注',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4;

/*Data for the table `site` */

insert  into `site`(`site_id`,`name`,`domain`,`title`,`keyword`,`description`,`remark`,`add_time`) values 
(0,'admin','https://lmr.admin.cn/','管理后台','','','','2021-08-27 18:01:45'),
(80,'bag','https://lmr.bag.cn/','PrettyBag','Litfad.com采用简化的商业模式，提供面向客户的服务；整合资源，提高运营效率，同时降低流通和交易成本。Litfad致力于为客户提供“快速、卓越、有价值”的购物体验。Litfad.com涵盖家庭照明产品，如吊灯、吊灯、壁灯、水晶灯、工业风格照明等。在线产品的SKU总数超过10万。','Litfad.com采用简化的商业模式，提供面向客户的服务；整合资源，提高运营效率，同时降低流通和交易成本。Litfad致力于为客户提供“快速、卓越、有价值”的购物体验。Litfad.com涵盖家庭照明产品，如吊灯、吊灯、壁灯、水晶灯、工业风格照明等。在线产品的SKU总数超过10万。','','2021-08-27 18:01:45');

/*Table structure for table `site_currency_used` */

DROP TABLE IF EXISTS `site_currency_used`;

CREATE TABLE `site_currency_used` (
  `site_id` int(11) NOT NULL COMMENT '对应站点ID',
  `code` varchar(4) NOT NULL DEFAULT '' COMMENT '语言码',
  `sort` smallint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`site_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `site_currency_used` */

insert  into `site_currency_used`(`site_id`,`code`,`sort`) values 
(80,'EUR',2),
(80,'GBP',1),
(80,'USD',0);

/*Table structure for table `site_language` */

DROP TABLE IF EXISTS `site_language`;

CREATE TABLE `site_language` (
  `site_id` tinyint(1) NOT NULL COMMENT '站点ID',
  `lan_id` varchar(4) NOT NULL COMMENT '语言码',
  `type` varchar(20) NOT NULL COMMENT '类型',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '翻译文本',
  PRIMARY KEY (`site_id`,`lan_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `site_language` */

/*Table structure for table `site_language_used` */

DROP TABLE IF EXISTS `site_language_used`;

CREATE TABLE `site_language_used` (
  `site_id` int(11) NOT NULL COMMENT '对应站点ID',
  `code` varchar(4) NOT NULL DEFAULT '' COMMENT '语言码',
  `sort` smallint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`site_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `site_language_used` */

insert  into `site_language_used`(`site_id`,`code`,`sort`) values 
(80,'en',0),
(80,'zh',0);

/*Table structure for table `site_static_file` */

DROP TABLE IF EXISTS `site_static_file`;

CREATE TABLE `site_static_file` (
  `static_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `type` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`name`,`type`),
  KEY `static_id` (`static_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `site_static_file` */

insert  into `site_static_file`(`static_id`,`name`,`type`,`status`) values 
(74,'admin/static/c_attribute_attrValue','css',0),
(75,'admin/static/c_attribute_attrValue','js',0),
(76,'admin/static/c_attribute_description','css',0),
(77,'admin/static/c_attribute_description','js',0),
(72,'admin/static/c_attribute_index','css',0),
(73,'admin/static/c_attribute_index','js',0),
(29,'admin/static/c_category_index','css',0),
(30,'admin/static/c_category_index','js',0),
(5,'admin/static/c_common','css',0),
(6,'admin/static/c_common','js',0),
(7,'admin/static/c_index_index','css',0),
(8,'admin/static/c_index_index','js',0),
(26,'admin/static/c_index_statInfo','css',0),
(27,'admin/static/c_index_statInfo','js',0),
(3,'admin/static/c_login_index','css',0),
(4,'admin/static/c_login_index','js',0),
(28,'admin/static/c_member_index','js',0),
(80,'admin/static/c_payment_index','css',0),
(81,'admin/static/c_payment_index','js',0),
(78,'admin/static/c_product_detail','css',0),
(79,'admin/static/c_product_detail','js',0),
(19,'admin/static/c_product_index','css',0),
(31,'admin/static/c_site_index','js',0),
(70,'admin/static/c_site_siteInfo','css',0),
(71,'admin/static/c_site_siteInfo','js',0),
(32,'admin/static/c_site_staticCache','js',0),
(34,'admin/static/c_task_index','css',0),
(35,'admin/static/c_task_index','js',0),
(33,'admin/static/c_transfer_index','js',0),
(82,'admin/static/m_common','css',0),
(84,'admin/static/m_common','js',0),
(83,'admin/static/m_login_index','css',0),
(85,'admin/static/m_login_index','js',0),
(39,'bag/static/c_cart_index','css',0),
(40,'bag/static/c_cart_index','js',0),
(64,'bag/static/c_checkout_index','css',0),
(65,'bag/static/c_checkout_index','js',0),
(9,'bag/static/c_common','css',0),
(11,'bag/static/c_common','js',0),
(10,'bag/static/c_index_index','css',0),
(12,'bag/static/c_index_index','js',0),
(22,'bag/static/c_login_index','css',0),
(23,'bag/static/c_login_index','js',0),
(49,'bag/static/c_pageNotFound_index','css',0),
(43,'bag/static/c_product_index','css',0),
(44,'bag/static/c_product_index','js',0),
(45,'bag/static/c_userInfo_address','css',0),
(46,'bag/static/c_userInfo_address','js',0),
(52,'bag/static/c_userInfo_index','css',0),
(53,'bag/static/c_userInfo_index','js',0),
(41,'bag/static/m_cart_index','css',0),
(42,'bag/static/m_cart_index','js',0),
(20,'bag/static/m_category_index','css',1),
(21,'bag/static/m_category_index','js',1),
(62,'bag/static/m_checkout_index','css',0),
(63,'bag/static/m_checkout_index','js',0),
(66,'bag/static/m_checkout_payOrder','css',0),
(67,'bag/static/m_checkout_payOrder','js',0),
(13,'bag/static/m_common','css',0),
(15,'bag/static/m_common','js',0),
(60,'bag/static/m_contact_index','css',0),
(61,'bag/static/m_contact_index','js',0),
(14,'bag/static/m_index_index','css',0),
(16,'bag/static/m_index_index','js',0),
(24,'bag/static/m_login_forget','css',1),
(25,'bag/static/m_login_forget','js',1),
(17,'bag/static/m_login_index','css',0),
(18,'bag/static/m_login_index','js',0),
(68,'bag/static/m_newIn_index','css',0),
(69,'bag/static/m_newIn_index','js',0),
(36,'bag/static/m_pageNotFound_index','css',0),
(37,'bag/static/m_product_index','css',0),
(38,'bag/static/m_product_index','js',0),
(47,'bag/static/m_userInfo_address','css',0),
(48,'bag/static/m_userInfo_address','js',0),
(54,'bag/static/m_userInfo_collection','css',0),
(55,'bag/static/m_userInfo_collection','js',0),
(58,'bag/static/m_userInfo_history','css',0),
(59,'bag/static/m_userInfo_history','js',0),
(50,'bag/static/m_userInfo_index','css',0),
(51,'bag/static/m_userInfo_index','js',0),
(56,'bag/static/m_userInfo_wishList','css',0),
(57,'bag/static/m_userInfo_wishList','js',0),
(1,'static/c_login_index','css',0),
(2,'static/c_login_index','js',0);

/*Table structure for table `translate` */

DROP TABLE IF EXISTS `translate`;

CREATE TABLE `translate` (
  `tran_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(150) NOT NULL DEFAULT '' COMMENT '名称',
  `type` char(10) NOT NULL DEFAULT '' COMMENT '类型',
  `value` varchar(150) NOT NULL DEFAULT '' COMMENT '值',
  PRIMARY KEY (`tran_id`),
  UNIQUE KEY `IDX_UNION` (`name`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `translate` */

insert  into `translate`(`tran_id`,`name`,`type`,`value`) values 
(1,'搜索宝贝','en','search');

/*Table structure for table `zone` */

DROP TABLE IF EXISTS `zone`;

CREATE TABLE `zone` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_code` varchar(64) NOT NULL COMMENT '地区代号',
  `country_code2` varchar(2) NOT NULL COMMENT '国家代号',
  `name_cn` varchar(32) DEFAULT NULL COMMENT '中文名称',
  `name_en` varchar(64) DEFAULT NULL COMMENT '英文名称',
  `sort` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`zone_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

/*Data for the table `zone` */

insert  into `zone`(`zone_id`,`zone_code`,`country_code2`,`name_cn`,`name_en`,`sort`) values 
(1,'ACT','AU','澳大利亚首都领地','Australian Capital Territory',1),
(2,'NSW','AU','新南威尔士州','New South Wales',2),
(3,'NT','AU','北领地','Northern Territory',3),
(4,'QLD','AU','昆士兰州','Queensland',4),
(5,'SA','AU','南澳大利亚州','South Australia',5),
(6,'TAS','AU','塔斯马尼亚','Tasmania',6),
(7,'VIC','AU','维多利亚州','Victoria',7),
(8,'WA','AU','西澳大利亚州','Western Australia',8),
(9,'AB','CA','阿尔伯塔省','Alberta',1),
(10,'BC','CA','不列颠哥伦比亚省','British Columbia',2),
(11,'MB','CA','马尼托巴省','Manitoba',3),
(12,'NB','CA','新不伦瑞克省','New Brunswick',4),
(13,'NL','CA','纽芬兰与拉布拉多省','Newfoundland and Labrador',5),
(14,'NS','CA','新斯科舍省','Nova Scotia',6),
(15,'NT','CA','西北地区','Northwest Territories',7),
(16,'NU','CA','努纳武特地区','Nunavut',8),
(17,'ON','CA','安大略省','Ontario',9),
(18,'PE','CA','爱德华王子岛省','Prince Edward Island',10),
(19,'QC','CA','魁北克省','Quebec',11),
(20,'SK','CA','萨斯喀彻温省','Saskatchewan',12),
(21,'YT','CA','育空地区','Yukon',13),
(22,'BB','DE','勃兰登堡','Brandenburg',1),
(23,'BE','DE','柏林','Berlin',2),
(24,'BW','DE','巴登-符腾堡','Baden-Wrttemberg',3),
(25,'BY','DE','拜仁','Bayern',4),
(26,'HB','DE','不莱梅','Bremen',5),
(27,'HE','DE','黑森','Hessen',6),
(28,'HH','DE','汉堡','Hamburg',7),
(29,'MV','DE','梅克伦堡-前波美尼亚','Mecklenburg-Vorpommern',8),
(30,'NI','DE','下萨克森','Niedersachsen',9),
(31,'NRW','DE','北莱茵-威斯特法伦','Nordrhein-Westfalen',10),
(32,'RP','DE','莱茵兰-普法尔茨','Rheinland-Pfalz',11),
(33,'SE','DE','萨克森','Sachsen',12),
(34,'SH','DE','石勒苏益格-荷尔斯泰因','Schleswig-Holstein',13),
(35,'SL','DE','萨尔','Saarland',14),
(36,'ST','DE','萨克森-安哈尔特','Sachsen-Anhalt',15),
(37,'TH','DE','图林根','Thuringen',16),
(38,'AA','US','美国军事基地','Armed Forces Americas',1),
(39,'AC','US','加拿大军事基地','Armed Forces Canada',2),
(40,'AE','US','欧洲军事基地','Armed Forces Europe',3),
(41,'AF','US','非洲军事基地','Armed Forces Africa',4),
(42,'AK','US','阿拉斯加州','Alaska',5),
(43,'AL','US','阿拉巴马州','Alabama',6),
(44,'AM','US','中东军事基地','Armed Forces Middle East',7),
(45,'AP','US','太平洋军事基地','Armed Forces Pacific',8),
(46,'AR','US','阿肯色州','Arkansas',9),
(47,'AZ','US','亚利桑那州','Arizona',10),
(48,'CA','US','加利福尼亚州','California',11),
(49,'CO','US','科罗拉多州','Colorado',12),
(50,'CT','US','康涅狄格州','Connecticut',13),
(51,'DC','US','华盛顿哥伦比亚特区','District of Columbia',14),
(52,'DE','US','特拉华州','Delaware',15),
(53,'FL','US','佛罗里达州','Florida',16),
(54,'GA','US','乔治亚州','Georgia',17),
(55,'HI','US','夏威夷州','Hawaii',18),
(56,'IA','US','爱荷华州','Iowa',19),
(57,'ID','US','爱达荷州','Idaho',20),
(58,'IL','US','伊利诺斯州','Illinois',21),
(59,'IN','US','印第安纳州','Indiana',22),
(60,'KS','US','堪萨斯州','Kansas',23),
(61,'KY','US','肯塔基州','Kentucky',24),
(62,'LA','US','路易斯安那州','Louisiana',25),
(63,'MA','US','马萨诸塞州 (麻省、麻州)','Massachusetts',26),
(64,'MD','US','马里兰州','Maryland',27),
(65,'ME','US','缅因州','Maine',28),
(66,'MI','US','密歇根州','Michigan',29),
(67,'MN','US','明尼苏达州','Minnesota',30),
(68,'MO','US','密苏里州','Missouri',31),
(69,'MS','US','密西西比州','Mississippi',32),
(70,'MT','US','蒙大拿州','Montana',33),
(71,'NC','US','北卡罗来纳州','North Carolina',34),
(72,'ND','US','北达科他州','North Dakota',35),
(73,'NE','US','内布拉斯加州','Nebraska',36),
(74,'NH','US','新罕布什尔州','New hampshire',37),
(75,'NJ','US','新泽西州','New jersey',38),
(76,'NM','US','新墨西哥州','New mexico',39),
(77,'NV','US','内华达州','Nevada',40),
(78,'NY','US','纽约州','New York',41),
(79,'OH','US','俄亥俄州','Ohio',42),
(80,'OK','US','俄克拉荷马州','Oklahoma',43),
(81,'OR','US','俄勒冈州','Oregon',44),
(82,'PA','US','宾夕法尼亚州','Pennsylvania',45),
(83,'RI','US','罗得岛州','Rhode island',46),
(84,'SC','US','南卡罗来纳州','South carolina',47),
(85,'SD','US','南达科他州','South dakota',48),
(86,'TN','US','田纳西州','Tennessee',49),
(87,'TX','US','得克萨斯州','Texas',50),
(88,'UT','US','犹他州','Utah',51),
(89,'VA','US','弗吉尼亚州','Virginia',52),
(90,'VT','US','佛蒙特州','Vermont',53),
(91,'WA','US','华盛顿州','Washington',54),
(92,'WI','US','威斯康辛州','Wisconsin',55),
(93,'WV','US','西弗吉尼亚州','West Virginia',56),
(94,'WY','US','怀俄明州','Wyoming',57);

/*Table structure for table `zone_languge` */

DROP TABLE IF EXISTS `zone_languge`;

CREATE TABLE `zone_languge` (
  `zone_id` int(11) NOT NULL COMMENT '省洲ID',
  `lan_id` tinyint(1) NOT NULL COMMENT '语言ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '文本',
  PRIMARY KEY (`zone_id`,`lan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `zone_languge` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
