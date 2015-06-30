<?php
require_once __DIR__.'/../../libraries/SolrAPI/src/SolrAPI.php';
require_once __DIR__.'/../../libraries/SolrAPI/src/SolrAPI/Query.php';

class SolrQ Extends SqQuery {
    protected $requestMethod;
    public function facetField($field,$mincount = 0, $sort = TRUE, $limit=100, $offset = 0) {
//        $this->params['facet'] = 'true';
//        $this->params['facet.limit'] = $limit;
//        $this->params['facet.offset'] = $offset;
//        $this->params['facet.mincount'] = $mincount;
        //, $method = 'fc'
        //$this->params['facet.method'] = $method;
        $this->params['facet.field'][] = $field;
        $this->params['f.'.$field.'.facet.mincount'] = $mincount;
        $this->params['f.'.$field.'.facet.limit'] = $limit;
        $this->params['f.'.$field.'.facet.offset'] = $offset;

        // 1.4 will allow 'count' and 'index' as sort values.
//        if (is_string($sort)) {
//            $this->params['f.'.$field.'.facet.sort'] = $sort;
//        }
//            else {
//            $this->params['f.'.$field.'.facet.sort'] = $sort ? 'true' : 'false';
//        }
        
        return $this;
    }
    public function setRequestMethod($method){
        $this->requestMethod = $method;
        return $this;
    }
    public function getRequestMethod(){
        if(!$this->requestMethod){
            $this->setRequestMethod(Apache_Solr_Service::METHOD_GET);
        }
        return $this->requestMethod;
    }
  /**
   * Execute a search and get the results.
   *
   * Note that this does only the minimal amount of preparation on a search. It does
   * not attempt (as apachesolr_search_execute() does) to do sophisticated parameter
   * setting. It assumes you have done that already.
   * 
   * @return
   *  The search results as an Apache_Solr_Response object.
   * @see Apache_Solr_Service
   */
  public function search() {

    // Do necessary quote encoding for XML payload.
    $query = htmlentities($this->query(), ENT_NOQUOTES, 'UTF-8');

    // Need to modify a few fields immediately before run.
    if ($this->isSpellchecking() && !isset($this->params['spellcheck.q'])) {
      $this->params['spellcheck.q'] = $query;
    }

    $response = $this->solr->search($query, $this->offset(), $this->limit(), $this->params,$this->getRequestMethod());

    return $response;
  }

}
