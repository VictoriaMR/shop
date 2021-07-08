<?php $this->load('common/header');?>
<div class="container-fluid">
    <table class="table">
        <tr>
            <th class="col1">名称</th>
            <th class="col1">版本要求</th>
            <th class="col6">状态</th>
        </tr>
        <tr>
            <td>PHP</td>
            <td><?PHP echo PHP_VERSION;?></td>
            <td>
                <?php if(version_compare(PHP_VERSION, '7.1', '>=')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>redis</td>
            <td><?php echo $redis_version;?></td>
            <td>
                <?php if(version_compare($redis_version, '2.8', '>=')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Mysqli</td>
            <td><?php echo mysqlVersion();?></td>
            <td>
                <?php if(extension_loaded('mysqli')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Mysqli.reconnect</td>
            <td>--</td>
            <td>
                <?php if(ini_get('mysqli.reconnect')){?>
                    <span class="green">已开启</span>
                <?php }else{?>
                    <span class="red">未开启</span>
                <?php }?>
            </td>
        </tr>

        <tr>
            <td>Mysqlnd</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('mysqlnd')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Memcached</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('memcached')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Memcache</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('memcache')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Json</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('json')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Openssl</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('openssl')){
                    $openssl_sign=true;
                    ?>
                    <span class="green">已安装</span>
                <?php }else{
                    $openssl_sign=false;
                    ?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>openssl.cnf</td>
            <td><?php
                if($openssl_sign) {
                    $ext = new ReflectionExtension('openssl');
                    ob_start();
                    $ext->info();
                    $info = ob_get_clean();
                    $info = explode("\n", $info);
                    $file = "";
                    foreach ($info as $v) {
                        if (strpos($v, "Openssl default config")) {
                            $file = trim(strip_tags(str_replace("Openssl default config", "", $v)));
                            break;
                        }
                    }
                    if(!empty($file)){
                        echo $file;
                    }
                    else{
                        echo "not found openssl.cnf path";
                    }
                }
                else{
                    echo "not found openssl.cnf path";
                }
                ?></td>
            <td>
                <?php if($openssl_sign && !empty($file) && is_file($file)){
                    echo '<span class="green">已配置</span>';
                }
                else{
                    echo '<span class="red">未配置</span> 可以通过系统配置文件节点：openssl_cnf 单独配置';
                }?>
            </td>
        </tr>
        <tr>
            <td>Pcre</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('pcre')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Exif</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('exif')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Curl</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('curl')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Session</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('session')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Filter</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('filter')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Iconv</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('iconv')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Reflection</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('reflection')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Mbstring</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('mbstring')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>php_zip</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('zip')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>php_xml</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('xml')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>php_gd2</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('gd')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Fileinfo</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('fileinfo')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Mailparse</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('mailparse')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Imap</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('imap')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td>Imagick</td>
            <td>--</td>
            <td>
                <?php if(extension_loaded('imagick')){?>
                    <span class="green">已安装</span>
                <?php }else{?>
                    <span class="red">未安装</span>
                <?php }?>
            </td>
        </tr>
    </table>
</div>
<?php $this->load('common/footer');?>