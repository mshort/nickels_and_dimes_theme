<?php

/**
 * @file
 * Template file for default facets
 *
 * @TODO document available variables
 */
?>

<ul class="<?php print $classes; ?>">
  <?php foreach($buckets as $key => $bucket): ?>
    <li><?php print $bucket['link']; ?>&nbsp;<span class="count">(<?php print $bucket['count']; ?>)</span>&nbsp;<span class="plusminus"><?php print $bucket['link_plus']; ?>&nbsp;<?php print $bucket['link_minus']; ?></span></li>
  <?php endforeach; ?>
</ul>
