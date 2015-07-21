<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function cal_size($img, $size)
    {
        if ($img == '' or $size == '')
        {
            $ret = array('img_name'=>'', 'widths'=>'110');
        }
        else
        {
            $f_name = explode('.', $img);
            $img_name = $f_name[0]."_p.".$f_name[1];
            $sz = explode('X', $size);
            $rate = (140 / $sz[1]) * 100;
            $widths = round($sz[0] * ($rate / 100));
            $ret = array('img_name'=>$img_name, 'widths'=>$widths);
        }

        return $ret;
    }

    function trim_text($str,$len,$tail="..")
    {
         if(strlen($str)<$len) {

            return $str; //자를길이보다 문자열이 작으면 그냥 리턴

         } else{
            $result_str='';
            for($i=0;$i<$len;$i++){
            if((Ord($str[$i])<=127)&&(Ord($str[$i])>=0)){$result_str .=$str[$i];}
            else if((Ord($str[$i])<=223)&&(Ord($str[$i])>=194)){$result_str .=$str[$i].$str[$i+1];$i+1;}
            else if((Ord($str[$i])<=239)&&(Ord($str[$i])>=224)){$result_str .=$str[$i].$str[$i+1].$str[$i+2];$i+2;}
            else if((Ord($str[$i])<=244)&&(Ord($str[$i])>=240)){$result_str .=$str[$i].$str[$i+1].$str[$i+2].$str[$i+3];$i+3;}
            }

            return $result_str.$tail;

        }

    }
    /**
    * checkmb=true, len=10
    * 한글과 Eng (한글=2*3 + 공백=1*1 + 영문=1*1 => 10)
    * checkmb=false, len=10
    * 한글과 Englis (모두 합쳐 10자)
    */
    function strcut_utf8($str, $len, $checkmb=false, $tail='..')
    {
        preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);

        $m = $match[0];
        $slen = strlen($str); // length of source string
        $tlen = strlen($tail); // length of tail string
        $mlen = count($m); // length of matched characters

        if ($slen <= $len) return $str;
        if (!$checkmb && $mlen <= $len) return $str;

        $ret = array();
        $count = 0;

        for ($i=0; $i < $len; $i++) {
            $count += ($checkmb && strlen($m[$i]) > 1)?2:1;

            if ($count + $tlen > $len) break;
            $ret[] = $m[$i];
        }

        return join('', $ret).$tail;
    }


    function segment_explode($seg)
    {
        //세크먼트 앞뒤 '/' 제거후 uri를 배열로 반환
        $len = strlen($seg);
        if(substr($seg, 0, 1) == '/') {
            $seg = substr($seg, 1, $len);
        }
        $len = strlen($seg);
        if(substr($seg, -1) == '/') {
            $seg = substr($seg, 0, $len-1);
        }
        $seg_exp = explode("/", $seg);
        return $seg_exp;
    }

    /**
     * 배열(쿼리스트링 포함)에서 주소만들기
     * @param Array $url : segment_explode 한 url값
     * @param Array $add_url : 추가하려는 변수 배열
     * @param Array $del_url : 삭제하려는 변수 배열
     * @return String : 풀주소 리턴
     */
    function segment_implode($url, $add_url, $del_url='')
    {
        if ($add_url) {
            foreach ($add_url as $key=>$val) {
                $q_url[] = $key."=".$val;
            }
            $q1_url = implode('&', $q_url);
            $s_url = implode('/', $url);
            return "/".$s_url."/?".$q1_url;
        } else {
            $s_url = implode('/', $url);
            return "/".$s_url;
        }
    }

    /**
     * url중 키값을 구분하여 값을 가져오도록
     * @param Array $url : segment_explode 한 url값
     * @param String $key : 가져오려는 값의 key
     * @return String $url[$k] : 리턴값
     */
    function url_explode($url,$key){
        for($i=0; count($url)>$i; $i++ ){
            if($url[$i] ==$key){
                $k = $i+1;
                return $url[$k];
            }
        }
    }

    function pagination($link, $paging_data)
    {
        $links = "";

        // The first page
        $links .= anchor($link . '/' . $paging_data['first'], 'First', array('title' => 'Go to the first page', 'class' => 'first_page'));
        $links .= "\n";
        // The previous page
        $links .= anchor($link . '/' . $paging_data['prev'], '<', array('title' => 'Go to the previous page', 'class' => 'prev_page'));
        $links .= "\n";

        // The other pages
        for ($i = $paging_data['start']; $i <= $paging_data['end']; $i++) {
            if ($i == $paging_data['page'])
                $current = '_current';
            else
                $current = "";

            $links .= anchor($link . '/' . $i, $i, array('title' => 'Go to page ' . $i, 'class' => 'page' . $current));
            $links .= "\n";
        }

        // The next page
        $links .= anchor($link . '/' . $paging_data['next'], '>', array('title' => 'Go to the next page', 'class' => 'next_page'));
        $links .= "\n";
        // The last page
        $links .= anchor($link . '/' . $paging_data['last'], 'Last', array('title' => 'Go to the last page', 'class' => 'last_page'));
        $links .= "\n";

        return $links;
    }



    /**
     * 로그인 여부 체크 및 운영자 여부 체크
     *
     * @param string $auth_code
     * @param string $menu segment(2), auth_code가 9일 경우 필수
     */

    function is_admin_login($auth_code = '9', $menu='')
    {
        $CI = & get_instance();
        $CI->load->library('tank_auth');

        if( ! $CI->tank_auth->is_logged_in())
        {
            $rpath = str_replace("index.php/", "", $CI->input->server('PHP_SELF'));
            $rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');

            redirect('/auth/login/0/'.$rpath_encode);
            exit;
        }
        else
        {
            //로그인이 되어 있다면 운영자 권한 체크
            if( $CI->session->userdata('auth_code') < $auth_code )
            {
                echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
                echo "<script type='text/javascript'>alert('권한이 부족합니다.');";
                echo "location.replace('/');";
                echo "</script>";
                exit;
            }
            else if( $CI->session->userdata('auth_code') == $auth_code AND $auth_code == '9' )
            {
                if ($menu != 'login')
                {
                    //운영자일 경우 레벨 체크
                    //$query = $CI->db->get_where('code', array('type'=>'LV'));
                    //$result = $query->result_array();
                    $level_arr = array('client'=>'1','report'=>'2');
                    $lv_val = $level_arr[$menu];

                    $level = $CI->session->userdata('levels');
                    $levels = explode('|', $level);

                    if (!in_array($lv_val, $levels))
                    {
                        if(is_array($levels))
                        {
                            $lls = $levels[0];
                        }
                        else
                        {
                        	$lls = $level;
                        }

                        switch ($lls)
                        {
                            case '1':
                                $t_url = '/admin/client/lists/0';
                                break;
                            case '2':
                                $t_url = '/admin/report/index/1';
                                break;

                        }

                        //echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
                        //echo "<script type='text/javascript'>alert('접근 권한이 없습니다.');";
                        echo "<script type='text/javascript'>";
                        echo "location.replace('".$t_url."');";
                        echo "</script>";
                        exit;
                    }
                }
            }
        }
    }

    /**
     * 새 함수 json 생성
     */
    function server_output($code, $message, $data)
    {
        $arr_codes = '';

        //라이브러리에서 codeigniter의 내부 함수 사용을 위한 선언
        $CI =& get_instance();

        //언어 로드
        $CI->lang->load('errors', 'korean');

        $code_array = array(
            200 => array("OK"=> $CI->lang->line('error_200')),
            400 => array("Bad Request"=> $CI->lang->line('error_400')),
            401 => array("Unauthorized"=> $CI->lang->line('error_401')),
            403 => array("Forbidden"=> $CI->lang->line('error_403')),
            404 => array("Not Found"=> $CI->lang->line('error_404')),
            500 => array("Server Error"=> $CI->lang->line('error_500')),
            501 => array("Not Implemented"=> $CI->lang->line('error_501')),
            502 => array("Service Unavailable"=> $CI->lang->line('error_502')),
            600 => array("Unusual Approach"=> $CI->lang->line('error_600')),
            601 => array("Not have permission"=> $CI->lang->line('error_601')),
            602 => array("Invalid Parameter"=> $CI->lang->line('error_602')),
            603 => array("Session is not valid"=> $CI->lang->line('error_603')),
            605 => array("Session is not valid"=> $CI->lang->line('error_605')),
            700 => array("포인트부족"=> $CI->lang->line('error_700')),
            701 => array("응모일아님"=> $CI->lang->line('error_701')),
            702 => array("포"=> $CI->lang->line('error_702')),
            800 => array("비밀번호가 틀리다"=> $CI->lang->line('error_800')),
            801 => array("비밀번호가 다르다"=> $CI->lang->line('error_801')),
            900 => array("중복응모"=> $CI->lang->line('error_900')),
            901 => array("데이터없음"=> $CI->lang->line('error_901'))
        );

        foreach ($code_array as $key => $value)
        {
            if($key == $code)
            {
                $vals = array_values($value);
                $arr_codes = $vals[0];
            }

        }

        $a = array(
                "code" => $code,
                "msg" => $arr_codes,
                "data" => $data
            );

        $post_data = json_encode($a);
        return $post_data;
    }

    function vdd($arr)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<pre>";
        print_r($arr);
        echo "<pre>";
    }

    /**
     * 대표 이미지를 가지고 size에 해당하는 Thumbnail 이미지를 가져옴.
     *
     * @author Jongwon Byun <blumine@freebee.kr>
     * @param string $image
     * @param string $width
     * @param string $height
     * @return string img tag
     */
    function get_thumbnail_image($image, $width, $height, $change_path="/img_error.gif", $class='')
    {
        if ( is_null($image) || strlen($image)==0 )  return "<img src='".IMG_DIR.$change_path."' width='".$width."' height='".$height."' class='".$class."'>";

        if ( $fp = @fopen(UP_ROOT.$image,"r") )
        {
            $image_info = pathinfo($image);

            if ( $width <= 50 )
            {
                $size = "_s";
            }
            else if( $width > 50 and $width <= 90 )
            {
                $size = "_m";
            }
            else if( $width > 90 and $width < 150 )
            {
                $size = "_b";
            }
            else
            {
                $size = '';
            }
            $size = '';

            if ($height == '')
            {
                $height_1 = '';
            }
            else
            {
                $height_1 = "height='".$height."'";
            }

            $image_url = "<img src='".UP_DIR . $image_info['dirname'] . "/" . urldecode($image_info['filename']) . $size . '.' . $image_info['extension']."' width='".$width."' ".$height_1." class='".$class."'>";
            //$image_url = "<img src='".IMG_DIR . $change_path."' width='".$width."' height='".$height."' class='".$class."'>";

            fclose($fp);
        }
        else if ( $fp = @fopen(IMG_ROOT.$image,"r") )
        {
            $image_info = pathinfo($image);

            if ( $width <= 50 )
            {
                $size = "_s";
            }
            else if( $width > 50 and $width <= 90 )
            {
                $size = "_m";
            }
            else if( $width > 90 and $width <= 150 )
            {
                $size = "_b";
            }
            else
            {
                $size = '';
            }
            $image_url = "<img src='".IMG_DIR . $change_path."' width='".$width."' height='".$height."' class='".$class."'>";

            fclose($fp);
        }
        else
        {
            if ($height == '')
            {
                $height_1 = '';
            }
            else
            {
            	$height_1 = "height='".$height."'";
            }
            $image_url = "<img src='".IMG_DIR.$change_path."' width='".$width."' ".$height_1." >";
        }

        return $image_url;
    }

    function euckr_utf8(&$item, $key = '', $prefix = '')
    {
        if(is_array($item))
        {
            array_walk($item, 'euckr_utf8');
        }
        else
        {
            $item = iconv('EUC-KR', 'UTF-8', $item);
        }

        return $item;
    }

    function utf8_euckr(&$item, $key = '', $prefix = '')
    {
        if(is_array($item))
        {
            array_walk($item, 'utf8_euckr');
        }
        else
        {
            $item = iconv('UTF-8', 'EUC-KR', $item);
        }

        return $item;
    }

    /**
     * BIGINT insert_id 처리
     *
     *
     */
    function big_last_id()
    {
        $CI =& get_instance();
        $last_id_query = $CI->db->query('select last_insert_id() as lst');
        $last_ids = $last_id_query->row();
        return $last_ids->lst;
    }

    /**
     * 내용중에서 이미지명 추출후 DB 입력, 파일갯수 리턴. fckeditor용
     */
    function strip_image_tags_fck($str, $no, $type, $table, $table_no)
    {
        $CI =& get_instance();
        $file_table="board_files";
        preg_match_all("<img [^<>]*>", $str, $out, PREG_PATTERN_ORDER);
        $strs = $out[0];
        //$arr=array();
        $cnt = count($strs);
        for ($i=0;$i<$cnt;$i++) {
            $arr = preg_replace("#img\s+.*?src\s*=\s*[\"']\s*\/uploads/\s*(.+?)[\"'].*?\/#", "\\1", $strs[$i]);
            $data = array(
                'module_id'=> $table_no,
                'module_name'=> $table,
                'module_no'=>$no,
                'module_type'=>$type,
                'file_name'=>$arr,
                //'reg_date'=>date("Y-m-d H:i:s")
                'reg_date'=>now()
            );
            if ( count($arr) <= 25 ) {
                $CI->db->insert($file_table, $data);
            }

        }
        return $cnt;
    }

    /**
     * 암호화
     *
     */
    function getEncrypt($sStr, $sKey=CIPHER_KEY, $sIV=IV )
    {
        $sCipher = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sKey, $sStr, MCRYPT_MODE_CFB, $sIV);
        return bin2hex($sCipher);
    }

    /**
     * 복호화
     *
     */
    function getDecrypt($sStr, $sKey=CIPHER_KEY, $sIV=IV )
    {
        $sDecipher = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, pack('H*', $sStr), MCRYPT_MODE_CFB, $sIV);
        return $sDecipher;
    }

    /**
     * iptc용 함수
     *
     */
    function iptc_make_tag($rec, $data, $value)
    {
        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr($data);

        if($length < 0x8000)
        {
            $retval .= chr($length >> 8) .  chr($length & 0xFF);
        }
        else
        {
            $retval .= chr(0x80) .
                       chr(0x04) .
                       chr(($length >> 24) & 0xFF) .
                       chr(($length >> 16) & 0xFF) .
                       chr(($length >> 8) & 0xFF) .
                       chr($length & 0xFF);
        }

        return $retval . $value;
    }

    function searcharray($sval1, $sval2, $sval3, $array)
    {
       foreach ($array as $k => $val) {
           if ($val[$key] == $value) {
               return $k;
           }
       }
       return null;
    }

    function urlExists($url=NULL)
    {
        if($url == NULL) return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode>=200 && $httpcode<300){
            return true;
        } else {
            return false;
        }
    }
?>
