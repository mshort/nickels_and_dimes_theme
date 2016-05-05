<?php
// $Id: page.tpl.php,v 1.1.2.2.4.2 2011/01/11 01:08:49 dvessel Exp $

/**
 * @file
 * Colorbox theme functions.
 */

/**
 * Returns HTML for an Colorbox image field formatter.
 *
 * @param $variables
 *   An associative array containing:
 *   - item: An array of image data.
 *   - image_style: An optional image style.
 *   - path: An array containing the link 'path' and link 'options'.
 *
 * @ingroup themeable
 */
 
function ninesixtytwain_colorbox_image_formatter($variables) 
{
  $item = $variables['item'];
  $entity_type = $variables['entity_type'];
  $entity = $variables['entity'];
  $field = $variables['field'];
  $settings = $variables['display_settings'];

  $image = array(
    'path' => $item['uri'],
    'alt' => $item['alt'],
    'title' => $item['title'],
    'style_name' => $settings['colorbox_node_style'],
  );

  if (isset($item['width']) && isset($item['height'])) 
  {
    $image['width'] = $item['width'];
    $image['height'] = $item['height'];
  }

  $entity_title = entity_label($entity_type, $entity);

  switch ($settings['colorbox_caption']) 
  {
     case 'auto':
      // If the title is empty use alt or the entity title in that order.
      if (!empty($image['title'])) 
	  {
        //$caption = $image['title'];
		$caption = "testing 123";
      }
      elseif (!empty($image['alt'])) 
	  {
        //$caption = $image['alt'];
		$caption = "testing 123";
      }
      elseif (!empty($entity_title)) 
	  {
        //$caption = $entity_title;
		$caption = "testing 123";
      }
      else 
	  {
        //$caption = '';
		$caption = "testing 123";
      }
      break;
    case 'title':
      //$caption = $image['title'];
      $caption = "testing 123";
	  break;
    case 'alt':
      //$caption = $image['alt'];
      $caption = "testing 123";
	  break;
    case 'node_title':
      //$caption = $entity_title;
      $caption = "testing 123";
	  break;
    case 'custom':
      //$caption = token_replace($settings['colorbox_caption_custom'], array($entity_type => $entity, 'file' => (object) $item), array('clear' => TRUE));
      $caption = "testing 123";
	  break;
    default:
      //$caption = '';
	  $caption = "testing 123";
  }

  // Shorten the caption for the example styles or when caption shortening is active.
  $colorbox_style = variable_get('colorbox_style', 'default');
  $trim_length = variable_get('colorbox_caption_trim_length', 75);
  if (((strpos($colorbox_style, 'colorbox/example') !== FALSE) || variable_get('colorbox_caption_trim', 0)) && (drupal_strlen($caption) > $trim_length)) {
    $caption = drupal_substr($caption, 0, $trim_length - 5) . '...';
  }

  // Build the gallery id.
  list($id, $vid, $bundle) = entity_extract_ids($entity_type, $entity);
  $entity_id = !empty($id) ? $entity_type . '-' . $id : 'entity-id';
  switch ($settings['colorbox_gallery']) {
    case 'post':
      $gallery_id = 'gallery-' . $entity_id;
      break;
    case 'page':
      $gallery_id = 'gallery-all';
      break;
    case 'field_post':
      $gallery_id = 'gallery-' . $entity_id . '-' . $field['field_name'];
      break;
    case 'field_page':
      $gallery_id = 'gallery-' . $field['field_name'];
      break;
    case 'custom':
      $gallery_id = $settings['colorbox_gallery_custom'];
      break;
    default:
      $gallery_id = '';
  }

  if ($style_name = $settings['colorbox_image_style']) {
    $path = image_style_url($style_name, $image['path']);
  }
  else {
    $path = file_create_url($image['path']);
  }

  return theme('colorbox_imagefield', array('image' => $image, 'path' => $path, 'title' => $caption, 'gid' => $gallery_id));
}

/**
 * Returns HTML for an image using a specific Colorbox image style.
 *
 * @param $variables
 *   An associative array containing:
 *   - image: image item as array.
 *   - path: The path of the image that should be displayed in the Colorbox.
 *   - title: The title text that will be used as a caption in the Colorbox.
 *   - gid: Gallery id for Colorbox image grouping.
 *
 * @ingroup themeable
 */
function ninesixtytwain_colorbox_imagefield($variables) 
{
  $class = array('colorbox');

  if ($variables['image']['style_name'] == 'hide') 
  {
    $image = '';
    $class[] = 'js-hide';
  }
  elseif (!empty($variables['image']['style_name'])) 
  {
    $image = theme('image_style', $variables['image']);
  }
  else 
  {
    $image = theme('image', $variables['image']);
  }

  $options = array(
    'html' => TRUE,
    'attributes' => array(
      'title' => $variables['title'],
      'class' => $class,
      'rel' => $variables['gid'],
    )
  );

  return l($image, $variables['path'], $options);
}


?>
<div id="page" class="container-16 clearfix">
 
	<div id="site-header" class="conainer-16 clearfix">
		<div id="sitebranding" class="column grid-4 alpha">
		<?php print $linked_logo_img; ?>
			<?php print render($page['sitebranding']); ?>
		</div>
		<div id="siteinfo" class="column grid-12">
			<?php print render($page['siteinfo']); ?>
		</div>
		<div id="site-subheader" class="grid-12  clearfix">
			<?php print render($page['site_subheader']); ?>			
		</div>
	</div>
	
	
		
	<div id="main-area" class="container-16 clearfix">
		<div id="line" class="grid-4 "   >
			<?php print render($page['line']); ?>
			
			<div id="sitenavigation" class="column grid-4 alpha">
			<?php print render($page['sitenavigation']); ?>
			</div>
		</div>
		
		<div id="sitecontent" class="grid-12">
			<?php //print render($page['sitecontent']); ?>
			
			
				
			<div id="contentarea" class="grid-12 " >
				<div id="content" class="column grid-12  ">
					<?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
					<?php print render($page['content']); ?>

						
				</div>
				
				
			</div>
			
		</div>
	</div>	
	
	<div id="footer" align=center class="conainer-16 clearfix" >
		 <div id="footerleft" align=left class="column grid-7 omega" >
                        <?php print render($page['footerleft']); ?>
                </div>
         <div id="footermiddle" align=center class="column grid-3 omega" >
                        <?php print render($page['footermiddle']); ?>
         </div>
         <div id="footerright" align=right class="column grid-6 omega" >
                        <?php print render($page['footerright']); ?>
         </div>
    </div>
</div>
	

 
  

 

