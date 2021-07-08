<?php 

namespace app\service;

use app\service\Base as BaseService;

class EmailService extends BaseService
{
	public function send($to, $content, $form='')
	{
		if (empty($to) || empty($content)) {
			return false;
		}

		$mail = new PHPMailer();
        // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
		$mail->SMTPDebug = 0;

		//自己定义发邮件头中的Message-ID 和Message-ID@后面对应的域名匹配正则/^<.*@.*>$/ MessageID为空时 使用generateId方法生成随机字符串
		$mail->MessageID='<'.Data::randString(32).'@'.site()->getConfig('pcDomain',$this->site_id).'>';
        // 使用smtp发送邮件
		$mail->isSMTP();
		$mail->isHTML();
		// 使用smtp时，SMTPAuth必须是true
		$mail->SMTPAuth = true;

		$host = explode(":",$auth["smtp"],2);
		if(count($host)<2){
		    $host[1] = 25;
        }
        // 链接qq域名邮箱的服务器地址
		$mail->Host = $host[0];
        // 设置连接smtp服务器的远程服务器端口号需要对应 ssl  tls端口
		$mail->Port = $host[1];
		// 设置使用加密方式登录鉴权 tls或者ssl
        if($auth["smtp_ssl"]){
            $mail->SMTPSecure = "ssl";
        }
        else{
            $mail->SMTPSecure ="";
            $mail->SMTPAutoTLS = false;
        }

        // 设置发送的邮件的编码
		$mail->CharSet = 'UTF-8';
        // 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
        // 设置发件人邮箱地址
        $mail->setFrom($auth['address'],$auth["name"]);

        // smtp登录的账号
		$mail->Username = $auth['user'];
        // smtp登录的密码 使用生成的授权码
		$mail->Password = $auth['password'];

        // 设置收件人邮箱地址 多个收件人时 多次使用addAddress方法
		$mail->addAddress($toEmail, $toName);
        // 添加该邮件的主题
		$mail->Subject = $subject;
        // 添加邮件正文
		$mail->Body = $content;

        // 为该邮件添加附件 多个附件时 多次使用addAttachment方法
        if(!empty($attachmentList)) {
            foreach ($attachmentList as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        // 发送邮件 返回状态 成功返回true
		$status = $mail->send();
		if(!$status){
			$this->error = $mail->ErrorInfo;
		}
		return $status;
	}
}