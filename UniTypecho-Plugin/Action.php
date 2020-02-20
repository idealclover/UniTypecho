<?php
header('Access-Control-Allow-Origin: *');
class UniTypecho_Action extends Typecho_Widget implements Widget_Interface_Do
{

    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->db  = Typecho_Db::get();
        $this->res = new Typecho_Response();
        $this->swiperPosts = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->swiperPosts;
        $this->apiSecret = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->apiSecret;
        $this->appId = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->appId;
        $this->appSecret = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->appSecret;
        $this->qqAppId = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->qqAppId;
        $this->qqAppSecret = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->qqAppSecret;
        if (method_exists($this, $this->request->type)) {
            call_user_func(array(
                $this,
                $this->request->type
            ));
        } else {
            $this->defaults();
        }
    }

    private function checkApisec($sec)
    {
        if (strcmp($sec, $this->apiSecret) != 0) {
            $this->export('API secret error');
        }
    }

    private function getConfig()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $result = array();
        $showComments = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->showComments;
        $showShare = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->showShare;
        $result['showComments'] = $showComments;
        $result['showShare'] = $showShare;
        $this->export($result);
    }

    private function getaboutcid()
    {
        $cid = 'none';
        $cid = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->aboutCid;

        $this->export($cid);
    }

    private function getSwiperPosts()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);

        $swiperPosts = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->swiperPosts;
        $cids = explode(",", $swiperPosts);
        $result = array();
        if (sizeof($cids) > 0) {
            foreach ($cids as $cid) {
                $post = $this->db->fetchAll($this->db->select('cid', 'title', 'created', 'type', 'slug', 'commentsNum')->from('table.contents')->where('cid = ?', $cid)->where('status = ?', 'publish')->where('type = ?', 'post')->where('created < ?', time()));
                if (sizeof($post) > 0 && $post[0] != null) {
                    $post[0]        = $this->widget("Widget_Abstract_Contents")->push($post[0]);
                    $post[0]['tag'] = $this->db->fetchAll($this->db->select('name')->from('table.metas')->join('table.relationships', 'table.metas.mid = table.relationships.mid', Typecho_DB::LEFT_JOIN)->where('table.relationships.cid = ?', $cid)->where('table.metas.type = ?', 'tag'));
                    $post[0]['thumb'] = $this->db->fetchAll($this->db->select('name', 'str_value')->from('table.fields')->where('cid = ?', $cid)) ? $this->db->fetchAll($this->db->select('name', 'str_value')->from('table.fields')->where('cid = ?', $cid)) : array(array("name" => "thumb", "str_value" => "https://api.isoyu.com/bing_images.php"));
                    $post[0]['views'] = $this->db->fetchAll($this->db->select('views')->from('table.contents')->where('table.contents.cid = ?', $cid));
                    $post[0]['likes'] = $this->db->fetchAll($this->db->select('likes')->from('table.contents')->where('table.contents.cid = ?', $cid));
                    $result[]    = $post[0];
                }
            }
            if (sizeof($result) > 0) {
                $this->export($result);
            } else {
                $this->export(null);
            }
        } else {
            $this->export(null);
        }
    }

    private function getCategories()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $temp = Typecho_Widget::widget('Widget_Options')->plugin('UniTypecho')->showedMid;
        $showedMids = explode(",", $temp);
        $select = $this->db->select('name', 'slug', 'type', 'description', 'mid')->from('table.metas')->where('table.metas.type = ?', 'category')->order('mid', Typecho_Db::SORT_DESC);
        $hidden = false;
        if (sizeof($showedMids) > 0 && intval($showedMids[0])) {
            $select->where('mid in ?', $showedMids);
            // TODO: bugs here.
            // if(!in_array("-1", $showedMids)) $hidden = true;
        }
        $categories = $this->db->fetchAll($select);
        if (!$hidden) {
            $recent = $categories[0];
            $recent['name'] = "最近发布";
            $recent['slug'] = "最近发布";
            $recent['mid'] = "-1";
            array_unshift($categories, $recent);
        }
        $this->export($categories);
    }

    private function getPostsByMid()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);

        // TODO
        $pageSize = (int) self::GET('pageSize', 1000);
        $except = (int) self::GET('except', 'null');
        $mid = self::GET('mid', null);
        $select = [];
        if ($mid == -1) {
            // TODO
            $posts = $this->db->fetchAll($this->db->select('cid', 'title', 'created', 'type', 'slug', 'commentsNum')->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->where('created < ?', time())->order('table.contents.created', Typecho_Db::SORT_DESC)->limit(10));
            foreach ($posts as $post) {
                $temp = $this->db->fetchAll($this->db->select('cid', 'title', 'created', 'commentsNum', 'views', 'likes')->from('table.contents')->where('cid = ?', $post['cid'])->where('status = ?', 'publish'));
                if (sizeof($temp) > 0) {
                    $temp['0']['thumb'] = $this->db->fetchAll($this->db->select('str_value')->from('table.fields')->where('cid = ?', $post['cid']));
                    array_push($select, $temp[0]);
                }
            }
            if (sizeof($posts) > 0) {
                $this->export($select);
            } else {
                $this->export(null);
            }
        } else if ($mid >= 0) {
            $categoryListWidget = $this->widget('Widget_Metas_Category_List', 'current=' . $mid);
            $children = $categoryListWidget->getAllChildren($mid);
            $children[] = $mid;
            $limit = 0;
            if ($except != 'null') {
                $posts = $this->db->fetchAll($this->db->select('cid', 'mid')->from('table.relationships')->where('mid IN ?', $children)->where('cid != ?', $except));
            } else {
                $posts = $this->db->fetchAll($this->db->select('cid', 'mid')->from('table.relationships')->where('mid IN ?', $children));
            }
            foreach ($posts as $post) {
                $temp = $this->db->fetchAll($this->db->select('cid', 'title', 'created', 'commentsNum', 'views', 'likes')->from('table.contents')->where('cid = ?', $post['cid'])->where('status = ?', 'publish'));
                if (sizeof($temp) > 0) {
                    $temp['0']['thumb'] = $this->db->fetchAll($this->db->select('str_value')->from('table.fields')->where('cid = ?', $post['cid']));
                    array_unshift($select, $temp[0]);
                }
                $limit++;
            }
            $overflow = sizeof($select) - $pageSize;
            for ($cnt = 0; $cnt < $overflow; $cnt++) {
                array_pop($select);
            }
            if (sizeof($posts) > 0) {
                $this->export($select);
            } else {
                $this->export(null);
            }
        } else $this->export(null);
    }

    private function getPosts()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $pageSize = (int) self::GET('pageSize', 1000);
        $page     = (int) self::GET('page', 1);
        $authorId = self::GET('authorId', 0);
        $offset   = $pageSize * ($page - 1);
        $ispage = self::GET('ispage', 0);
        $idx     = self::GET('idx', -1);

        // 根据cid偏移获取文章
        if (isset($_GET['cid'])) {
            $cid = self::GET('cid');
            if ($ispage) {
                $select = $this->db->select('cid', 'title', 'created', 'type', 'slug', 'text', 'commentsNum')->from('table.contents')->where('type = ?', 'page')->where('status = ?', 'publish')->where('created < ?', time())->order('table.contents.created', Typecho_Db::SORT_DESC)->offset($offset)->limit($pageSize);
            } else {
                $select = $this->db->select('cid', 'title', 'created', 'type', 'slug', 'text', 'commentsNum')->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->where('created < ?', time())->order('table.contents.created', Typecho_Db::SORT_DESC)->offset($offset)->limit($pageSize);
            }
            $select->where('cid = ?', $cid);
            //更新点击量数据库
            $row = $this->db->fetchRow($this->db->select('views')->from('table.contents')->where('cid = ?', $cid));
            $this->db->query($this->db->update('table.contents')->rows(array('views' => (int) $row['views'] + 1))->where('cid = ?', $cid));
        } else {
            //如果不指定具体文章CID，不抓取text
            $select   = $this->db->select('cid', 'title', 'created', 'type', 'slug', 'commentsNum')->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->where('created < ?', time())->order('table.contents.created', Typecho_Db::SORT_DESC)->offset($offset)->limit($pageSize);
        }
        // 根据分类或标签获取文章
        if (isset($_GET['category']) || isset($_GET['tag'])) {
            $name     = isset($_GET['category']) ? $_GET['category'] : $_GET['tag'];
            $resource = $this->db->fetchAll($this->db->select('cid')->from('table.relationships')->join('table.metas', 'table.metas.mid = table.relationships.mid', Typecho_Db::LEFT_JOIN)->where('slug = ?', $name));
            $cids     = array();
            foreach ($resource as $item) {
                $cids[] = $item['cid'];
            }
            $select->where('cid IN ?', $cids);
        }
        if ($idx >= 0) {
            switch ($idx) {
                case 0:
                    //浏览量
                    $select->order('table.contents.views', Typecho_Db::SORT_DESC);
                    break;
                case 1:
                    //评论数
                    $select->order('table.contents.commentsNum', Typecho_Db::SORT_DESC);
                    break;
                case 2:
                    //点赞数
                    $select->order('table.contents.likes', Typecho_Db::SORT_DESC);
                    break;
                default:
                    break;
            }
        }
        $posts  = $this->db->fetchAll($select);
        $result = array();
        foreach ($posts as $post) {
            $post        = $this->widget("Widget_Abstract_Contents")->push($post);
            $post['tag'] = $this->db->fetchAll($this->db->select('name')->from('table.metas')->join('table.relationships', 'table.metas.mid = table.relationships.mid', Typecho_DB::LEFT_JOIN)->where('table.relationships.cid = ?', $post['cid'])->where('table.metas.type = ?', 'tag'));
            $post['thumb'] = $this->db->fetchAll($this->db->select('str_value')->from('table.fields')->where('cid = ?', $post['cid'])) ? $this->db->fetchAll($this->db->select('str_value')->from('table.fields')->where('cid = ?', $post['cid'])) : array(array("str_value" => "https://api.isoyu.com/bing_images.php"));
            $post['views'] = $this->db->fetchAll($this->db->select('views')->from('table.contents')->where('table.contents.cid = ?', $post['cid']));
            $post['likes'] = $this->db->fetchAll($this->db->select('likes')->from('table.contents')->where('table.contents.cid = ?', $post['cid']));
            $result[]    = $post;
        }
        $this->export($result);
    }

    private function getComments()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $cid = self::GET('cid', -1);
        $comments = $this->db->fetchAll($this->db->select('cid', 'coid', 'created', 'author', 'text', 'parent', 'authorImg')->from('table.comments')->where('cid = ?', $cid)->where('status = ?', 'approved')->order('table.comments.created', Typecho_Db::SORT_DESC));
        $result = array();
        //获取根评论
        foreach ($comments as $comment) {
            if ($comment['parent'] == 0) {
                $result[] = $comment;
            }
        }
        //获取子评论
        foreach ($comments as $comment) {
            if ($comment['parent'] != 0) {
                $parent = $comment['parent'];
                $temp = $this->db->fetchAll($this->db->select('cid', 'coid', 'created', 'author', 'text', 'parent', 'authorImg', 'mail')->from('table.comments')->where('cid = ?', $cid)->where('coid = ?', $parent)->where('status = ?', 'approved')->order('table.comments.created', Typecho_Db::SORT_DESC));
                if (sizeof($temp) > 0) {
                    while ($temp[0]['parent'] != 0) {
                        $parent = $temp[0]['parent'];
                        $temp = $this->db->fetchAll($this->db->select('cid', 'coid', 'created', 'author', 'text', 'parent', 'authorImg')->from('table.comments')->where('cid = ?', $cid)->where('coid = ?', $parent)->where('status = ?', 'approved')->order('table.comments.created', Typecho_Db::SORT_DESC));
                    }
                    for ($i = 0; $i < sizeof($result); $i++) {
                        if ($result[$i]['coid'] == $temp[0]['coid']) {
                            $comment['parentitem'] = $this->db->fetchAll($this->db->select('cid', 'coid', 'created', 'author', 'text', 'parent', 'authorImg')->from('table.comments')->where('cid = ?', $cid)->where('coid = ?', $comment['parent'])->where('status = ?', 'approved')->order('table.comments.created', Typecho_Db::SORT_DESC));
                            $result[$i]['replies'][] = $comment;
                        }
                    }
                }
            }
        }
        $this->export($result);
    }

    private function login()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);

        $code = self::GET('code', 'null');
        if ($code != 'null') {
            $nickname = self::GET('nickname', 'null');
            $avatarUrl = self::GET('avatarUrl', 'null');
            $city = self::GET('city', 'null');
            $country = self::GET('country', 'null');
            $gender = self::GET('gender', 'null');
            $province = self::GET('province', 'null');
            $mp = self::GET('mp', 'weixin');
            if ($mp == 'weixin') $url = sprintf('https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code', $this->appId, $this->appSecret, $code);
            else if ($mp == 'qq') $url = sprintf('https://api.q.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code', $this->qqAppId, $this->qqAppSecret, $code);
            $info = file_get_contents($url);
            $json = json_decode($info); //对json数据解码
            $arr = get_object_vars($json);
            $openid = $arr['openid'];
            if ($openid != null && $openid != '') {
                $row = $this->db->fetchRow($this->db->select('openid', 'lastlogin')->from('table.unitypecho')->where('openid = ?', $openid));
                //已存在的用户,更新上次登录时间
                if (sizeof($row) > 0) {
                    $this->db->query($this->db->update('table.unitypecho')->rows(array(
                        'openid' => $openid, 'createtime' => time(), 'lastlogin' => time(),
                        'nickname' => $nickname, 'avatarUrl' => $avatarUrl, 'city' => $city, 'country' => $country,
                        'gender' => $gender, 'province' => $province
                    ))->where('openid = ?', $openid));
                    $this->export($openid);
                } else {
                    //新用户
                    $this->db->query($this->db->insert('table.unitypecho')->rows(array(
                        'openid' => $openid, 'createtime' => time(), 'lastlogin' => time(),
                        'nickname' => $nickname, 'avatarUrl' => $avatarUrl, 'city' => $city, 'country' => $country,
                        'gender' => $gender, 'province' => $province, 'mp' => $mp
                    )));
                    $this->export($openid);
                }
            } else {
                $this->export($url);
            }
        } else {
            $this->export("empty code");
        }
    }

    private function getLikedNum()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $cid = self::GET('cid', 'null');
        if ($cid != 'null') {
            $likes = $this->db->fetchAll($this->db->select('likes')->from('table.contents')->where('table.contents.cid = ?', $cid));
            $this->export($likes);
        }
    }

    private function getLikedList()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $cid = self::GET('cid', 'null');
        if ($cid != 'null') {
            $openids = $this->db->fetchAll($this->db->select('openid')->from('table.unitypecholike')->where('cid = ?', $cid));
            foreach ($openids as $openid) {
                $temp = $this->db->fetchAll($this->db->select('nickname', 'avatarUrl')->from('table.unitypecho')->where('openid = ?', $openid));
                if (sizeof($temp) > 0) {
                    $likeinfo[] = $temp[0];
                }
            }
            $this->export($likeinfo);
        } else {
            $this->export("No one like");
        }
    }

    private function checkLiked($openid, $cid)
    {
        $row = $this->db->fetchRow($this->db->select('openid', 'cid')->from('table.unitypecholike')->where('cid = ?', $cid)->where('openid = ?', $openid));
        if (sizeof($row) > 0) {
            //已存在该用户点赞
            return 1;
        } else {
            return 0;
        }
    }

    private function getLikeStatus()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $cid = self::GET('cid', -1);
        $openid = self::GET('openid', 'null');
        if ($cid != -1 && $openid != null) {
            $row = $this->db->fetchRow($this->db->select('openid', 'cid')->from('table.unitypecholike')->where('cid = ?', $cid)->where('openid = ?', $openid));
            if (sizeof($row) > 0) {
                //已存在该用户点赞
                $this->export(true);
            } else {
                $this->export(false);
            }
        } else {
            $this->export("Method invalid.");
        }
    }

    private function postLike()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);
        $cid = self::GET('cid', -1);
        $like = self::GET('like', -1);
        $openid = self::GET('openid', 'null');
        $row = $this->db->fetchRow($this->db->select('likes')->from('table.contents')->where('cid = ?', $cid));
        if (self::checkliked($openid, $cid)) {
            //已点赞-1
            $this->db->query($this->db->update('table.contents')->rows(array('likes' => (int) $row['likes'] - 1))->where('cid = ?', $cid));
            $this->db->query($this->db->delete('table.unitypecholike')->rows(array('openid' => $openid, 'cid' => $cid))->where('openid =?', $openid)->where('cid =?', $cid));
            $status = 'dislike';
        } else {
            $this->db->query($this->db->update('table.contents')->rows(array('likes' => (int) $row['likes'] + 1))->where('cid = ?', $cid));
            //更新赞数据库
            $this->db->query($this->db->insert('table.unitypecholike')->rows(array('openid' => $openid, 'cid' => $cid)));
            $status = 'like';
        }
        $likes = $this->db->fetchAll($this->db->select('likes')->from('table.contents')->where('table.contents.cid = ?', $cid));
        $likes['status'] = $status;
        $this->export($likes);
    }

    private function postComment()
    {
        $sec = self::GET('apisec', 'null');
        self::checkApisec($sec);

        $type = self::GET('type', 'mp');
        if ($type == 'mp') {
            $cid = self::GET('cid', -1);
            $author = self::GET('author', "None");
            $text = self::GET('text', "None");
            $parent = self::GET('parent', 0);
            $headicon = self::GET('icon', "NULL");
            $formid = self::GET('formid', "NULL");
            $openid = self::GET('openid', "NULL");

            $coid = $this->db->query($this->db->insert('table.comments')->rows(array(
                'cid' => $cid, 'created' => time(), 'author' => $author, 'authorId' => '0',
                'ownerId' => '1', 'mail' => $openid . '@wx.com', 'ip' => '8.8.8.8', 'agent' => 'wx-miniprogram', 'text' => $text, 'type' => 'comment',
                'status' => 'approved', 'parent' => $parent,
                'authorImg' => $headicon
            )));
            if ($coid > 0) {
                $row = $this->db->fetchRow($this->db->select('commentsNum')->from('table.contents')->where('cid = ?', $cid));
                $this->db->query($this->db->update('table.contents')->rows(array('commentsNum' => (int) $row['commentsNum'] + 1))->where('cid = ?', $cid));
                $this->db->query($this->db->update('table.unitypecho')->rows(array('formid' => $formid))->where('openid = ?', $openid));
            }
            $this->export($coid);
        } else if ($type == "app") {
            $cid = self::GET('cid', -1);
            $author = self::GET('name', "None");
            $parent = self::GET('parent', 0);
            $mail = self::GET('mail', "None");
            $url = self::GET('website', NULL);
            $text = self::GET('text', "None");
            $coid = $this->db->query($this->db->insert('table.comments')->rows(array(
                'cid' => $cid, 'created' => time(), 'author' => $author, 'authorId' => '0',
                'ownerId' => '1', 'mail' => $mail, 'ip' => '8.8.8.8', 'agent' => 'app', 'text' => $text, 'type' => 'comment',
                'status' => 'approved', 'parent' => $parent
            )));
            if ($coid > 0) {
                $row = $this->db->fetchRow($this->db->select('commentsNum')->from('table.contents')->where('cid = ?', $cid));
                $this->db->query($this->db->update('table.contents')->rows(array('commentsNum' => (int) $row['commentsNum'] + 1))->where('cid = ?', $cid));
            }
            $this->export($coid);
        }
    }

    private function defaults()
    {
        $this->export('Method not found.');
    }

    public function export($data = array(), $status = 200)
    {
        $this->res->throwJson(array(
            'status' => $status,
            'data' => $data
        ));
        exit;
    }

    public static function GET($key, $default = '')
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function action()
    {
        $this->on($this->request);
    }

}
