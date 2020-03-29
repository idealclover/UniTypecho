<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * UniTypecho
 * @package UniTypecho
 * @author idealclover
 * @version 1.0.0
 * @link https://idealclover.top
 */
class UniTypecho_Plugin implements Typecho_Plugin_Interface
{

    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('UniTypecho_Plugin', 'sendFeedback');
        Helper::addRoute("uniapi", "/uniapi/[type]", "UniTypecho_Action", 'action');
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $type = explode('_', $db->getAdapterName());
        $type = array_pop($type);
        //创建用户数据库
        $scripts = file_get_contents('usr/plugins/UniTypecho/sql/' . $type . '.sql');
        $scripts = str_replace('typecho_', $prefix, $scripts);
        $scripts = explode(';', $scripts);

        try {
            foreach ($scripts as $script) {
                $script = trim($script);
                if ($script) {
                    $db->query($script, Typecho_Db::WRITE);
                }
            }
            return '插件启用成功啦！快快配置使用吧！(๑>ᴗ<๑)';
        } catch (Typecho_Db_Exception $e) {
            throw new Typecho_Plugin_Exception(_t('数据表建立失败，插件启用失败，错误信息：%s。', $e->getMessage()));
        } catch (Exception $e) {
            throw new Typecho_Plugin_Exception($e->getMessage());
        }

        //创建赞数据库
        try {
            //增加点赞和阅读量
            if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
                $db->query(
                    'ALTER TABLE `' . $prefix
                        . 'contents` ADD `views` INT DEFAULT 0;'
                );
            }
            if (!array_key_exists('likes', $db->fetchRow($db->select()->from('table.contents')))) {
                $db->query(
                    'ALTER TABLE `' . $prefix
                        . 'contents` ADD `likes` INT DEFAULT 0;'
                );
            }
            if (!array_key_exists('authorImg', $db->fetchRow($db->select()->from('table.comments')))) {
                $db->query(
                    'ALTER TABLE `' . $prefix
                        . 'comments` ADD `authorImg` varchar(500) DEFAULT NULL;'
                );
            }
        } catch (Exception $e) {
            echo ($e->getMessage());
        }
    }

    public static function deactivate()
    {
        Helper::removeRoute("uniapi");
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $swiperPosts = new Typecho_Widget_Helper_Form_Element_Text('swiperPosts', NULL, '1,2', _t('滑动文章列表'),  _t('要在滑动列表里面显示的文章的cid值，用英文逗号隔开。'));
        $form->addInput($swiperPosts);
        $apiSecret = new Typecho_Widget_Helper_Form_Element_Text('apiSecret', NULL, 'xxx', _t('API密钥'),  _t('要与小程序端config.js中API_SECRET字段保持一致，否则无法从服务器读取数据'));
        $form->addInput($apiSecret);
        $appId = new Typecho_Widget_Helper_Form_Element_Text('appId', NULL, 'xxx', _t('微信小程序的APPID'),  _t('小程序的APP ID'));
        $form->addInput($appId);
        $appSecret = new Typecho_Widget_Helper_Form_Element_Text('appSecret', NULL, 'xxx', _t('微信小程序的APP secret ID'),  _t('小程序的APP secret ID'));
        $form->addInput($appSecret);
        $templateId = new Typecho_Widget_Helper_Form_Element_Text('templateId', NULL, 'xxx', _t('微信小程序订阅模板的 template ID'),  _t('小程序的 template ID'));
        $form->addInput($templateId);
        $defaultURL = new Typecho_Widget_Helper_Form_Element_Text('defaultURL', NULL, 'https://api.isoyu.com/bing_images.php', _t('默认图片 URL'),  _t('微信分享时的默认图片 URL'));
        $form->addInput($defaultURL);
        $qqAppId = new Typecho_Widget_Helper_Form_Element_Text('qqAppId', NULL, 'xxx', _t('QQ 小程序的APPID'),  _t('小程序的APP ID'));
        $form->addInput($qqAppId);
        $qqAppSecret = new Typecho_Widget_Helper_Form_Element_Text('qqAppSecret', NULL, 'xxx', _t('QQ 小程序的APP secret ID'),  _t('小程序的APP secret ID'));
        $form->addInput($qqAppSecret);
        $aboutCid = new Typecho_Widget_Helper_Form_Element_Text('aboutCid', NULL, '1', _t('关于页面CID'),  _t('小程序关于页面显示内容'));
        $form->addInput($aboutCid);
        $showedMid = new Typecho_Widget_Helper_Form_Element_Text('showedMid', NULL, NULL, _t('要在小程序端显示的分类的mid(其余隐藏)，为了过微信审核你懂的^-^，可在过审核后取消隐藏（不填写则不隐藏任何分类）。'),  _t('可在Typecho后台分类管理中查看分类的mid，以英文逗号隔开。不填写则不隐藏任何分类'));
        $form->addInput($showedMid);
        $showComments = new Typecho_Widget_Helper_Form_Element_Radio('showComments', array('0' => '禁用', '1' => '启用'), '1', _t('是否开启小程序端评论功能，1为开启，0为关闭。可在过审核后打开该功能'),  _t('审核时建议关闭，防止微信判定小程序禁止评论，审核通过后再开启。'));
        $form->addInput($showComments);
        $showShare = new Typecho_Widget_Helper_Form_Element_Radio('showShare', array('0' => '禁用', '1' => '启用'), '1', _t('是否开启小程序端分享，转发功能，1为开启，0为关闭。可在过审核后打开该功能'),  _t('审核时建议关闭，防止微信判定小程序有诱导用户分享的嫌疑，审核通过后再开启。'));
        $form->addInput($showShare);
        $showDonate = new Typecho_Widget_Helper_Form_Element_Radio('showDonate', array('0' => '禁用', '1' => '启用'), '1', _t('是否开启小程序赞赏'),  _t('审核时建议关闭'));
        $form->addInput($showDonate);
        $defaultStatus = new Typecho_Widget_Helper_Form_Element_Radio('defaultStatus', array('0' => '通过', '1' => '待审核'), '1', _t('评论默认状态'),  _t('评论默认为通过/待审核'));
        $form->addInput($defaultStatus);
        $blackList = new Typecho_Widget_Helper_Form_Element_Textarea('blackList', NULL, NULL, _t('黑名单'), _t('默认为通过时启用，评论需要审核的 openid'));
        $form->addInput($blackList);
        $whiteList = new Typecho_Widget_Helper_Form_Element_Textarea('whiteList', NULL, NULL, _t('白名单'), _t('默认为待审核时启用，评论直接通过的 openid'));
        $form->addInput($whiteList);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
    public static function sendFeedback($comment)
    {
        $cfg = array(
            'cid'       => $comment->cid,
            'author'    => $comment->author,
            'authorId'  => $comment->authorId,
            'ownerId'   => $comment->ownerId,
            'title'     => $comment->title,
            'text'      => $comment->text,
            'mail'      => $comment->mail,
            'status'    => $comment->status,
            'parent'    => $comment->parent,
        );


        if ($comment->status != "approved" || $comment->parent == "0") return;

        $appId = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->appId;
        $appSecret = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->appSecret;
        $templateId = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->templateId;

        if ($templateId == null || $templateId == "") return;

        $db  = Typecho_Db::get();

        $original = $db->fetchRow($db->select('author', 'mail', 'text')->from('table.comments')->where('coid = ?', $comment->parent));
        if (preg_match('/(.*)@wx\.com/', $original['mail'], $matches)) {
            // print('qwq');
            $openid = $matches[1];
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $appId, $appSecret);
            $info = file_get_contents($url);
            $json = json_decode($info); //对json数据解码
            $arr = get_object_vars($json);
            $access_token = $arr['access_token'];
            if ($access_token == null) return;
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=%s', $access_token);
            if (mb_strlen($cfg['title']) > 20) $cfg['title'] = mb_substr($cfg['title'], 0, 17, 'utf-8') . "...";
            if (mb_strlen($cfg['author']) > 10) $cfg['author'] = mb_substr($cfg['author'], 0, 10, 'utf-8');
            if (mb_strlen($cfg['text']) > 20) $cfg['text'] = mb_substr($cfg['text'], 0, 17, 'utf-8') . "...";

            $data = array(
                'touser' => $openid,
                "template_id" => $templateId,
                "page" => "pages/index/index?cid=" . $cfg . cid,
                'data' => array(
                    "thing4" => array(
                        "value" => $cfg["title"]
                    ),
                    "name1" => array(
                        "value" => $cfg['author']
                    ),
                    "thing2" => array(
                        "value" => $cfg['text']
                    )
                )
            );

            $result = json_encode($data, true);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $result);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $info = curl_exec($ch);
            $row = $db->fetchRow($db->select('formid')->from('table.unitypecho')->where('openid = ?', $openid));
            $db->query($db->update('table.unitypecho')->rows(array('formid' => (int) $row['formid'] - 1))->where('openid = ?', $openid));
            return $info;
        }
    }
}
