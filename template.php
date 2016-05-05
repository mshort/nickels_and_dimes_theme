<?php

function ninesixtydimev2_preprocess_islandora_book_book(array &$variables) {
  module_load_include('inc', 'islandora', 'includes/metadata');

  $object = $variables['object'];
  $variables['metadata'] = islandora_retrieve_metadata_markup($object);
  $connection = islandora_get_tuque_connection();
  $query = "PREFIX islandora: <" . ISLANDORA_RELS_EXT_URI . ">
            SELECT ?object
            FROM <#ri>
            WHERE {
              ?object <islandora:isPageOf> <info:fedora/$object>;
                      <islandora:isPageNumber> '1'.
              }";
  $results = $connection->repository->ri->sparqlQuery($query);
  $variables['cover'] = $connection->repository->getObject($results[0]['object']['value']);

  // Begin custom code for related editions

  // Get the issue edition URI from the copy's RELS-EXT
  $rels = $object->relationships->get();
  $variables['rels'] = $rels;
  $related = array();

  foreach ($rels as $key => $rel) {
        if ($rel['predicate']['value'] == "IsCopyOf") {
                $edition = $rel['object']['value'];
        }
  }
  try {
	if (isset($edition)) {
		// Using the issue edition URI, query dimenovels.org for all contained works in the issue
		include_once('/var/www/drupal/htdocs/sites/all/libraries/arc2/ARC2.php');
		
		/* configuration */
		$config = array(
  			/* db */
  			'db_host' => 'localhost', /* optional, default is localhost */
  			'db_name' => 'arc2',
  			'db_user' => 'xxx',
  			'db_pwd' => 'xxx',

  			/* store name (= table prefix) */
  			'store_name' => 'dimenovels_store',
		);
		
		/* instantiation */
		$sparql = ARC2::getStore($config);
		
		if (!$sparql->isSetUp()) {
		  $sparql->setUp();
		}
		
		$work_results = $sparql->query('SELECT ?work ?title WHERE {<'.$edition.'> <http://rdaregistry.info/Elements/u/containerOf> ?work_edition . ?work_edition <https://dimenovels.org/ontology#IsRealizationOfCreativeWork> ?work . ?work <http://rdaregistry.info/Elements/u/preferredTitleForTheResource> ?title .}', 'rows');
		$editions = array();
		foreach ($work_results as $row) {
			$work = $row['work'];
			$title = $row['title'];
			// Start the query
			$query_string = "islandora/search/PID:";
			// Get the edition URIs
			$edition_results = $sparql->query('SELECT ?edition WHERE {<'.$work.'> <https://dimenovels.org/ontology#HasRealizationOfCreativeWork> ?work_editions .'.'?work_editions <http://rdaregistry.info/Elements/u/containedIn> ?edition .}', 'rows');
			$i = 0;
			$queryPids = array();
			foreach ($edition_results as $row) {
				$edition = $row['edition'];
				// Retrieve the pid
				$query = "SELECT ?object
							FROM <#ri>
							WHERE {
						?object <https://dimenovels.org/ontology#IsCopyOf> <".$edition."> .
					FILTER(?object!=<info:fedora/".$object.">)
							}";
				$edition_results = $connection->repository->ri->sparqlQuery($query);
				if (isset($edition_results[0])) {
					$edition_pid = '"'.$edition_results[0]['object']['value'].'"';
					$queryPids[] = $edition_pid;
				}
			}
			if (count($queryPids) > 0) {
				$works[$work]['title']=$title;
				$works[$work]['query']=$query_string . '(' . implode(' OR ', $queryPids) . ')';
				$editions[] = l($works[$work]['title'], $works[$work]['query'], array('query' =>array('sort'=>'mods_dateIssued_dt asc')));
			}
		}
	   }
	   if (!empty($editions)) {
		   $list_variables = array(
			'items' => $editions,
				'title' => t('Browse related editions:'),
				'type' => 'ul',
				'attributes' => array('class' => 'related_editions'),
				);
			$variables['editions'] = theme_item_list($list_variables);
		}  
	}
   catch (Exception $e) {
	         watchdog_exception('Related editions', $e, 'Got an exception while searching for related editions.', array(), WATCHDOG_ERROR);
	       }
// End custom code
}

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function ninesixtydimev2_preprocess_node(&$variables) {

$node = $variables['node'];
 
  // Check first the "body field" exists or not.
  $field = field_get_items('node', $node, 'body');

  // If available do execution ..  
  if($field){
    $body = $node->body['und'][0]['safe_value'];
    if ($variables['content']['body'][0]['#markup'] == $body){
      unset($variables['content']['links']['node']['#links']['node-readmore']);
    }
  }

  // And if not available ..
  else{
    // Do nothing ..
  }
}

function ninesixtydimev2_preprocess_page(&$variables) {
  $query = 'PID:dimenovels\:* AND RELS_EXT_hasModel_uri_ms:"info:fedora/islandora:bookCModel"';
  $params = array(
    'sort' => 'fgs_createdDate_dt desc',
    'fl' => 'dc.title, PID',
  );
  $url = parse_url(variable_get('islandora_solr_url', 'localhost:8080/solr'));
  $solr = new Apache_Solr_Service($url['host'], $url['port'], $url['path'] . '/');
  $solr->setCreateDocuments(FALSE);
  try {
    $results = $solr->search($query, 0, 15, $params);
    $json = json_decode($results->getRawResponse(), TRUE);
  }
  catch (Exception $e) {
    watchdog_exception('Dime Novel theme', $e, 'Got an exception while searching recent titles for callback.', array(), WATCHDOG_ERROR);
  }
  $links = array();
  foreach ($json['response']['docs'] as $choice) {
    if (isset($choice['dc.title'])) {
      $links[] = l($choice['dc.title'][0], 'islandora/object/' . $choice['PID']);
    }
  }
  if (count($links) > 0) {
    $list_variables = array(
      'items' => $links,
      'title' => t('Recently added titles'),
      'type' => 'ul',
      'attributes' => array('class' => 'recent_titles'),
    );
    $variables['recent_titles'] = theme_item_list($list_variables);
  }
}
?>
