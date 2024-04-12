<?php 

namespace app\service\email;
use app\service\Base;

class Email extends Base
{
	protected $emailAccountId = 0;
	protected $siteInfo = [];

	public function sendEmail($memId, $code, $type)
	{
		$data = service('email/Used')->getSiteAccountId();
		if (empty($data)) {
			return false;
		}
		$data['type'] = $type;
		$data['mem_id'] = $memId;
		$data['content'] = $code;
		$rst = $this->insert($data);
		if ($rst) {
			frame('Task')->taskStart('EmailTask');
		}
		return $rst;
	}

	public function sendEmailById($id)
	{
		$info = $this->loadData($id);
		if (empty($info)) {
			return false;
		}
		$memInfo = service('Member')->loadData($info['mem_id'], 'email');
		if (empty($memInfo['email'])) {
			return false;
		}
		$this->emailAccountId = $info['account_id'];
		$toName = explode('@', $memInfo['email'])[0];
		switch ($info['type']) {
			case $this->getConst('TYPE_LOGIN_SEND_CODE'):
				$subject = 'Login Verification Code';
				$template = 'code';
				$this->siteInfo = site()->getInfoCache($info['site_id']);
				$vars = [
					'name' => $toName,
					'siteName' => $this->siteInfo['name'],
					'code' => $info['content'],
					'link' => url('login', ['email'=>$memInfo['email'], 'verify_code'=>$info['content']], $this->siteInfo['domain']),
				];
				break;
			default:
				return false;
				break;
		}
		$rst = $this->sendTemplate($memInfo['email'], $toName, $subject, $this->siteInfo['name'], $template, $vars);
		if ($rst) {
			$status = $this->getConst('STATU_SENT_SUCCESS');
		} else {
			$status = $this->getConst('STATU_SENT_FAILED');
		}
		return $this->updateData($id, ['status'=>$status, 'send_time'=>now()]);
	}

	protected function getSiteEmailTemplate($siteName, $name)
	{
		$file = ROOT_PATH.$name.DS.'template'.DS.'email'.DS.'code.php';
		if (!is_file($file)) {
			$file = ROOT_PATH.'template'.DS.'email'.DS.'code.php';
		}
		return $file;
	}

	protected function sendTemplate($toEmail, $toName, $subject, $siteName, $templateName, $vars=[])
	{
		$file = $this->getSiteEmailTemplate($siteName, $templateName);
		return $this->send($toEmail, $toName, $subject, view()->getContent($file, $vars));
	}

	protected function send($toEmail, $toName, $subject, $content)
	{
		$auth = service('email/Account')->getInfoCache($this->emailAccountId);
		if(!$auth || !$auth['status']){
			return false;
		}

		$mail = service('phpmailer/PHPMailer');
		$mail->SMTPDebug = 4;
		$mail->MessageID = randString(32).'@'.$this->siteInfo['domain'];
		$mail->isSMTP();
		$mail->isHTML();
		// 使用smtp时，SMTPAuth必须是true
		$mail->SMTPAuth = true;
		$host = explode(':', $auth['smtp'], 2);
		if (count($host) < 2) {
			$host[1] = 25;
		}
		// 链接qq域名邮箱的服务器地址
		$mail->Host = $host[0];
		// 设置连接smtp服务器的远程服务器端口号需要对应 ssl  tls端口
		$mail->Port = $host[1];
		// 设置使用加密方式登录鉴权 tls或者ssl
		if ($auth['smtp_ssl']) {
		    $mail->SMTPSecure = 'ssl';
		} else {
		    $mail->SMTPSecure = '';
		    $mail->SMTPAutoTLS = false;
		}

		// 设置发送的邮件的编码
		$mail->CharSet = 'UTF-8';
		// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
		// 设置发件人邮箱地址
		$mail->setFrom($auth['address'], $auth['name']);

		// smtp登录的账号
		$mail->Username = $auth['email_user'];
		// smtp登录的密码 使用生成的授权码
		$mail->Password = $auth['password'];

		// 设置收件人邮箱地址 多个收件人时 多次使用addAddress方法
		$mail->addAddress($toEmail, $toName);
		// 添加该邮件的主题
		$mail->Subject = $subject;
		// 添加邮件正文
		$mail->Body = $content;

		$status = $mail->send();
		if (!$status) {
			frame('Debug')->addLog($mail->ErrorInfo, 'email_error');
		}
		return $status;
	}
}