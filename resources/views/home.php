<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="theme-color" content="#0096bf" />

		<title>Mage, by Skayo</title>

		<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
		<link rel="manifest" href="/manifest.json">

		<script async defer data-domain="mage.skayo.dev" src="https://analytics.skayo.dev/js/plausible.js"></script>

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.min.css" />
		<style type="text/css">
			header {
				text-align: center;
			}

			.subtitle {
				color: var(--text-muted);
			}

			.row {
				display:      flex;
				margin-left:  -0.75rem;
				margin-right: -0.75rem;
			}

			.col {
				display:     block;
				flex-basis:  0;
				flex-grow:   1;
				flex-shrink: 1;
				padding:     0.75rem;
			}

			.option {
				padding:          2px 4px;
				background-color: var(--selection);
				color:            #fff;
			}

			body > footer {
				text-align: center;
			}
		</style>
	</head>
	<body>
		<header>
			<h1>&#x1F9D9; Mage</h1>
			<h3 class="subtitle">Magically creates wonderful images for you!</h3>
		</header>

		<section>
			<h2>Placeholder</h2>
			<p>Generate custom placeholder images for your websites and templates on the fly.</p>

			<h3>Examples</h3>
			<div class="row">
				<div class="col"><img src="/placeholder/500x250/f05945/fff" alt="Example Placeholder 1" /></div>
				<div class="col"><img src="/placeholder/500x250/f7f3e9/5eaaa8?Custom+Text" alt="Example Placeholder 2" /></div>
			</div>

			<h3>How to use</h3>

			<h4>TL;DR</h4>
			<pre><code><?= $URL ?>/placeholder/<code
						class="option">width</code>x<code
						class="option">height</code>/<code
						class="option">background-color</code>/<code
						class="option">text-color</code>.<code
						class="option">format</code>?<code
						class="option">text</code></code></pre>


			<h4>How to set image size</h4>
			<p>
				The image size is the only required option.<br />
				Just specify it after the placeholder endpoint (<code>/placeholder</code> or <code>/ph</code>)
				and you'll get a placeholder image:
			</p>
			<pre><code><?= $URL ?>/placeholder/<strong>500</strong></code></pre>
			<p>
				The height is optional. If no height is specified, your placeholder image will be a square.<br />
				So if you want to set the height, use the <code>&lt;width&gt; x &lt;height&gt;</code> format:
			</p>
			<pre><code><?= $URL ?>/placeholder/500<strong>x250</strong></code></pre>


			<h4>How to set image background & text color</h4>
			<p>
				By default, text color is dark grey and background color is grey.<br />
				Colors are represented as either a hex code (like <code>#ff0000</code> or <code>#f00</code>)
				or a CSS color name (like <code>red</code>).
				They are specified after the image size, with the first option
				being the background color and the second option being the text color.
				Both are optional so you can leave out the text color if you want.<br />
				For example, an image with red background and white text would be:
			</p>
			<pre><code><?= $URL ?>/placeholder/500x250/<strong>red</strong>/<strong>white</strong>
OR
<?= $URL ?>/placeholder/500x250/<strong>f00</strong>/<strong>fff</strong></code></pre>


			<h4>How to set custom text</h4>
			<p>
				By default, the text on the image is just the image dimensions in pixels.<br />
				To specify custom text, use a query string at the <i>very end</i> of the URL.
				Everything after the <code>?</code> will be used as the text:
			</p>
			<pre><code><?= $URL ?>/placeholder/500x250/f05945/fff<strong>?This+is+some+custom+text</strong></code></pre>
			<p>
				The text should be URL-encoded. So spaces are <code>+</code> or <code>%20</code> and newlines are <code>%0A</code>.<br />
				For more information, see <a href="https://wikipedia.org/wiki/Percent-encoding" target="_blank" rel="noopener">Percent-encoding</a>.
			</p>


			<h4>How to set image format</h4>
			<p>
				To set a image format, add the file extension after any of the options:
			</p>
			<pre><code><?= $URL ?>/placeholder/500x250/f05945/fff<strong>.png</strong>
<?= $URL ?>/placeholder/500x250/f05945<strong>.png</strong>/fff
<?= $URL ?>/placeholder/500x250<strong>.png</strong>/f05945/fff</code></pre>
			<p>
				Supported image formats are:
			</p>
			<ul>
				<li>PNG (<code>.png</code>)</li>
				<li>JPEG (<code>.jpg</code> or <code>.jpeg</code>)</li>
				<li>GIF (<code>.gif</code>)</li>
				<li>WEBP (<code>.webp</code>)</li>
			</ul>
		</section>

		<hr />

		<section>
			<h2>Identicon</h2>

			<p>Generate unique Identicons for your users.</p>

			<blockquote>
				"An Identicon is a visual representation of a hash value, usually of an username or IP address,
				that serves to identify a user of a computer system as a form of avatar while protecting the users' privacy."

				<footer>
					<a href="https://en.wikipedia.org/wiki/Identicon" target="_blank" rel="noopener">Wikipedia</a>
				</footer>
			</blockquote>

			<h3>Examples</h3>
			<div class="row">
				<div class="col"><img src="/identicon/500" alt="Example Identicon 1" /></div>
				<div class="col"><img src="/identicon/500?Skayo" alt="Example Identicon 2" /></div>
				<div class="col"><img src="/identicon/500?Mage" alt="Example Identicon 3" /></div>
				<div class="col"><img src="/identicon/500?Hey+There+:)" alt="Example Identicon 4" /></div>
			</div>

			<h3>How to use</h3>

			<h4>TL;DR</h4>
			<pre><code><?= $URL ?>/identicon/<code class="option">size</code>?<code class="option">seed</code></code></pre>

			<h4>How to set image size</h4>
			<p>
				The image size is the only required option.<br />
				Just specify it after the identicon endpoint (<code>/identicon</code> or <code>/id</code>)
				and you'll get an identicon image:
			</p>
			<pre><code><?= $URL ?>/identicon/<strong>500</strong></code></pre>

			<h4>How to set a seed</h4>
			<p>
				By default, the seed is just your IP address.
				To provide a custom seed, use a query string at the <i>very end</i> of the URL.
				Everything after the <code>?</code> will be used as the seed:
			</p>
			<pre><code><?= $URL ?>/identicon/500<strong>?Some+seed</strong></code></pre>
			<p>
				The seed can be any value you want: A username, an IP address, an email, a timestamp, etc.<br />
				The generator uses the seed to randomly generate the image, so if you provide the same seed multiple times,
				it'll always produce the same image.
			</p>
		</section>

		<footer class="footer">
			Made with &#x2764; by <a href="https://skayo.dev" rel="noopener" target="_blank">Skayo</a>
			&bull;
			<a href="/privacy">Privacy Policy</a>
		</footer>
	</body>
</html>
