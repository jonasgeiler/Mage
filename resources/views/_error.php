<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<title><?= $code ?> - <?= $status ?></title>

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css" />
		<style type="text/css">
			body {
				height:          100vh;
				display:         flex;
				flex-direction:  column;
				justify-content: center;
				align-items:     center;
				margin-top:      0;
				margin-bottom:   0;
			}

			.code {
				margin-bottom: 0;
				font-size:     6em;
			}

			.message {
				margin-top: 0;
				text-align: center;
				color:      #a9b1ba;
			}

			.error {
				max-width: 100%;
			}
		</style>
	</head>
	<body>
		<h1 class="code"><?= $code ?></h1>
		<h2 class="message"><?= strtoupper($status) ?></h2>

		<?php if (!$this->fw->PRODUCTION): ?>
			<pre class="error"><code><b><?= $text ?></b>

<?= $trace ?></code></pre>
		<?php endif; ?>
	</body>
</html>
