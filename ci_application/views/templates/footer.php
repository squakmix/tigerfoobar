	<div id="dynamicSpacing"></div>

	<footer>
		<div id='bottomBar'>
			<ul>
				<li><h4>Site Nav</h4></li>
				<li><a href='<?=base_url()?>'>home</a></li>
				<li><a href='<?=base_url()?>company'>companies</a></li>
				<li><a href='<?=base_url()?>claim'>claims</a></li>
			</ul>

			<ul>
				<li><h4>About</h4></li>
				<li><a href='<?=base_url()?>about'>about</a></li>
				<li><a href='<?=base_url()?>team'>team</a></li>
				<li><a href='//github.com/ungv/tigerfoobar'>source code</a></li>
			</ul>

			<ul>
				<li><h4>Help</h4></li>
				<li><a href="<?=base_url()?>faq">FAQ</a></li>
				<li>rules</li>
				<li>contact us</li>
			</ul>

			<p>Use of this site constitutes acceptance of our User Agreement and Privacy Policy. &copy; 2013 Tiger Foobar. All rights reserved</p>
		</div>
	</footer>
	</body>
	<?php 
	/*Our Scripts*/
	foreach($jsFiles as $jsFile) { ?>
		<script src="<?= base_url() . 'js/' . $jsFile . '.js' ?>" type="text/javascript"></script>
	<?php } ?>
</html>