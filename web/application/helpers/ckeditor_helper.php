<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

function form_ckeditor($data)
{
    //��� ���û��
    switch($data['toolbar']){
        case ('reply'):
        $tool_bar = '
            [
                ["Font","FontSize"], ["TextColor","BGColor"],
                ["Bold","Italic","Underline"],
                ["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],
                ["Link","Unlink"],
                ["Image","Flash","Table","HorizontalRule","Smiley","SpecialChar"],
                ["Preview","Source","Maximize"]
            ]
        ';
        break;
        case ('basic'):
        $tool_bar = '
            [
                ["Source","-","Preview"],
                ["Bold","Italic","Underline"],
                ["Link","Unlink"]
            ]
        ';
        break;
        //����Ʈ�� FUll�Դϴ�.
        default:
        $tool_bar = '
            [
                ["Source","-","Save","NewPage","Preview","-","Templates"],
                ["Cut","Copy","Paste","PasteText","PasteFromWord","-","Print", "SpellChecker", "Scayt"],
                ["Undo","Redo","-","Find","Replace","-","SelectAll","RemoveFormat"],
                ["Form", "Checkbox", "Radio", "TextField", "Textarea", "Select", "Button", "ImageButton", "HiddenField"],
                "/",
                ["Bold","Italic","Underline","Strike","-","Subscript","Superscript"],
                ["NumberedList","BulletedList","-","Outdent","Indent","Blockquote"],
                ["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],
                ["Link","Unlink","Anchor"],
                ["Image","Flash","Table","HorizontalRule","Smiley","SpecialChar","PageBreak"],
                "/",
                ["Styles","Format","Font","FontSize"],
                ["TextColor","BGColor"],
                ["Maximize", "ShowBlocks","-","About","-","Ckfinder"]
            ]
        ';
        break;
    }
//["Maximize", "ShowBlocks","-","About","-","Ckfinder","-","Pgrfilemanager"]

    $data['language'] = isset($data['language']) ? $data['language'] : 'ko';

    $size    = isset($data['width']) ? 'width: "'.$data['width'].'", ' : '';
    $size  .= isset($data['height']) ? 'height: "'.$data['height'].'", ' : '';

	$options = '{'.
		$size.
		'language: "'.$data['language'].'",
            startupOutlineBlocks: false,
            entities: false,
            entities_latin: false,
            entities_greek: false,
            forcePasteAsPlainText: false,
			filebrowserBrowseUrl : "/include/ckfinder/ckfinder.html",
			filebrowserImageBrowseUrl : "/include/ckfinder/ckfinder.html?Type=Images",
			filebrowserFlashBrowseUrl : "/include/ckfinder/ckfinder.html?Type=Flash",
			filebrowserUploadUrl : "/include/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files",
            filebrowserImageUploadUrl : "/include/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images",
            filebrowserFlashUploadUrl : "/include/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash",
            toolbar : '.$tool_bar.'
        }';




    //ckeditor �����
    return
    // fix: move to <HEAD...
    '<script type="text/javascript" src="'.INCLUDE_DIR.'/ckeditor/ckeditor.js"></script>' .

    // put the CKEditor
     '<script type="text/javascript">CKEDITOR.replace("'.$data['id'].'", ' . $options . ');</script>';
}
?>