<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Faq extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$faqId = (int)iget('f', 0);
		$groupId = (int)iget('g', 0);
		$keyword = trim((string)iget('q', ''));
		$lanId = lanId() ?: 1;

		// Load active FAQ groups
		$groups = service('faq/Group')->getListData(['status' => 1], 'group_id,status', 0, 0, ['group_id' => 'asc']);
		$groupLangs = service('faq/GroupLanguage')->getListData(['lan_id' => $lanId], 'group_id,name');
		if (empty($groupLangs)) {
			$groupLangs = service('faq/GroupLanguage')->getListData(['lan_id' => 1], 'group_id,name');
		}
		$groupNameMap = array_column($groupLangs, 'name', 'group_id');
		foreach ($groups as $k => $g) {
			$groups[$k]['name'] = $groupNameMap[$g['group_id']] ?? ('Topic #' . $g['group_id']);
		}

		// Load active FAQs
		$faqs = service('faq/Faq')->getListData(['status' => 1], 'faq_id,group_id,visit_total', 0, 0, ['faq_id' => 'asc']);
		$faqLangs = service('faq/FaqLanguage')->getListData(['lan_id' => $lanId], 'faq_id,title,content');
		if (empty($faqLangs)) {
			$faqLangs = service('faq/FaqLanguage')->getListData(['lan_id' => 1], 'faq_id,title,content');
		}
		$faqLangMap = array_column($faqLangs, null, 'faq_id');

		$allArticles = [];
		$activeArticle = null;
		foreach ($faqs as $f) {
			$fid = $f['faq_id'];
			$lang = $faqLangMap[$fid] ?? null;
			if (!$lang) {
				continue;
			}
			$item = [
				'faq_id'     => $fid,
				'group_id'   => $f['group_id'],
				'group_name' => $groupNameMap[$f['group_id']] ?? 'General',
				'title'      => $lang['title'],
				'content'    => $lang['content'],
				'url'        => url('faq', ['f' => $fid]),
			];
			if ($faqId > 0 && $fid === $faqId) {
				$activeArticle = $item;
				if ($groupId === 0) {
					$groupId = (int)$f['group_id'];
				}
			}
			if ($keyword !== '') {
				if (stripos($item['title'], $keyword) === false && stripos(strip_tags($item['content']), $keyword) === false) {
					continue;
				}
			}
			$allArticles[] = $item;
		}

		$title = $activeArticle ? ($activeArticle['title'] . ' - Help Center') : 'Help Center & FAQ';
		$this->assign('groups', $groups);
		$this->assign('articles', $allArticles);
		$this->assign('activeArticle', $activeArticle);
		$this->assign('activeFaqId', $faqId);
		$this->assign('activeGroupId', $groupId);
		$this->assign('keyword', $keyword);
		$this->assign('_title', $title);
		$this->view();
	}
}
