<?php
namespace app\service;
class Widget
{
	public function pageBar($page, $totalPage, $buttonNumber=7)
	{
		$page = $page ? $page : 1;
		$totalPage = $totalPage ? $totalPage : 1;
		$pageBarHtml = '';
		$pageBarHtml .= '<ul class="widget-page-bar" data-page="'.$page.'" data-totalpage="'.$totalPage.'" data-buttonnumber="'.$buttonNumber.'">';
		$middleNum = ceil($buttonNumber / 2) - 1;
		$diff = $page - $middleNum;
		//向上翻页箭头
		$pageBarHtml .= '<li class="arrow-li" data-page="'.($page - 1).'"><a href="'.''.'"><span class="iconfont icon-xiangzuo1"></span></a></li>';
		if ($diff > 0) {
			for ($i=$diff; $i < $page; $i++) { 
				$pageBarHtml .= '<li class="arrow-li" data-page="'.$i.'"><a href="'.''.'">'.$i.'</a></li>';
			}
		}
		$pageBarHtml .= '<li class="arrow-li" data-page="'.$page.'"><a href="'.''.'">'.$page.'</a></li>';
		if ($page < $totalPage) {
			for ($i=$page+1; $i < $buttonNumber-$diff; $i++) { 
				$pageBarHtml .= '<li class="arrow-li" data-page="'.$i.'"><a href="'.''.'">'.$i.'</a></li>';
			}
		}
		//向下翻页箭头
		$pageBarHtml .= '<li class="arrow-li" data-page="'.($page - 1).'"><a href="'.''.'"><i class="iconfont icon-xiangyou1"></i></a></li>';
		$pageBarHtml .= '</ul>';
		return $pageBarHtml;
	}
}