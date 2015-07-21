<?php
/* Copyright (C) Pushwing <http://www.pushwing.com> */
if(!defined("__XE__")) exit();

/**
 * @file pushwing.addon.php
 * @author hoksi (hoksi@naver.com)
 * @brief Pushwing addon
 * */
if(!class_exists('PushWing'))
{
    class PushWing {
        var $addon_info;
        
        function init(&$addon_info)
        {
            $this->addon_info = $addon_info;
        }

        function after_module_proc($obj)
        {
            if($obj->act == 'procBoardInsertDocument') {
                $board_name = $obj->module_info->browser_title;
                if(!strncmp($board_name, '$user_lang->', 12)) {
                    $oMctrl = &getController('module');
                    $oMctrl->replaceDefinedLangCode($board_name);
                }
                
                $this->send_pushwing(array(
                    'board_name' => $board_name,
                    'document_srl' => $obj->variables['document_srl'],
                    'data' => Context::getRequestVars()
                ));
            }
            
        }
        
        function send_pushwing($pdata)
        {
            $config = array(
                'server' => strip_tags(isset($this->addon_info->server) && $this->addon_info->server ? $this->addon_info->server : 'www.pushwing.com'),
                'user' => strip_tags(isset($this->addon_info->pushwing_id) && $this->addon_info->pushwing_id ? $this->addon_info->pushwing_id : ''),
                'password' => strip_tags(isset($this->addon_info->pushwing_pw) && $this->addon_info->pushwing_pw ? $this->addon_info->pushwing_pw : ''),
                'mno' => isset($this->addon_info->pushwing_mno) && $this->addon_info->pushwing_mno ? $this->addon_info->pushwing_mno : '',
                'client_id' => addslashes(strip_tags(isset($this->addon_info->client_id) && $this->addon_info->client_id ? $this->addon_info->client_id : ''))
            );
            
            $con = mysql_connect($config['server'], $config['user'], $config['password']);

            if($con) {
                mysql_select_db("pushwing", $con);
				mysql_query('set names utf8');
                
                $subject_msg = addslashes(cut_str(strip_tags('[' . $pdata['board_name'] . '] 게시물 등록'),20,'...'));
                $content_msg = addslashes(cut_str('제목 : ' . strip_tags(!empty($pdata['data']->title) ? $pdata['data']->title : 'Untitled'),20,'...').PHP_EOL);
                $content_msg .= addslashes(cut_str(strip_tags(!empty($pdata['data']->content) ? $pdata['data']->content : ''),160,'...'));

				$oMemberModel = &getModel('member');
				$oMyInfo = $oMemberModel->getLoggedInfo();

				if(isset($this->addon_info->pushwing_mnocol) &&
				$this->addon_info->pushwing_mnocol != '' &&
				isset($oMyInfo->{$this->addon_info->pushwing_mnocol}) && 
                $oMyInfo->{$this->addon_info->pushwing_mnocol}) {
                	$mno = $oMyInfo->{$this->addon_info->pushwing_mnocol}; 
                	$my_number = is_array($mno) && !empty($mno) ? implode('', $mno) : $mno;
                } else {
                	$my_number = NULL;
                }
					
				$hplist = strstr($config['mno'], ',') ? explode(',', $config['mno']) : array($config['mno']);
				foreach($hplist as $hp) {
					$hp = preg_replace('/[^0-9]*/s', '', $hp);
					if($hp && $hp != $my_number) {
		                $idata = array(
		                    'hp' => $hp, 
		                    'client_id' => $config['client_id'], 
		                    'subject' => $subject_msg,
		                    'contents' => $content_msg, 
		                    'url' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . '?mid=' . $pdata['data']->mid . '&document_srl=' . $pdata['document_srl']
		                );
		                
		                $columns = '';
		                $values = '';
		                foreach($idata as $column => $value)
		                {
		                    $columns .= $column . ', ';
		                    $values .= ("'" . $value . "', ");
		                }

		                mysql_query(sprintf('INSERT INTO push_wait (%s timestamp, ymd, time) VALUES (%s UNIX_TIMESTAMP(), CURDATE() + 0, CURTIME() + 0)', $columns, $values));
					}
                }
                 
                mysql_close($con);
            }
        }
    }
    
    $GLOBALS['__AddonPushWing__'] = new PushWing;
    $GLOBALS['__AddonPushWing__']->init($addon_info);
    Context::set('oPushWing', $GLOBALS['__AddonPushWing__']);    
}

$oPushWing = &$GLOBALS['__AddonPushWing__'];

if(method_exists($oPushWing, $called_position))
{
    if(!call_user_func_array(array(&$oPushWing, $called_position), array(&$this)))
    {
        return false;
    }
}
/* End of file pushwing.addon.php */
/* Location: ./addons/pushwing/pushwing.addon.php */