<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-08-21
 * Time: 17:39
 */

namespace Com;


use Ctrl\GameController;

class Com_new_ad extends GameController
{

    public function Index()
    {

        return $this->View();
    }

    public function Index_notice()
    {
        $data['results'] = $this->GmNotice->select(['type' => 2], ['noticetime' => -1]);
        $noticeArr = $this->GmNotice->notice;
        foreach ($data['results'] as $key => $vo) {
            $data['results'][$key]['handler'] = '系统管理员';
            //$data['results'][$key]['content'] = substr($vo['content'],0,40);
            $data['results'][$key]['type'] = $noticeArr[$vo['type']];
            $data['results'][$key]['tag'] = $this->getTagName($vo['tag']);
            //$data['results'][$key]['operate'] = showOperate($this->makeButton($vo['id']));
        }
        $this->response('0000', $data);
    }

    private function getTagName($tag)
    {
        $result = '默认';
        switch ($tag)
        {
            case 0:
                $result = '默认';
                break;
            case 1:
                $result = '热门';
                break;
            case 2:
                $result = '新';
                break;

        }
        return $result;
    }
    public function Index_race()
    {
        $data['results'] = $this->AdCopy->select([], ['adtype' =>2]);
        $noticeArr = $this->AdCopy->type;
        foreach ($data['results'] as $key => $vo) {
            $data['results'][$key]['handler'] = '系统管理员';
            //$data['results'][$key]['content'] = substr($vo['content'],0,40);
            $data['results'][$key]['type'] = $noticeArr[$vo['adtype']];
            $data['results'][$key]['noticetime'] = $vo['tim'];
            $data['results'][$key]['tag'] = $this->getTagName($vo['tag']);
            //$data['results'][$key]['operate'] = showOperate($this->makeButton($vo['id']));
        }
        $this->response('0000', $data);
    }

    public function Index_active()
    {
        $data['results'] = $this->GmNotice->select(['type' => 1], ['noticetime' => -1]);
        $noticeArr = $this->GmNotice->notice;
        foreach ($data['results'] as $key => $vo) {
            $data['results'][$key]['handler'] = '系统管理员';
            //$data['results'][$key]['content'] = substr($vo['content'],0,40);
            $data['results'][$key]['type'] = $noticeArr[$vo['type']];
            $data['results'][$key]['tag'] = $this->getTagName($vo['tag']);
            //$data['results'][$key]['operate'] = showOperate($this->makeButton($vo['id']));
        }
        $this->response('0000', $data);
    }

    public function Index_noticeAdd()
    {

        $type = [0 => '公告', 1 => '活动', 2 => '跑马灯'];
        $data['title'] = $this->request->param('title', '');
        $data['type'] = $this->request->param('type', 0);
        $data['content'] = trim(strip_tags($this->request->param('content', '', false)));
        $data['handler'] = $this->token['username'];
        $data['status'] = 1;
        $data['picurl'] = $this->request->param('picurl', '') ;
        $data['noticetime'] = $this->request->param('endtime', '') ;
        $data['tag'] = $this->request->param('tag', '0') ;;
        if ($data['type']==0){
            $data['title'] = '跑马灯';
            $add =[
                'playtype'=>1,
                'adtype'=>2,
                'starttime'=>$this->request->param('noticetime', ''),
                'endtime'=>$this->request->param('endtime', ''),
                'content'=>$data['content']
            ];

            $id = $this->AdCopy->insert($add);
            //定时任务
            //马上推送
            if(strtotime($data['noticetime']) <time())
            {
                $id=$this->Ad->insert($add);
            }
            empty($id) AND $this->response('0001', [], '添加失败.');
            $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '添加游戏' . $type[$data['type']] . ':' . $id);
            $this->response('0000', $data);

        }elseif ($data['type'] == 1) {
            $cond['type'] = 1;
            /*
             $yes = $this->GmNotice->count($cond);

             if ($yes >= 4) {
                 $this->response('0001', [], '活动已超4条记录，请删除其他后再重试！');
             }
             */
        }

        $id = $this->GmNotice->insert($data);
        empty($id) AND $this->response('0001', [], '添加失败.');
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '添加游戏' . $type[$data['type']] . ':' . $id);
        //$data['type'] == 2 && $this->GmNotice->update(['id' => $id], ['txturl' => $this->request->_S('HOST') . '/' . $id . '.txt']);
        $this->response('0000', $data);
    }

    public function Index_noticeDelete()
    {
        $type = [0 => '跑马灯', 1 => '活动', 2 => '公告'];
        $data['type'] = $this->request->param('type', 0);
        $id = $this->request->param('id', '');
        if($data['type']==0){
            $ad = $this->AdCopy->read(['id'=>$id]);
            empty($ad['id']) AND $this->response('0001', [], '内容不存在.');
            $this->AdCopy->delete(['id'=>$id]);
            $notice['type']=0;
        }else{
            $notice = $this->GmNotice->read(['id'=>$id]);
            empty($notice['id']) AND $this->response('0001', [], '内容不存在.');
            $this->GmNotice->delete(['id'=>$id]);
        }
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '删除游戏' . $type[$notice['type']] . ':' . $id);
        $this->response('0000');
    }


    public function  Index_uploadNoticeImg()
    {
		$files = $this->request->files;
		empty($files['file']['tmp_name']) && $this->response('0001',[],'文件异常,禁止上传');
		$this->is_safe_image($files['file']['tmp_name']);
        $id = $this->request->param('id');
        $data = file_copy($files['file'], $this->token['id']);
        $data['src'] = '/'.str_replace('../../','',$data['src']);
        if(!empty($id))
        {
            if($data['src'])
            {
                $this->GmNotice->update(['id'=>$id],['picurl'=>$data['src']]);
            }
        }
        $data['id'] = $id;
        $this->response('0000', ['data'=>$data],'上传成功','',200,1);
    }

	public function is_safe_image($filename) {
		$s = file_read($filename);
		if(strpos($s, '<script') !== FALSE) {
			unset($s);
			$this->response('0001',[],'文件异常,禁止上传');
		}
		unset($s);
		return TRUE;
	}


}