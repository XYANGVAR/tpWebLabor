<?php /*a:1:{s:63:"D:\xampp\tpWebLabor\application\index\view\user\teachlogin.html";i:1562835763;}*/ ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<link href="/public/favicon.ico" rel="icon" type="image/x-icon" />
		<script type="text/javascript" src="/public/static/js/jquery-3.3.1.min.js"></script>
		<title>老师登录</title>
		<!--管理员登录界面-->
		<style>
			* {
				font-family: 'montserrat', sans-serif;
			}
			body {
				margin: 0;
				padding: 0;
				background: #333;
			}
			.login-box {
				position: absolute;
				top: 0;
				left: -100%;
				width: 100%;
				height: 100vh; /* vh 视口高度 viewport height 百分比单位*/
				background-image: linear-gradient(
					45deg,
					#9fbaa8,
					#31354c
				); /*设置颜色渐变 方向(0deg垂直向上) 起点颜色 终点颜色*/
				transition: 0.4s; /*过度效果  property duration timing-function delay 默认属性:all 0 ease 0*/
			}
			.login-form {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%); /*定义 2D 转换8 */
				color: white;
				text-align: center;
			}
			.login-form h1 {
				font-weight: 400;
				margin-top: 0;
			}
			.txtb {
				display: block;
				box-sizing: border-box;
				width: 240px;
				background: #ffffff28;
				border: 1px solid white;
				padding: 10px 20px;
				color: white;
				outline: none;
				margin: 10px 0;
				border-radius: 6px;
				text-align: center;
			}
			.login-btn {
				width: 240px;
				background: #2c3e50;
				border: 0;
				color: white;
				padding: 10px;
				border-radius: 6px;
				cursor: pointer;
			}
			.hide-login-btn {
				color: #000;
				position: absolute;
				top: 40px;
				right: 40px;
				cursor: pointer;
				font-size: 30px;
				opacity: 0.7;
				transform: rotate(45deg); /*定义 2D 转换8 */
			}
			.show-login-btn {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				color: white;
				border: 2px solid;
				padding: 10px;
				cursor: pointer;
			}
			.showed {
				left: 0;
			}
		</style>
	</head>
	<body>
		<div class="show-login-btn">
			-> To Login
		</div>

		<div class="login-box">
			<div class="hide-login-btn">
				+
			</div>
			<form method="POST" class="login-form" id="login">
				<h1>Administrator</h1>
				<input class="txtb" type="text" name="num" placeholder="工号" />
				<input class="txtb" type="password" name="password" placeholder="密码" />
				<button type="button" class="login-btn" id="sub">Login</button>
				<!--<input class="login-btn" type="submit" name="" value="Login" disabled />-->
			</form>
		</div>

		<script>
			$(function () {
				$("#sub").on('click',function () {
					$.ajax({
						type: 'post',
						url:"<?php echo url('teacher/loginCheck'); ?>",
						data:$('#login').serialize(),
						dataType:'json',
						success:function (data) {
							switch (data.success) {
								case 1:
									alert(data.message);
									window.location.href = "<?php echo url('teacher/teachspace'); ?>";
									break;
								case 0:
								case -1:
									alert(data.message);
									window.location.back;
							}
						},
						error:function () {
							alert('error');
						}
					})
				})
			});
		</script>

		<script type="text/javascript">
			function hasClass(element, clssname) {
				return element.className.match(new RegExp('(\\s|^)' + clssname + '(\\s|$)'))
			}
			function addClass(element, clssname) {
				if (!this.hasClass(element, clssname)) element.className += ' ' + clssname
			}
			function removeClass(element, clssname) {
				if (hasClass(element, clssname)) {
					var reg = new RegExp('(\\s|^)' + clssname + '(\\s|$)')
					element.className = element.className.replace(reg, ' ')
				}
			}
			function toggleClass(element, clssname) {
				if (hasClass(element, clssname)) {
					removeClass(element, clssname)
				} else {
					addClass(element, clssname)
				}
			}
			var loginBox = document.getElementsByClassName('login-box')
			var showBtn = document.getElementsByClassName('show-login-btn')
			var hideBtn = document.getElementsByClassName('hide-login-btn')
			showBtn[0].addEventListener('click', function() {
				toggleClass(loginBox[0], 'showed')
			})
			hideBtn[0].addEventListener('click', function() {
				toggleClass(loginBox[0], 'showed')
			})
		</script>
	</body>
</html>
