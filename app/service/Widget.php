<?php
namespace app\service;
class Widget
{
	public function pageBar($page, $totalPage, $buttonNumber=7)
	{
		if($page <= 0){
			$page = 1;
		}
		if($totalPage <= 0){
			$totalPage = 1;
		}
		$pageBarHtml = '';
		$params = iget();
		$pageBarHtml .= '<ul class="widget-page-bar totalpage-'.$totalPage.'" data-totalpage="'.$totalPage.'" data-buttonnumber="'.$buttonNumber.'">';
		//左箭头
		if($page - 1 > 0){
			$url = url('', array_merge($params, ['page' => $page - 1]));
			$pageBarHtml .= '<li class="arrow-li" data-page="'.($page - 1).'"><a href="' . $url . '"><i class="theme-icon left-arrow"></i></a></li>';
		}else{
			$pageBarHtml .= '<li class="arrow-li disabled"><a href="javascript:void(0)"><i class="theme-icon left-arrow"></i></a></li>';
		}
		//页码 1 按钮
		if($page == 1){
			$firstPageUrl = 'javascript:void(0)';
		}else{
			$firstPageUrl = $isAjax ? 'javascript:void(0);' : url('', array_merge($params, ['page' => 1]));
		}
		$pageBarHtml .= '<li '.($page == 1 ? '' : 'data-page="1"').' class="' . ($page == 1 ? 'current' : '') . '"><a href="' . $firstPageUrl . '"><span class="' . ($page == 1 ? 'color-green border-green' : '') . ' font-medium">1</span></a></li>';
		if ($totalPage > 2) {
			//总页数小于等于可点击按钮数, 全部页数显示
			if ($totalPage <= $buttonNumber) {
				for ($i=2; $i<$totalPage; $i++) {
					if ($page == $i) {
						$pageBarHtml .= '<li class="current"><a href="javascript:void(0)"><span class="color-green border-green font-medium">' . $i . '</span></a></li>';
					} else {
						$pageBarHtml .= '<li data-page="'.$i.'"><a href="'.($isAjax ? 'javascript:void(0);' : url('', array_merge($params, ['page' => $i]))).'"><span class="font-medium">' . $i . '</span></a></li>';
					}
				}
			//总页数大于可点击按钮数量
			} else {
				$buttonNumber -= 2;
				//当前页大于第1页两个page, 则用...代替第2个点击按钮
				if ($page > 3) {
					$pageBarHtml .= '<li class="disabled"><a href="javascript:void(0)"><span class="font-medium">...</span></a></li>';
					$buttonNumber --;
				}
				//如果当前页大于尾页$buttonNumber+1个page, 则右侧倒数第2按钮...
				if ($page + $buttonNumber < $totalPage) {
					$startPage = 1;
					$rightButtonNumber = $buttonNumber - ($driver=='mobile'?1:0);
					if ($page > 3) {
						$startPage = 0;
						$rightButtonNumber = $buttonNumber - 1;
					}
					for ($i=$startPage; $i<$rightButtonNumber; $i++) {
						if ($page > 3) {
							$nowPage = $page+$i;
						} else {
							$nowPage = 1+$i;
						}
						if ($page == $nowPage) {
							$pageBarHtml .= '<li class="current"><a href="javascript:void(0)"><span class="color-green border-green font-medium">' . $nowPage . '</span></a></li>';
						} else {
							$pageBarHtml .= '<li data-page="'.$nowPage.'"><a href="'.url('', array_merge($params, ['page' => $nowPage])).'"><span class="font-medium">' . $nowPage . '</span></a></li>';
						}
					}
					$pageBarHtml .= '<li class="disabled"><a href="javascript:void(0)"><span class="font-medium">...</span></a></li>';
				} else {
					//计算开始页码
					$startPage = $totalPage - $page - $buttonNumber;
					//当前页码左侧按钮
					for ($i=$startPage; $i<0; $i++) {
						$nowPage = $page+$i;
						$pageBarHtml .= '<li data-page="'.$nowPage.'"><a href="'.url('', array_merge($params, ['page' => $nowPage])).'"><span class="font-medium">' . $nowPage . '</span></a></li>';
					}
					//当前页码
					if ($page < $totalPage) {
						$pageBarHtml .= '<li data-page="'.$page.'" class="current"><a href="javascript:void(0)"><span class="color-green border-green font-medium">' . $page . '</span></a></li>';
					}
					//当前页码右侧按钮 > $buttonNumber+1,需要加... 特殊位置时, 不要新增按钮, 按钮数量应该小于等于 buttonNumber(不包括.. 左右箭头)
					$rightButtonNumber = $totalPage - $page - $buttonNumber > 1 ? $buttonNumber - 2 : $buttonNumber;
					for ($i=1; $i<=$rightButtonNumber; $i++) {
						$nowPage = $page+$i;
						if ($nowPage >= $totalPage) {
							break;
						}
						$pageBarHtml .= '<li data-page="'.$nowPage.'"><a href="'.url('', array_merge($params, ['page' => $nowPage])).'"><span class="font-medium">' . $nowPage . '</span></a></li>';
					}
					if ($totalPage - $page - $rightButtonNumber > $buttonNumber - 2) {
						$pageBarHtml .= '<li class="disabled"><a href="javascript:void(0)"><span class="font-medium">...</span></a></li>';
					}
				}
			}
		}
		//尾页按钮
		if($totalPage > 1 && $totalPage >= $page){
			if($page == $totalPage){
				$lastPageUrl = 'javascript:void(0)';
			}else{
				$lastPageUrl = url('', array_merge($params, ['page' => $totalPage]));
			}
			$pageBarHtml .= '<li data-page="'.$totalPage.'" class="' . ($page == $totalPage ? 'current' : '') . '"><a href="' . $lastPageUrl . '"><span class="' . ($page == $totalPage ? 'color-green border-green' : '') . ' font-medium">' . $totalPage . '</span></a></li>';
		}
		//右箭头
		if($page + 1 <= $totalPage){
			$url = $isAjax ? 'javascript:void(0);' : url('', array_merge($params, ['page' => $page + 1]));
			$pageBarHtml .= '<li class="arrow-li" data-page="'.($page + 1).'"><a href="' . $url . '"><i class="theme-icon right-arrow"></i></a></li>';
		}else{
			$pageBarHtml .= '<li class="disabled arrow-li"><a href="javascript:void(0)"><i class="theme-icon right-arrow"></i></a></li>';
		}
		$pageBarHtml .= '</ul>';
		return $pageBarHtml;
	}
}