<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<div class="system-login" style="background-image: url(<?php  if(!empty($_W['setting']['copyright']['background_img'])) { ?> <?php  echo to_global_media($_W['setting']['copyright']['background_img']);?> <?php  } ?>);">
	<div class="head">
		<a href="/" class="logo-version">
			<img src="<?php  if(!empty($_W['setting']['copyright']['flogo'])) { ?><?php  echo to_global_media($_W['setting']['copyright']['flogo'])?><?php  } else { ?>./resource/images/logo/login-logo.png<?php  } ?>" class="logo">
			<span class="version hidden"><?php echo IMS_VERSION;?></span>
		</a>
		<?php  if(!empty($_W['setting']['copyright']['showhomepage'])) { ?>
		<a href="<?php  echo $_W['siteroot'];?>" class="pull-right">首页</a>
		<?php  } ?>
	</div>
	<div class="login-panel">
		<div class="title">
			<a href="javascript:void(0);">账号/手机登录</a>
		</div>
		<form id="login-form" action="" method="post" class="we7-form">
			<div class="input-group-vertical">
				<input name="login_type" type="hidden" class="form-control " value="system">
				<input name="referer" type="hidden" value="<?php  echo $_GPC['referer'];?>">
				<input name="username" type="text" class="form-control " placeholder="请输入用户名/手机登录">
				<input name="password" id="password" type="password" class="form-control password" placeholder="请输入登录密码">
				<span style="display:none;color:red;">大写锁定已打开</span>
				<?php  if(!empty($_W['setting']['copyright']['verifycode'])) { ?>
				<div class="input-group">
					<input name="verify" type="text" class="form-control" placeholder="请输入验证码">
					<a href="javascript:;" id="toggle" class="input-group-btn imgverify"><img id="imgverify" src="<?php  echo url('utility/code')?>" title="点击图片更换验证码" /></a>
				</div>
				<?php  } ?>
			</div>
			<div class="form-inline" style="margin-bottom: 15px;">
				<div class="pull-right">
					<a href="<?php  echo url('user/find-password');?>" target="_blank" class="color-default">忘记密码？</a>
				</div>
				<div class="checkbox">
					<input type="checkbox" value="true" id="rember" name="rember">
					<label for="rember">记住用户名</label>
				</div>
			</div>
			<div class="login-submit text-center">
				<input type="submit" class="btn btn-primary btn-block " value="登录" />
				<div class="text-right">
					<?php  if(!$_W['siteclose'] && $setting['register']['open']) { ?>
						<?php  if(empty($_GPC['login_type']) || $_GPC['login_type'] == 'system') { ?>
						<a href="javascript:;" class="color-default" onclick="console_register()">立即注册</a>
						<?php  } ?>
					<?php  } ?>

					<?php  if(!$_W['siteclose'] && !empty($_W['setting']['copyright']['mobile_status'])) { ?>
						<?php  if($_GPC['login_type'] == 'mobile') { ?>
						<a href="javascript:;" class="color-default" onclick="console_register()">立即注册</a>
						<?php  } ?>
					<?php  } ?>
				</div>
				<input name="token" value="<?php  echo $_W['token'];?>" type="hidden" />
			</div>
			<?php  if(!empty($setting['thirdlogin']['qq']['authstate']) || !empty($setting['thirdlogin']['wechat']['authstate'])) { ?>
			<div class="text-center">
				<span class="color-gray">使用第三方账号登录</span>
				<div class="form-control-static">
					<?php  if(!empty($setting['thirdlogin']['qq']['authstate'])) { ?><a href="<?php  echo $login_urls['qq'];?>"><img src="./resource/images/qqlogin.png" width="35px"></a>&nbsp;&nbsp;<?php  } ?>
					<?php  if(!empty($setting['thirdlogin']['wechat']['authstate'])) { ?><a href="<?php  echo $login_urls['wechat'];?>"><img src="./resource/images/wxlogin.png" width="35px"></a><?php  } ?>
				</div>
			</div>
			<?php  } ?>
		</form>
	</div>
	<div id="user-expired" class="modal fade in" tabindex="-1"  data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true" >
		<div class="modal-dialog modal-tip">
			<div class="modal-content">
				<div class="modal-header clearfix">
				</div>
				<div class="modal-body">
					<div class="text-center">
						<i class="text-info wi wi-info"></i>
						<p class="title">系统提示</p>
						<p class="content"></p>
					</div>
					<div class="clearfix"></div></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	<div id="modal-message" class="modal fade in" tabindex="-1" role="dialog" aria-hidden="false" >
		<div class="modal-dialog modal-tip">
			<div class="modal-content">
				<div class="modal-header clearfix">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<div class="text-center">
						<i class="text-info wi wi-info"></i>
						<p class="title">系统提示</p>
						<p class="content" id="modalmessage">注册功能已迁移至应用商城注册页，请前往注册后登录操作。</p>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<a href="<?php  echo url('user/register/register_url');?>" type="button" class="btn btn-primary" >去注册</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>

<script>
	<?php  if(STATUS_OFF == $_W['ishttps']) { ?>
		util.message('该站点没有配置https，部分功能无法正常使用，请联系站点管理员处理')
	<?php  } ?>
	function detectCapsLock(event) {
		var e = event || window.event;
		var o = e.target || e.srcElement;
		var oTip = o.nextElementSibling;
		var keyCode = e.keyCode || e.switch;
		var isShift = e.shiftKey || (keyCode == 16) || false;
		if (((keyCode >= 65 && keyCode <= 90) && !isShift) || ((keyCode >= 97 && keyCode <= 122) && isShift)) {
			oTip.style.display = '';
		} else {
			oTip.style.display = 'none';
		}
	}
	function console_register() {
		$('#modal-message').modal('show');
	}
	var loginAction = function(e) {
		<?php  if(!empty($_W['setting']['copyright']['verifycode'])) { ?>
			var verify = $(':text[name="verify"]').val();
			if (verify == '') {
				alert('请填写验证码');
				return false;
			}
		<?php  } ?>
		e.preventDefault();
		var postData = $("input").serializeArray();
		var postInit = {}
		for(var key in postData) {
			var data = postData[key]
			postInit[data.name] = data.value
		}
		if(postInit['rember']) {
			util.cookie.set('remember-username', postInit['username']);
		} else {
			util.cookie.del('remember-username');
		}
		if($('input[name="smscode"]').val()) {
			postInit.smscode = $('input[name="smscode"]').val()
		}
		$.post('', postInit, function(data) {
			if(!data || !data.message) {
				return false
			}
			if(data.message.errno === 0) {
				if (data.message.message.status == -1) {
					var showLabel = '';
					if (data.message.message.extend_buttons.status_store_button) {
						showLabel += '<a href="'+data.message.message.extend_buttons.status_store_button.url+'"class="btn btn-primary">去续费</a>';
					}
					showLabel += '<a href="'+data.message.message.extend_buttons.cancel.url+'" class="btn btn-default">取消</a>';
					$('#user-expired').find('.modal-footer').html(showLabel);
					$('#user-expired').find('.content').html(data.message.message.message);
					$('#user-expired').modal('show')
					if (data.message.message.redirect) {
						setTimeout(function(){ window.location.href = data.message.message.redirect; }, 5000);
					}
					return;
				}
				util.message(data.message.message, data.redirect, 'success')
			} else if(data.message.errno === -4){
				if (data.message.message.message) {
					var showLabel = '';
					if (data.message.message.extend_buttons.status_console_button) {
						showLabel = '<a href="'+data.message.message.extend_buttons.status_console_button.url+'"class="btn btn-primary">'+data.message.message.extend_buttons.status_console_button.title+'</a>';
					}
					$('#user-expired').find('.modal-footer').html(showLabel);
					$('#user-expired').find('.content').html(data.message.message.message);
					$('#user-expired').modal('show')
					return;
				}
				window.location.href = data.redirect;
			} else {
				util.message(data.message.message)
				$('#imgverify').prop('src', '<?php  echo url('utility/code')?>r='+Math.round(new Date().getTime()));
			}
		}, 'json')
	}
	$('#login-form').on('submit', loginAction)
	$('.js-login').click(loginAction)
	$('.js-send-code').click(function() {
		$.post('./index.php?c=utility&a=verifycode&do=send_code', function(data) {
			if(data.message && data.message.errno === 0) {
				util.message(data.message.message, '', 'success')
				window.expire = 120
				var time = setInterval(function () {
					$('.js-send-code').attr("disabled",true);
					$('.js-send-code').val(window.expire + '秒后重新获取');
					window.expire--;
					if(window.expire <= 0) {
						$('.js-send-code').attr("disabled", false);
						$('.js-send-code').val('重新获取验证码');
						clearInterval(time)
					}
				}, 1000);
			} else {
				util.message(data.message ? data.message.message : '发送失败', '')
			}
		}, 'json')
	})

	document.getElementById('password').onkeypress = detectCapsLock;

	function formcheck() {
		if($('#remember:checked').length == 1) {
			cookie.set('remember-username', $(':text[name="username"]').val());
		} else {
			cookie.del('remember-username');
		}
		return true;
	}
	var h = document.documentElement.clientHeight;
	if($('.footer').length) {
		h = h - $('.footer').outerHeight();
	}
	$('#toggle').click(function() {
		$('#imgverify').prop('src', '<?php  echo url('utility/code')?>r='+Math.round(new Date().getTime()));
		return false;
	});
	<?php  if(!empty($_W['setting']['copyright']['verifycode'])) { ?>
		$('#form1').submit(function() {
			var verify = $(':text[name="verify"]').val();
			if (verify == '') {
				alert('请填写验证码');
				return false;
			}
		});
	<?php  } ?>
</script>
