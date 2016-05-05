<?php
// $Id: page.tpl.php,v 1.1.2.2.4.2 2011/01/11 01:08:49 dvessel Exp $
?>
<div id="page" class="container-16 clearfix">
	<div id="siteheader" class="conainer-16 clearfix">
		<div id="sitebranding" class="column grid-4 alpha">
		<?php print $linked_logo_img; ?>
			<?php print render($page['sitebranding']); ?>
		</div>
		<div id="siteinfo" class="column grid-12 omega" style="cursor: pointer;" onclick="window.location='/';">
			<?php print render($page['siteinfo']); ?>
		</div>
	</div>
	<div id="subheaderarea" class="container-16 clearfix">
	        <div id="islandorasearch" class="column grid-4">
				<?php print render($page['islandora_search']); ?>
			</div>
        	<div id="sitesubheader" class="column grid-12">
                 <?php print render($page['site_subheader']); ?>
        	</div>
	</div>	
	<div id="mainarea" class="container-16 clearfix">	
		<div id="sitecontent" class="grid-16">
			<?php print render($page['content']); ?>
		</div>
	</div>
	<div id="footer" class="conainer-16 clearfix" >
		<div id="footerleft" class="column grid-8">
              <?php print render($page['footerleft']); ?>
        </div>
         <div id="footerright" class="column grid-8">
              <?php print render($page['footerright']); ?>
         </div>
	</div>
</div>
