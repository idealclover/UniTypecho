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
        Helper::addRoute("uniapi", "/uniapi/[type]", "UniTypecho_Action", 'action');
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
		$type = explode('_', $db->getAdapterName());
		$type = array_pop($type);
        //创建用户数据库
        $scripts = file_get_contents('usr/plugins/UniTypecho/sql/unitypecho.sql');
        $scripts = str_replace('typecho_', $prefix, $scripts);
        $scripts = explode(';', $scripts);

        try {
            if (!$db->fetchRow($db->query("SHOW TABLES LIKE '{$prefix}unitypecho';", Typecho_Db::READ))) {
                foreach ($scripts as $script) {
                    $script = trim($script);
                    if ($script) {
                        $db->query($script, Typecho_Db::WRITE);
                    }
                }
            }
        } catch (Typecho_Db_Exception $e) {
            // try {} catch (Exception $e) {
            //     throw new Typecho_Plugin_Exception(_t('数据表建立失败，插件启用失败，错误信息：%s。', $e->getMessage()));
            // }
            throw new Typecho_Plugin_Exception(_t('数据表建立失败，插件启用失败，错误信息：%s。', $e->getMessage()));
        } catch (Exception $e) {
            throw new Typecho_Plugin_Exception($e->getMessage());
        }

        //创建赞数据库
        $scriptslike = file_get_contents('usr/plugins/UniTypecho/sql/unitypecholike.sql');
        $scriptslike = str_replace('typecho_', $prefix, $scriptslike);
        $scriptslike = explode(';', $scriptslike);
        try {
            if (!$db->fetchRow($db->query("SHOW TABLES LIKE '{$prefix}unitypecholike';", Typecho_Db::READ))) {
                foreach ($scriptslike as $script) {
                    $script = trim($script);
                    if ($script) {
                        $db->query($script, Typecho_Db::WRITE);
                    }
                }
            }
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
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
}
