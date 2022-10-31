<?php

namespace template\newsme\controller\computer;
use app\controller\Base;

class Faq extends Base
{
    public function index()
    {
        $fid = (int)iget('fid', 0);
        $gid = (int)iget('gid', 0);
        $keyword = trim(iget('search', ''));
        $this->assign('_title', 'FAQ');
        $this->assign('_keyword', 'FAQ');
        $this->assign('_desc', 'FAQ');
        if ($fid > 0) {
            $info = make('app/service/faq/Faq')->loadData(['faq_id'=>$fid, 'status'=>1], 'faq_id,group_id');
            if (!empty($info)) {
                $where = [];
                if (lanId() != 1) {
                    $where['lan_id'] = ['in', [lanId(), 1]];
                } else {
                    $where['lan_id'] = 1;
                }
                make('app/service/faq/Faq')->where(['faq_id'=>$fid])->increment('visit_total');
                $info = array_merge($info, current(make('app/service/faq/FaqLanguage')->getListData($where+['faq_id'=>$fid], 'lan_id,title,content', 0, 0, ['lan_id'=>'DESC'])));
                $info = array_merge($info, current(make('app/service/faq/GroupLanguage')->getListData($where+['group_id'=>$info['group_id']], 'lan_id,name', 0, 0, ['lan_id'=>'DESC'])));
                $info['content'] = str_replace(['{{site_name}}'], [\App::get('base_info', 'name')], $info['content']);
                $this->assign('info', $info);
                $crumbs = [];
                $crumbs[] = [
                    'name' => 'FAQ',
                    'url' => url('faq'),
                ];
                $crumbs[] = [
                    'name' => $info['name'],
                    'url' => url($info['name'], ['g'=>$info['group_id']]),
                ];
                $crumbs[] = [
                    'name' => $info['title'],
                    'url' => '',
                ];
                $this->assign('crumbs', $crumbs);
                $faqList = make('app/service/faq/Faq')->getListData(['status'=>1, 'group_id'=>$info['group_id']], 'faq_id,group_id');
                if (!empty($faqList)) {
                    $language = make('app/service/faq/FaqLanguage')->getListData($where+['faq_id'=>['in', array_column($faqList, 'faq_id')]], 'faq_id,title', 0, 0, ['lan_id'=>'ASC']);
                    $language = array_column($language, null, 'faq_id');
                    foreach ($faqList as $key=>$value) {
                        $faqList[$key] = array_merge($value, $language[$value['faq_id']] ?? []);
                    }
                    $this->assign('faqList', $faqList);
                }
                $this->assign('_title', $info['title']);
                $this->assign('_keyword', $info['title']);
                $this->assign('_desc', $info['title']);
            }
        } else if ($keyword){
            $where = [
                'title' => ['like', '%'.$keyword.'%'],
                'lan_id' => lanId(),
            ];
            $faqList = make('app/service/faq/FaqLanguage')->getListData($where);
            if (!empty($faqList)) {
                $faq = make('app/service/faq/Faq')->getListData(['faq_id'=>['in', array_column($faqList, 'faq_id')]], 'faq_id,visit_total');
                $faq = array_column($faq, 'visit_total', 'faq_id');
                $siteName = \App::get('base_info', 'name');
                foreach ($faqList as $key=>$value) {
                    $temp = explode('.', str_replace(['&nbsp;', PHP_EOL, '   '], [' ', '', ' '], strip_tags($value['content'])));
                    $temp = array_map('trim', $temp);
                    $temp = preg_replace('/\s(?=\s)/', '\\1', implode('.', $temp));
                    $temp = str_replace(['{{site_name}}', $keyword], [$siteName, '<span class="keyword">'.$keyword.'</span>'], $temp);
                    $value['content'] = $temp;
                    $value['title_format'] = str_replace([$keyword, ucfirst($keyword)], ['<span class="keyword">'.$keyword.'</span>', '<span class="keyword">'.ucfirst($keyword).'</span>'], $value['title']);
                    $value['visit_total'] = $faq[$value['faq_id']] ?? 0;
                    $faqList[$key] = $value;
                }
                $this->assign('faqList', $faqList);

            }
            $crumbs = [];
            $crumbs[] = [
                'name' => 'FAQ',
                'url' => url('faq'),
            ];
            $crumbs[] = [
                'name' => 'Search Result',
                'url' => '',
            ];
            $this->assign('crumbs', $crumbs);
            $this->assign('keyword', $keyword);
        } else{
            $where = [];
            if (lanId() != 1) {
                $where['lan_id'] = ['in', [lanId(), 1]];
            } else {
                $where['lan_id'] = 1;
            }
            $groupList = make('app/service/faq/Group')->getListData(['status'=>1], 'group_id,icon');
            if (!empty($groupList)) {
                $language = make('app/service/faq/GroupLanguage')->getListData($where+['group_id'=>['in', array_column($groupList, 'group_id')]], 'group_id,name', 0, 0, ['lan_id'=>'ASC']);
                $language = array_column($language, null, 'group_id');
                foreach ($groupList as $key=>$value) {
                    $value['name'] = $language[$value['group_id']]['name'] ?? '';
                    $groupList[$key] = $value;
                }
                $this->assign('groupList', $groupList);
                if ($gid < 1) {
                    $gid = current($groupList)['group_id'];
                }
                $faqList = make('app/service/faq/Faq')->getListData(['status'=>1], 'faq_id,group_id');
                if (!empty($faqList)) {
                    $language = make('app/service/faq/FaqLanguage')->getListData($where+['faq_id'=>['in', array_column($faqList, 'faq_id')]], 'faq_id,title', 0, 0, ['lan_id'=>'ASC']);
                    $language = array_column($language, null, 'faq_id');
                    $temp = [];
                    foreach ($faqList as $key=>$value) {
                        $temp[$value['group_id']][] = array_merge($value, $language[$value['faq_id']] ?? []);
                    }
                    $this->assign('faqList', $temp);
                }
            }
            $this->assign('gid', $gid);
        }
    }
}