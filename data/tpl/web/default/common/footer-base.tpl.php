<?php defined('IN_IA') or exit('Access Denied');?>
	<?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
	<?php  if(!empty($_GPC['m']) && !in_array($_GPC['m'], array('keyword', 'special', 'welcome', 'default', 'userapi')) || defined('IN_MODULE')) { ?>
	<script>
		if(typeof $.fn.tooltip != 'function' || typeof $.fn.tab != 'function' || typeof $.fn.modal != 'function' || typeof $.fn.dropdown != 'function') {
			require(['bootstrap']);
		}
		$('[data-toggle="tooltip"]').tooltip()
	</script>
	<?php  } ?>
	<?php  if(!defined('IN_MODULE')) { ?>
	<script>
		$(document).ready(function() {
			if($('select').niceSelect) {
				$('select').niceSelect();
			}
		});
	</script>
	<script>
		$(document).ready(function() {
			$('.is-main-page').click(function() {
				var url,
					frame = '<?php echo FRAME;?>',
					console_siteurl = 'http://console.w7.cc/console/<?php  echo $_W['setting']['site']['key'];?>/we7';
				switch (frame) {
					case 'account_manage':
						url = '/account/manage';
						break;
					case 'module_manage':
						url = '/module/manage';
						break;
					case 'user_manage':
						url = '/user/manage';
						break;
					case 'permission':
						url = '/permission/modules';
						break;
					case 'system':
						url = '/system/notice/list';
						break;
				}

				url = url ? console_siteurl + url : 'http://console.w7.cc/v2/console/<?php  echo $_W['setting']['site']['key'];?>/go-license-console';

				var d = util.message('最新版系统功能已经迁移到控制台，请前往控制台操作。<p><<a class="btn btn-link" href="' + url + '">如果你的浏览器没有自动跳转，请点击此链接</a></p>', '', 'info')
				$(d).find('.modal-footer .btn').text('确认跳转');
				$(d).find('.modal-footer .btn').click(function() {
					location.href = url;
				});
				var goTime = setTimeout(function() {
					window.location.href = url;
				}, 3000)
				d.on('hidden.bs.modal', function() {
					goTime && clearTimeout(goTime)
				})
			})
		});
	</script>
	<?php  } ?>