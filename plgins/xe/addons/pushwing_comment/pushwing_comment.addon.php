<?php
/* Copyright (C) Pushwing <http://www.pushwing.com> */

if(!defined("__XE__")) exit();

/**
 * @file pushwing_comment.addon.php
 * @author hoksi (hoksi@naver.com)
 * @brief Pushwing addon
 * */
if(!class_exists('PushWingComment'))
{
    class PushWingComment {
        var $addon_info;
        
        function init(&$addon_info)
        {
            $this->addon_info = $addon_info;
        }

        function after_module_proc($obj)
        {
            if($obj->act == 'procBoardInsertComment') {
                $data = Context::getRequestVars();

                $oDocumentModel = &getModel('document');
                $oMemberModel = &getModel('member');
                $oDocument = $oDocumentModel->getDocument($data->document_srl);
                $oMemberInfo = $oMemberModel->getMemberInfoByMemberSrl($oDocument->variables['member_srl']);
                $oMyInfo = $oMemberModel->getLoggedInfo();
                
                if( isset($this->addon_info->pushwing_mnocol) &&
                    $this->addon_info->pushwing_mnocol != '' &&
                    isset($oMemberInfo->{$this->addon_info->pushwing_mnocol}) && 
                    $oMemberInfo->{$this->addon_info->pushwing_mnocol} &&
                    $oMyInfo->member_srl != $oMemberInfo->member_srl
                 ) {
                     $mno = $oMemberInfo->{$this->addon_info->pushwing_mnocol};
                     $mno = is_array($mno) && !empty($mno) ? implode('', $mno) : $mno;
                     $this->send_pushwing(array(
                        'mno' => $mno,
                        'data' => $data,
                        'document' => $oDocument,
                        'nick_name' => $oMyInfo->nick_name
                    ));
                }
            }
            
        }
        
        function send_pushwing($pdata)
        {
            $config = array(
                'server' => strip_tags(isset($this->addon_info->server) && $this->addon_info->server ? $this->addon_info->server : 'www.pushwing.com'),
                'user' => strip_tags(isset($this->addon_info->pushwing_id) && $this->addon_info->pushwing_id ? $this->addon_info->pushwing_id : ''),
                'password' => strip_tags(isset($this->addon_info->pushwing_pw) && $this->addon_info->pushwing_pw ? $this->addon_info->pushwing_pw : ''),
                'mno' => isset($pdata['mno']) && $pdata['mno'] ? preg_replace('/[^0-9]*/s', '', $pdata['mno']) : '',
                'client_id' => addslashes(strip_tags(isset($this->addon_info->client_id) && $this->addon_info->client_id ? $this->addon_info->client_id : ''))
            );
            
            $con = mysql_connect($config['server'], $config['user'], $config['password']);
            if($con) {
                mysql_select_db("pushwing", $con);
                
                $subject_msg = addslashes('[' . cut_str(strip_tags($pdata['document']->variables['title']),10,'...') . '] 새 댓글 등록');
                $content_msg = addslashes(strip_tags($pdata['nick_name']) . '님이 댓글을 달았습니다.' .PHP_EOL.PHP_EOL);
                $content_msg .= addslashes(cut_str(strip_tags(!empty($pdata['data']->content) ? $pdata['data']->content : ''),160,'...'));
                
                $idata = array(
                    'hp' => $config['mno'], 
                    'client_id' => $config['client_id'], 
                    'subject' => $subject_msg,
                    'contents' => $content_msg, 
                    'url' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . '?mid=' . $pdata['data']->mid . '&document_srl=' . $pdata['data']->document_srl
                );
                
                $columns = '';
                $values = '';
                foreach($idata as $column => $value)
                {
                    $columns .= $column . ', ';
                    $values .= ("'" . $value . "', ");
                }

                mysql_query('set names utf8');
                mysql_query(sprintf('INSERT INTO push_wait (%s timestamp, ymd, time) VALUES (%s UNIX_TIMESTAMP(), CURDATE() + 0, CURTIME() + 0)', $columns, $values));
                
                mysql_close($con);
            }
        }
    }
    
    $GLOBALS['__AddonPushWingComment__'] = new PushWingComment;
    $GLOBALS['__AddonPushWingComment__']->init($addon_info);
    Context::set('PushWingComment', $GLOBALS['__AddonPushWingComment__']);    
}

$PushWingComment = &$GLOBALS['__AddonPushWingComment__'];

if(method_exists($PushWingComment, $called_position))
{
    if(!call_user_func_array(array($PushWingComment, $called_position), array(&$this)))
    {
        return false;
    }
}
/* End of file pushwing_comment.addon.php */
/* Location: ./addons/pushwing/pushwing_comment.addon.php */