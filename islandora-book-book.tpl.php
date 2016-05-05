<?php /**
 * @file
 * Template file to style output.
 */ ?> 

<?php
$imgpath = "http://dimenovels-dev.lib.niu.edu/islandora/object/{$object->id}/datastream/TN";
$element = array(
  '#tag' => 'meta', 
  '#attributes' => array(
    'property' => 'og:image',
    'content' => $imgpath,
  ),
);
drupal_add_html_head($element, 'og_image');
?>

<?php if (isset($viewer)): ?>
  <div id="book-viewer">
    <?php print $viewer; ?>
  </div>
<?php endif; ?>

<div id="context">
	<div id="metadata" class="column grid-10">
		<?php print $metadata; ?>
	</div>
       	<div id="sharing" class="column grid-4">
		<div id="download">
			<h2>Download</h2>
			<ul>
				<?php if(isset($object['PDF'])): ?>
        			<li>
                			<a href='<?php print "/islandora/object/{$object->id}/datastream/PDF/download"; ?>'>PDF (<?php print human_filesize($object['PDF']->size); ?>)</a>
				</li>
				<?php endif; ?>
				<li>
					<a href='<?php print "/islandora/object/{$cover->id}/datastream/JPG/download"; ?>'>Cover (<?php print human_filesize($cover['JPG']->size); ?>)</a>
				</li>
			</ul>
        	</div>
		<div id="services">
			<h2>Share</h2>
			<?php $block = module_invoke('service_links', 'block_view', 'service_links_not_node'); ?>
			<?php print render($block['content']); ?>
		</div>
	</div>
  <?php if(!empty($editions)): ?>
        <div id="related" class="column grid-4">
                <?php print $editions; ?>
        </div>
  <?php endif; ?>

</div>
