<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $config = array(
    	'artist_reg'=>array(
    		array(
    			'field'=>'username',
    			'lable'=>'用户名',
    			'rules'=>'required|min_length[5]|max_length[12]|htmlspecialchars',
    			),
    		array(
    			'field'=>'password',
    			'lable'=>'密码',
    			'rules'=>'required|matches[passconf]|htmlspecialchars',
    			),
    		array(
    			'field'=>'passconf',
    			'lable'=>'密码',
    			'rules'=>'Password Confirmation', 'required|htmlspecialchars',
    			),
    		array(
    			'field'=>'email',
    			'lable'=>'邮箱',
    			'rules'=>'required|valid_email|is_unique[users.email]',
    			),
    	),
        'artist_login'=>array(
            array(
                'field'=>'password',
                'lable'=>'密码',
                'rules'=>'required',
            ),
            array(
                'field'=>'login',
                'lable'=>'邮箱/用户名',
                'rules'=>'required',
                // 'rules'=>'required|valid_email|is_unique[users.email]',
            ),
        ),
        'artist_edit'=>array(
            array(
                'field'=>'realname',
                'lable'=>'姓名',
                'rules'=>'required|min_length[3]|max_length[12]|htmlspecialchars',
                ),
            array(
                'field'=>'intro',
                'lable'=>'简介',
                'rules'=>'required|min_length[20]|max_length[600]|htmlspecialchars',
                ),
        ),
        'pro_submit'=>array(
            // array(
            //     'field'=>'price',
            //     'lable'=>'价格',
            //     'rules'=>'required|max_length[6]|htmlspecialchars',
            //     ),
            array(
                'field'=>'name',
                'lable'=>'名称',
                'rules'=>'required|min_length[5]|htmlspecialchars',
                ),
            // array(
            //     'field'=>'size',
            //     'lable'=>'尺寸',
            //     'rules'=>'required|numeric|max_le1ngth[10]|htmlspecialchars',
            //     ),
            // array(
            //     'field'=>'width',
            //     'lable'=>'宽度',
            //     'rules'=>'required|numeric|htmlspecialchars',
            //     ),
            // array(
            //     'field'=>'height',
            //     'lable'=>'高度',
            //     'rules'=>'required|numeric|htmlspecialchars',
            //     ),
            array(
                'field'=>'description',
                'lable'=>'描述',
                'rules'=>'required|max_le1ngth[100]',
                ),
        ),

    );