<?php
/*===== SYSTEM ===============================================================*/
define('BASE_TITLE',$_ENV['BASE_TITLE']);
define('BASE_URL',$_ENV['BASE_URL']);
// define('BASE_URL',$_ENV['http://192.168.157.1:88/Pos5FC_R2/']);
// define('BASE_DATABASE',$_ENV['PTT_AdaPos5tester']);
// define('BASE_DATABASE',$_ENV['AdaAcc00002_PTT_Production']);
// define('BASE_DATABASE',$_ENV['SKC_Fullloop2']);
define('BASE_DATABASE',$_ENV['BASE_DATABASE']);
define('DATABASE_IP',$_ENV['DATABASE_IP']);
define('SYS_BCH_CODE',$_ENV['SYS_BCH_CODE']);
define('DATABASE_USERNAME',$_ENV['DATABASE_USERNAME']);
define('DATABASE_PASSWORD',$_ENV['DATABASE_PASSWORD']);
// define('BASE_DATABASE',$_ENV['SKC_Fullloop_18082020']);
// define('DATABASE_IP',$_ENV['.']);
// define('SYS_BCH_CODE',$_ENV['00003']);
// define('DATABASE_USERNAME',$_ENV['sa']);
// define('DATABASE_PASSWORD',$_ENV['123456']);

// define('BASE_DATABASE',$_ENV['AdaVending_Tester']);
// define('DATABASE_IP',$_ENV['202.44.55.94']);
// define('DATABASE_USERNAME',$_ENV['sa']);
// define('DATABASE_PASSWORD',$_ENV['Ad@soft2016']);
/*===== RABBIT MQ ============================================================*/
define('HOST',$_ENV['HOST']); // Server
define('USER',$_ENV['USER']);
define('PASS',$_ENV['PASS']);
define('VHOST',$_ENV['VHOST']);
define('EXCHANGE',$_ENV['EXCHANGE']);
define('PORT', $_ENV['PORT']);
// define('HOST',$_ENV['202.44.55.94']); // Server
// define('USER',$_ENV['Admin']);
// define('PASS',$_ENV['Admin']);
// define('VHOST',$_ENV['AdaPos5.0Doc_Vending']);
// define('EXCHANGE',$_ENV['']);
// define('PORT', 5672);
/*===== REPORT RABBIT MQ =====================================================*/
// define('MQ_REPORT_HOST','202.44.55.96']);
// define('MQ_REPORT_USER','Admin']);
// define('MQ_REPORT_PASS','Admin']);
// define('MQ_REPORT_VHOST','AdaPos5.0Report']);
// define('MQ_REPORT_EXCHANGE',$_ENV['']);
// define('MQ_REPORT_PORT', 5672);
/*===== LOCKER Booking RABBIT MQ =====================================================*/
define('MQ_BOOKINGLK_HOST',$_ENV['MQ_BOOKINGLK_HOST']);
define('MQ_BOOKINGLK_USER',$_ENV['MQ_BOOKINGLK_USER']);
define('MQ_BOOKINGLK_PASS',$_ENV['MQ_BOOKINGLK_PASS']);
define('MQ_BOOKINGLK_VHOST',$_ENV['MQ_BOOKINGLK_VHOST']);
define('MQ_BOOKINGLK_EXCHANGE',$_ENV['MQ_BOOKINGLK_EXCHANGE']);
define('MQ_BOOKINGLK_PORT', $_ENV['MQ_BOOKINGLK_PORT']);
//========= Interface ==================///
define('INTERFACE_HOST',$_ENV['INTERFACE_HOST']);
define('INTERFACE_USER',$_ENV['INTERFACE_USER']);
define('INTERFACE_PASS',$_ENV['INTERFACE_PASS']);
define('INTERFACE_VHOST',$_ENV['INTERFACE_VHOST']);
define('INTERFACE_EXCHANGE',$_ENV['INTERFACE_EXCHANGE']);
define('INTERFACE_PORT', $_ENV['INTERFACE_PORT']);

//============== Member ==================//
define('MemberV5_HOST',$_ENV['MemberV5_HOST']);
define('MemberV5_USER',$_ENV['MemberV5_USER']);
define('MemberV5_PASS',$_ENV['MemberV5_PASS']);
define('MemberV5_VHOST',$_ENV['MemberV5_VHOST']);
define('MemberV5_EXCHANGE',$_ENV['MemberV5_EXCHANGE']);
define('MemberV5_PORT', $_ENV['MemberV5_PORT']);

// ของ  local เครื่องตัวเอง เวลาที่จะ เช็ค report
define('MQ_REPORT_HOST',$_ENV['MQ_REPORT_HOST']);
define('MQ_REPORT_USER',$_ENV['MQ_REPORT_USER']);
define('MQ_REPORT_PASS',$_ENV['MQ_REPORT_PASS']);
define('MQ_REPORT_VHOST',$_ENV['MQ_REPORT_VHOST']);
define('MQ_REPORT_EXCHANGE',$_ENV['MQ_REPORT_EXCHANGE']);
define('MQ_REPORT_PORT', $_ENV['MQ_REPORT_PORT']);

// ของ  local เครื่องตัวเอง เวลาที่จะ เช็ค report
// define('MQ_REPORT_HOST','172.16.30.210']);
// define('MQ_REPORT_USER','Admin']);
// define('MQ_REPORT_PASS','Admin']);
// define('MQ_REPORT_VHOST','AdaPos5.0Report']);
// define('MQ_REPORT_EXCHANGE',$_ENV['']);
// define('MQ_REPORT_PORT', 5672);

// ของ Request Sale
define('MQ_Sale_HOST',$_ENV['MQ_Sale_HOST']);
define('MQ_Sale_USER',$_ENV['MQ_Sale_USER']);
define('MQ_Sale_PASS',$_ENV['MQ_Sale_PASS']);
define('MQ_Sale_VHOST',$_ENV['MQ_Sale_VHOST']);
define('MQ_Sale_QUEUES',$_ENV['MQ_Sale_QUEUES']);
define('MQ_Sale_EXCHANGE',$_ENV['MQ_Sale_EXCHANGE']);
define('MQ_Sale_PORT', $_ENV['MQ_Sale_PORT']);


$aLastVersion = file(FCPATH.'version_deploy.txt');
define('VERSION_DEPLOY',$aLastVersion[0]);