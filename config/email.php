<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  $config['protocol']  = 'smtp';//采用smtp方式
  $config['smtp_host'] = 'smtp.163.com';
  $config['smtp_user'] = 'immoart@163.com';//你的邮箱帐号
  $config['smtp_pass'] = '123456aa';//你的邮箱密码
  //$config['smtp_pass'] = 25;
  $config['charset']   = 'utf-8';
  $config['wordwrap']  = TRUE;
  $config['mailtype']  = "html";