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
		$leftLen = ceil($buttonNumber / 2) - 2;//左侧数量
		$rightLen = ceil($buttonNumber / 2) - 1;//右侧数量
		$leftStart = $page - $leftLen > 0 ? $page - $leftLen : 1;//左侧起始页码
		$leftDiff = $leftLen - $page + $leftStart;//左侧按钮剩余数量
		$rightEnd = $totalPage - $page - $rightLen > 0 ? $page + $rightLen : $totalPage;//右侧结束页码
		$rightDiff = $rightLen - $rightEnd + $page;//右侧按钮剩余数量
		$rightEnd = $rightEnd + $leftDiff > $totalPage ? $rightEnd : $rightEnd + $leftDiff;
		$leftStart = $leftStart - $rightDiff > 0 ? $leftStart - $rightDiff : 1;
		$router = make('frame/Router');
		//向上翻页箭头
		if ($totalPage > 1) {
			if ($page == 1) {
				$pageBarHtml .= '<li class="arrow-li disabled" data-page="0"><a href="javascript:;"><span class="iconfont icon-xiangzuo1"></span></a></li>';
			} else {
				$pageBarHtml .= '<li class="arrow-li" data-page="'.($page-1).'"><a href="'.$router->setParam(['page'=>$page-1]).'"><span class="iconfont icon-xiangzuo1"></span></a></li>';
			}
		}
		//左侧页码
		if ($page > 1) {
			for ($i=$leftStart; $i<$page; $i++) { 
				$pageBarHtml .= '<li class="arrow-li" data-page="'.$i.'"><a href="'.$router->setParam(['page'=>$i]).'">'.$i.'</a></li>';
			}
		}
		$pageBarHtml .= '<li class="arrow-li active" data-page="'.$page.'"><a href="'.$router->setParam(['page'=>$page]).'">'.$page.'</a></li>';
		if ($totalPage > 1) {
			for ($i=$page+1; $i<=$rightEnd; $i++) { 
				$pageBarHtml .= '<li class="arrow-li" data-page="'.$i.'"><a href="'.$router->setParam(['page'=>$i]).'">'.$i.'</a></li>';
			}
		}
		//向下翻页箭头
		if ($page == $totalPage) {
			$pageBarHtml .= '<li class="arrow-li disabled" data-page="0"><a href="javascript:;"><i class="iconfont icon-xiangyou1"></i></a></li>';
		} else {
			$pageBarHtml .= '<li class="arrow-li" data-page="'.($page+1).'"><a href="'.$router->setParam(['page'=>$page+1]).'"><i class="iconfont icon-xiangyou1"></i></a></li>';
		}
		$pageBarHtml .= '</ul>';
		return $pageBarHtml;
	}
}