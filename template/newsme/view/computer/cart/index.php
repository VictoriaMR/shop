<?php $this->load('common/buyer_header');?>
<div class="cart-page mt12 mb20">
    <div class="layer bg-f">
        <?php if (empty($checkedList) && empty($unCheckList)) {?><div class="empty-info tc">
            <img src="<?php echo siteUrl('image/common/empty-bag.png');?>">
            <p class="mt12 f16">Your bag is empty.</p>
            <a href="<?php echo url('list', ['c'=>0]);?>" class="btn btn-black">GO SHOPPING</a>
        </div>
        <?php $this->load('product/recommend');?>
        <?php } else {?><div class="relative">
            <div class="bread-nav">
                <span>Shopping Bag</span>&nbsp;&nbsp;&gt;&nbsp;&nbsp;Place Order&nbsp;&nbsp;&gt;&nbsp;&nbsp;Pay&nbsp;&nbsp;&gt;&nbsp;&nbsp;Order Complete
            </div>
            <div class="list-left">
                <?php if (!empty($checkedList)){?><div class="bag-title e1">
                    <span class="f18 f600">Shopping Bag</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="c9">Prices are subject to change based on the price in effect the day you checkout.</span>
                </div>
                <table border="0" class="w100">
                    <thead>
                        <tr>
                            <th width="60%">Item</th>
                            <th width="15%">Qty</th>
                            <th width="15%">Price</th>
                            <th width="10%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($checkedList as $value){?><tr data-id="<?php echo $value['cart_id'];?>" data-sid="<?php echo $value['sku_id'];?>">
                            <td>
                                <div class="table w100">
                                    <div class="tcell checkbox">
                                        <span class="f18 pointer iconfont icon-<?php echo $value['checked']?'yuanxingxuanzhongfill':'yuanxingweixuanzhong';?>"></span>
                                    </div>
                                    <div class="tcell product-info">
                                        <a class="img-content" href="<?php echo $value['url'];?>">
                                            <img data-src="<?php echo $value['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
                                        </a>
                                        <div class="info-content">
                                            <a class="name" href="<?php echo $value['url'];?>"><?php echo $value['name'];?></a>
                                            <p class="mt8">
                                                <?php foreach($value['attr'] as $attrValue){?><span title="<?php echo $attrValue['attrn_name'];?>"><?php echo $attrValue['attrv_name'];?></span>&nbsp;&nbsp;<?php }?>
                                            </p>
                                            <p class="c9 mt8">SKU: <?php echo $value['sku_id'];?></p>
                                            <div class="btn-content mt12 noselect">
                                                <span class="edit">Edit</span>
                                                <span class="wish">Save for wishlist</span>
                                                <span class="delete">Delete</span>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="tc quantity-content">
                                <div class="item minus" <?php echo $value['quantity']<2?'disabled="disabled"':'';?>></div>
                                <div class="num">
                                    <input type="text" value="<?php echo $value['quantity'];?>" data-stock="<?php echo $value['stock'];?>">
                                </div>
                                <div class="item plus" <?php echo $value['quantity']>=$value['stock']?'disabled="disabled"':'';?>></div>
                            </td>
                            <td class="tc price-info">
                                <p class="f14"><?php echo $value['price_format'];?></p>
                                <?php if (!$value['show_price'] && $value['original_price'] > $value['price']){?><p class="mt6 c9"><?php echo $value['original_price_format'];?></p>
                                <p class="mt6 cred"><?php echo number_format(1-$value['price']/$value['original_price'],2)*100;?>% off</p>
                                <?php }?>
                            </td>
                            <td>
                                <span class="f14 f500"><?php echo $value['price_total_format'];?></span>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                <?php }?>
            </div>
            <div class="summary-right">
                <p class="summary-title">Order Summary</p>
                <div class="summary-content mb30">
                    <?php foreach ($summary as $value){?><div class="item">
                        <p data-type="<?php echo $value['type'];?>">
                            <span><?php echo $value['name'];?></span>
                            <span class="right"><?php echo $value['price_format'];?></span>
                        </p>
                    </div>
                    <?php }?>
                </div>
                <button id="checkout-btn" class="btn btn-black">SECURE CHECKOUT</button>
                <div class="checkout-tips">Apply a Coupon Code on the next step.</div>
                <?php $this->load('payment/paypal', ['orderTotal'=>array_column($summary, 'price', 'type')[2]]);?>
            </div>
            <div class="clear"></div>
            <?php $this->load('product/recommend');?>
        </div>
        <?php }?>
    </div>
</div>
<?php $this->load('common/base_footer');?>