<header id="header">
    <div class="container6">
        <table class="top-header" border="0" width="100%">
            <tr>
                <td class="side">
                    <div class="icon-content to-modal" data-modal="left-meau-modal">
                        <i class="icon icon24 icon-meau"></i>
                    </div>
                </td>
                <td class="middle">
                    <a href="<?php echo url();?>" class="block" title="logo">
                        <img src="<?php echo siteUrl('img/mobile/main_logo.png');?>" alt="<?php echo ucfirst(\App::get('base_info', 'name'));?>">
                    </a>
                </td>
                <td class="side textright">
                    <a href="<?php echo url('userInfo');?>" class="icon-content" title="user-info">
                        <i class="icon icon24 icon-user"></i>
                    </a>
                    <a href="<?php echo url('cart');?>" class="icon-content" title="cart-info">
                        <i class="icon icon24 icon-cart"></i>
                    </a>
                </td>
            </tr>
        </table>
    </div>
    <div class="container">
        <div class="search-header">
            <form>
                <input type="text" name="search" placeholder="<?php echo appT('search_tips');?>">
            </form>
        </div>
    </div>
</header>
<div class="header-empty"></div>
<div class="modal modal-2" id="left-meau-modal">
    <div class="mask" opacity="0"></div>
    <div class="modal-content">
        <div class="left-content common-content">
            <div>
                
            </div>
            <div class="footer">
                <button type="button" class="item to-modal" data-modal="currency-modal">
                    <i class="icon-flag icon-usd"></i><span>USD</span><i class="icon icon16 icon-edit"></i>
                </button type="button">
                <button type="button" class="item to-modal" data-modal="language-modal">
                    <span>Englist</span><i class="icon icon16 icon-edit"></i>
                </button type="button">
            </div>
        </div>
        <div class="right-content common-content"></div>
    </div>
</div>
<div class="modal modal-1" id="currency-modal">
    <div class="mask"></div>
    <div class="modal-content">
        <div class="header">
            <span class="name"><?php echo appT('select_currency');?></span>
            <span class="close-btn"><i class="icon icon20 icon-close"></i></span>
        </div>
        <div class="content"></div>
    </div>
</div>
<div class="modal modal-1" id="language-modal">
    <div class="mask"></div>
    <div class="modal-content">
        <div class="header">
            <span class="name"><?php echo appT('select_language');?></span>
            <span class="close-btn"><i class="icon icon20 icon-close"></i></span>
        </div>
        <div class="content"></div>
    </div>
</div>