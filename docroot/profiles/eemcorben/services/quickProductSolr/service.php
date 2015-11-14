<?php
//define('DRUPAL_CORE_COMPATABILITY','7.x');
header('X-Generator:Drupal 7 (http://drupal.org)');
//require_once __DIR__.'/../../libraries/vendor/autoload.php';
require_once __DIR__.'/../../libraries/SolrPhpClient/Apache/Solr/Service.php';
require_once __DIR__.'/../../libraries/SolrAPI/src/SolrAPI.php';
//require_once __DIR__.'/SolrAPI.php';
require_once __DIR__.'/../../services/quickProductSolr/SolrAPI.php';


//use GuzzleHttp\Client;

// Getting the settings into the service
require_once realpath(__DIR__.'/../../../../sites/default/settings.php');

$dbn = $databases['default']['default']['database'];

$location = preg_match('/stylecraft/i',$dbn)? 'stylecraft.com.au':'';
$location = preg_match('/staging/i',$dbn)? 'staging.stylecraft.com.au':$location;
$location = preg_match('/dev/i',$dbn)? 'dev.stylecraft.com.au':$location;
$location = preg_match('/local/i',$dbn)? /**'localhost:9000'/**/'staging.stylecraft.com.au'/**/:$location;
$solrVersionLocal = preg_match('/local/i',$dbn)? true:false;
$return = array();
/** 
 * TODO:    1) add a reference to the configuration file and extract the current configuration
 *          2) automatically extract the facets available.
 *          3) enhance the translation section to be a little more automated
 **/
 
 /*
if(!$solrVersionLocal){
    $solr = new Apache_Solr_Service('au.opensolr.com',80,'/solr/stylecraft_default_index');
}else{
    $solr = new Apache_Solr_Service('localhost',8983,'/solr/stylecraft');
}
*/

$cache_dir = __DIR__.'/../../../../sites/default/files/solr_cache/';
// Make the cache dir if it does not exist
if(!file_exists($cache_dir)){
    try{
        mkdir($cache_dir);
    }catch(Exception $e){
        echo "{error:'Could not create the cache'}";
    }
}
$product_cache_file = $cache_dir.'product_cache.bin';
function solrqs($query = NULL,  Apache_Solr_Service $solr = NULL) {
//function solrqs($query = NULL) {
  return new SolrQ($query, $solr);
  //return node(7);
}



// Exclude brand from the facet filter if it is the only filter


function get_products($nid,$product_cache_file,$location){
    if(!file_exists($product_cache_file)){
        $request_uri = 'http://'.$location.'/products_services/product_solr.json';//.'?nid='.$nid;
        try{
//            $client = new Client();
            $ch = curl_init();
            $options = array(
                CURLOPT_URL=>$request_uri,
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_HEADER=>0);
            if(preg_match('/staging/i',$location)){
                $options[CURLOPT_USERPWD] = "Stylecraft:Preview";
            }
            curl_setopt_array($ch,$options);

            $data = curl_exec($ch);
            $status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            if($status == 200){
                $body = json_decode($data);
                file_put_contents($product_cache_file,serialize($body));
            }else{
                $return['error'] = array('message'=>'Could not connect to the data source');
                return $return;
            }
        }catch(Exception $e){
            $return['error']= array('message' =>'Could not use guzzle','exception'=>$e); 
        }
    }else{
        $body = unserialize(file_get_contents($product_cache_file));
    }
    foreach($body as $v){
        if(in_array($v->nid,$nid)){
            $return['products'][] = $v;
        }
    }
    return $return;
}


$field_translate = array(
    "field_product_type"=>"{!tag=type}im_field_product_type",
    "field_status"=>"ss_field_status",
    "field_architect"=>"im_field_architect",
    "field_stackablity"=>"is_field_stackability",
    "field_client"=>"im_field_client",
    "field_tags"=>"im_field_tags",
    "field_designer"=>'is_field_range$field_designer',
    "field_article_location"=>"is_field_article_location",
    "field_origin"=>"im_field_origin",
    "field_application"=>"im_field_application",
    "field_article_type"=>"im_field_article_type",
    "field_range"=>"{!tag=range}is_field_range",
    "field_price_category"=>"im_field_price_category",
    "field_product_colour"=>"im_field_product_colour",
    "field_brand"=>"is_field_range\:field_brand",
    "field_range%253Afield_brand"=>'{!tag=brand}is_field_range$field_brand',
    "field_range%253Afield_designer"=>'is_field_range$field_designer',
    "field_is_environmental"=>'bm_field_certification$field_is_environmental',
    "field_is_available"=>"bs_field_is_available",
    "field_lead_time"=>"im_field_lead_time",
    "field_can_customise"=>"bs_field_can_customise",
    "field_is_abw"=>"bs_field_is_abw",
    "field_is_australian"=>"bs_field_is_australian",
    "field_is_new"=>"bs_field_is_new",
    "field_finish"=>"{!tag=finish}im_field_finish",
    "field_can_fold"=>"bs_field_can_fold",
    "field_can_stack"=>"bs_field_can_stack",
    "field_type"=>'ss_type',
    "field_certification"=>'im_field_certification'
);
if($solrVersionLocal){
    $field_translate = array_merge($field_translate,array(
        "field_range%253Afield_brand"=>'{!tag=brand}is_field_range\:field_brand',
        "field_range%253Afield_designer"=>"is_field_range\:field_designer",
        "field_is_environmental"=>"bm_field_certification\:field_is_environmental")
    );
}
$field_translate_flip = array_flip($field_translate);

if($solrVersionLocal){
    $field_translate_flip['is_field_range:field_designer'] = "field_range%253Afield_designer";
    $field_translate_flip['is_field_range:field_brand']= "field_range%253Afield_brand";
    $field_translate_flip['bm_field_certification:field_is_environmental'] = 'field_certification%253A:field_is_environmental';
}else{
    $field_translate_flip['is_field_range$field_designer'] = "field_range%253Afield_designer";
    $field_translate_flip['is_field_range$field_brand']= "field_range%253Afield_brand";
    $field_translate_flip['bm_field_certification$field_is_environmental'] = 'field_certification%253A:field_is_environmental';
}

$field_translate_flip['im_field_product_type']= "field_product_type";
$field_translate_flip['is_field_range']= "field_range";
$field_translate_flip['im_field_finish']= "field_finish";


if(isset($_POST['filter_fields'])){
    foreach($_POST['filter_fields'] as $k=>$v){
        $items = "";
        if(is_array($v)){
            foreach($v as $k2=>$v2){
                $items .= "\"{$v2}\" OR ";
            }
            $items = preg_replace("/ OR $/","",$items);
        }else{
            $items = $v;
        }
        $filters[] = "{$field_translate[$k]}:({$items})";
        $facet_filters[] = "{$field_translate[$k]}:({$items})";
    }
}

$result = array();


//$result = solrqs('product',$solr)
$result = solrqs('product')
//    ->queryFields('ss_type')
    ->useQueryParser(Query::QUERY_PARSER_DISMAX)
    ->setRequestMethod(Apache_Solr_Service::METHOD_POST)
    ->retrieveFields('is_nid')
    ->facetField('ss_field_status',0,FALSE,-1)
    ->facetField('im_field_architect',0,FALSE,-1)
    ->facetField('is_field_stackability',0,FALSE,-1)
    ->facetField('im_field_client',0,FALSE,-1)
    ->facetField('im_field_tags',0,FALSE,-1)
    ->facetField('is_field_article_location',0,FALSE,-1)
    ->facetField('im_field_origin',0,FALSE,-1)
    ->facetField('im_field_application',0,FALSE,-1)
    ->facetField('im_field_article_type',0,FALSE,-1)
    ->facetField('{!ex=range}is_field_range',0,FALSE,-1)
    ->facetField('im_field_price_category',0,FALSE,-1)
    ->facetField('im_field_product_colour',0,FALSE,-1)
    ->facetField('{!ex=type}im_field_product_type',0,FALSE,-1)
    ->facetField('bs_field_is_available',0,FALSE,-1)
    ->facetField('im_field_lead_time',0,FALSE,-1)
    ->facetField('ss_type',0,FALSE,-1)
    ->facetField('bs_field_can_customise',0,FALSE,-1)
    ->facetField('bs_field_is_abw',0,FALSE,-1)
    ->facetField('bs_field_is_australian',0,FALSE,-1)
    ->facetField('bs_field_is_new',0,FALSE,-1)
    ->facetField('{!ex=finish}im_field_finish',0,FALSE,-1)
    ->facetField('im_field_certification',0,FALSE,-1)
    ->facetField('bs_field_can_fold',0,FALSE,-1)
    ->facetField('bs_field_can_stack',0,FALSE,-1)
    ->mergeParams(array('facet'=>'true'));
    
echo "moops";
exit;
if($solrVersionLocal){
    $result->facetField('{!ex=brand}is_field_range:field_brand',0,FALSE,-1)
    ->facetField('is_field_range:field_designer',0,FALSE,-1)
    ->facetField('bm_field_certification:field_is_environmental',0,FALSE,-1);
}else{
    $result->facetField('{!ex=brand}is_field_range$field_brand',0,FALSE,-1)
    ->facetField('is_field_range$field_designer',0,FALSE,-1)
    ->facetField('bm_field_certification$field_is_environmental',0,FALSE,-1);
}


if(isset($filters)){
    $result->filters($filters);
}
if(isset($_POST['offset'])){
    $result->offset($_POST['offset']);
}
if(isset($_POST['limit'])){
    $result->limit($_POST['limit']);
}

$result = $result->search();
echo "moops2";
exit;
if(isset($_POST['userid'])){
    if($_POST['userid'] != 'none'){
        // $dbi = $databases['default']['default'];
       // $db = mysqli_connect($dbi['host'], $dbi["username"], $dbi["password"],$dbi['database']);
        $sql = <<<SQL
            SELECT flag_lists_flags.fid AS fid, flag_lists_flags.uid AS flag_lists_flags_uid FROM {flag_lists_flags} flag_lists_flags WHERE (( (flag_lists_flags.uid = '1' ) )) LIMIT 10 OFFSET 0
            SELECT node.title AS node_title, node.nid AS nid, node.created AS node_created FROM {node} node LEFT JOIN {flag_lists_content} flag_lists_content ON node.nid = flag_lists_content.entity_id LEFT JOIN {flag_lists_flags} flag_lists_flags ON flag_lists_content.fid = flag_lists_flags.fid WHERE (( (flag_lists_flags.fid = '61' ) )AND(( (node.status = '1') AND (node.type IN ('product')) ))) ORDER BY node_created DESC LIMIT 10 OFFSET 0
SQL;
    }
}

//print_r($result->response->docs);
foreach($result->response->docs as $doc){
    $nid[] = $doc->is_nid;
}

$return = array_merge($return,get_products($nid,$product_cache_file,$location));



$facets = $result->facet_counts->facet_fields;
foreach($facets as $f=>$v){
    $return['facets'][$field_translate_flip[$f]]=$v;
}
$return['numFound'] = $result->response->numFound;


echo json_encode($return);
